<?php

namespace Modules\Core\Http\Controllers\Auth;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Core\Http\Requests\ModuleCreateRequest;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;

class ModuleController extends Controller
{
    private $module;

    public function __construct(ModuleInterface $_module)
    {
        $this->module = $_module;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($prefix)
    {
        return view('core::auth.modules.index',[
            'prefix'=>$prefix,
            'datas'=>$this->module->getAll()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($prefix)
    {
        return view('core::auth.modules.create',[
            'prefix'=>$prefix,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ModuleCreateRequest $request,$prefix)
    {
       if($this->module->store($request)){
            Session::flash('success','Store your Data successfully');
            return redirect()->route('core.modules.index',$prefix);
       }else{
           Session::flash('error',$this->module->Errors);
           return redirect()->route('core.modules.index',$prefix);
       }
    }


    /**
     * This method use for Filter
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function filter(Request $request,$prefix){
        return view('core::auth.modules.index',[
            'prefix'=>$prefix,
            'datas'=>$this->module->filter($request)
        ]);
    }





    /**
     * Change Data status
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($prefix,$id){
        if($this->module->changeStatus($id)){
            Session::flash('success','Change your data status.');
            return redirect()->back();
        }else{
            Session::flash('error',$this->shop->Errors);
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
        $data = $this->module->findBy(['id'=>$id]);
        return view('core::auth.modules.edit',[
            'prefix'=>$prefix,
            'data'=>$data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ModuleCreateRequest $request,$prefix,$id)
    {
        if($this->module->update($request,$id)){
            Session::flash('success','Update your Data successfully');
            return redirect()->route('core.modules.index',$prefix);
        }else{
            Session::flash('error',$this->module->Errors);
            return redirect()->route('core.modules.index',$prefix);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($prefix,$id)
    {
        if($this->module->delete($id)){
            Session::flash('success','Delete your Data successfully');
            return redirect()->route('core.modules.index',$prefix);
        }else{
            Session::flash('error',$this->module->Errors);
            return redirect()->route('core.modules.index',$prefix);
        }
    }
}
