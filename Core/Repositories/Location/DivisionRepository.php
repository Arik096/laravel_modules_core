<?php

namespace Modules\Core\Repositories\Location;

use App\Repository\AppSettingRepositoryInterface;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Core\Models\Setting\Division;
use Illuminate\Support\Facades\Session;
use Modules\Core\Repositories\Contracts\Location\DivisionRepositoryInterface;

class DivisionRepository implements DivisionRepositoryInterface
{

    /**
     * @return mixed
     */
    public function all()
    {
        return Division::paginate(50);
    }


    /**
     * @return mixed
     */
    public function allIsActive()
    {
        return Division::where(['is_active' => 1])->paginate(50);
    }


    /**
     * @return mixed
     */
    public function allWithOutPaginate()
    {
        return Division::where(['is_active' => 1])->get();
    }



    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Division::find($id);
    }


    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique_translation:divisions',
            'bn_name' => 'required|min:3|max:191',
        ]);

        if ($validator->fails()) {
            Session::flash('showModel', 'yes');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $obj = new Division();
        $obj->is_active = 1;
        $obj->setTranslation('name', 'en', $request->name);
        $obj->setTranslation('name', 'bn', $request->bn_name);
        $obj->save();
        Session::flash('success', "Add Division Successfully");
    }


    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique_translation:division,' . $id,
            'bn_name' => 'required|min:3|max:191',
        ]);

        if ($validator->fails()) {
            Session::flash('showEditModel', 'yes');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pram = $this->find($id);
        $pram->setTranslation('name', 'en', $request->name);
        $pram->setTranslation('name', 'bn', $request->bn_name);
        $pram->is_active = 1;
        $pram->update();
        Session::flash('success', "Update Division Successfully");
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
     * @return mixed
     */
    public function search($request)
    {
        $search = trim($request->search);

        $data = Division::select('*');

        if ($search != "") {
            $data->where('name', 'LIKE', '%' . $search . '%');
        }

        if ($request->activation != "") {
            $data->where(['is_active' => $request->activation]);
        }
        return $data->paginate(50);
    }


    /**
     * @param $id
     */
    public function delete($id)
    {
        $pram = $this->find($id);
        $pram->delete();
    }


}





