<?php

namespace Modules\Core\Repositories\Contracts\Location;



interface DivisionRepositoryInterface
{
    /**
     * @return mixed
     */
    public function all();

    /**
     * @return mixed
     */
    public function allIsActive();

    /**
     * @return mixed
     */
    public function allWithOutPaginate();

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

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
     * @return mixed
     */
    public function search($request);

    /**
     * @param $id
     */
    public function delete($id);
}
