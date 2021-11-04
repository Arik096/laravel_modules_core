<?php

namespace Modules\Core\Repositories\Contracts;

interface RepositoryInterface
{
    public function all($prefix);

    public function find($prefix, $id);

    public function store($request, $prefix);

    public function update($request, $prefix, $id);

    public function isActive($prefix, $status, $id);

    public function delete($prefix, $id);

    public function search($request, $prefix);
}
