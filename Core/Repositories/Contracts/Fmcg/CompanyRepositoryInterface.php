<?php

namespace Modules\Core\Repositories\Contracts\Fmcg;

interface CompanyRepositoryInterface
{

    public function all();

    public function allPaginate();

    public function allActive();

    public function filter($request);

    public function find($id);

    public function create($request);

    public function store($request);

    public function update($request, $company_id, $id);

    public function activation($status, $id);

    public function delete($id);
}
