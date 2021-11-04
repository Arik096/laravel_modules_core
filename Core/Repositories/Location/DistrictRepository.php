<?php

namespace Modules\Core\Repositories\Location;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Modules\Core\Models\Setting\District;
use Modules\Core\Repositories\Contracts\Location\DistrictRepositoryInterface;


class DistrictRepository implements DistrictRepositoryInterface
{

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all()
    {
        return District::with('division')->paginate(50);
    }


    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function allWithOutPaginate()
    {
        return District::with('division')
            ->where(['is_active'=>1])
            ->get();
    }



    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findByDivisionId($id)
    {
        return District::with('division')
            ->where(['division_id'=>$id])
            ->where(['is_active'=>1])
            ->get();
    }



    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function find($id)
    {
        return District::with('division')->find($id);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findIsActive()
    {
        return District::with('division')->where(['is_active' => 1])->get();
    }


    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($request)
    {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique_translation:districts,name',
            'bn_name' => 'required|min:3|max:191',
            'division_id' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('showModel', 'yes');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $obj = new District();
        $obj->setTranslation('name', 'en', $request->en_name);
        $obj->setTranslation('name', 'bn', $request->bn_name);
        $obj->division_id = $request->division_id;
        $obj->is_active = 1;
        $obj->save();
        Session::flash('success', "Data saved successfully");
    }


    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($request, $id)
    {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique_translation:districts,name',
            'bn_name' => 'required|min:3|max:191',
            'division_id' => 'required'
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
        $pram->division_id = $request->division_id;
        $pram->is_active = 1;
        $pram->update();
        Session::flash('success', "Update District Successfully");
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

        $data = District::with('division');

        if ($search != "") {
            $data->where('name', 'LIKE', '%' . $search . '%');
        }

        if ($request->activation != "") {
            $data->where(['is_active' => $request->activation]);
        }

        if ($request->division_id != "") {
            $data->where(['division_id' => $request->division_id]);
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





