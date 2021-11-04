<?php

namespace Modules\Core\Repositories\Product;

use Modules\Core\Models\Products\ProblemArea;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Session;

class ProblemAreaRepository implements RepositoryInterface{

    public $Error;

    public function export($prefix){
        return ProblemArea::withoutTrashed()->get();
    }

//withoutTrashed()
	public function all($prefix){
        return ProblemArea::withoutTrashed()->get();
	}


    public function searchProductList($prefix){

    }


	public function allByActive($prefix){
		return ProblemArea::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return ProblemArea::find($id);
	}


	public function store($request,$prefix){
        try{
            $obj = new ProblemArea();
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

	public function update($request, $prefix,  $id){
        try{
            $obj = ProblemArea::find($id);
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
            $obj = ProblemArea::find($id);
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
        //dd($request->all());
		$search = trim($request->search);
		$data = ProblemArea::withoutTrashed()->paginate(100);
		if($search != ""){
			$data = ProblemArea::withoutTrashed()->where('title', 'LIKE', '%'. $search .'%')->paginate(100);
		}
		if($request->is_active != ""){
			$data = ProblemArea::withoutTrashed()->where(['is_active'=>$request->is_active])->paginate(100);
		}
		return $data;
	}


	public function delete($prefix,$id){
        try{
            $obj = ProblemArea::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





