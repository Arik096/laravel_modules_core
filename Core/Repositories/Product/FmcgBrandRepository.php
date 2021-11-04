<?php

namespace Modules\Core\Repositories\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Products\Brand;
use Modules\Core\Repositories\Contracts\BrandRepositoryInterface;
use Modules\Core\Repositories\Contracts\Fmcg\FmcgBrandRepositoryInterface;

use Illuminate\Support\Facades\DB;


class FmcgBrandRepository implements FmcgBrandRepositoryInterface
{

    public $Errors;

    public function all()
    {
        return Brand::withoutTrashed()->with(['company'])
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function allPaginate()
    {
        return Brand::withoutTrashed()->with(['company'])
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }

    public function allActive()
    {
        return Brand::withoutTrashed()->with(['company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function filter($request)
    {
        $search = trim($request->search);

        $data = Brand::withoutTrashed()->with(['company']);

        if ($search != "") {
            $data->where('name', 'LIKE', '%' . $search . '%');
        }

        if ($request->company_id != "") {
            $data->where(['company_id' => $request->company_id]);
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        return $data->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }

    public function find($id)
    {
        return Brand::withoutTrashed()->with(['company'])
            ->where(['is_hidden' => 0, 'id' => $id])
            ->orderBy('id', 'desc')
            ->first();
    }

    public function create($request)
    {
        // TODO: Implement create() method.
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {

            $obj = new Brand();
            $obj->is_active = 1;
            $obj->setTranslation('name', 'en', $request->en_name);
            $obj->setTranslation('name', 'bn', $request->bn_name);
            $obj->company_id = $request->company_id;
            $obj->save();

            DB::commit();

            return true;

        } catch (\Exception $exception) {


            DB::rollBack();

            $this->Error = $exception->getMessage();

            return false;

        }

    }

    public function update($request, $prefix, $id)
    {
        DB::beginTransaction();

        try {

            $obj = Brand::find($id);
            $obj->is_active = 1;
            $obj->setTranslation('name', 'en', $request->en_name);
            $obj->setTranslation('name', 'bn', $request->bn_name);
            $obj->company_id = $request->company_id;
            $obj->update();

            DB::commit();

            return true;

        } catch (\Exception $exception) {

            DB::rollBack();

            $this->Error = $exception->getMessage();

            return false;
        }
    }

    public function activation($status, $id)
    {

        DB::beginTransaction();

        try {

            $obj = Brand::find($id);

            if ($status === "yes") {
                $obj->is_active = 1;
            } else {
                $obj->is_active = 0;
            }

            $obj->update();

            DB::commit();

            return true;

        } catch (\Exception $exception) {

            DB::rollBack();

            $this->Error = $exception->getMessage();

            return false;

        }

    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $obj = Brand::find($id);

            $obj->delete();

            DB::commit();

            return true;

        } catch (\Exception $exception) {

            DB::rollBack();

            $this->Error = $exception->getMessage();

            return false;
        }
    }
}





