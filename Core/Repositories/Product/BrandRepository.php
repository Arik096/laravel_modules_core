<?php

namespace Modules\Core\Repositories\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Products\Brand;
use Modules\Core\Repositories\Contracts\BrandRepositoryInterface;


class BrandRepository implements BrandRepositoryInterface
{

    /**
     * @var
     */
    public $Error;


    /**
     * @return mixed
     */
    public function allForPublic()
    {
        return Brand::withoutTrashed()->with(['company'])
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }


    /**
     * @return mixed
     */
    public function all($company_id)
    {
        return Brand::withoutTrashed()->where(['company_id' => $company_id])
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function find($company_id, $id)
    {
        return Brand::withoutTrashed()->where(['company_id' => $company_id])
            ->where(['is_hidden' => 0])
            ->findOrFail($id);
    }


    /**
     * @return mixed
     */
    public function findByActive($company_id)
    {
        return Brand::withoutTrashed()->where(['is_active' => 1])
            ->where(['company_id' => $company_id])
            ->where(['is_hidden' => 0])
            ->get();
    }

    /**
     * @param $request
     */
    public function store($request)
    {
        try {

            $obj = new Brand();
            $obj->is_active = 1;
            $obj->setTranslation('name', 'en', $request->en_name);
            $obj->setTranslation('name', 'bn', $request->bn_name);
            $obj->company_id = $request->company_id;
            $obj->save();
            return true;
        } catch (\Exception $exception) {
            $this->Error = $exception->getMessage();
            return false;
        }

    }


    /**
     * @param $request
     * @param $id
     */
    public function update($request, $company_id, $id)
    {
        try {
            $obj = Brand::find($id);
            $obj->is_active = 1;
            $obj->setTranslation('name', 'en', $request->en_name);
            $obj->setTranslation('name', 'bn', $request->bn_name);
            $obj->company_id = $company_id;
            $obj->update();
            return true;
        } catch (\Exception $exception) {
            $this->Error = $exception->getMessage();
            return false;
        }

    }


    /**
     * @param $status
     * @param $id
     */
    public function isActive($status, $id)
    {
        try {

            $obj = Brand::find($id);

            if ($status === "yes") {
                $obj->is_active = 1;
            } else {
                $obj->is_active = 0;
            }

            $obj->update();

            return true;

        } catch (\Exception $exception) {

            $this->Error = $exception->getMessage();
            return false;

        }

    }


    /**
     * @param $request
     * @return mixed
     */

    public function search($request, $company_id)
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


    /**
     * @param $id
     */
    public function delete($id)
    {
        try {

            $obj = Brand::find($id);

            $obj->delete();

            return true;

        } catch (\Exception $exception) {

            $this->Error = $exception->getMessage();

            return false;
        }
    }


}





