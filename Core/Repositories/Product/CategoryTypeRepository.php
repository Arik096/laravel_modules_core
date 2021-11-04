<?php


namespace Modules\Core\Repositories\Product;

use Modules\Core\Models\Products\CategoryType;
use App\Models\RetailNetwork\ProductOrServiceManagement\InvestmentRequirement;
use App\Providers\AppServiceProvider;
use Modules\Core\Repositories\Contracts\CategoryTypeRepositoryInterface;

use Illuminate\Http\Request;

class CategoryTypeRepository implements CategoryTypeRepositoryInterface
{

    public $Errors;

    public function index($request, $prefix)
    {
        #dd($request->all());

        $data = CategoryType::withoutTrashed()->with('investmentRequirment');

        if ($request->search != "") {
            $data->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        $data = $data->orderBy('id', 'desc')->paginate(10);

        return $data;

    }


    public function create($request, $prefix)
    {
        try {

            $obj = new CategoryType();

            $obj->title = $request->title;
            $obj->comments = $request->comments;
            $obj->investment_requirement_id = $request->investment_requirement_id;
            $obj->is_active = 1;
            $obj->save();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;
        }

    }

    public function investment($prefix)
    {
        return InvestmentRequirement::withoutTrashed()->with('educationRequirement')->where(['is_active' => 1])->paginate(100);
    }

    public function activation($prefix, $status, $id)
    {

        try {

            $obj = CategoryType::find($id);

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

    public function delete($prefix, $id)
    {

        try {
            $obj = CategoryType::find($id);
            $obj->delete();
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }

    }

    public function find($prefix, $id)
    {
        try {

            $obj = CategoryType::where(['id' => $id])->first();

            return $obj;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;
        }
    }

    public function edit($request, $prefix, $id)
    {
        try {

            $obj = CategoryType::find($id);

            $obj->title = $request->title;
            $obj->comments = $request->comments;
            $obj->investment_requirement_id = $request->investment_requirement_id;
            $obj->is_active = 1;
            $obj->update();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;
        }

    }

    public function allByActive($prefix)
    {
        return CategoryType::withoutTrashed()->with('investmentRequirment')->where(['is_active' => 1])->get();
    }



}
