<?php

namespace Modules\Core\Repositories\SignUp;

use Intervention\Image\Facades\Image;
use Modules\Core\Models\Products\AssetAvailability;
use Modules\Core\Models\Products\EducationRequirement;
use Modules\Core\Models\SignUp\Gender;
use Modules\Core\Models\SignUp\GenderEducationAsset;
use Modules\Core\Models\User\Role;
use Modules\Core\Repositories\Contracts\RoleNotificationInterface;
use Modules\SignUp\Entities\RoleNotification;
use Session;


class RoleNotificationRepository implements RoleNotificationInterface {

    public $Error;

    /*public function export($prefix){
        return \Modules\Core\Models\Products\AssetAvailability::withoutTrashed()->paginate(100);
    }*/

//withoutTrashed()
	public function all($prefix){
        return RoleNotification::with('role')->paginate(100);
	}

	public function role($prefix){
	    return \Spatie\Permission\Models\Role::where(['is_active'=>1])->paginate(100);
	    //return Role::where(['is_active'=>1])->paginate(100);
    }


	/*public function education($prefix){
        return EducationRequirement::where(['is_active'=>1])->paginate(100);
	}*/

	/*public function assetAvailability($prefix){
        return AssetAvailability::where(['is_active'=>1])->paginate(100);
	}*/

    /*public function gender($prefix){
        return Gender::paginate(100);
	}*/

    /*public function genderEducationAsset($prefix, $id){
        return GenderEducationAsse::find($id);
	}*/


    public function searchProductList($prefix){

    }


	public function allByActive($prefix){
		//return AssetAvailability::withoutTrashed()->where(['is_active'=>1])->paginate(100);
	}


	public function find($prefix,$id){
		return RoleNotification::with('role')->find($id);
	}


	public function store($request, $prefix){
        try{

            //dd($request->all());
            $obj = new RoleNotification();
            $obj->role_id = $request->role_id;
            $obj->setTranslation('question', 'en', $request->question_english);
            $obj->setTranslation('question', 'bn', $request->question_bangla);
            $obj->setTranslation('answer', 'en', $request->answer_english);
            $obj->setTranslation('answer', 'bn', $request->answer_bangla);

//            if($request->hasFile('images')){
//                $imageName  = time().'.'.$request->images->extension();
//                $move_img   = $request->images->move(public_path('sign_up_image/'), $imageName);
//                $obj->image = $imageName;
//            }

            $imageName = null;
            if($request->hasFile('images')){
                $image          = $request->file('images');
                $filename       = time().'.'.$image->getClientOriginalExtension();
                $location       = public_path().'/sign-up/notification_image/'.$filename;
                Image::make($image)->resize(150,100)->save($location);
                $imageName    = 'public/sign-up/notification_image/'.$filename;
            }
            $obj->image = $imageName;
            $obj->save();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}

	public function update($request, $prefix, $id){
        //dd($request->all());
        try{

            $obj = RoleNotification::find($id);
            $obj->setTranslation('question', 'en', $request->question_english);
            $obj->setTranslation('question', 'bn', $request->question_bangla);
            $obj->setTranslation('answer', 'en', $request->answer_english);
            $obj->setTranslation('answer', 'bn', $request->answer_bangla);
            $obj->role_id   = $request->role_id;

            if($request->hasFile('images')){
                $image          = $request->file('images');
                $filename       = time().'.'.$image->getClientOriginalExtension();
                $location       = public_path().'/sign-up/notification_image/'.$filename;
                Image::make($image)->resize(150,100)->save($location);
                $imageName    = 'public/sign-up/notification_image/'.$filename;
                $obj->image = $imageName;
            }
            $obj->update();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


	public function isActive($prefix, $status, $id){
        /*try{
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
        }*/
	}

	public function search($request,$prefix){

		$data = RoleNotification::with('role');
		if($request->search != ""){
            $data->where('question', 'LIKE', '%'. $request->search .'%')
                ->orWhere('answer', 'LIKE', '%'. $request->search .'%');
		}

		if($request->role_id != ""){
            $data->where(['role_id'=>$request->role_id]);
		}

		return $data->paginate(100);
	}


	public function delete($prefix,$id){
        try{
            $obj = RoleNotification::find($id);
            $obj->delete();
            return true;
        }catch (\Exception $ex){
            $this->Error = $ex->getMessage();
            return false;
        }
	}


}





