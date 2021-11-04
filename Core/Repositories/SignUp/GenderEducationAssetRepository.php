<?php

namespace Modules\Core\Repositories\SignUp;

use Modules\Core\Models\Products\AssetAvailability;
use Modules\Core\Models\Products\EducationRequirement;
use Modules\Core\Models\SignUp\Gender;
use Modules\Core\Models\SignUp\GenderEducation;
use Modules\Core\Models\SignUp\GenderEducationAsset;
use Modules\Core\Repositories\Contracts\GenderEducationAssetInterface;
use Modules\Core\Repositories\Contracts\GenderEducationInterface;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Session;

class GenderEducationAssetRepository implements GenderEducationAssetInterface {

    public $Error;

    /*public function export($prefix){
        return \Modules\Core\Models\Products\AssetAvailability::withoutTrashed()->paginate(100);
    }*/

//withoutTrashed()
	public function all($prefix){
        return GenderEducationAsset::paginate(100);
	}


	public function education($prefix){
        return EducationRequirement::where(['is_active'=>1])->paginate(100);
	}

	public function assetAvailability($prefix){
        return AssetAvailability::where(['is_active'=>1])->paginate(100);
	}

    public function gender($prefix){
        return Gender::paginate(100);
	}

    /*public function genderEducationAsset($prefix, $id){
        return GenderEducationAsse::find($id);
	}*/


    public function searchProductList($prefix){

    }


	public function allByActive($prefix){
		//return AssetAvailability::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return GenderEducationAsset::find($id);
	}


	public function store($request, $prefix){
        try{
            //dd($request->all());
            $obj = new GenderEducationAsset();
            $obj->education_id          = $request->education_id;
            $obj->available_asset_id    = $request->asset_id;
            $obj->gender_id             = $request->gender_id;
            $obj->save();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}

	public function update($request, $prefix, $id){
        try{
            $obj = GenderEducationAsset::find($id);
            $obj->education_id          = $request->education_id;
            $obj->available_asset_id    = $request->asset_id;
            $obj->gender_id             = $request->gender_id;
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
        //return GenderEducationAsset::paginate(100);

		$data = GenderEducationAsset::query();
		if($request->gender_id != ""){
			$data->where(['gender_id'=>$request->gender_id]);
		}

		if($request->education_id != ""){
            $data->where(['education_id'=>$request->education_id]);
		}

		if($request->asset_id != ""){
            $data->where(['available_asset_id'=>$request->asset_id]);
		}
		return $data->paginate(100);
	}


	public function delete($prefix,$id){
        try{
            $obj = GenderEducationAsset::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





