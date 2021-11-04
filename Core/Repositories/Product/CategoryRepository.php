<?php

namespace Modules\Core\Repositories\Product;

use Modules\Core\Models\Management\CategoryCategorytype;
use Modules\Core\Models\Management\CategoryCourse;
use Modules\Core\Models\Management\CategoryEducation;
use Modules\Core\Models\Management\CategoryFlag;
use Modules\Core\Models\Management\CategoryImageByChannel;
use Modules\Core\Models\Management\CategoryInvestment;
use Modules\Core\Models\Management\CategoryTypeOfService;
use Modules\Core\Models\Management\ChannelCategory;
use Modules\Core\Models\Management\UserSelectedCategoryOrService;
use Modules\Core\Models\Products\Category;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Contracts\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{

    public $Error;

    public function export($prefix)
    {
        return Category::withoutTrashed()
            ->with('categoryType')
            ->where(['is_active' => 1])
            ->get();
    }


    /**
     * return  all product Category
     * @param $prefix
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function allCategory($prefix)
    {
        $categoryIdes = CategoryFlag::withoutTrashed()->where(['flags_for_category_id' => '1'])->pluck('category_id')->toArray();

        return Category::withoutTrashed()
            ->with('categoryType', 'channelsIds', 'channels', 'categoryTypes', 'categoryEducations', 'categoryInvestment', 'typeofService', 'categoryFlag', 'ownership')
            ->whereIn('id', $categoryIdes)
            ->orderBy('id', 'desc')
            ->get();
    }


    /**
     * @param $prefix
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all($prefix)
    {
        return Category::withoutTrashed()
            ->with('categoryType', 'channelsIds', 'channels', 'categoryTypes', 'categoryEducations', 'categoryInvestment', 'typeofService', 'categoryFlag', 'ownership')
            ->orderBy('id', 'desc')
            ->paginate(100);
    }


    /**
     * @param $prefix
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function selectedCategoryByUserId($prefix)
    {
        $categoryIds = UserSelectedCategoryOrService::where(['user_id' => Auth::guard('web')->user()->id, 'is_active' => 1, 'is_approve' => 1, 'category_type_flag' => '1'])
            ->pluck('category_id')
            ->toArray();
        return Category::withoutTrashed()
            ->with('categoryType', 'channelsIds', 'channels', 'categoryTypes', 'categoryEducations', 'categoryInvestment', 'typeofService', 'categoryFlag', 'ownership')
            ->whereIn('id', $categoryIds)
            ->orderBy('id', 'desc')
            ->paginate(100);
    }


    /**
     * @param $prefix
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function searchProductList($prefix)
    {
        return Category::withoutTrashed()
            ->with('categoryType')
            ->where(['is_active' => 1])
            ->where(['category_flag' => 1])
            ->get();
    }


    /**
     * @param $prefix
     * @param $course_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|CategoryCourse[]
     */
    public function MultipleCategory($prefix, $course_id)
    {
        return CategoryCourse::withoutTrashed()
            ->with('category')
            ->where(['course_id' => $course_id])
            ->get();
    }


    /**
     * @param $prefix
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function allByActive($prefix)
    {
        return Category::withoutTrashed()
            ->with('categoryType')
            ->where(['is_active' => 1])
            ->get();
    }


    /**
     * @param $prefix
     * @param $category_flag
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function allByActiveWithType($prefix, $category_flag)
    {
        return Category::withoutTrashed()
            ->with('categoryType')
            ->where(['category_flag' => $category_flag])
            ->where(['is_active' => 1])
            ->get();
    }


    /**
     * @param $prefix
     * @param $id
     * @return mixed
     */
    public function find($prefix, $id)
    {
        $obj = new Category();
        $obj->setTable($prefix . '_categories');
        return $data = $obj->find($id);
    }


    /**
     * @param $prefix
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function findIsActive($prefix)
    {
        return Category::withoutTrashed()
            ->where(['is_active' => 1])
            ->get();
    }


    /**
     * @param $request
     * @param $prefix
     * @return bool
     */
    public function store($request, $prefix)
    {

        DB::beginTransaction();

        try {

             $filename= null;
            if ($request->hasFile('category_image')) {
                $image = $request->file('category_image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = public_path() . '/category_image/' . $filename;
                Image::make($image)->resize(150, 150)->save($location);
            }


            $obj = new Category();
            $obj->setTable($prefix . '_categories');
            $obj->setTranslation('name', 'en', $request->en_name_category);
            $obj->setTranslation('name', 'bn', $request->bn_name_category);
            $obj->ownership_of_service_id = $request->ownership_of_service_id;
            $obj->comments = $request->comments;
            $obj->priority = $request->priority;
            $obj->is_active = 1;
            $obj->images = $filename;
            $obj->save();

            if (!is_null($request->channels)) {
                foreach ($request->channels as $channel) {
                    $cha = new ChannelCategory();
                    $cha->category_id = $obj->id;
                    $cha->channel_id = $channel;
                    $cha->save();
                }
            }

            if (!is_null($request->education_requirement)) {
                //Category Education
                foreach ($request->education_requirement as $edu) {
                    $cha = new CategoryEducation();
                    $cha->category_id = $obj->id;
                    $cha->education_id = $edu;
                    $cha->save();
                }
            }

            //Investment Requirement
            if (!is_null($request->investment_requirement)) {
                foreach ($request->investment_requirement as $investment) {
                    $cha = new CategoryInvestment();
                    $cha->category_id = $obj->id;
                    $cha->investment_id = $investment;
                    $cha->save();
                }
            }


            //Investment Requirement
            foreach ($request->category_type_id as $type) {
                $cha = new CategoryCategorytype();
                $cha->category_id = $obj->id;
                $cha->category_type_id = $type;
                $cha->save();
            }

            //Category Flags
            foreach ($request->category_flags_id as $type) {
                $cha = new CategoryFlag();
                $cha->category_id = $obj->id;
                $cha->flags_for_category_id = $type;
                $cha->save();
            }

            //Type Of Service
            if (!is_null($request->type_of_service_id)) {
                foreach ($request->type_of_service_id as $type) {
                    $cha = new CategoryTypeOfService();
                    $cha->category_id = $obj->id;
                    $cha->type_of_services_id = $type;
                    $cha->save();
                }
            }



//            if ($request->hasFile('category_image')) {
//                $can = 0;
//                foreach ($request->category_image as $image) {
//                    $filename = null;
//                    //$image = $request->file($image);
//                    $filename = time() . '.' . $image->getClientOriginalExtension();
//                    $location = public_path() . '/category_image/' . $filename;
//                    Image::make($image)->resize(150, 150)->save($location);
//
//                    $cc = new CategoryImageByChannel();
//                    $cc->category_id = $obj->id;
//                    $cc->channel_id = $request->channel_id_for_image[$can];
//                    $cc->images = $filename;
//                    $cc->save();
//                    $can++;
//                }
//            }






            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;
        }

    }


    /**
     * @param $request
     * @param $prefix
     * @param $id
     * @return bool
     */
    public function update($request, $prefix, $id)
    {

        DB::beginTransaction();

        try {

            $filename= null;
            if ($request->hasFile('category_image')) {
                $image = $request->file('category_image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = public_path() . '/category_image/' . $filename;
                Image::make($image)->resize(150, 150)->save($location);
            }


            $obj = Category::find($id);
            $obj->setTranslation('name', 'en', $request->en_name_category);
            $obj->setTranslation('name', 'bn', $request->bn_name_category);
            // $obj->category_type_id = $request->category_type_id;
            //$obj->category_flag = $request->category_flag;
            $obj->ownership_of_service_id = $request->ownership_of_service_id;
            $obj->comments = $request->comments;
            $obj->priority = $request->priority;
            if($filename != ""){
                $obj->images = $filename;
            }
            $obj->is_active = 1;
            $obj->save();

//            ChannelCategory::where(['category_id' => $id])->delete();
//
//            foreach ($request->channels as $channel) {
//                $cha = new ChannelCategory();
//                $cha->category_id = $obj->id;
//                $cha->channel_id = $channel;
//                $cha->save();
//            }

            if (!is_null($request->education_requirement)) {
                //Category Education
                CategoryEducation::where(['category_id' => $id])->delete();
                foreach ($request->education_requirement as $edu) {
                    $cha = new CategoryEducation();
                    $cha->category_id = $obj->id;
                    $cha->education_id = $edu;
                    $cha->save();
                }
            }

//            //Investment Requirement
//            if (!is_null($request->investment_requirement)) {
//                CategoryInvestment::where(['category_id' => $id])->delete();
//                foreach ($request->investment_requirement as $investment) {
//                    $cha = new CategoryInvestment();
//                    $cha->category_id = $obj->id;
//                    $cha->investment_id = $investment;
//                    $cha->save();
//                }
//            }


            //Investment Requirement
            CategoryCategorytype::where(['category_id' => $id])->delete();
            foreach ($request->category_type_id as $type) {
                $cha = new CategoryCategorytype();
                $cha->category_id = $obj->id;
                $cha->category_type_id = $type;
                $cha->save();
            }

            //Category Flags
            CategoryFlag::where(['category_id' => $id])->delete();
            foreach ($request->category_flags_id as $type) {
                $cha = new CategoryFlag();
                $cha->category_id = $obj->id;
                $cha->flags_for_category_id = $type;
                $cha->save();
            }

            //Type Of Service
            if (!is_null($request->type_of_service_id)) {

                CategoryTypeOfService::where(['category_id' => $id])->delete();

                foreach ($request->type_of_service_id as $type) {

                    $cha = new CategoryTypeOfService();
                    $cha->category_id = $obj->id;
                    $cha->type_of_services_id = $type;
                    $cha->save();
                }
            }


            //Type Of Service
//            if ($request->hasFile('category_image')) {
//                $can = 0;
//
//                foreach ($request->category_image as $image) {
//                    CategoryImageByChannel::where(['channel_id' => $request->channel_id_for_image[$can]])->delete();
//                    $filename = null;
//                    //$image = $request->file($image);
//                    $filename = time() . '.' . $image->getClientOriginalExtension();
//                    $location = public_path() . '/category_image/' . $filename;
//                    Image::make($image)->resize(150, 150)->save($location);
//
//                    $cha = new CategoryImageByChannel();
//                    $cha->category_id = $obj->id;
//                    $cha->channel_id = $request->channel_id_for_image[$can];
//                    $cha->images = $filename;
//                    $cha->save();
//                    $can++;
//                }
//            }

            DB::commit();
            return true;

        } catch (\Exception $ex) {
            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;
        }
    }


    /**
     * @param $prefix
     * @param $status
     * @param $id
     * @return bool
     */
    public function isActive($prefix, $status, $id)
    {
        try {
            $obj = $this->find($prefix, $id);
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


    /**
     * @param $request
     * @param $prefix
     * @return mixed
     */
    public function search($request, $prefix)
    {
        $search = trim($request->search);
        $query = Category::withoutTrashed()
            ->with('categoryType');

        if ($search != "") {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        if ($request->is_active != "") {
            $query->where(['is_active' => $request->is_active]);
        }

        if ($request->category_type_id != "") {

            $category_type_ids = CategoryCategorytype::where(['category_type_id' => $request->category_type_id])
                ->pluck('category_id')
                ->toArray();

            $query->whereIn('id', $category_type_ids);
        }

        return $query->paginate(200);
    }


    /**
     * @param $prefix
     * @param $id
     * @return bool
     */
    public function delete($prefix, $id)
    {
        try {

            $pram = Category::find($id);

            $pram->delete();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;
        }
    }


    public function filter($request, $prefix)
    {

        $data = Category::withoutTrashed()
            ->with('categoryType', 'channelsIds', 'channels', 'categoryTypes', 'categoryEducations', 'categoryInvestment', 'typeofService', 'categoryFlag', 'ownership');


        if ($request->search != "") {
            $data->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        if ($request->category_type_id != "") {

            $category_type_ids = CategoryCategorytype::where(['category_type_id' => $request->category_type_id])->pluck('category_id')->toArray();

            $data->whereIn('id', $category_type_ids);

        }

        return $data->orderBy('id', 'desc')->paginate(100);
    }

    public function activation($prefix, $status, $id)
    {

        try {

            $obj = Category::find($id);

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





