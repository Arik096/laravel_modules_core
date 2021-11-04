<?php
namespace Modules\Core\Repositories\Location;


use App\Repository\AppSettingRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\Setting\Union;
use Illuminate\Support\Facades\Session;
use Modules\Core\Repositories\Contracts\Location\UnionRepositoryInterface;

class UnionRepository implements UnionRepositoryInterface
{


    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
	public function all(){
		return Union::with('upazila')->paginate(50);
	}


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function allWithOutPaginate(){
        return Union::with('upazila')
            ->get();
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByUpazilaId($id){
        return Union::with('upazila')
            ->where(['is_active'=>1])
            ->where(['upazila_id'=>$id])
            ->get();
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
	public function find($id){
		return Union::with('upazila')->findOrFail($id);
	}


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findIsActive(){
		return Union::with('upazila')->where(['is_active'=>1])->get();
	}


    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function store($request){
		$validator = Validator::make($request->all(), [
	            'en_name' => 'required|unique_translation:unions,name',
	            'bn_name' => 'required|min:3|max:191',
	            'upazila_id' => 'required',
	    ]);

        if ($validator->fails()) {
        	Session::flash('showModel','yes');
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
		$obj = new Union();
		$obj->setTranslation('name', 'en', $request->en_name);
		$obj->setTranslation('name', 'bn',$request->bn_name);
		$obj->upazila_id = $request->upazila_id;
		$obj->is_active = 1;
		$obj->save();
		Session::flash('success','Saved your union data successfully');
	}


    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
	public function update($request,$id){
		$validator = Validator::make($request->all(), [
	            'en_name' => 'required|unique_translation:unions,name',
	            'bn_name' => 'required|min:3|max:191',
	            'upazila_id'=>'required'
	    ]);

        if ($validator->fails()) {
        	Session::flash('showEditModel','yes');
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

		$pram = $this->find($id);
		$pram->setTranslation('name', 'en', $request->en_name);
		$pram->setTranslation('name', 'bn',$request->bn_name);
		$pram->upazila_id = $request->upazila_id;
		$pram->is_active = 1;
		$pram->update();
		Session::flash('success','Update your union data successfully');
	}


    /**
     * @param $status
     * @param $id
     */
	public function isActive($status,$id){
		$obj = $this->find($id);
        if($status === "yes"){
            $obj->is_active = 1;
        }else{
           $obj->is_active = 0;
        }
        $obj->update();
	}


    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
	public function search($request){
		$search = trim($request->search);
		$data = Union::paginate(50);
		if($search != ""){
			$data = Union::with('upazila')->where('name', 'LIKE', '%'. $search .'%')->paginate(50);
		}

		if($request->activation != ""){
			$data = Union::with('upazila')->where(['is_active'=>$request->activation])->paginate(50);
		}

		if($request->upazila_id != ""){
			$data = Union::with('upazila')->where(['upazila_id'=>$request->upazila_id])->paginate(50);
		}

		return $data;
	}


    /**
     * @param $id
     * @throws \Exception
     */
	public function delete($id){
		$pram = $this->find($id);
		$pram->delete();
	}


}





