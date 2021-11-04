<?php

namespace Modules\Core\Repositories\Contracts\Auth;

use Modules\Core\Repositories\Auth\Shop;

interface ModuleInterface
{
    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll();

    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllActive();


    /**
     * @return mixed
     */
    public function getAllActiveWithoutPaginate();



    /*
     * Get Module Data in Id
     */
    public function getAllWithModuleId($moduleIds);


    /**
     * when send col name and value
     * @param $key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|Shop
     */
    public function findBy($key);

    /**
     * Insert Shop Table Data
     * @param $request
     * @return bool
     */
    public function store($request);

    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filter($request);

    /**
     * Update Shop Table Data
     * @param $request
     * @param $id
     * @return bool
     */
    public function update($request, $id);

    /**
     * Change Status Active or Deactive
     * @param $id
     * @return bool
     */
    public function changeStatus($id);

    /**
     * Hard Delete
     * @param $id
     * @return bool
     */
    public function delete($id);
}
