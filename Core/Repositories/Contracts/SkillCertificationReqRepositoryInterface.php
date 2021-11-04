<?php

namespace Modules\Core\Repositories\Contracts;

interface SkillCertificationReqRepositoryInterface
{

    /**
     * @return mixed
     */
    public function allForPublic($prefix);

    /**
     * @return mixed
     */
    public function all($prefix);

    /**
     * @param $id
     * @return mixed
     */


    public function find($id);

    public function filter($request, $prefix);

    public function store($request, $prefix);

    public function update($request, $prefix, $id);

    public function activation($prefix, $status, $id);

    public function delete($prefix, $id);


}
