<?php

namespace Modules\Core\Http\Livewire\Permission;

use Livewire\Component;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;
use Modules\Core\Repositories\Contracts\Auth\SubModuleInterface;

class Edit extends Component
{

    public $Moduleid;
    public $data;
    public $module_id = null;
    public $submodule_id = null;

    public function mount($data){
        $this->module_id = $data->module_id;
        $this->submodule_id = $data->sub_module_id;
    }

    public function render(SubModuleInterface $subModule, ModuleInterface $module)
    {
        $modules = $module->getAllActiveWithoutPaginate();
        if($this->Moduleid != ""){
            $submodules = $subModule->getAllActiveWithoutPaginate()->where('module_id','',$this->Moduleid);
        }else{
            $submodules = $subModule->getAllActiveWithoutPaginate();
        }

        return view('core::livewire.permission.edit',[
            'modules'=>$modules,
            'submodules'=>$submodules,
        ]);
    }
}
