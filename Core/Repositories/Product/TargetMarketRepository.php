<?php

namespace Modules\Core\Repositories\Product;

use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\Products\TargetMarket;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Session;

class TargetMarketRepository implements RepositoryInterface {

    public $Error;

    public function export($prefix){
        return TargetMarket::withoutTrashed()->paginate(100);
    }

//withoutTrashed()
	public function all($prefix){
        return TargetMarket::withoutTrashed()->paginate(100);
	}


    public function searchProductList($prefix){

    }


	public function allByActive($prefix){
		return TargetMarket::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return TargetMarket::find($id);
	}


	public function store($request,$prefix){
        try{

            $obj = new TargetMarket();
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
            $obj = TargetMarket::find($id);
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


	public function isActive($prefix,$status,$id){
        try{
            $obj = TargetMarket::find($id);
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

	public function search($request, $prefix){
		$search = trim($request->search);
		$data = TargetMarket::withoutTrashed()->paginate(100);
		if($search != ""){
			$data = TargetMarket::withoutTrashed()->where('title', 'LIKE', '%'. $search .'%')->paginate(100);
		}
		if($request->is_active != ""){
			$data = TargetMarket::withoutTrashed()->where(['is_active'=>$request->is_active])->paginate(100);
		}
		return $data;
	}


	public function delete($prefix,$id){
        try{
            $obj = TargetMarket::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





