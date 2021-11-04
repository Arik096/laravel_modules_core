<?php

namespace Modules\Core\Repositories\Fmcg;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\Core\Models\Fmcg\Fmcg;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Products\Product;
use Modules\Core\Repositories\Contracts\Fmcg\FmcgProductRepositoryInterface;
use Image;

class FmcgProductRepository implements FmcgProductRepositoryInterface
{

    /**
     * @var
     */
    public $Error;


    public function all()
    {
        return Product::with('company', 'brand')
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function allPaginate()
    {
        return Product::with('company', 'brand')
            ->where(['is_hidden' => 0])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }

    public function allActive()
    {
        return Product::with('company', 'brand')
            ->where(['is_hidden' => 0, 'is_active' => 1])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function filter($request)
    {
        $search = trim($request->search);

        $product = Product::with('company', 'brand');

        if ($search != "") {
            $product->where(function ($wh) use ($search) {
                $wh->where('product_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('fmcg_price', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->is_active != "") {
            $product->where('is_active', '=', $request->is_active);
        }

        if ($request->company_id != "") {
            $product->where('company_id', '=', $request->company_id);
        }

        if ($request->brand_id != "") {
            $product->where('brand_id', '=', $request->brand_id);
        }

        return $product->orderBy('id', 'desc')->where(['is_hidden' => 0])->paginate(100);

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

            $product = new Product();

            $product->setTranslation('product_name', 'en', $request->en_product_name);
            $product->setTranslation('product_name', 'bn', $request->bn_product_name);

            $product->setTranslation('description', 'en', $request->description_en);
            $product->setTranslation('description', 'bn', $request->description_bn);

            $product->company_id = $request->company_id;
            $product->brand_id = $request->brand_id;
            $product->is_active = 1;


            if ($request->hasFile('images')) {

                $image = $request->file('images');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = public_path() . '/product_image/' . $filename;
                Image::make($image)->resize(150, 150)->save($location);
                $product->images = $filename;
            }

            $product->save();

            DB::commit();

            return true;

        } catch (Exception $ex) {

            DB::rollBack();

            $this->Error = $ex->getMessage();

            return false;

        }

    }

    public function update($request, $company_id, $id)
    {
        DB::begintransaction();

        try {

            //dd($request->file('images'));
            /*$imageService = new ImageService();
            $fileName = $imageService->storeImage($request,'images',asset('storage/app/public/product_image/'),['height'=>150,'weight'=>150]);*/
            $filename = null;

            $product = Product::find($id);

            $product->setTranslation('product_name', 'en', $request->en_product_name);
            $product->setTranslation('product_name', 'bn', $request->bn_product_name);

            $product->setTranslation('description', 'en', $request->description_en);
            $product->setTranslation('description', 'bn', $request->description_bn);

            $product->company_id = $request->company_id;
            $product->brand_id = $request->brand_id;

            $product->is_active = 1;

            if ($filename != "") {
                $product->images = $filename;
            }


            if ($request->hasFile('images')) {

                if (!is_null($product->images)) {

                    $image_path = public_path('product_image/' . $product->images);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }

                $image = $request->file('images');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = public_path() . '/product_image/' . $filename;
                Image::make($image)->resize(150, 150)->save($location);

                $product->images = $filename;
            }

            $product->update();

            DB::commit();

            return true;

        } catch (Exception $ex) {

            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function activation($status, $id)
    {
        try {

            $obj = Product::find($id);

            if ($status === "yes") {
                $obj->is_active = 1;
            } else {
                $obj->is_active = 0;
            }

            $obj->update();

            return true;

        } catch (Exception $ex) {

            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $pram = Product::find($id);

            if (!is_null($pram->images)) {

                $image_path = public_path('product_image/' . $pram->images);

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }
            }


            $pram->delete();

            DB::commit();

            return true;

        } catch (Exception $ex) {

            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;

        }

    }

}





