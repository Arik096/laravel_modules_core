<?php

namespace Modules\Core\Repositories\Contracts;

use Livewire\Request;

interface CategoryTypeRepositoryInterface
{
    public function index($request, $prefix);

    public function allByActive($prefix);

    public function create($request, $prefix);

    public function investment($prefix);

    public function activation($prefix, $status, $id);

    public function delete($prefix, $id);

    public function edit($request, $prefix, $id);

    public function find($prefix, $id);

}
