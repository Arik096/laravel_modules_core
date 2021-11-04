<?php

namespace Modules\Core\Repositories\Contracts;

use Modules\Core\Models\Management\CategoryCourse;
use Modules\Core\Models\Products\Category;

interface CategoryRepositoryInterface
{
    public function export($prefix);

    /**
     * @param $prefix
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all($prefix);


    /**
     * @param $prefix
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function allCategory($prefix);


    /**
     * @param $prefix
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function selectedCategoryByUserId($prefix);

    /**
     * @param $prefix
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function searchProductList($prefix);

    /**
     * @param $prefix
     * @param $course_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|CategoryCourse[]
     */
    public function MultipleCategory($prefix, $course_id);

    /**
     * @param $prefix
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function allByActive($prefix);

    /**
     * @param $prefix
     * @param $category_flag
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function allByActiveWithType($prefix, $category_flag);

    /**
     * @param $prefix
     * @param $id
     * @return mixed
     */
    public function find($prefix, $id);

    /**
     * @param $prefix
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Category[]
     */
    public function findIsActive($prefix);

    /**
     * @param $request
     * @param $prefix
     * @return bool
     */
    public function store($request, $prefix);

    /**
     * @param $request
     * @param $prefix
     * @param $id
     * @return bool
     */
    public function update($request, $prefix, $id);

    /**
     * @param $prefix
     * @param $status
     * @param $id
     * @return bool
     */
    public function isActive($prefix, $status, $id);

    /**
     * @param $request
     * @param $prefix
     * @return mixed
     */
    public function search($request, $prefix);

    /**
     * @param $prefix
     * @param $id
     * @return bool
     */
    public function delete($prefix, $id);


    /**
     * @param $prefix
     * Request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filter($request, $prefix);

    public function activation($prefix, $status, $id);


}
