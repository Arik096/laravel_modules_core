<?php

namespace Modules\Core\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function submitOrder($request, $prefix);

    public function orderHistoryFilter($request,$prefix);

    public function index();

    public function orderHistoryExport();
}
