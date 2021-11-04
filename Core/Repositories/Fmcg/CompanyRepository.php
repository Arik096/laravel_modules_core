<?php

namespace Modules\Core\Repositories\Fmcg;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\Core\Models\Fmcg\Fmcg;
use Modules\Core\Models\Products\Brand;
use Modules\Core\Repositories\Contracts\BrandRepositoryInterface;
use Modules\Core\Repositories\Contracts\Fmcg\CompanyRepositoryInterface;
use Illuminate\Support\Facades\DB;


class CompanyRepository implements CompanyRepositoryInterface
{

    /**
     * @var
     */
    public $Error;


    public function all()
    {
        return Fmcg::orderBy('id', 'desc')->get();
    }

    public function allPaginate()
    {
        return Fmcg::orderBy('id', 'desc')->paginate(100);
    }

    public function allActive()
    {
        return Fmcg::where('is_active', '1')->orderBy('id', 'desc')->get();
    }

    public function filter($request)
    {
        $search = trim($request->search);

        $data = Fmcg::orderBy('id', 'desc');

        if ($search != "") {
            $data->where('company_name', 'LIKE', '%' . $search . '%');
        }
        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }
        return $data->paginate(100);

    }

    public function find($id)
    {
        return Fmcg::orderBy('id', 'desc')->where('id', $id)->first();
    }

    public function create($request)
    {
        // TODO: Implement create() method.
    }

    public function store($request)
    {
        DB::begintransaction();

        try {

            $image = new ImageService();

            $fileName = $image->UploadImage($request, 'images', 'fmcg_logo/', ['height' => 50, 'weight' => 150]);

            $fmcg = new Fmcg();
            $fmcg->setTranslation('company_name', 'en', $request->company_name_en);
            $fmcg->setTranslation('company_name', 'bn', $request->company_name_bn);
            $fmcg->slug = str_replace(" ", "-", strtolower($request->company_name_en));
            $fmcg->setTranslation('description', 'en', $request->description_en);
            $fmcg->setTranslation('description', 'bn', $request->description_bn);
            $fmcg->is_active = 1;
            $fmcg->images = $fileName;
            $fmcg->save();

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

            $image = new ImageService();

            $fileName = $image->UploadImage($request, 'images', 'fmcg_logo/', ['height' => 50, 'weight' => 150]);

            $fmcg = Fmcg::find($id);
            $fmcg->setTranslation('company_name', 'en', $request->company_name_en);
            $fmcg->setTranslation('company_name', 'bn', $request->company_name_bn);
            $fmcg->slug = str_replace(" ", "-", strtolower($request->company_name_en));
            $fmcg->setTranslation('description', 'en', $request->description_en);
            $fmcg->setTranslation('description', 'bn', $request->description_bn);
            $fmcg->is_active = 1;

            if ($fileName != "" || $fileName != null) {
                $fmcg->images = $fileName;
            }
            $fmcg->update();

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

            $obj = Fmcg::find($id);

            if ($status === "yes") {
                $obj->is_active = 1;
            } else {
                $obj->is_active = 0;
            }

            $obj->update();

            clearCache('fmcg_account');

            Cache::rememberForEver('fmcg_account', function () {
                return Fmcg::all();
            });

            return true;

        } catch (Exception $ex) {

            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function delete($id)
    {
        try {

            $pram = Fmcg::find($id);

            $image_path = public_path('fmcg_logo/' . $pram->images);

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            $pram->delete();

            return true;

        } catch (Exception $ex) {

            $this->Error = $ex->getMessage();
            return false;

        }

    }

}





