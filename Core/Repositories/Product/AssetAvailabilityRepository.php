<?php

namespace Modules\Core\Repositories\Product;

use Modules\Core\Models\Products\AssetAvailability;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Session;

class AssetAvailabilityRepository implements RepositoryInterface {

    public $Error;

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
            $obj->setTranslation('title', 'en', $request->title_english);
            $obj->setTranslation('title', 'bn', $request->title_bangla);
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
            $obj->setTranslation('title', 'en', $request->title_english);
            $obj->setTranslation('title', 'bn', $request->title_bangla);
            $obj->comments  = $request->comments;
            $obj->is_active = $request->action_type;
            $obj->update();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


	public function isActive($prefix, $status, $id){
        try{
            $obj = AssetAvailability::find($id);
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





