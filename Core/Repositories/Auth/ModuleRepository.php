<?php


namespace Modules\Core\Repositories\Auth;


use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Modules\Core\Models\Auth\Module;
use Modules\Core\Repositories\Contracts\Auth\ModuleInterface;

class ModuleRepository implements ModuleInterface
{
    public $Errors;

    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll()
    {
        return Module::paginate(50);
    }


    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllActive()
    {
        return Module::with('permissions')->where(['is_active' => 1])->paginate(50);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveWithoutPaginate()
    {
        return Module::with('permissions')->where(['is_active' => 1])->get();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithModuleId($moduleIds)
    {
        return Module::with('permissions')
            ->where(['is_active' => 1])
            ->orWhereIn('id', $moduleIds)
            ->get();
    }


    /**
     * when send col name and value
     * @param $key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|Shop
     */
    //$arry = ['id'=>2];
    public function findBy($key)
    {
        $shops = Module::where(['is_active' => 1]);
        foreach ($key as $col => $v) {
            $shops->where($col, $v);
        }
        return $shops->firstOrFail();
    }


    /**
     * Insert Shop Table Data
     * @param $request
     * @return bool
     */
    public function store($request)
    {
        try {

            $filename = null;
            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = public_path('uploads/module-icon/' . $filename);
                Image::make($image)->resize(50, 50)->save($location);
            }

            $module = new Module();
            $module->title = $request->title;
            $module->action = $request->action;
            $module->is_active = 1;
            $module->icons = $request->icons;
            $module->comments = $request->comments;
            $module->upload_icon = $filename;

            $module->save();
            return true;
        } catch (\Exception $exception) {
            $this->Errors = $exception->getMessage();
            return false;
        }

    }


    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filter($request)
    {
        $data = Module::query();
        if ($request->search != "") {
            $data = Module::where('title', 'like', '%' . $request->search . '%');
        }
        return $data->paginate(20);
    }


    /**
     * Update Shop Table Data
     * @param $request
     * @param $id
     * @return bool
     */
    public function update($request, $id)
    {
        try {

            $filename = null;
            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = public_path('uploads/module-icon/' . $filename);
                Image::make($image)->resize(50, 50)->save($location);
            }

            $module = Module::find($id);
            $module->title = $request->title;
            $module->action = $request->action;
            $module->is_active = 1;
            $module->icons = $request->icons;
            $module->comments = $request->comments;

            if ($filename != "") {
                $module->upload_icon = $filename;
            }

            $module->update();
            return true;
        } catch (\Exception $exception) {
            $this->Errors = $exception->getMessage();
            return false;
        }
    }


    /**
     * Change Status Active or Deactive
     * @param $id
     * @return bool
     */
    public function changeStatus($id)
    {
        try {
            $data = Module::find($id);
            if ($data->is_active == 1) {
                $data->is_active = 0;
            } else {
                $data->is_active = 1;
            }
            $data->update();
            return true;
        } catch (\Exception $exception) {
            $this->Errors = $exception->getMessage();
            return false;
        }
    }


    /**
     * Hard Delete
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $shop = Module::find($id);
            $shop->delete();
            return true;
        } catch (\Exception $exception) {
            $this->Errors = $exception->getMessage();
            return false;
        }
    }

}
