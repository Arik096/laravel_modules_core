<?php

namespace Modules\Core\Repositories\Product;

use App\Models\RetailNetwork\ProductOrServiceManagement\EducationInvestment;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\Products\EducationRequirement;
use Session;

class EducationRequirementRepository implements RepositoryInterface{

    public $Error;


    public function export($prefix){
        return EducationRequirement::withoutTrashed()->paginate(100);
    }

    //withoutTrashed()
	public function all($prefix){
        return EducationRequirement::withoutTrashed()->paginate(100);
	}


    public function searchProductList($prefix){

    }


    public function educationMultiData($prefix,$investment_id){
        return EducationInvestment::withoutTrashed()->where(['investment_id'=>$investment_id])->paginate(100);
    }


	public function allByActive($prefix){
		return EducationRequirement::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix, $id){
		return EducationRequirement::find($id);
	}


	public function store($request, $prefix){
        try{
            $obj = new EducationRequirement();
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

	public function update($request,$prefix,$id){
        try{
            $obj = EducationRequirement::find($id);
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


	public function isActive($prefix,$status,$id){
        try{
            $obj = EducationRequirement::find($id);
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
		$data = EducationRequirement::withoutTrashed()->paginate(100);
		if($search != ""){
			$data = EducationRequirement::withoutTrashed()->where('title', 'LIKE', '%'. $search .'%')->paginate(100);
		}
		if($request->is_active != ""){
			$data = EducationRequirement::withoutTrashed()->where(['is_active'=>$request->is_active])->paginate(100);
		}
		return $data;
	}


	public function delete($prefix,$id){
        try{
            $obj = EducationRequirement::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





