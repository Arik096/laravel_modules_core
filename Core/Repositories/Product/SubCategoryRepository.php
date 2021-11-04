<?php


namespace Modules\Core\Repositories\Product;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Management\ChannelSubcategory;
use Modules\Core\Models\Management\SubcategoryTypeOfService;
use Modules\Core\Models\Products\Subcategory;
use Modules\Core\Repositories\Contracts\SubCategoryRepositoryInterface;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{

    public $Error;

    public function export($prefix)
    {
        // TODO: Implement export() method.
    }

    public function all($prefix)
    {
        return Subcategory::withoutTrashed()->with(['category', 'ownership', 'channels', 'typeofservice'])->paginate(100);
    }

    public function allByActive($prefix)
    {
        return Subcategory::withoutTrashed()->with(['category', 'ownership', 'channels', 'typeofservice'])->get();
    }

    public function find($prefix, $id)
    {
        // TODO: Implement find() method.
    }

    public function store($request, $prefix)
    {

        DB::beginTransaction();

        try {

            $obj = new Subcategory();

            $obj->setTranslation('name', 'en', $request->name);
            $obj->setTranslation('name', 'bn', $request->bn_name);
            $obj->comments = $request->comments;
            $obj->main_category_id = $request->main_category_id;
            $obj->ownership_of_service_id = $request->ownership_of_service_id;
            $obj->is_active = 1;
            //channels
            $obj->save();

            foreach ($request->channels as $channel) {

                $cha = new ChannelSubcategory();
                $cha->sub_category_id = $obj->id;
                $cha->channel_id = $channel;
                $cha->save();
            }


            if (!is_null($request->type_of_service_id)) {

                foreach ($request->type_of_service_id as $type) {

                    $cha = new SubcategoryTypeOfService();
                    $cha->sub_category_id = $obj->id;
                    $cha->type_of_services_id = $type;
                    $cha->save();
                }
            }


            DB::commit();

            return true;

        } catch (\Exception $ex) {

            DB::rollBack();
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function update($request, $prefix, $id)
    {

        DB::beginTransaction();

        try {

            $obj = Subcategory::find($id);
            $obj->setTranslation('name', 'en', $request->name);
            $obj->setTranslation('name', 'bn', $request->bn_name);
            $obj->comments = $request->comments;
            $obj->main_category_id = $request->main_category_id;
            $obj->ownership_of_service_id = $request->ownership_of_service_id;
            $obj->is_active = 1;
            $obj->update();


            ChannelSubcategory::where(['sub_category_id' => $id])->delete();

            foreach ($request->channels as $channel) {

                $cha = new ChannelSubcategory();
                $cha->sub_category_id = $id;
                $cha->channel_id = $channel;
                $cha->save();
            }


            SubcategoryTypeOfService::where(['sub_category_id' => $id])->delete();

            if (!is_null($request->type_of_service_id)) {

                foreach ($request->type_of_service_id as $type) {
                    $cha = new SubcategoryTypeOfService();
                    $cha->sub_category_id = $id;
                    $cha->type_of_services_id = $type;
                    $cha->save();
                }
            }


            DB::commit();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;
        }
    }

    public function delete($prefix, $id)
    {
        try {

            $pram = Subcategory::find($id);

            $pram->delete();

            return true;

        } catch (\Exception $ex) {

            $this->Error = $ex->getMessage();

            return false;
        }

    }

    public function filter($request, $prefix)
    {
        $data = Subcategory::withoutTrashed()->with(['category', 'ownership', 'channels', 'typeofservice']);

        if ($request->search != "") {
            $data->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->is_active != "") {
            $data->where(['is_active' => $request->is_active]);
        }

        return $data->orderBy('id', 'desc')->paginate(100);
    }

    public function activation($prefix, $status, $id)
    {
        try {

            $obj = Subcategory::find($id);

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

    public function create($prefix)
    {
        // TODO: Implement create() method.
    }
}
