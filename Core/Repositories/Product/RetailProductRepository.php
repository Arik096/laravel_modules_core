<?php

namespace Modules\Core\Repositories\Product;


use App\Models\Admin\FmcgProductEable;
use App\Models\Distribution\DistrubutionFmcg;
use App\Models\Distribution\Package;
use App\Models\Distribution\PackageDiscount;
use App\Models\Distribution\PackageItem;
use App\Models\Distribution\PackagePriceRole;
use App\Models\Distribution\ProductDiscount;
use App\Models\Distribution\RoleProduct;
use App\Models\Distribution\TemporaryPackageItem;
use App\Models\Fmcg\Brand;
use App\Models\Fmcg\Fmcg;
use App\Models\Fmcg\Product;
use App\Models\RetailNetwork\ProductOrServiceManagement\ChannelProduct;
use App\Models\RetailNetwork\ProductOrServiceManagement\EducationRequirement;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductEducationRequirement;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductImpactArea;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductInvestmentRequirement;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductManagementRole;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductProblemArea;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductSkillCertificate;
use App\Models\RetailNetwork\ProductOrServiceManagement\ProductTargetMarket;
use App\Models\RetailNetwork\RetailNetworkProduct;
use App\Models\RetailNetwork\RetailNetworkService;
use App\Models\RetailNetwork\UserSelectedCategoryOrService;
use App\Repository\RepositoryInterface;
use App\Services\CacheService;
use App\Services\ImageService;
use App\User;
use DB;
use Dompdf\Exception;
use http\Env\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


use Modules\Core\Models\Products\RetailProduct;
use Modules\Core\Repositories\Contracts\RetailProductRepositoryInterface;


use Session;

class RetailProductRepository implements RetailProductRepositoryInterface
{

    public $Error;

    /**
     * @param $prefix
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     * this method use for Manual order Module
     */
    public function allWmmProductList($prefix)
    {

        $productIdFromRole = RoleProduct::where(['flag' => 4])->pluck('product_id')->toArray();
        if (count($productIdFromRole) == 0) {
            return array();
        }

        //'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll',
        return RetailProduct::with(['product', 'ProductStockAll', 'category', 'brand', 'company'])
            ->where(['is_hidden' => 0])
            ->where(['is_active' => 1])
            ->whereIn('id', $productIdFromRole)
            ->orderBy('updated_at', 'desc')
            ->paginate(200);
    }


    public function fmcgProductEable($prefix)
    {
        $enable = new  FmcgProductEable();
        $enable->setTable($prefix . '_fmcg_enables');
        $data = $enable->where(['prefix' => $prefix])->pluck('fmcg_account_id')->toArray();
        return $data;
    }

    public function FmcgCompanyName()
    {
        $enable = new  DistrubutionFmcg;
        return $fmcgId = $enable->with('company')->get();
    }


    public function FmcgBrandName()
    {
        $enable = new  DistrubutionFmcg;
        $fmcgId = $enable->pluck('fmcg_account_id')->toArray();
        return $brands = Brand::whereIn('company_id', $fmcgId)->where(['is_hidden' => 0])->get();
    }


    public function fmcgProductSubmitByRetail($request, $prefix)
    {

        try {
            $pram = new RetailNetworkProduct();
            $pram->setTable($prefix . '_retailproducts');
            $productByRetails = $pram->where(['is_hidden' => 0])->pluck('product_id')->toArray();

            foreach ($request->product_id as $value) {
                if (!in_array($value, $productByRetails)) {
                    $product = Product::where(['id' => $value])->where(['is_hidden' => 0])->firstOrFail();
                    $pram = new RetailNetworkProduct();
                    $pram->setTable($prefix . '_retailproducts');
                    $pram->product_id = $value;
                    $pram->brand_id = $product->brand_id;
                    $pram->company_id = $product->company_id;
                    $pram->save();
                    Session::flash("success", "Added your product Successfully.");

                } else {
                    //Session::flash("error","Already Added This Product");
                }
            }
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }


    }


    //method use for product Order
    public function findProduct($id)
    {
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'brand', 'company'])
            ->where(['id' => $id])
            ->get()
            ->first();
    }


    public function all($prefix)
    {
        $obj = new RetailNetworkProduct();
        $obj->setTable($prefix . '_retailproducts');
        return $obj->with(['product', 'unit', 'category', 'productDiscount'])
            ->where('distributor_purchase_price', '!=', null)
            ->where('user_purchase_price', '!=', null)
            ->where(['is_hidden' => 0])
            ->orderBy('created_at', 'desc')
            ->paginate(100);
    }


    public function productExport()
    {
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount'])->where('distributor_purchase_price', '!=', null)->where('user_purchase_price', '!=', null)->where(['is_hidden' => 0])->orderBy('created_at', 'desc')->get();
    }


    public function allForSelect($prefix)
    {
        $obj = new RetailNetworkProduct();
        $obj->setTable($prefix . '_retailproducts');
        //return $obj->with(['product', 'unit', 'category'])->whereNull('distributor_purchase_price')->whereNull('user_purchase_price')->where(['is_hidden'=>0])->orderBy('created_at', 'desc')->get();
        return $obj->with(['product', 'unit', 'category'])->where(['is_hidden' => 0])->orderBy('created_at', 'desc')->get();
    }


    public function wmmProductall($prefix)
    {
        /*$obj = new RetailNetworkProduct();
        $obj->setTable($prefix.'_retailproducts');
        $product_id = RoleProduct::whereIn('flag',[4])->pluck('product_id')->toArray();
        [$keys, $values] = Arr::divide($product_id);
        $products = $obj->with(['product','unit','category'])->whereIn('id',$values)->paginate(100);

        return $products;*/

        /*		$products = DB::table($prefix.'_role_products')->distinct()
                        ->select($prefix.'_retailproducts.*','products.product_name','products.images')
                        ->join($prefix.'_retailproducts', $prefix.'_role_products.product_id', '=', $prefix.'_retailproducts.id')
                        ->join('products', $prefix.'_retailproducts.product_id', '=', 'products.id')
                        ->where(['flag'=>Auth::guard('web')->user()->flag])
                        ->paginate(100);
                return $products;	*/
        $products = DB::table($prefix . '_role_products')->distinct()
            ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
            ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
            ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
            ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
            ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
            ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
            ->where(['flag' => Auth::guard('web')->user()->flag])
            ->orderBy('products.product_name', 'asc')
            ->paginate(1000);

        //dd($products);
        return $products;


    }


    public function mWmmProductSearch($request, $prefix)
    {

        //return $request->all();

        $query_builder = DB::table($prefix . '_role_products')->distinct()
            ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
            ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
            ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
            ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
            ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
            ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
            ->where(['flag' => Auth::guard('web')->user()->flag]);

        if ($request['company_id'] != null) {
            $query_builder->where('products.company_id', $request['company_id']);
        }

        if ($request['search'] != null) {

            $query_builder->where('products.product_name', 'like', '%' . $request['search'] . '%');

        }

        if ($request['category_id'] != null) {
            $query_builder->where([$prefix . '_retailproducts.category_id' => $request->category_id]);
        }

        return $query_builder->orderBy('products.product_name', 'asc')
            ->paginate(100);

        //dd($products);
        return $products;
    }

// wmmProductRequisitionSearch Not Used In this
    public function wmmProductSearch($request, $prefix)
    {
        $search = trim(strip_tags($request->search));

        $products = DB::table($prefix . '_role_products')->distinct()
            ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
            ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
            ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
            ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
            ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
            ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
            ->where(['flag' => Auth::guard('web')->user()->flag])
            ->orderBy('products.product_name', 'asc')
            ->paginate(100);


        if ($request->search != "") {
            /*			$products = DB::table($prefix.'_role_products')->distinct()
                            ->select($prefix.'_retailproducts.*','products.product_name','products.images')
                            ->join($prefix.'_retailproducts', $prefix.'_role_products.product_id', '=', $prefix.'_retailproducts.id')
                            ->join('products', $prefix.'_retailproducts.product_id', '=', 'products.id')
                            ->where(['flag'=> Auth::guard('web')->user()->flag])
                            ->where('products.product_name','like','%'.$search.'%')
                            ->orWhere([$prefix.'_retailproducts.user_selling_price'=>$search])
                            ->paginate(100);*/


            $products = DB::table($prefix . '_role_products')->distinct()
                ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
                ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
                ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
                ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
                ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
                ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
                ->where(['flag' => Auth::guard('web')->user()->flag])
                ->where('products.product_name', 'like', '%' . $search . '%')
                ->orderBy('products.product_name', 'asc')
                ->paginate(100);

        }

        if ($request->category_id != "") {

            /*			$products = DB::table($prefix.'_role_products')->distinct()
                            ->select($prefix.'_retailproducts.*','products.product_name','products.images')
                            ->join($prefix.'_retailproducts', $prefix.'_role_products.product_id', '=', $prefix.'_retailproducts.id')
                            ->join('products', $prefix.'_retailproducts.product_id', '=', 'products.id')
                            ->where(['flag'=> Auth::guard('web')->user()->flag])
                            ->where([$prefix.'_retailproducts.category_id'=>$request->category_id])
                            ->paginate(100);*/

            $products = DB::table($prefix . '_role_products')->distinct()
                ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
                ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
                ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
                ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
                ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
                ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
                ->where(['flag' => Auth::guard('web')->user()->flag])
                ->where([$prefix . '_retailproducts.category_id' => $request->category_id])
                ->orderBy('products.product_name', 'asc')
                ->paginate(100);


        }

        return $products;
    }


    public function wmmAllPackage($prefix)
    {
        $packages = Package::paginate(100);
        return $packages;
    }

    //service list for sale
    public function wmmService($prefix)
    {
        $service = RetailNetworkService::with('category', 'brand', 'serviceDiscount', 'serviceRole', 'servicePrice', 'servicePriceRange')->orderBy('id', 'desc')->paginate(100);
        return $service;
    }

    //service search for Sale
    public function wmmServiceSearchForSale($request, $prefix)
    {
        $service = RetailNetworkService::with('category', 'brand', 'serviceDiscount', 'serviceRole', 'servicePrice', 'servicePriceRange')->orderBy('id', 'desc')->paginate(100);
        if ($request->search != "") {
            $service = RetailNetworkService::with('category', 'brand', 'serviceDiscount', 'serviceRole', 'servicePrice', 'servicePriceRange')->where('name', 'LIKE', '%' . $request->search . '%')->orderBy('id', 'desc')->paginate(100);
        }

        if ($request->company_id != "") {
            $service = RetailNetworkService::with('category', 'brand', 'serviceDiscount', 'serviceRole', 'servicePrice', 'servicePriceRange')->where(['company_id' => $request->company_id])->orderBy('id', 'desc')->paginate(100);
        }

        if ($request->brand_id != "") {
            $service = RetailNetworkService::with('category', 'brand', 'serviceDiscount', 'serviceRole', 'servicePrice', 'servicePriceRange')->where(['brand_id' => $request->brand_id])->orderBy('id', 'desc')->paginate(100);
        }

        if ($request->category_id != "") {
            $service = RetailNetworkService::with('category', 'brand', 'serviceDiscount', 'serviceRole', 'servicePrice', 'servicePriceRange')->whereHas('category', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            })->orderBy('id', 'desc')->paginate(100);
        }

        return $service;
    }


    public function wmmAllPackageSearch($request, $prefix)
    {
        $search = trim(strip_tags($request->search));
        $packages = Package::paginate(100);
        if ($search != "") {
            $packages = Package::where('name', 'like', '%' . $search . '%')->orWhere(['mrp' => $search])->paginate(100);
        }
        return $packages;
    }


    //show product with his category
    public function orderProductListByUserId($prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();
        if (count($productIdFromRole) == 0) {
            return array();
        }
        $categoryIds = UserSelectedCategoryOrService::where(['user_id' => Auth::guard('web')->user()->id, 'is_active' => 1, 'is_approve' => 1, 'category_type_flag' => '1'])->pluck('category_id')->toArray();
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company', 'mesurment'])->where(['is_hidden' => 0])->where(['is_active' => 1])->whereIn('id', $productIdFromRole)->whereIn('category_id', $categoryIds)->orderBy('updated_at', 'desc')->paginate(100);
    }


    public function wmmProductRequisition($prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();
        if (count($productIdFromRole) == 0) {
            return array();
        }
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company', 'mesurment'])->where(['is_hidden' => 0])->where(['is_active' => 1])->whereIn('id', $productIdFromRole)->orderBy('updated_at', 'desc')->paginate(100);
    }


    public function wmmProductForSaleAllStock($prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();
        if (count($productIdFromRole) == 0) {
            return array();
        }

        #dd(Auth::guard('web')->user()->id);
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStockAll', 'ProductStock', 'brand', 'company'])
            ->where(['is_hidden' => 0])
            ->whereIn('id', $productIdFromRole)
            ->whereHas('ProductStockAll', function ($query) {
                $query->where('user_id', Auth::guard('web')->user()->id)
                    ->where('stock_qty', '>', '0');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(100);

    }


    public function wmmProductForSaleAllStockSearch($request, $prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();

        if (count($productIdFromRole) == 0) {
            return array();
        }

        $queryFilter = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->whereIn('id', $productIdFromRole)
            ->whereHas('ProductStock', function ($query) {
                $query->where('stock_qty', '!=', Null);
            });

        if ($request->search != "") {
            $queryFilter->whereHas('product', function ($query) use ($request) {
                $query->where('product_name', 'LIKE', '%' . $request->search . '%')->where(['is_hidden' => 0]);
            });
        }

        if ($request->category_id != "") {
            $queryFilter->where(['category_id' => $request->category_id]);
        }

        if ($request->company_id != "") {
            $queryFilter->whereHas('product', function ($subquery) use ($request) {
                $subquery->where('company_id', $request->company_id)->where(['is_hidden' => 0]);
            });
        }

        if ($request->brand_id != "") {
            $queryFilter->where(['brand_id' => $request->brand_id]);
        }

        return $queryFilter->orderBy('created_at', 'desc')->paginate(100);

    }


    // use for all product view for sale
    public function wmmAllProductForSale($prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();
        if (count($productIdFromRole) == 0) {
            return array();
        }
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])->where(['is_hidden' => 0, 'is_active' => 1])->whereIn('id', $productIdFromRole)->orderBy('created_at', 'desc')->paginate(200);
    }


    // all product view By wmm for Sale Search
    public function wmmAllProductForSaleSearch($request, $prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();
        if (count($productIdFromRole) == 0) {
            return array();
        }
        $data = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->whereIn('id', $productIdFromRole)->orderBy('created_at', 'desc')->paginate(200);

        if ($request->search != "") {
            $data = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])
                ->where(['is_hidden' => 0, 'is_active' => 1])
                ->whereIn('id', $productIdFromRole)->whereHas('product', function ($subquery) use ($request) {
                    $subquery->where('product_name', 'LIKE', '%' . $request->search . '%');
                })->orderBy('created_at', 'desc')->paginate(200);
        }

        if ($request->category_id != "") {
            $data = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])
                ->where(['is_hidden' => 0, 'is_active' => 1])->whereIn('id', $productIdFromRole)->where(['category_id' => $request->category_id])->orderBy('created_at', 'desc')->paginate(200);
        }

        if ($request->company_id != "") {
            $data = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])
                ->where(['is_hidden' => 0, 'is_active' => 1])
                ->whereIn('id', $productIdFromRole)->whereHas('product', function ($subquery) use ($request) {
                    $subquery->where('company_id', $request->company_id);
                })->orderBy('created_at', 'desc')->paginate(200);
        }

        if ($request->brand_id != "") {
            $data = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])->where(['is_hidden' => 0, 'is_active' => 1])->whereIn('id', $productIdFromRole)->where(['brand_id' => $request->brand_id])->orderBy('created_at', 'desc')->paginate(200);
        }
        return $data;

    }


    //Product Order list Search by userId
    public function productOrderListSearchByUserId($request, $prefix)
    {

        $categoryIds = UserSelectedCategoryOrService::where(['user_id' => Auth::guard('web')->user()->id, 'is_active' => 1, 'is_approve' => 1, 'category_type_flag' => '1'])->pluck('category_id')->toArray();

        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();


        if (count($productIdFromRole) == 0) {
            return array();
        }

        $queryFilter = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->whereIn('id', $productIdFromRole)
            ->whereIn('category_id', $categoryIds);

        if ($request->category_id != null) {
            $queryFilter->where(['category_id' => $request->category_id]);
        }

        if ($request->search != null) {
            $queryFilter->whereHas('product', function ($query) use ($request) {
                $query->where('product_name', 'LIKE', '%' . trim($request->search) . '%')->where(['is_hidden' => 0]);
            });
        }

        if ($request->brand_id != null) {
            $queryFilter->where(['brand_id' => $request->brand_id]);
        }

        if ($request->company_id != null) {
            $queryFilter->where(['company_id' => $request->company_id]);
        }

        return $queryFilter->orderBy('created_at', 'asc')->paginate(200);
    }


    /**
     * @param $request
     * @param $prefix
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function allWmmProductListFilter($request, $prefix)
    {

        //$categoryIds = UserSelectedCategoryOrService::where(['user_id' => Auth::guard('web')->user()->id, 'is_active' => 1, 'is_approve' => 1, 'category_type_flag' => '1'])->pluck('category_id')->toArray();

        $productIdFromRole = RoleProduct::where(['flag' => 4])->pluck('product_id')->toArray();


        if (count($productIdFromRole) == 0) {
            return array();
        }

        $queryFilter = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->whereIn('id', $productIdFromRole);
        //->whereIn('category_id', $categoryIds);

        if ($request->category_id != null) {
            $queryFilter->where(['category_id' => $request->category_id]);
        }

        if ($request->search != null) {
            $queryFilter->whereHas('product', function ($query) use ($request) {
                $query->where('product_name', 'LIKE', '%' . trim($request->search) . '%')->where(['is_hidden' => 0]);
            });
        }

        if ($request->brand_id != null) {
            $queryFilter->where(['brand_id' => $request->brand_id]);
        }

        if ($request->company_id != null) {
            $queryFilter->where(['company_id' => $request->company_id]);
        }

        return $queryFilter->orderBy('created_at', 'asc')->paginate(200);
    }


    /**
     * @param $request
     * @param $prefix
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function productByIds($ids, $prefix)
    {
        $queryFilter = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->whereIn('id', $ids);
        return $queryFilter->orderBy('created_at', 'asc')->paginate(200);
    }


    /**
     * @param $request
     * @param $prefix
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function productById($id, $prefix)
    {
        return RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1, 'id' => $id])->paginate(10);


    }


    // old method this
    public function mWmmProductRequisitionSearch($request, $prefix)
    {
        $productIdFromRole = RoleProduct::where(['flag' => Auth::guard('web')->user()->flag])->pluck('product_id')->toArray();


        if (count($productIdFromRole) == 0) {
            return array();
        }

        $queryFilter = RetailNetworkProduct::with(['product', 'unit', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->whereIn('id', $productIdFromRole);
        #->orderBy('created_at', 'asc')->paginate(200);


        if ($request->category_id != null) {
            $queryFilter->where(['category_id' => $request->category_id]);
        }

        if ($request->search != null) {
            $queryFilter->whereHas('product', function ($query) use ($request) {
                $query->where('product_name', 'LIKE', '%' . trim($request->search) . '%')->where(['is_hidden' => 0]);
            });
        }

        if ($request->brand_id != null) {
            $queryFilter->where(['brand_id' => $request->brand_id]);
        }

        if ($request->company_id != null) {
            $queryFilter->where(['company_id' => $request->company_id]);
        }

        return $queryFilter->orderBy('created_at', 'asc')->paginate(200);


        //return $data;
    }

// wmmProductRequisitionSearch Not Used In this
    public function wmmProductRequisitionSearch($request, $prefix)
    {
        $search = trim(strip_tags($request->search));

        $products = DB::table($prefix . '_role_products')->distinct()
            ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.company_id', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
            ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
            ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
            ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
            ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
            ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
            ->where(['flag' => Auth::guard('web')->user()->flag])
            ->orderBy('products.product_name', 'asc')
            ->paginate(100);


        if ($request->search != "") {
            $products = DB::table($prefix . '_role_products')->distinct()
                ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.company_id', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
                ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
                ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
                ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
                ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
                ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
                ->where(['flag' => Auth::guard('web')->user()->flag])
                ->where('products.product_name', 'like', '%' . $search . '%')
                ->orderBy('products.product_name', 'asc')
                ->paginate(100);

        }

        if ($request->category_id != "") {

            $products = DB::table($prefix . '_role_products')->distinct()
                ->select($prefix . '_retailproducts.*', 'products.product_name', 'products.company_id', 'products.images', $prefix . '_categories.name as category_name', $prefix . '_product_units.name as unit_name', 'fmcg_brand.name as brand_name')
                ->join($prefix . '_retailproducts', $prefix . '_role_products.product_id', '=', $prefix . '_retailproducts.id')
                ->join('products', $prefix . '_retailproducts.product_id', '=', 'products.id')
                ->join($prefix . '_categories', $prefix . '_retailproducts.category_id', '=', $prefix . '_categories.id')
                ->join($prefix . '_product_units', $prefix . '_retailproducts.product_unit_id', '=', $prefix . '_product_units.id')
                ->join('fmcg_brand', 'products.brand_id', '=', 'fmcg_brand.id')
                ->where(['flag' => Auth::guard('web')->user()->flag])
                ->where([$prefix . '_retailproducts.category_id' => $request->category_id])
                ->orderBy('products.product_name', 'asc')
                ->paginate(100);

        }


        return $products;
    }


    public function packageDescription($prefix, $id)
    {
        return Package::with('packagePrice')->findOrFail($id);
    }


    public function productDescription($prefix, $id)
    {
        return RetailProduct::with(['product', 'category', 'productDiscount', 'roleProduct', 'ProductStock', 'ProductStockAll', 'brand', 'company'])
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->where(['id' => $id])
            ->get()
            ->first();
    }


    public function wmmPackageRequisition($prefix)
    {
        $packageId = PackagePriceRole::where(['role_id' => Auth::guard('web')->user()->role_id])->pluck('package_id')->toArray();

        $packages = Package::with('packagePrice', 'packageDiscount')
            ->whereIn('id', $packageId)
            ->orderBy('id', 'desc')
            ->paginate(200);

        return $packages;
    }

    public function wmmPackageRequisitionFilter($request, $prefix)
    {
        $packageId = PackagePriceRole::where(['role_id' => Auth::guard('web')->user()->role_id])->pluck('package_id')->toArray();

        $packages = Package::with('packagePrice')->whereIn('id', $packageId);

        if ($request->search != null) {
            $packages->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id != null) {
            $packages->where('category', 'like', '%' . $request->category_id . '%');
        }

        return $packages->orderBy('id', 'desc')->paginate(200);

    }


    //all package list for Sale
    public function wmmPackageListForSale($prefix)
    {
        $packages = Package::with('packagePrice')->orderBy('id', 'desc')->paginate(100);
        return $packages;
    }

    //all package list for Sale Search
    public function wmmPackageListForSaleSearch($request, $prefix)
    {
        $packages = Package::with('packagePrice')->orderBy('id', 'desc')->paginate(100);

        if ($request->search != "") {
            $packages = Package::with('packagePrice')->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%')->orWhere(['mrp' => $request->search]);
            })->orderBy('id', 'desc')->paginate(100);
        }

        if ($request->category_id != "") {
            $packages = Package::with('packagePrice')->where('category', 'LIKE', '%' . $request->category_id . '%')->orderBy('id', 'desc')->paginate(100);
        }

        return $packages;
    }


    public function productByCompany($prefix, $id)
    {
        $obj = new RetailNetworkProduct();
        $obj->setTable($prefix . '_retailproducts');
        $productsId = Product::where(['company_id' => $id])->where(['is_hidden' => 0])->pluck('id')->toArray();
        /*		$products = Arr::dot($productsId);
                [$keys, $values] = Arr::divide($products);*/


        $data = $obj->with(['product', 'unit', 'category'])->where(['is_hidden' => 0])->whereIn('product_id', $productsId)->get();

        if (count($data) > 0) {
            return $data;
        }

        Session::flash('error', 'Do not have Any product in this Company');
        return [];
    }


    public function productByBrand($prefix, $id)
    {

        $obj = new RetailNetworkProduct();
        $obj->setTable($prefix . '_retailproducts');
        $productsId = Product::where(['brand_id' => $id])->where(['is_hidden' => 0])->pluck('id')->toArray();

        $data = $obj->with(['product', 'unit', 'category'])->where(['is_hidden' => 0])->whereIn('product_id', $productsId)->get();

        if (count($data) > 0) {
            return $data;
        } else {
            return [];
        }


    }


    public function find($prefix, $id)
    {
        $obj = new RetailNetworkProduct();
        $obj->setTable($prefix . '_retailproducts');
        return $data = $obj->with('product', 'brand')->where(['is_hidden' => 0])->find($id);

    }


    public function store($request, $prefix)
    {


        try {
            DB::beginTransaction();
            $obj = RetailNetworkProduct::where(['product_id' => $request->product_id])->get()->first();
            $obj->category_id = $request->category_id;
            $obj->subcategory_id = $request->subcategory_id;
            $obj->sales_point = $request->sales_point;
            $obj->client_sales_point = $request->client_sales_point;
            $obj->distributor_purchase_price = $request->fmcg_price;
            $obj->bouns_point = $request->point;
            //$obj->moq = $request->moq;

            $obj->user_selling_price = $request->mrp;
            $obj->delivery_time = $request->delivery_time;
            $obj->sku_number = $request->sku_number;

            $obj->warranty_type = $request->warranty_type;
            $obj->warranty = $request->warranty;
            $obj->return_policy = $request->return_policy;
            $obj->imei_nubmer = $request->imei_nubmer;
            $obj->emi_payment = $request->emi_payment;


            //unit Management
            $obj->quanity_identifi = $request->identify;
            $jsonData = json_decode($request->quantity_type, true);
            if (count($jsonData) == 2) {
                $obj->setTranslation('quantity_type', 'en', $jsonData['en']);
                $obj->setTranslation('quantity_type', 'bn', $jsonData['bn']);
            }

            $array = [
                'moq_of_unit_type' => 1,
                'moq_of_quantity_type' => 1,
                'number_of_unit_quantity' => 0,
            ];

            //dd($request->moq_quantity_type);

            if ($request->identify == "Piece") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Box");
                    $obj->setTranslation('unit_type', 'bn', "বক্স");
                    $obj->number_of_unit_quantity = 10;
                    // $obj->setTranslation('micro_quantity_type','bn',null);
                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 10;
                    $array['moq_of_unit_type'] = 1;
                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;
                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;
                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Box") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;
                    $obj->setTranslation('micro_quantity_type', 'en', "Packet");
                    $obj->setTranslation('micro_quantity_type', 'bn', "প্যাকেট");
                    $obj->number_of_micro_quantity = 24;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 20;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "KG") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Bosta");
                    $obj->setTranslation('unit_type', 'bn', "বস্তা");
                    $obj->number_of_unit_quantity = 50;

                    $obj->setTranslation('micro_quantity_type', 'en', "gm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "গ্রাম");
                    $obj->number_of_micro_quantity = 1000;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 50;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Litter") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;

                    $obj->setTranslation('micro_quantity_type', 'en', "ml");
                    $obj->setTranslation('micro_quantity_type', 'bn', "মিলি লিটার");
                    $obj->number_of_micro_quantity = 1000;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 20;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Inches") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Foot");
                    $obj->setTranslation('unit_type', 'bn', "ফুট");
                    $obj->number_of_unit_quantity = 12;

                    $obj->setTranslation('micro_quantity_type', 'en', "cm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "সেন্টিমিটার");
                    $obj->number_of_micro_quantity = 2.54;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 12;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Foot") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Goj");
                    $obj->setTranslation('unit_type', 'bn', "গজ");
                    $obj->number_of_unit_quantity = 3;

                    $obj->setTranslation('micro_quantity_type', 'en', "Inches");
                    $obj->setTranslation('micro_quantity_type', 'bn', "ইঞ্চি");
                    $obj->number_of_micro_quantity = 12;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 3;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            }


            $inArray = array();
            $i = 0;
            foreach ($request->role_id as $role) {

                if (isset($request->purchase_price[$i]) && isset($request->role_id[$i])) {
                    $ex = explode(":", $role);
                    $flag_id = null;
                    switch ($ex[1]) {
                        case 3:
                            $key = "fo";
                            $flag_id = 3;
                            break;
                        case 4:
                            $key = "wmm";
                            $flag_id = 4;
                            break;
                        case 5:
                            $key = "offgrid";
                            $flag_id = 5;
                            break;
                    }
                    $inArray[$key] = $request->purchase_price[$i];
                    $orderPrice = $request->purchase_price[$i];

                    /********************** start discount ************************/
                    ProductDiscount::where(['retail_product_id' => $request->product_id])->delete();

                    //if discount have percentange
                    $discount = 0;
                    $unit_discount = 0;
                    if ($request->discount_type[$i] == "percentage") {
                        $discount = ($orderPrice / 100) * $request->discount_amount[$i];
                        $unit_discount = ($orderPrice / 100) * $request->discount_unit_type_amount[$i];
                    } else {
                        $discount = $request->discount_amount[$i];
                        $unit_discount = $request->discount_unit_type_amount[$i];
                    }

                    ProductDiscount::insert([
                        'retail_product_id' => $obj->id,
                        'role_id' => $ex[0],
                        'flag' => $flag_id,
                        'type' => $request->discount_type[$i],
                        'amount' => $request->discount_amount[$i],
                        'discount_unit_type_amount' => $request->discount_unit_type_amount[$i],
                        'total_discount' => $discount,
                        'total_discount_for_unit_type' => $unit_discount,
                        'valid_till' => $request->discount_validity[$i],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    /********************** end discount ************************/
                }
                $i++;
            }

            $obj->user_purchase_price = json_encode($inArray);
            $obj->moq_for_unit = json_encode($array);
            $obj->is_active = 1;
            $obj->update();


            //remove Role Product
            RoleProduct::where(['product_id' => $obj->id])->delete();
            foreach ($request->role_id as $role) {
                $ex = explode(":", $role);
                RoleProduct::insert([
                    'product_id' => $obj->id,
                    'role_id' => $ex[0],
                    'flag' => $ex[1],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            /************* ************/
//            $obj->problem_area_id = $request->problem_area_id;
//            $obj->target_market_id = $request->target_market_id;
//            $obj->education_requirement_id = $request->education_requirement_id;
//            $obj->skills_certification_id = $request->skills_certification_id;
//            $obj->investment_requirment_id = $request->investment_requirment_id;
//            $obj->management_role_id = $request->management_role_id;

            // $obj->impact_area_id = $request->impact_area_id; Impact Area

            //Impact Area
            foreach ($request->impact_area_id as $item) {
                ProductImpactArea::insert([
                    'product_id' => $obj->id,
                    'impact_area_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            //problem area
            foreach ($request->problem_area_id as $item) {
                ProductProblemArea::insert([
                    'product_id' => $obj->id,
                    'problem_area_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //target market
            foreach ($request->target_market_id as $item) {
                ProductTargetMarket::insert([
                    'product_id' => $obj->id,
                    'target_market_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //education requirement
            foreach ($request->education_requirement_id as $item) {
                ProductEducationRequirement::insert([
                    'product_id' => $obj->id,
                    'education_requirement_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //skill requirement
            foreach ($request->skills_certification_id as $item) {
                ProductSkillCertificate::insert([
                    'product_id' => $obj->id,
                    'skills_certification_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //investment Requirement
            foreach ($request->investment_requirment_id as $item) {
                ProductInvestmentRequirement::insert([
                    'product_id' => $obj->id,
                    'investment_requirment_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            //management role
            foreach ($request->management_role_id as $item) {
                ProductManagementRole::insert([
                    'product_id' => $obj->id,
                    'management_role_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            /************* ************/


            //product channel
            $proChannel = ChannelProduct::where(['product_id' => $obj->id]);
            if ($proChannel->count() > 0) {
                ChannelProduct::where(['product_id' => $obj->id])->delete();
            }
            foreach ($request->channel_id as $channel) {
                ChannelProduct::insert([
                    'product_id' => $obj->id,
                    'channel_id' => $channel,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            //end Product channel

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->Error = $e->getMessage();
            return false;
        }

    }


    public function update($request, $prefix, $id)
    {
        try {
            DB::beginTransaction();
            $obj = RetailNetworkProduct::where(['product_id' => $request->product_id])->get()->first();
            $obj->category_id = $request->category_id;
            $obj->subcategory_id = $request->subcategory_id;
            $obj->sales_point = $request->sales_point;
            $obj->client_sales_point = $request->client_sales_point;
            $obj->distributor_purchase_price = $request->fmcg_price;
            $obj->bouns_point = $request->point;
            //$obj->moq = $request->moq;

            $inArray = array();
            $i = 0;
            foreach ($request->role_id as $role) {
                if (isset($request->purchase_price[$i]) && isset($request->role_id[$i])) {
                    $ex = explode(":", $role);
                    switch ($ex[1]) {
                        case 3:
                            $key = "fo";
                            break;
                        case 4:
                            $key = "wmm";
                            break;
                        case 5:
                            $key = "offgrid";
                            break;
                    }

                    $inArray[$key] = $request->purchase_price[$i];
                }
                $i++;
            }

            $obj->user_purchase_price = json_encode($inArray);
            $obj->user_selling_price = $request->mrp;
            $obj->delivery_time = $request->delivery_time;
            $obj->sku_number = $request->sku_number;

            $obj->warranty_type = $request->warranty_type;
            $obj->warranty = $request->warranty;
            $obj->return_policy = $request->return_policy;
            $obj->imei_nubmer = $request->imei_nubmer;
            $obj->emi_payment = $request->emi_payment;


            //unit Management
            $obj->quanity_identifi = $request->identify;
            $jsonData = json_decode($request->quantity_type, true);
            if (count($jsonData) == 2) {
                $obj->setTranslation('quantity_type', 'en', $jsonData['en']);
                $obj->setTranslation('quantity_type', 'bn', $jsonData['bn']);
            }

            $array = [
                'moq_of_unit_type' => 1,
                'moq_of_quantity_type' => 1,
                'number_of_unit_quantity' => 0,
            ];

            //dd($request->moq_quantity_type);

            if ($request->identify == "Piece") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Box");
                    $obj->setTranslation('unit_type', 'bn', "বক্স");
                    $obj->number_of_unit_quantity = 10;
                    // $obj->setTranslation('micro_quantity_type','bn',null);
                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 10;
                    $array['moq_of_unit_type'] = 1;
                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;
                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;
                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Box") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;
                    $obj->setTranslation('micro_quantity_type', 'en', "Packet");
                    $obj->setTranslation('micro_quantity_type', 'bn', "প্যাকেট");
                    $obj->number_of_micro_quantity = 24;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 20;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "KG") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Bosta");
                    $obj->setTranslation('unit_type', 'bn', "বস্তা");
                    $obj->number_of_unit_quantity = 50;

                    $obj->setTranslation('micro_quantity_type', 'en', "gm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "গ্রাম");
                    $obj->number_of_micro_quantity = 1000;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 50;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Litter") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;

                    $obj->setTranslation('micro_quantity_type', 'en', "ml");
                    $obj->setTranslation('micro_quantity_type', 'bn', "মিলি লিটার");
                    $obj->number_of_micro_quantity = 1000;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 20;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Inches") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Foot");
                    $obj->setTranslation('unit_type', 'bn', "ফুট");
                    $obj->number_of_unit_quantity = 12;

                    $obj->setTranslation('micro_quantity_type', 'en', "cm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "সেন্টিমিটার");
                    $obj->number_of_micro_quantity = 2.54;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 12;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            } else if ($request->identify == "Foot") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Goj");
                    $obj->setTranslation('unit_type', 'bn', "গজ");
                    $obj->number_of_unit_quantity = 3;

                    $obj->setTranslation('micro_quantity_type', 'en', "Inches");
                    $obj->setTranslation('micro_quantity_type', 'bn', "ইঞ্চি");
                    $obj->number_of_micro_quantity = 12;

                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = 3;
                    $array['moq_of_unit_type'] = 1;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                    if ($request->moq_unit_type != "") {
                        $array['moq_of_unit_type'] = $request->moq_unit_type;
                    }
                    $array['moq_of_quantity_type'] = $request->moq_quantity_type;
                    $array['number_of_unit_quantity'] = $request->number_of_unit;

                }

            }
            $obj->moq_for_unit = json_encode($array);
            $obj->is_active = 1;
            $obj->update();


            //remove Role Product
            RoleProduct::where(['product_id' => $obj->id])->delete();
            foreach ($request->role_id as $role) {
                $ex = explode(":", $role);
                RoleProduct::insert([
                    'product_id' => $obj->id,
                    'role_id' => $ex[0],

                    'flag' => $ex[1],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            /************* ************/

            //Impact Area
            ProductImpactArea::where(['product_id' => $obj->id])->delete();
            foreach ($request->impact_area_id as $item) {
                ProductImpactArea::insert([
                    'product_id' => $obj->id,
                    'impact_area_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            //problem area
            ProductProblemArea::where(['product_id' => $obj->id])->delete();
            foreach ($request->problem_area_id as $item) {
                ProductProblemArea::insert([
                    'product_id' => $obj->id,
                    'problem_area_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //target market
            ProductTargetMarket::where(['product_id' => $obj->id])->delete();
            foreach ($request->target_market_id as $item) {
                ProductTargetMarket::insert([
                    'product_id' => $obj->id,
                    'target_market_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //education requirement
            ProductEducationRequirement::where(['product_id' => $obj->id])->delete();
            foreach ($request->education_requirement_id as $item) {
                ProductEducationRequirement::insert([
                    'product_id' => $obj->id,
                    'education_requirement_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //skill requirement
            ProductSkillCertificate::where(['product_id' => $obj->id])->delete();
            foreach ($request->skills_certification_id as $item) {
                ProductSkillCertificate::insert([
                    'product_id' => $obj->id,
                    'skills_certification_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            //investment Requirement
            ProductInvestmentRequirement::where(['product_id' => $obj->id])->delete();
            foreach ($request->investment_requirment_id as $item) {
                ProductInvestmentRequirement::insert([
                    'product_id' => $obj->id,
                    'investment_requirment_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            //management role
            ProductManagementRole::where(['product_id' => $obj->id])->delete();
            foreach ($request->management_role_id as $item) {
                ProductManagementRole::insert([
                    'product_id' => $obj->id,
                    'management_role_id' => $item,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            /************* ************/


            //add product discount
            $k = 0;
            if ($request->discount_role[0] != "") {
                //remove old discount for this product
                ProductDiscount::where(['retail_product_id' => $obj->id])->delete();

                foreach ($request->discount_role as $discount) {
                    $roleEx = explode(':', $request->discount_role[$k]);
                    $key = null;
                    switch ($roleEx[1]) {
                        case 3:
                            $key = "fo";
                            break;
                        case 4:
                            $key = "wmm";
                            break;
                        case 5:
                            $key = "offgrid";
                            break;
                    }
                    $orderPrice = 0;
                    if (isset($inArray[$key])) {
                        $orderPrice = $inArray[$key];
                    }

                    //if discount have percentange
                    $discount = 0;
                    $unit_discount = 0;
                    if ($request->discount_type[$k] == "percentage") {
                        $discount = ($orderPrice / 100) * $request->discount_amount[$k];
                    } else {
                        $discount = $request->discount_amount[$k];
                    }

                    //Unit type discount
                    $unit_discount = 0;
                    if ($request->discount_type[$k] == "percentage") {
                        $unit_discount = ($orderPrice / 100) * $request->discount_unit_type_amount[$k];
                    } else {
                        $unit_discount = $request->discount_unit_type_amount[$k];
                    }


                    // if moq is geather then 1
                    if ($array['moq_of_quantity_type'] > 1) {
                        $discount = number_format($discount / $array['moq_of_quantity_type']);
                    }

                    if ($array['moq_of_unit_type'] > 1) {
                        $unit_discount = number_format($unit_discount / $array['moq_of_unit_type'], 2);
                    }

                    //dd($unit_discount);
                    /***
                     * $array = [
                     * 'moq_of_unit_type'=>1,
                     * 'moq_of_quantity_type'=>1,
                     * 'number_of_unit_quantity'=>0,
                     * ];
                     **/

                    /*
                    $array['moq_of_unit_type'] = $request->moq_unit_type;
                    $array['moq_of_quantity_type'] = $request->moq_of_quantity_typ
                    */

                    ProductDiscount::insert([
                        'retail_product_id' => $obj->id,
                        'role_id' => $roleEx[0],
                        'flag' => $roleEx[1],
                        'type' => $request->discount_type[$k],
                        'amount' => $request->discount_amount[$k],
                        'discount_unit_type_amount' => $request->discount_unit_type_amount[$k],
                        'total_discount' => $discount,
                        'total_discount_for_unit_type' => $unit_discount,
                        'valid_till' => $request->discount_validity[$k],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $k++;
                }
            }

            //product channel
            $proChannel = ChannelProduct::where(['product_id' => $obj->id]);
            if ($proChannel->count() > 0) {
                ChannelProduct::where(['product_id' => $obj->id])->delete();
            }
            foreach ($request->channel_id as $channel) {
                ChannelProduct::insert([
                    'product_id' => $obj->id,
                    'channel_id' => $channel,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            //end Product channel

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->Error = $e->getMessage();
            return false;
        }
    }


    public function isActive($prefix, $status, $id)
    {
        try {
            $obj = $this->find($prefix, $id);
            if ($status === "yes") {
                $obj->is_active = 1;
            } else {
                $obj->is_active = 0;
            }
            $obj->update();
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }

    }


    public function isActiveFmcgAccount($prefix, $status, $id)
    {
        try {
            if ($status === "yes") {
                DistrubutionFmcg::where(['fmcg_account_id' => $id])->update(['is_active' => 1]);
                $productIdes = Product::where(['company_id' => $id])->pluck('id')->toArray();
                if (!empty($productIdes)) {
                    RetailNetworkProduct::whereIn('product_id', $productIdes)->update(['is_active' => 1]);
                    //cache all Enable Fmcg Account
                    $this->cache->retailEnableFmcgCompany($prefix);

                    //cache all product after update
                    $this->cache->retailProductCache($prefix);
                }

            } else {
                DistrubutionFmcg::where(['fmcg_account_id' => $id])->update(['is_active' => 0]);
                $productIdes = Product::where(['company_id' => $id])->pluck('id')->toArray();
                if (!empty($productIdes)) {
                    RetailNetworkProduct::whereIn('product_id', $productIdes)->update(['is_active' => 0]);
                    //cache all Enable Fmcg Account
                    $this->cache->retailEnableFmcgCompany($prefix);

                    //cache all product after update
                    $this->cache->retailProductCache($prefix);
                }
            }
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }


    }


    //retails product company search
    public function companySearch($request)
    {
        $search = trim($request->search);
        $company = DistrubutionFmcg::with('company')->get();
        if ($request->search != "") {
            $fmcgCompany = Fmcg::where('company_name', 'LIKE', '%' . $search . '%')->pluck('id')->toArray();
            if (!empty($fmcgCompany)) {
                $company = DistrubutionFmcg::with('company')->whereIn('fmcg_account_id', $fmcgCompany)->get();
            }
        }

        if ($request->is_active != "") {
            $company = DistrubutionFmcg::with('company')->where(['is_active' => $request->is_active])->get();
        }
        return $company;
    }


    //retails product brand search
    public function brandSearch($request)
    {
        $search = trim($request->search);
        $companyIdes = DistrubutionFmcg::with('company')->where(['is_active' => 1])->pluck('fmcg_account_id')->toArray();

        $brands = Brand::whereIn('company_id', $companyIdes)->where(['is_hidden' => 0])->paginate(100);


        if ($request->search != "") {
            if (!empty($companyIdes)) {

                $brands = Brand::where('name', 'LIKE', '%' . $search . '%')->where(['is_hidden' => 0])->whereIn('company_id', $companyIdes)->paginate(100);
            }
        }

        if ($request->is_active != "") {

            $brands = Brand::where(['is_active' => $request->is_active])->where(['is_hidden' => 0])->whereIn('company_id', $companyIdes)->paginate(100);

        }
        return $brands;
    }


    public function productSearch($request, $prefix)
    {

        $search = trim($request->search);
        $pram = new RetailNetworkProduct();
        $pram->setTable($prefix . '_retailproducts');
        $data = $pram->all();

        if ($search != "") {
            $data = $pram->where('name', 'LIKE', '%' . $search . '%')->orWhere('user_purchase_price', 'LIKE', '%' . $search . '%')->paginate(200);
        }

        if ($request->is_active != "") {
            $data = $pram->where(['is_active' => $request->is_active])->paginate(200);
        }
        return $data;

    }


    public function search($request, $prefix)
    {
        $search = trim($request->search);
        $pram = new RetailNetworkRole();
        $pram->setTable($prefix . '_roles');
        return $pram->where('name', 'LIKE', '%' . $search . '%')->paginate(50);
    }


    public function delete($prefix, $id)
    {
        try {
            $pram = $this->find($prefix, $id);
            $pram->delete();
            return true;

        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }

    }


    public function getBrandForUser($prefix)
    {
        /**
         * $companyIdes = DistrubutionFmcg::with('company')->where(['is_active'=>1])->pluck('fmcg_account_id')->toArray();
         * $brands = Brand::whereIn('company_id', $companyIdes)->get();
         **/


//        $fmcg = DistrubutionFmcg::where(['prefix' => $prefix, 'is_active' => 1])->get('fmcg_account_id')->toArray();
//        $brands = Brand::whereIn('company_id', $companyIdes)->get();
//        $brandid = Arr::dot($fmcg);
//        [$keys, $brands] = Arr::divide($brandid);
//        return Brand::find($brands);

        $companyIdes = DistrubutionFmcg::with('company')->where(['is_active' => 1])->pluck('fmcg_account_id')->toArray();
        $brands = Brand::whereIn('company_id', $companyIdes)->where(['is_hidden' => 0])->paginate(100);

        return $brands;

    }


    public function createPackage($request, $prefix)
    {

        $image = new ImageService();
        $fileName = $image->storeImage($request, 'images', 'public/package_image/', ['height' => 150, 'weight' => 150]);

        DB::beginTransaction();
        try {
            //add package
            $package = new Package();
//            $package->name = $request->en_name;
//            $package->package_category = $request->en_package_category;
            $package->setTranslation('name', 'en', $request->en_name);
            $package->setTranslation('name', 'bn', $request->bn_name);
            $package->setTranslation('category', 'en', $request->en_package_category);
            $package->setTranslation('category', 'bn', $request->bn_package_category);
            $package->mrp = $request->mrp;
            if ($fileName != null) {
                $package->image = $fileName;
            }


            $inArray = array();
            $i = 0;
            foreach ($request->role_id as $role) {
                if (isset($request->purchase_price[$i]) && isset($request->role_id[$i])) {
                    $ex = explode(":", $role);
                    switch ($ex[1]) {
                        case 3:
                            $key = "fo";
                            break;
                        case 4:
                            $key = "wmm";
                            break;
                        case 5:
                            $key = "offgrid";
                            break;
                    }
                    $inArray[$key] = $request->purchase_price[$i];
                }
                $i++;
            }
            $package->purches_price = json_encode($inArray);
            $package->save();

            $priceCount = 0;
            foreach ($request->role_id as $role) {
                $ex = explode(":", $role);
                PackagePriceRole::insert([
                    'package_id' => $package->id,
                    'role_id' => $ex[0],
                    'purchase_price' => $request->purchase_price[$priceCount],
                    'flag' => $ex[1],
                ]);
                $priceCount++;
            }


            /***************************************** Add Package Item  *************************************/

            foreach (TemporaryPackageItem::where(['session_id' => Session::getId()])->get() as $value) {
                //dd($value);
                PackageItem::insert([
                    'product_id' => $value->product_id,
                    'package_id' => $package->id,
                    'type' => $value->type,
                    'qty' => $value->qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            //remove temporary Data
            TemporaryPackageItem::where(['session_id' => Session::getId()])->delete();

            /***************************************** end Package Item  *************************************/


            /***************************************** Add Package Discount  *************************************/
            $k = 0;
            PackageDiscount::where(['package_id' => $package->id])->delete();
            foreach ($request->discount_role as $discount) {
                $roleEx = explode(':', $request->discount_role[$k]);
                $key = null;
                switch ($roleEx[1]) {
                    case 3:
                        $key = "fo";
                        break;
                    case 4:
                        $key = "wmm";
                        break;
                    case 5:
                        $key = "offgrid";
                        break;
                }
                $orderPrice = 0;
                if (isset($inArray[$key])) {
                    $orderPrice = $inArray[$key];
                }

                //if discount have percentange
                $discount = 0;
                if ($request->discount_type[$k] == "percentage") {
                    $discount = ($orderPrice / 100) * $request->discount_amount[$k];
                } else {
                    $discount = $request->discount_amount[$k];
                }

                PackageDiscount::insert([
                    'package_id' => $package->id,
                    'role_id' => $roleEx[0],
                    'flag' => $roleEx[1],
                    'type' => $request->discount_type[$k],
                    'amount' => $request->discount_amount[$k],
                    'total_discount' => $discount,
                    'valid_till' => $request->discount_validity[$k],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $k++;
            }
            /***************************************** Add Package Discount  *************************************/


            DB::commit();
            return true;
        } catch (Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }

    }


    public function updatePackage($request, $prefix, $id)
    {

        $image = new ImageService();
        $fileName = $image->storeImage($request, 'images', 'public/package_image/', ['height' => 150, 'weight' => 150]);
        DB::beginTransaction();
        try {
            //add package
            $package = Package::find($id);
//            $package->name = $request->en_name;
//            $package->package_category = $request->en_package_category;
            $package->setTranslation('name', 'en', $request->en_name);
            $package->setTranslation('name', 'bn', $request->bn_name);
            $package->setTranslation('category', 'en', $request->en_package_category);
            $package->setTranslation('category', 'bn', $request->bn_package_category);
            $package->mrp = $request->mrp;
            if ($fileName != null) {
                $package->image = $fileName;
            }

            $inArray = array();
            $i = 0;
            foreach ($request->role_id as $role) {
                if (isset($request->purchase_price[$i]) && isset($request->role_id[$i])) {
                    $ex = explode(":", $role);
                    switch ($ex[1]) {
                        case 3:
                            $key = "fo";
                            break;
                        case 4:
                            $key = "wmm";
                            break;
                        case 5:
                            $key = "offgrid";
                            break;
                    }
                    $inArray[$key] = $request->purchase_price[$i];
                }
                $i++;
            }
            $package->purches_price = json_encode($inArray);
            $package->update();


            $priceCount = 0;
            PackagePriceRole::where(['package_id' => $package->id])->delete();
            foreach ($request->role_id as $role) {
                $ex = explode(":", $role);
                PackagePriceRole::insert([
                    'package_id' => $package->id,
                    'role_id' => $ex[0],
                    'purchase_price' => $request->purchase_price[$priceCount],
                    'flag' => $ex[1],
                ]);
                $priceCount++;
            }


            /***************************************** Add Package Item  *************************************/
            PackageItem::where(['package_id' => $package->id])->delete();
            foreach (TemporaryPackageItem::where(['session_id' => Session::getId()])->get() as $value) {
                //dd($value);
                PackageItem::insert([
                    'product_id' => $value->product_id,
                    'package_id' => $package->id,
                    'type' => $value->type,
                    'qty' => $value->qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            //remove temporary Data
            TemporaryPackageItem::where(['session_id' => Session::getId()])->delete();
            /***************************************** end Package Item  *************************************/


            /***************************************** Add Package Discount  *************************************/
            $k = 0;
            PackageDiscount::where(['package_id' => $package->id])->delete();
            foreach ($request->discount_role as $discount) {
                $roleEx = explode(':', $request->discount_role[$k]);
                $key = null;
                switch ($roleEx[1]) {
                    case 3:
                        $key = "fo";
                        break;
                    case 4:
                        $key = "wmm";
                        break;
                    case 5:
                        $key = "offgrid";
                        break;
                }
                $orderPrice = 0;
                if (isset($inArray[$key])) {
                    $orderPrice = $inArray[$key];
                }

                //if discount have percentange
                $discount = 0;
                if ($request->discount_type[$k] == "percentage") {
                    $discount = ($orderPrice / 100) * $request->discount_amount[$k];
                } else {
                    $discount = $request->discount_amount[$k];
                }

                PackageDiscount::insert([
                    'package_id' => $package->id,
                    'role_id' => $roleEx[0],
                    'flag' => $roleEx[1],
                    'type' => $request->discount_type[$k],
                    'amount' => $request->discount_amount[$k],
                    'total_discount' => $discount,
                    'valid_till' => $request->discount_validity[$k],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $k++;
            }
            /***************************************** Add Package Discount  *************************************/


            DB::commit();
            return true;
        } catch (Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }
    }


}





