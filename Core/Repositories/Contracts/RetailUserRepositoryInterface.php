<?php

namespace Modules\Core\Repositories\Contracts;

interface RetailUserRepositoryInterface
{
    public function allForPublic($prefix, $flag);

    public function all($prefix);

    public function find($prefix, $id);

    public function store($request, $prefix);

    public function update($request, $prefix, $id);

    public function isActive($prefix, $status, $id);

    public function search($request, $prefix);

    public function delete($prefix, $id);
}
