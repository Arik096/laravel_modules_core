<?php

namespace Modules\Core\Repositories\Contracts\Location;

interface UnionRepositoryInterface
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all();

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function allWithOutPaginate();

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByUpazilaId($id);

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function find($id);

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findIsActive();

    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($request);

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($request, $id);

    /**
     * @param $status
     * @param $id
     */
    public function isActive($status, $id);

    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($request);

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id);
}
