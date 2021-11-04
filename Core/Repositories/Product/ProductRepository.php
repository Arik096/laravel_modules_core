<?php

namespace Modules\Core\Repositories\Product;

use App\Models\RetailNetwork\ProductOrServiceManagement\ProductSkillCertificate;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\Core\Models\Fmcg\Fmcg;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Products\ChannelProduct;
use Modules\Core\Models\Products\Product;
use Image;
use Modules\Core\Models\Products\ProductDiscount;
use Modules\Core\Models\Products\ProductEducationRequirement;
use Modules\Core\Models\Products\ProductImpactArea;
use Modules\Core\Models\Products\ProductInvestmentRequirement;
use Modules\Core\Models\Products\ProductManagementRole;
use Modules\Core\Models\Products\ProductProblemArea;
use Modules\Core\Models\Products\ProductSkillCertificationReq;
use Modules\Core\Models\Products\ProductTargetMarket;
use Modules\Core\Models\Products\RetailProduct;
use Modules\Core\Models\Products\RoleProduct;
use Modules\Core\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{

    /**
     * @var
     */
    public $Error;


    public function all()
    {
        return RetailProduct::with(['product', 'category', 'productDiscount'])
            ->where('distributor_purchase_price', '!=', null)
            ->where('user_purchase_price', '!=', null)
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function allPaginate()
    {
        return RetailProduct::with(['product', 'category', 'productDiscount'])
            ->where('distributor_purchase_price', '!=', null)
            ->where('user_purchase_price', '!=', null)
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }

    public function allActive()
    {
        return RetailProduct::with(['product', 'category', 'productDiscount'])
            ->where('distributor_purchase_price', '!=', null)
            ->where('user_purchase_price', '!=', null)
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function filter($request)
    {

        $products = RetailProduct::with(['product', 'category', 'productDiscount']);

        if ($request->search != "") {

            $products->whereHas('product', function ($p) use ($request) {
                $p->where('product_name', 'like', '%' . $request->search . '%');
            });

        }

        if ($request->company_id != "") {

            $products->where(['company_id' => $request->company_id]);
        }

        if ($request->category_id != "") {
            $products->where(['category_id' => $request->category_id]);
        }

        if ($request->is_active != "") {
            $products->where(['is_active' => $request->is_active]);
        }

        $products->where(function ($wh) {

            $wh->where('distributor_purchase_price', '!=', null)->where('user_purchase_price', '!=', null)->where(['is_hidden' => 0]);

        });

        return $products->orderBy('id', 'desc')->paginate(100);

    }

    public function find($id)
    {
        return Product::with('company', 'brand')
            ->where(['is_hidden' => 0, 'id' => $id])
            ->orderBy('id', 'desc')
            ->first();
    }

    public function create($request)
    {
        // TODO: Implement create() method.
    }

    public function store($request)
    {
        DB::begintransaction();

        try {

            $obj = RetailProduct::where(['product_id' => $request->product_id])->get()->first();

            $obj->category_id = $request->category_id;
            $obj->subcategory_id = $request->subcategory_id;
            $obj->distributor_purchase_price = $request->fmcg_price;;
            $obj->moq = $request->moq;

            if ($request->client_sales_point != "") {
                $obj->client_sales_point = $request->client_sales_point;
            }

            if ($request->point != "") {
                $obj->bouns_point = $request->point;
            }

            if ($request->sales_point != "") {
                $obj->sales_point = $request->sales_point;
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

            if ($request->identify == "Piece") {

                if ($request->unit_type_en == "") {

                    $obj->setTranslation('unit_type', 'en', "Box");
                    $obj->setTranslation('unit_type', 'bn', "বক্স");
                    $obj->number_of_unit_quantity = 10;
                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;

                } else {

                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;
                }

            } else if ($request->identify == "Box") {

                if ($request->unit_type_en == "") {

                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;

                    $obj->setTranslation('micro_quantity_type', 'en', "Packet");
                    $obj->setTranslation('micro_quantity_type', 'bn', "প্যাকেট");
                    $obj->number_of_micro_quantity = 24;

                } else {

                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "KG") {

                if ($request->unit_type_en == "") {

                    $obj->setTranslation('unit_type', 'en', "Bosta");
                    $obj->setTranslation('unit_type', 'bn', "বস্তা");
                    $obj->number_of_unit_quantity = 50;

                    $obj->setTranslation('micro_quantity_type', 'en', "gm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "গ্রাম");
                    $obj->number_of_micro_quantity = 1000;

                } else {

                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "Litter") {

                if ($request->unit_type_en == "") {

                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;

                    $obj->setTranslation('micro_quantity_type', 'en', "ml");
                    $obj->setTranslation('micro_quantity_type', 'bn', "মিলি লিটার");
                    $obj->number_of_micro_quantity = 1000;

                } else {

                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "Inches") {

                if ($request->unit_type_en == "") {

                    $obj->setTranslation('unit_type', 'en', "Foot");
                    $obj->setTranslation('unit_type', 'bn', "ফুট");
                    $obj->number_of_unit_quantity = 12;

                    $obj->setTranslation('micro_quantity_type', 'en', "cm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "সেন্টিমিটার");
                    $obj->number_of_micro_quantity = 2.54;

                } else {

                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "Foot") {

                if ($request->unit_type_en == "") {

                    $obj->setTranslation('unit_type', 'en', "Goj");
                    $obj->setTranslation('unit_type', 'bn', "গজ");
                    $obj->number_of_unit_quantity = 3;

                    $obj->setTranslation('micro_quantity_type', 'en', "Inches");
                    $obj->setTranslation('micro_quantity_type', 'bn', "ইঞ্চি");
                    $obj->number_of_micro_quantity = 12;

                } else {

                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;

                }

            }


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
                ProductSkillCertificationReq::insert([
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
                    if ($request->discount_type[$k] == "percentage") {
                        $discount = ($orderPrice / 100) * $request->discount_amount[$k];
                    } else {
                        $discount = $request->discount_amount[$k];
                    }

                    // if moq is geather then 1
                    if ($request->moq > 1) {
                        $discount = ($discount / $request->moq);
                    }

                    ProductDiscount::insert([
                        'retail_product_id' => $obj->id,
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

    public function update($request, $company_id, $id)
    {
        DB::begintransaction();

        try {

            $obj = RetailProduct::where(['product_id' => $request->product_id])->get()->first();

            $obj->category_id = $request->category_id;
            $obj->subcategory_id = $request->subcategory_id;
            $obj->distributor_purchase_price = $request->fmcg_price;
            $obj->moq = $request->moq;

            if ($request->client_sales_point != "") {
                $obj->client_sales_point = $request->client_sales_point;
            }

            if ($request->point != "") {
                $obj->bouns_point = $request->point;
            }

            if ($request->sales_point != "") {
                $obj->sales_point = $request->sales_point;
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

            $obj->user_purchase_price = json_encode($inArray);
            $obj->user_selling_price = $request->mrp;
            $obj->delivery_time = $request->delivery_time;
            #$obj->sku_number = $request->sku_number;
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

            if ($request->identify == "Piece") {

                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Box");
                    $obj->setTranslation('unit_type', 'bn', "বক্স");
                    $obj->number_of_unit_quantity = 10;
                    // $obj->setTranslation('micro_quantity_type','bn',null);
                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->forgetMyselfTranslation('micro_quantity_type');
                    $obj->micro_quantity_type = null;
                    $obj->number_of_micro_quantity = 0;
                }

            } else if ($request->identify == "Box") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;

                    $obj->setTranslation('micro_quantity_type', 'en', "Packet");
                    $obj->setTranslation('micro_quantity_type', 'bn', "প্যাকেট");
                    $obj->number_of_micro_quantity = 24;
                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "KG") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Bosta");
                    $obj->setTranslation('unit_type', 'bn', "বস্তা");
                    $obj->number_of_unit_quantity = 50;

                    $obj->setTranslation('micro_quantity_type', 'en', "gm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "গ্রাম");
                    $obj->number_of_micro_quantity = 1000;
                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "Litter") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Cartoon");
                    $obj->setTranslation('unit_type', 'bn', "কার্টুন");
                    $obj->number_of_unit_quantity = 20;

                    $obj->setTranslation('micro_quantity_type', 'en', "ml");
                    $obj->setTranslation('micro_quantity_type', 'bn', "মিলি লিটার");
                    $obj->number_of_micro_quantity = 1000;
                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "Inches") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Foot");
                    $obj->setTranslation('unit_type', 'bn', "ফুট");
                    $obj->number_of_unit_quantity = 12;

                    $obj->setTranslation('micro_quantity_type', 'en', "cm");
                    $obj->setTranslation('micro_quantity_type', 'bn', "সেন্টিমিটার");
                    $obj->number_of_micro_quantity = 2.54;
                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            } else if ($request->identify == "Foot") {
                if ($request->unit_type_en == "") {
                    $obj->setTranslation('unit_type', 'en', "Goj");
                    $obj->setTranslation('unit_type', 'bn', "গজ");
                    $obj->number_of_unit_quantity = 3;

                    $obj->setTranslation('micro_quantity_type', 'en', "Inches");
                    $obj->setTranslation('micro_quantity_type', 'bn', "ইঞ্চি");
                    $obj->number_of_micro_quantity = 12;

                } else {
                    $obj->setTranslation('unit_type', 'en', $request->unit_type_en);
                    $obj->setTranslation('unit_type', 'bn', $request->unit_type_bn);
                    $obj->number_of_unit_quantity = $request->number_of_unit;

                    $obj->setTranslation('micro_quantity_type', 'en', $request->micro_unit_type_en);
                    $obj->setTranslation('micro_quantity_type', 'bn', $request->micro_unit_type_bn);
                    $obj->number_of_micro_quantity = $request->micro_number_of_unit;
                }

            }

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
            ProductSkillCertificationReq::where(['product_id' => $obj->id])->delete();

            foreach ($request->skills_certification_id as $item) {
                ProductSkillCertificationReq::insert([
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

            if (isset($request->discount_role)) {

                if ($request->discount_role[0] != "") {

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


                        $discount = 0;

                        if ($request->discount_type[$k] == "percentage") {

                            $discount = ($orderPrice / 100) * $request->discount_amount[$k];
                        } else {

                            $discount = $request->discount_amount[$k];

                        }

                        // if moq is geather then 1
                        if ($request->moq > 1) {
                            $discount = ($discount / $request->moq);
                        }

                        ProductDiscount::insert([
                            'retail_product_id' => $obj->id,
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


            DB::commit();

            return true;

        } catch (Exception $e) {

            DB::rollBack();
            $this->Error = $e->getMessage();
            return false;

        }

    }

    public function activation($status, $id)
    {
        DB::beginTransaction();

        try {

            $obj = RetailProduct::find($id);

            if ($status === "yes") {

                $obj->is_active = 1;

            } else {

                $obj->is_active = 0;
            }

            $obj->update();

            DB::commit();

            return true;

        } catch (Exception $ex) {

            DB::rollBack();

            $this->Error = $ex->getMessage();

            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $pram = RetailProduct::find($id);

            $pram->delete();

            DB::commit();

            return true;

        } catch (Exception $ex) {

            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;

        }

    }

    public function order_status($status, $id)
    {
        DB::beginTransaction();

        try {

            $obj = RetailProduct::find($id);

            if ($status === "yes") {

                $obj->is_order_active = 0;

            } else {

                $obj->is_order_active = 1;
            }

            $obj->update();

            DB::commit();

            return true;

        } catch (Exception $ex) {

            DB::rollBack();

            $this->Error = $ex->getMessage();

            return false;
        }
    }

    public function allForSelect($prefix)
    {
        return RetailProduct::with(['product', 'category'])
            #->whereNull('distributor_purchase_price')
            #->whereNull('user_purchase_price')
            ->where(['is_hidden' => 0])
            ->orderBy('created_at', 'desc')
            ->get();
    }

}





