<?php

namespace Modules\Core\Repositories\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Products\Unit;
use Modules\Core\Repositories\Contracts\UnitRepositoryInterface;


class UnitRepository implements UnitRepositoryInterface
{

    public $Errors;

    public function export($prefix)
    {
        // TODO: Implement export() method.
    }

    public function all($prefix)
    {

        $data = Unit::withoutTrashed()->where(['is_hidden' => 0])->orderBy('id', 'desc')->paginate(100);

        return $data;
    }

    public function allByActive($prefix)
    {
        $data = Unit::withoutTrashed()->where(['is_hidden' => 0])->orderBy('id', 'desc')->get();

        return $data;
    }

    public function create($prefix)
    {
        // TODO: Implement create() method.
    }

    public function find($prefix, $id)
    {
        // TODO: Implement find() method.
    }

    public function store($request, $prefix)
    {
        DB::beginTransaction();

        try {

            $obj = new Unit();
            $obj->setTranslation('name', 'en', $request->en_name_unit);
            $obj->setTranslation('name', 'bn', $request->bn_name_unit);
            $obj->sub_unit = $request->sub_unit;
            $obj->is_active = 1;
            $obj->save();


            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollBack();

            $this->Error = $ex->getMessage();

            return false;

        }

    }

    public function update($request, $prefix, $id)
    {
        DB::beginTransaction();

        try {

            $pram = Unit::find($id);

            $pram->setTranslation('name', 'en', $request->en_name_unit);
            $pram->setTranslation('name', 'bn', $request->bn_name_unit);
            $pram->sub_unit = $request->sub_unit_edit;
            $pram->is_active = 1;
            $pram->update();

            DB::commit();
            return true;

        } catch (\Exception $ex) {

            DB::rollBack();

            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function delete($prefix, $id)
    {
        try {

            $obj = Unit::find($id);
            $obj->delete();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function filter($request, $prefix)
    {

        $data = Unit::withoutTrashed();

        if ($request->search != "") {
            $data->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        return $data->where(['is_hidden' => 0])->orderBy('id', 'desc')->paginate(100);
    }

    public function activation($prefix, $status, $id)
    {
        try {

            $obj = Unit::find($id);

            if ($status === "yes") {

                $obj->is_active = 1;

            } else {

                $obj->is_active = 0;
            }

            $obj->update();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;

        }
    }

    public function edit($prefix, $id)
    {
        $data = Unit::where('id', $id)->first();

        return $data;
    }
}





