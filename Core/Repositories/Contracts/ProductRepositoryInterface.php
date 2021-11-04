<?php

namespace Modules\Core\Repositories\Contracts;

interface ProductRepositoryInterface
{

    public function all();

    public function allPaginate();

    public function allActive();

    public function filter($request);

    public function find($id);

    public function create($request);

    public function store($request);

    public function update($request, $prefix, $id);

    public function activation($status, $id);

    public function order_status($status, $id);

    public function delete($id);

    public function allForSelect($prefix);
}
