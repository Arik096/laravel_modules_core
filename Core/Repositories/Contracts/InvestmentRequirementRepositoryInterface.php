<?php

namespace Modules\Core\Repositories\Contracts;

interface InvestmentRequirementRepositoryInterface
{
    public function export($prefix);

    public function all($prefix);

    public function allByPaginate($prefix);

    public function create($prefix);

    public function find($prefix, $id);

    public function store($request, $prefix);

    public function edit($prefix, $id);

    public function update($request, $prefix, $id);

    public function delete($prefix, $id);

    public function filter($request, $prefix);

    public function activation($prefix, $status, $id);


}
