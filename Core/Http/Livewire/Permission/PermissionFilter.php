<?php

namespace Modules\Core\Http\Livewire\Permission;

use Livewire\Component;
use Modules\Core\Models\Auth\Module;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;
use Modules\Core\Repositories\Contracts\Auth\SubModuleInterface;

class PermissionFilter extends Component
{

    public $Moduleid;

    public function render(SubModuleInterface $subModule, ModuleInterface $module)
    {
        $modules = $module->getAllActiveWithoutPaginate();
        if($this->Moduleid != ""){
            $submodules = $subModule->getAllActiveWithoutPaginate()->where('module_id','',$this->Moduleid);
        }else{
            $submodules = $subModule->getAllActiveWithoutPaginate();
        }
        return view('core::livewire.permission.permission-filter',[
            'modules'=>$modules,
            'submodules'=>$submodules,
        ]);
    }



}
