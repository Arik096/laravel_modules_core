<?php

namespace Modules\Core\Repositories\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Management\EducationInvestment;
use Modules\Core\Models\Management\InvestmentRequirement;
use Modules\Core\Models\Products\Unit;
use Modules\Core\Repositories\Contracts\InvestmentRequirementRepositoryInterface;
use Modules\Core\Repositories\Contracts\UnitRepositoryInterface;


class InvestmentRequirementRepository implements InvestmentRequirementRepositoryInterface
{

    public $Errors;

    public function export($prefix)
    {
        // TODO: Implement export() method.
    }

    public function all($prefix)
    {
        return InvestmentRequirement::withoutTrashed()
            ->with('educationRequirement', 'educationInvestment')
            ->orderBy('id', 'desc')->get();
    }

    public function allByPaginate($prefix)
    {
        return InvestmentRequirement::withoutTrashed()
            ->with('educationRequirement', 'educationInvestment')
            ->orderBy('id', 'desc')
            ->paginate(100);

    }

    public function create($prefix)
    {
        // TODO: Implement create() method.
    }

    public function find($prefix, $id)
    {
        return InvestmentRequirement::with('educationRequirement', 'educationInvestment')->where('id', $id)->first();
    }

    public function store($request, $prefix)
    {
        DB::beginTransaction();

        try {

            $obj = new InvestmentRequirement();
            $obj->title = $request->title;
            $obj->comments = $request->comments;
            $obj->is_active = 1;
            $obj->save();

            foreach ($request->education_requirement_id as $edu) {

                $dd = new EducationInvestment();
                $dd->education_id = $edu;
                $dd->investment_id = $obj->id;
                $dd->save();
            }

            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollback();

            $this->Error = $ex->getMessage();

            return false;
        }

    }

    public function edit($prefix, $id)
    {
        $data = InvestmentRequirement::with('educationRequirement', 'educationInvestment')->where('id', $id)->first();

        return $data;
    }

    public function update($request, $prefix, $id)
    {
        DB::beginTransaction();

        try {

            $obj = InvestmentRequirement::find($id);

            $obj->title = $request->title;
            $obj->comments = $request->comments;
            $obj->is_active = 1;
            $obj->update();

            //remove Education Investment
            EducationInvestment::where(['investment_id' => $id])->delete();

            foreach ($request->education_requirement_id as $edu) {

                $dd = new EducationInvestment();

                $dd->education_id = $edu;
                $dd->investment_id = $obj->id;
                $dd->save();
            }

            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollback();
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function delete($prefix, $id)
    {
        try {

            $obj = InvestmentRequirement::find($id);
            $obj->delete();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function filter($request, $prefix)
    {

        $data = InvestmentRequirement::withoutTrashed()->with('educationRequirement', 'educationInvestment');

        if ($request->search != "") {
            $data->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        return $data->orderBy('id', 'desc')->paginate(100);
    }

    public function activation($prefix, $status, $id)
    {
        try {

            $obj = InvestmentRequirement::find($id);

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


}





