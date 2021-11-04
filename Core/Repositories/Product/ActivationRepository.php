<?php

namespace Modules\Core\Repositories\Product;

use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Session;

class ActivationRepository implements RepositoryInterface {

    public $Error;

    public function isActive($component, $prefix, $status, $id, $module_name, $tbl_field_name){
        try{
            return 'okey nasim';
            $obj = \Modules\Core\Models\Products\AssetAvailability::find($id);
            if($status === "yes"){
                $obj->is_active = 1;
            }else{
                $obj->is_active = 0;
            }
            $obj->update();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
    }










    public function export($prefix){
        return \Modules\Core\Models\Products\AssetAvailability::withoutTrashed()->paginate(100);
    }

//withoutTrashed()
	public function all($prefix){
        return AssetAvailability::withoutTrashed()->paginate(100);
	}


    public function searchProductList($prefix){

    }


	public function allByActive($prefix){
		return AssetAvailability::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return AssetAvailability::find($id);
	}


	public function store($request, $prefix){
        try{
            $obj = new AssetAvailability();
            $obj->title     = $request->title;
            $obj->comments  = $request->comments;
            $obj->is_active = $request->action_type;
            $obj->save();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}

	public function update($request, $prefix, $id){
        try{
            $obj = AssetAvailability::find($id);
            $obj->title     = $request->title;
            $obj->comments  = $request->comments;
            $obj->is_active = $request->action_type;
            $obj->update();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}

	public function search($request,$prefix){
		$search = trim($request->search);
		$data = AssetAvailability::withoutTrashed()->paginate(100);
		if($search != ""){
			$data = AssetAvailability::withoutTrashed()->where('title', 'LIKE', '%'. $search .'%')->paginate(100);
		}
		if($request->is_active != ""){
			$data = AssetAvailability::withoutTrashed()->where(['is_active'=>$request->is_active])->paginate(100);
		}
		return $data;
	}


	public function delete($prefix,$id){
        try{
            $obj = AssetAvailability::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





