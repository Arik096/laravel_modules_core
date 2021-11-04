<?php

namespace Modules\Core\Repositories\Contracts;

interface BrandRepositoryInterface
{
    /**
     * @return mixed
     */
    public function allForPublic();

    /**
     * @return mixed
     */
    public function all($company_id);

    /**
     * @param $id
     * @return mixed
     */
    public function find($company_id, $id);

    /**
     * @return mixed
     */
    public function findByActive($company_id);

    /**
     * @param $request
     */
    public function store($request);

    /**
     * @param $request
     * @param $id
     */
    public function update($request, $company_id, $id);

    /**
     * @param $status
     * @param $id
     */
    public function isActive($status, $id);

    /**
     * @param $request
     * @return mixed
     */
    public function search($request, $company_id);

    /**
     * @param $id
     */
    public function delete($id);
}
