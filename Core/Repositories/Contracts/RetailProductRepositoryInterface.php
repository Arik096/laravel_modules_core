<?php

namespace Modules\Core\Repositories\Contracts;

interface RetailProductRepositoryInterface
{
    public function fmcgProductEable($prefix);

    public function FmcgCompanyName();

    public function FmcgBrandName();

    public function fmcgProductSubmitByRetail($request, $prefix);

    public function findProduct($id);

    public function all($prefix);

    public function productExport();

    public function allForSelect($prefix);

    public function wmmProductall($prefix);

    public function productByIds($ids, $prefix);

    public function productById($id, $prefix);

    public function mWmmProductSearch($request, $prefix);

    public function wmmProductSearch($request, $prefix);

    public function wmmAllPackage($prefix);

    public function wmmService($prefix);

    public function wmmServiceSearchForSale($request, $prefix);

    public function wmmAllPackageSearch($request, $prefix);

    public function orderProductListByUserId($prefix);

    public function allWmmProductList($prefix);

    public function wmmProductRequisition($prefix);

    public function wmmProductForSaleAllStock($prefix);

    public function wmmProductForSaleAllStockSearch($request, $prefix);

    public function wmmAllProductForSale($prefix);

    public function wmmAllProductForSaleSearch($request, $prefix);

    public function productOrderListSearchByUserId($request, $prefix);

    public function mWmmProductRequisitionSearch($request, $prefix);

    public function wmmProductRequisitionSearch($request, $prefix);

    public function packageDescription($prefix, $id);

    public function productDescription($prefix, $id);

    public function wmmPackageRequisition($prefix);

    public function wmmPackageRequisitionFilter($request, $prefix);

    public function wmmPackageListForSale($prefix);

    public function wmmPackageListForSaleSearch($request, $prefix);

    public function productByCompany($prefix, $id);

    public function productByBrand($prefix, $id);

    public function find($prefix, $id);

    public function store($request, $prefix);

    public function update($request, $prefix, $id);

    public function isActive($prefix, $status, $id);

    public function isActiveFmcgAccount($prefix, $status, $id);

    public function companySearch($request);

    public function brandSearch($request);

    public function productSearch($request, $prefix);

    public function search($request, $prefix);

    public function delete($prefix, $id);

    public function getBrandForUser($prefix);

    public function createPackage($request, $prefix);

    public function updatePackage($request, $prefix, $id);
}
