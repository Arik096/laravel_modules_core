<?php

namespace Modules\Core\Repositories\Contracts;

use Modules\Core\Models\Management\CategoryCourse;
use Modules\Core\Models\Products\Category;

interface SubCategoryRepositoryInterface
{
    public function export($prefix);

    public function all($prefix);

    public function allByActive($prefix);

    public function create($prefix);

    public function find($prefix, $id);

    public function store($request, $prefix);

    public function update($request, $prefix, $id);

    public function delete($prefix, $id);

    public function filter($request, $prefix);

    public function activation($prefix, $status, $id);


}
