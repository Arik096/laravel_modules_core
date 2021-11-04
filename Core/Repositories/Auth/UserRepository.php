<?php


namespace Modules\Core\Repositories\Auth;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Modules\Core\Models\User\RetailUser;
use Modules\Core\Repositories\Contracts\Auth\UserInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRepository implements UserInterface
{
    public $Errors;

    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(){
        return RetailUser::with('spatieRole')->orderBy('id','desc')->paginate(100);
    }



    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllActive(){
        return RetailUser::with('spatieRole')
            ->where(['is_active'=>1])
            ->paginate(100);
    }


    /**
     * when send col name and value
     * @param $key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|Shop
     */
    //$arry = ['id'=>2];
    public function findBy($key){
        $shops = RetailUser::with('spatieRole')
            ->where(['is_active'=>1]);
        foreach ($key as $col => $v){
            $shops->where($col,$v);
        }
        return $shops->firstOrFail();
    }


    /**
     * Insert Shop Table Data
     * @param $request
     * @return bool
     */
    public function store($request){

        $role_has_permission = DB::table('role_has_permissions')->where(['role_id'=>$request->role_id])->pluck('permission_id')->toArray();
        $permissions = Permission::whereIn('id',$role_has_permission)->get();



        if(count($permissions) == 0){
            $this->Errors = "Do not have Any Module or Sub-Module in this Role.";
            return false;
        }

        try{
            DB::beginTransaction();
            $obj = new RetailUser();
            $obj->setTranslation('name','en',$request->name_en);
            $obj->setTranslation('name','bn',$request->name_bn);
            $obj->spatie_role_id = $request->role_id;
            $obj->role_id = 24;
            $obj->flag = 18;
            $obj->email = $request->email;
            $obj->mobile  = $request->mobile;
            $obj->is_active = 1;
            $obj->password = bcrypt($request->password);
            $obj->username = date('ymdhis');
            $obj->save();

            /***********************************
             * Role Management
             **************************************/
            $role = Role::findById($request->role_id);
            $user = RetailUser::find($obj->id);
            $user->assignRole($role->name);


            $modules = [];
            $submodules = [];
            //module assign and submodule
            foreach ($permissions as $pp){
                $modules[] = $pp->module_id;
                $submodules[] = $pp->sub_module_id;
            }

            //Direct Assign Permission
            $user->syncPermissions($permissions->pluck('name')->toArray());


            $unique_modules = array_unique($modules);
            $unique_sub_modules = array_unique($submodules);


            foreach ($unique_modules as $module){
                DB::table('module_user')->insert([
                    'user_id'=>$obj->id,
                    'module_id'=>$module,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            foreach ($unique_sub_modules as $submodule){
                DB::table('submodule_user')->insert([
                    'user_id'=>$obj->id,
                    'submodule_id'=>$submodule,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }







            /***********************************
             * Role Management
             **************************************/
            DB::commit();
            return true;
        }catch (\Exception $exception){
            DB::rollback();
            $this->Errors = $exception->getMessage();
            return false;
        }

    }



    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filter($request){
        $data = RetailUser::with('spatieRole');
        if($request->search != ""){
            $data->where('name','like','%'.$request->search.'%');
        }

        if($request->role_id !=  ""){
            $data->where(['spatie_role_id'=>$request->role_id]);
        }
        return $data->paginate(100);
    }



    /**
     * Update Shop Table Data
     * @param $request
     * @param $id
     * @return bool
     */
    public function update($request,$id){

        try{

            $obj = RetailUser::find($id);
            $selected_role = $obj->spatie_role_id;
            $obj->setTranslation('name','en',$request->name_en);
            $obj->setTranslation('name','bn',$request->name_bn);
            $obj->spatie_role_id = $request->role_id;
            $obj->email = $request->email;
            $obj->mobile  = $request->mobile;
            $obj->is_active = 1;
            $obj->password = bcrypt($request->password);
            $obj->username = date('ymdhis');


            if($selected_role != $request->role_id){

                $role_has_permission = DB::table('role_has_permissions')->where(['role_id'=>$request->role_id])->pluck('permission_id')->toArray();
                $permissions = Permission::whereIn('id',$role_has_permission)->get();

                //Remove Role
                $obj->removeRole($selected_role);

                $obj->update();

                //Syn Role
                $obj->assignRole($request->role_id);

                //Direct Assign Permission
                $obj->syncPermissions($permissions->pluck('name')->toArray());

            }else{
                $obj->update();
            }


            return true;
        }catch (\Exception $exception){
            $this->Errors = $exception->getMessage();
            return false;
        }
    }


    /**
     * Change Status Active or Deactive
     * @param $id
     * @return bool
     */
    public function changeStatus($id){
        try{
            $data = RetailUser::find($id);
            if($data->is_active == 1){
                $data->is_active = 0;
            }else{
                $data->is_active = 1;
            }
            $data->update();
            return true;
        }catch (\Exception $exception){
            $this->Errors = $exception->getMessage();
            return false;
        }
    }


    /**
     * Hard Delete
     * @param $id
     * @return bool
     */
    public function delete($id){
        try{
            $shop = RetailUser::find($id);
            $shop->delete();
            return true;
        }catch (\Exception $exception){
            $this->Errors = $exception->getMessage();
            return false;
        }
    }

}
