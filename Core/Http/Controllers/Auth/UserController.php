<?php

namespace Modules\Core\Http\Controllers\Auth;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Core\Models\Auth\ModuleUser;
use Modules\Core\Models\User\RetailUser;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;
use Modules\Core\Repositories\Contracts\Auth\UserInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private $user;
    private $module;

    public function __construct(UserInterface $_user,ModuleInterface $_module)
    {
        $this->user = $_user;
        $this->module = $_module;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($prefix)
    {
        return view('core::auth.users.index',[
            'prefix'=>$prefix,
            'datas'=>$this->user->getAll(),
            'roles'=>Role::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($prefix)
    {
        return view('core::auth.users.create',[
            'prefix'=>$prefix,
            'roles'=>Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request,$prefix)
    {
        if($this->user->store($request)){
            Session::flash('success','Store your Data successfully');
            return redirect()->route('core.users.index',$prefix);
        }else{
            Session::flash('error',$this->user->Errors);
            return redirect()->route('core.users.index',$prefix);
        }
    }


    /**
     * This method use for Filter
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function filter(Request $request,$prefix){
        return view('core::auth.users.index',[
            'prefix'=>$prefix,
            'datas'=>$this->user->filter($request),
            'roles'=>Role::all()
        ]);
    }




    public function userModulePermission($prefix,$user_id){

        $fnd_user = RetailUser::with('roles')->find($user_id);
        $user = $fnd_user->getDirectPermissions();

        $role_name = $fnd_user->roles->first()->name;

        $permissionIds = $user->pluck('id')->toArray();
        $moduleids = $user->pluck('module_id')->toArray();
        $submodulesIds = $user->pluck('sub_module_id')->toArray();

       // dd($this->module->getAllWithModuleId($moduleids));


        return view('core::auth.users.permissions',[
            'prefix'=>$prefix,
            'modules'=>$this->module->getAllWithModuleId($moduleids),
            //'modules'=>$this->module->getAllActive(),
            'allPermissions'=>$user,
            'moduleIds'=>$moduleids,
            'submodulesIds'=>$submodulesIds,
            'permissionIds'=>$permissionIds,
            'user_id'=>$user_id,
            'role_name'=>$role_name,
        ]);
    }


    public function userModulePermissionSubmit(Request $request,$prefix,$user_id){

        try{
            DB::beginTransaction();

            $user = RetailUser::find($user_id);
            $all_old_permissions = $user->getAllPermissions()->pluck('id')->toArray();

            $permissionName = Permission::whereIn('id',$request->permission_id)->pluck('name')->toArray();
            $user->syncPermissions($permissionName);
            //update user permission

            $permissions = Permission::whereIn('id',$request->permission_id)->get();
            $modules = [];
            $submodules = [];
            foreach ($permissions as $permission){
                $modules[] = $permission->module_id;
                $submodules[] = $permission->sub_module_id;
            }


            $unique_modules = array_unique($modules);
            $unique_sub_modules = array_unique($submodules);

            //module insert
            DB::table('module_user')->where(['user_id'=>$user_id])->delete();
            foreach ($unique_modules as $module){
                DB::table('module_user')->insert([
                    'user_id'=>$user_id,
                    'module_id'=>$module,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            // submodules
            DB::table('submodule_user')->where(['user_id'=>$user_id])->delete();
            foreach ($unique_sub_modules as $submodule){
                DB::table('submodule_user')->insert([
                    'user_id'=>$user_id,
                    'submodule_id'=>$submodule,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            DB::commit();
            Session::flash('success','Update your data successfully');
            return redirect()->route('core.users.index',$prefix);
        }catch (\Exception $exception){
            DB::rollback();
            Session::flash('error',$exception->getMessage());
            return redirect()->back();
        }

    }






    /**
     * Change Data status
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($prefix,$id){
        if($this->user->changeStatus($id)){
            Session::flash('success','Change your data status.');
            return redirect()->back();
        }else{
            Session::flash('error',$this->user->Errors);
            return redirect()->back();
        }
    }



    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('core::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($prefix,$id)
    {
        $data = $this->user->findBy(['id'=>$id]);
        return view('core::auth.users.edit',[
            'prefix'=>$prefix,
            'data'=>$data,
            'roles'=>Role::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,$prefix,$id)
    {
        if($this->user->update($request,$id)){
            Session::flash('success','Update your Data successfully');
            return redirect()->route('core.users.index',$prefix);
        }else{
            Session::flash('error',$this->user->Errors);
            return redirect()->route('core.users.index',$prefix);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($prefix,$id)
    {
        if($this->user->delete($id)){
            Session::flash('success','Delete your Data successfully');
            return redirect()->route('core.users.index',$prefix);
        }else{
            Session::flash('error',$this->user->Errors);
            return redirect()->route('core.users.index',$prefix);
        }
    }
}
