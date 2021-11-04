<?php

namespace Modules\Core\Repositories\Location;


use App\Repository\AppSettingRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\Setting\Upazila;
use Illuminate\Support\Facades\Session;
use Modules\Core\Repositories\Contracts\Location\UpazilaRepositoryInterface;


class UpazilaRepository implements UpazilaRepositoryInterface
{

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all()
    {
        return Upazila::with('district')
            ->paginate(50);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function allWithOutPaginate()
    {
        return Upazila::with('district')
            ->where(['is_active'=>1])
            ->get();
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByDistrictId($id)
    {
        return Upazila::with('district')
            ->where(['is_active'=>1])
            ->where(['district_id'=>$id])
            ->get();
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function find($id)
    {
        return Upazila::with('district')->findOrFail($id);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findIsActive()
    {
        return Upazila::with('district')->where(['is_active' => 1])->get();
    }


    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($request)
    {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique_translation:upazilas,name',
            'bn_name' => 'required|min:3|max:191',
            'district_id' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('showModel', 'yes');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $obj = new Upazila();
        $obj->setTranslation('name', 'en', $request->en_name);
        $obj->setTranslation('name', 'bn', $request->bn_name);
        $obj->district_id = $request->district_id;
        $obj->is_active = 1;
        $obj->save();
        Session::flash('success', "Add Upazila Successfully");
    }


    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($request, $id)
    {

        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique_translation:upazilas,name',
            'bn_name' => 'required|min:3|max:191',
            'district_id' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('showEditModel', 'yes');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pram = $this->find($id);
        $pram->setTranslation('name', 'en', $request->en_name);
        $pram->setTranslation('name', 'bn', $request->bn_name);
        $pram->district_id = $request->district_id;
        $pram->is_active = 1;
        $pram->update();
        Session::flash('success', "Update Upazila Successfully");
    }


    /**
     * @param $status
     * @param $id
     */
    public function isActive($status, $id)
    {
        $obj = $this->find($id);
        if ($status === "yes") {
            $obj->is_active = 1;
        } else {
            $obj->is_active = 0;
        }
        $obj->update();
    }


    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($request)
    {
        $search = trim($request->search);

        $data = Upazila::with('district');

        if ($search != "") {
            $data->where('name', 'LIKE', '%' . $search . '%');
        }

        if ($request->activation != "") {
            $data->where(['is_active' => $request->activation]);
        }

        if ($request->district_id != "") {
            $data->where(['district_id' => $request->district_id]);
        }

        return $data->paginate(100);
    }


    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        $pram = $this->find($id);
        $pram->delete();
    }


}





