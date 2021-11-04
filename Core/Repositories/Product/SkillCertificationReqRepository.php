<?php

namespace Modules\Core\Repositories\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Management\CategoryCourse;
use Modules\Core\Models\Management\SkillCertificationReq;
use Modules\Core\Models\Management\SubCategoryCourse;
use Modules\Core\Models\Products\Brand;
use Modules\Core\Repositories\Contracts\BrandRepositoryInterface;
use Modules\Core\Repositories\Contracts\SkillCertificationReqRepositoryInterface;
use Illuminate\Support\Facades\DB;


class SkillCertificationReqRepository implements SkillCertificationReqRepositoryInterface
{

    /**
     * @var
     */
    public $Error;


    /**
     * @return mixed
     */
    public function allForPublic($prefix)
    {
        return SkillCertificationReq::withoutTrashed()->with('category', 'courseCategories', 'subcateogryCourse')->get();
    }


    /**
     * @return mixed
     */
    public function all($prefix)
    {
        return SkillCertificationReq::withoutTrashed()->with('category', 'courseCategories', 'subcateogryCourse')
            ->orderBy('id', 'desc')
            ->paginate(100);
    }

    public function filter($request, $prefix)
    {

        $data = SkillCertificationReq::withoutTrashed()->with('category', 'courseCategories', 'subcateogryCourse');


        if ($request->search != "") {
            $data->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        if ($request->category_id != "") {

            $catgory_ids = CategoryCourse::where(['category_id' => $request->category_id])->pluck('course_id')->toArray();

            $data->whereIn('id', $catgory_ids);
        }

        return $data->orderBy('id', 'desc')->paginate(100);
    }


    public function store($request, $prefix)
    {

        DB::beginTransaction();

        try {

            $obj = new SkillCertificationReq();
            $obj->title = $request->title;
            $obj->link = $request->link;
            $obj->comments = $request->comments;
//            $obj->category_id = $request->category_id;
            $obj->is_required = $request->is_required;
            $obj->is_distance_learning = $request->is_distance_learning;
            $obj->is_face_to_face_learning = $request->is_face_to_face_learning;
            $obj->is_self_learning = $request->is_self_learning;
            $obj->is_cretification = $request->is_cretification;
            $obj->is_active = 1;
            $obj->save();

            foreach ($request->category_id as $edu) {

                $dd = new CategoryCourse();
                $dd->category_id = $edu;
                $dd->course_id = $obj->id;
                $dd->save();
            }

            //Entry Subcategory Course
            if (!is_null($request->subcategory_id)) {

                foreach ($request->subcategory_id as $edu) {
                    $dd = new SubCategoryCourse();
                    $dd->sub_category_id = $edu;
                    $dd->course_id = $obj->id;
                    $dd->save();
                }
            }

            DB::commit();

            return true;

        } catch (\Exception $ex) {
            DB::rollback();
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function update($request, $prefix, $id)
    {

        DB::beginTransaction();

        try {

            $obj = SkillCertificationReq::find($id);
            $obj->title = $request->title;
            $obj->link = $request->link;
            $obj->comments = $request->comments;
            $obj->is_required = $request->is_required;
            $obj->is_required = $request->is_required;
            $obj->is_distance_learning = $request->is_distance_learning;
            $obj->is_face_to_face_learning = $request->is_face_to_face_learning;
            $obj->is_self_learning = $request->is_self_learning;
            $obj->is_cretification = $request->is_cretification;
            $obj->is_active = 1;
            $obj->update();

            //Delete Category Course
            CategoryCourse::where(['course_id' => $id])->delete();
            foreach ($request->category_id as $edu) {
                $dd = new CategoryCourse();
                $dd->category_id = $edu;
                $dd->course_id = $obj->id;
                $dd->save();
            }

            //Entry Subcategory Course
            if (!is_null($request->subcategory_id)) {
                SubCategoryCourse::where(['course_id' => $id])->delete();
                foreach ($request->subcategory_id as $edu) {
                    $dd = new SubCategoryCourse();
                    $dd->sub_category_id = $edu;
                    $dd->course_id = $obj->id;
                    $dd->save();
                }
            }

            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollback();
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function activation($prefix, $status, $id)
    {
        try {

            $obj = SkillCertificationReq::find($id);

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
        DB::beginTransaction();

        try {

            $obj = SkillCertificationReq::find($id);
            $obj->delete();

            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function find($id)
    {
        return SkillCertificationReq::withoutTrashed()->with('category', 'courseCategories', 'subcateogryCourse')
            ->where(['id' => $id])
            ->first();
    }
}





