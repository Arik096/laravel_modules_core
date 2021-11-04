<?php

namespace Modules\Core\Http\Controllers\Auth;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Core\Http\Requests\RoleCreateRequest;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;
use Modules\Core\Repositories\Contracts\Auth\SubModuleInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    private $submodule;
    private $module;

    public function __construct(SubModuleInterface $_submodule,ModuleInterface $_module)
    {
        $this->submodule = $_submodule;
        $this->module = $_module;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($prefix)
    {
        return view('core::auth.roles.index',[
            'prefix'=>$prefix,
            'submodules'=>$this->submodule->getAllActiveWithoutPaginate(),
            'modules'=>$this->module->getAllActiveWithoutPaginate(),
            'datas'=>Role::paginate(100)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($prefix)
    {

        return view('core::auth.roles.create',[
            'prefix'=>$prefix,
            'modules'=>$this->module->getAllActiveWithoutPaginate(),
            'submodules'=>$this->submodule->getAllActiveWithoutPaginate(),
            'flags'=>DB::table('flag')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(RoleCreateRequest $request,$prefix)
    {

        try{

            if(isset($request->permission_id)){
                $role = Role::create([
                    'name' => strtolower($request->name),
                    'flag'=>$request->flag,
                    'comments'=>$request->comments
                ]);

                //$premissions = Permission::whereIn('id',$request->permission_id)->get();
                $role->syncPermissions($request->permission_id);


                $submodules = $request->select_submodule_id;
                $unique_sub_modules = array_unique($submodules);

                //Set Role Submodule
                foreach ($unique_sub_modules as $submodule){
                    DB::table('component_role')->insert([
                        'role_id'=>$role->id,
                        'component_id'=>$submodule,
                        'created_at'=>now(),
                        'updated_at'=>now(),
                    ]);
                }



                Session::flash('success','Store your Data successfully');
                return redirect()->route('core.roles.index',$prefix);

            }else{
                Session::flash('warning','Please Select Your Role permission.');
                return redirect()->back();
            }


        }catch (\Exception $exception){
            Session::flash('error',$exception->getMessage());
            return redirect()->route('core.permissions.index',$prefix);
        }
    }


    /**
     * This method use for Filter
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function filter(Request $request,$prefix){

        $data = Role::paginate(100);
        if($request->search != ""){
            $data = Role::where('name','like','%'.$request->search.'%')->paginate(100);
        }

        return view('core::auth.roles.index',[
            'prefix'=>$prefix,
            'datas'=>$data,
            'modules'=>$this->module->getAllActiveWithoutPaginate(),
            'submodules'=>$this->submodule->getAllActiveWithoutPaginate(),
        ]);
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
        $data = Role::with('permissions')->find($id);
        $permissionIds = null;
        $moduleids = null;
        $submodulesIds = null;
        if(!is_null($data->permissions)){
            $permissionIds = $data->permissions->pluck('id')->toArray();
            $moduleids = $data->permissions->pluck('module_id')->toArray();
            $submodulesIds = $data->permissions->pluck('sub_module_id')->toArray();
        }

        return view('core::auth.roles.edit',[
            'prefix'=>$prefix,
            'data'=>$data,
            'modules'=>$this->module->getAllActiveWithoutPaginate(),
            'submodules'=>$this->submodule->getAllActiveWithoutPaginate(),
            'flags'=>DB::table('flag')->get(),
            'moduleIds'=>$moduleids,
            'submodulesIds'=>$submodulesIds,
            'permissionIds'=>$permissionIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(RoleCreateRequest $request,$prefix,$id)
    {

        try{

            if(isset($request->permission_id)){

                try {
                    DB::beginTransaction();

                    $role = Role::find($id);
                    $role->name = strtolower($request->name);
                    $role->flag = $request->flag;
                    #$role->comments = $request->comments;
                    $role->update();
                    //$premissions = Permission::whereIn('id',$request->permission_id)->get();
                    $role->syncPermissions($request->permission_id);

                    $submodules = $request->select_submodule_id;
                    $unique_sub_modules = array_unique($submodules);



                    //Remove all data
                    DB::table('component_role')->where(['role_id'=>$id])->delete();

                    //Set Role Submodule
                    foreach ($unique_sub_modules as $submodule){
                        DB::table('component_role')->insert([
                            'role_id'=>$role->id,
                            'component_id'=>$submodule,
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ]);
                    }

                    DB::commit();
                    Session::flash('success','Update your Data successfully');
                    return redirect()->route('core.roles.index',$prefix);


                }catch (\Exception $exception){
                    DB::rollBack();
                    Session::flash('error',$exception->getMessage());
                    return redirect()->back();

                }


            }else{
                Session::flash('warning','Please Select Your Role permission.');
                return redirect()->back();
            }


        }catch (\Exception $exception){
            Session::flash('error',$exception->getMessage());
            return redirect()->route('core.permissions.index',$prefix);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($prefix,$id)
    {
        try{
            $permission = Role::findById($id);
            $permission->delete();
            Session::flash('success','Delete your Data successfully');
            return redirect()->route('core.roles.index',$prefix);

        }catch (\Exception $exception){
            Session::flash('error',$exception->getMessage());
            return redirect()->route('core.roles.index',$prefix);
        }
    }
}
