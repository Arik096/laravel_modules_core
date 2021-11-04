<?php

namespace Modules\Core\Repositories\SignUp;

use Modules\Core\Models\Products\AssetAvailability;
use Modules\Core\Models\Products\EducationRequirement;
use Modules\Core\Models\SignUp\Gender;
use Modules\Core\Models\SignUp\GenderEducation;
use Modules\Core\Repositories\Contracts\GenderEducationInterface;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Session;

class GenderEducationRepository implements GenderEducationInterface {

    public $Error;


	public function all($prefix){
        return GenderEducation::with('education','gender')->paginate(100);
	}


	public function education($prefix){
        return EducationRequirement::where(['is_active'=>1])->paginate(100);
	}

    public function gender($prefix){
        return Gender::paginate(100);
	}


    public function genderFind($prefix, $id){
        return GenderEducation::find($id);
	}



	public function allByActive($prefix){
		return AssetAvailability::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return GenderEducation::findOrFail($id);
	}


	public function store($request, $prefix){
        try{
            //dd($request->all());
            foreach ($request->education_id as $edu_id) {
                $obj = new GenderEducation();
                $obj->gender_id     = $request->gender_id;
                $obj->education_id  = $edu_id;
                $obj->save();
            }
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}




	public function update($request, $prefix, $id){
        try{
            $obj = GenderEducation::find($id);
            $obj->gender_id     = $request->gender_id;
            $obj->education_id  = $request->education_id;
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

		$data = GenderEducation::with('gender','education');
		if($request->gender_id != ""){
		    $data->where(['gender_id'=>$request->gender_id]);
		}

        if($request->education_id != ""){
            $data->where(['education_id'=>$request->education_id]);
        }

		return $data->paginate(100);
	}


	public function delete($prefix,$id){
        try{
            $obj = GenderEducation::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





