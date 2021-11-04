<?php

namespace Modules\Core\Repositories\Product;

use App\Repository\RepositoryInterface;
use Modules\Core\Models\Products\ImpactArea;
use Illuminate\Support\Facades\Validator;
use Session;

class ImpactAreaRepository implements RepositoryInterface{

    public $Error;

    public function export($prefix){
        return ImpactArea::withoutTrashed()->paginate(100);
    }

//withoutTrashed()
	public function all($prefix){
        return ImpactArea::withoutTrashed()->paginate(100);
	}


    public function searchProductList($prefix){

    }


	public function allByActive($prefix){
		return ImpactArea::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return ImpactArea::find($id);
	}


	public function store($request, $prefix){
        try{
            $obj = new ImpactArea();
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

	public function update($request,$prefix,$id){
        try{
            $obj = ImpactArea::find($id);
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


	public function isActive($prefix, $status, $id){
        try{
            $obj = ImpactArea::find($id);
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
		$data = ImpactArea::withoutTrashed()->paginate(100);
		if($search != ""){
			$data = ImpactArea::withoutTrashed()->where('title', 'LIKE', '%'. $search .'%')->paginate(100);
		}
		if($request->is_active != ""){
			$data = ImpactArea::withoutTrashed()->where(['is_active'=>$request->is_active])->paginate(100);
		}
		return $data;
	}


	public function delete($prefix,$id){
        try{
            $obj = ImpactArea::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





