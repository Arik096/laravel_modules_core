<?php


namespace Modules\Core\Repositories\Auth;


use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Modules\Core\Models\Auth\Submodule;
use Modules\Core\Repositories\Contracts\Auth\SubModuleInterface;

class SubModuleRepository implements SubModuleInterface
{

    public $Errors;

    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll()
    {
        return Submodule::with('module')->paginate(50);
    }


    /**
     * Return All Shop data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllActive()
    {
        return Submodule::with('module')
            ->where(['is_active' => 1])
            ->paginate(50);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveWithoutPaginate()
    {
        return Submodule::with('module')->where(['is_active' => 1])->get();
    }


    /**
     * when send col name and value
     * @param $key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|Shop
     */
    //$arry = ['id'=>2];
    public function findBy($key)
    {
        $shops = Submodule::with('module')
            ->where(['is_active' => 1]);
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
                $location = public_path('uploads/submodule-icon/' . $filename);
                Image::make($image)->resize(50, 50)->save($location);
            }



            $filename1 = null;
            if ($request->hasFile('hover_images')) {

                $image = $request->file('hover_images');
                $filename1 = 'hover_'.time() . '.' . $image->getClientOriginalExtension();
                $location = public_path('uploads/submodule-icon/' . $filename1);
                Image::make($image)->resize(50, 50)->save($location);
            }

            $obj = new Submodule();
            $obj->title = $request->title;
            $obj->nick_name = $request->nick_name;
            $obj->action = $request->action;
            $obj->is_active = 1;
            $obj->icons = $request->icons;
            $obj->module_id = $request->module_id;
            $obj->comments = $request->comments;
            $obj->upload_icon = $filename;
            $obj->icon_hover = $filename1;
            $obj->save();
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
        $data = Submodule::with('module');
        if ($request->search != "") {
            $data->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->module_id != "") {
            $data->where(['module_id' => $request->module_id]);
        }
        return $data->paginate(50);
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
                $location = public_path('uploads/submodule-icon/' . $filename);
                Image::make($image)->resize(50, 50)->save($location);
            }


            $filename1 = null;
            if ($request->hasFile('hover_images')) {
                $image1 = $request->file('hover_images');
                $filename1 = 'hover_'.time() . '.' . $image1->getClientOriginalExtension();
                $location1 = public_path('uploads/submodule-icon/' . $filename1);
                Image::make($image1)->resize(50, 50)->save($location1);
            }


            $obj = Submodule::find($id);
            $obj->title = $request->title;
            $obj->nick_name = $request->nick_name;
            $obj->action = $request->action;
            $obj->is_active = 1;
            $obj->icons = $request->icons;
            $obj->module_id = $request->module_id;
            $obj->comments = $request->comments;
            $obj->upload_icon = $filename;

            if ($filename != "") {
                $obj->upload_icon = $filename;
            }

            if ($filename1 != "") {
                $obj->icon_hover = $filename1;
            }

            $obj->update();
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
            $data = Submodule::find($id);
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
            $shop = Submodule::find($id);
            $shop->delete();
            return true;
        } catch (\Exception $exception) {
            $this->Errors = $exception->getMessage();
            return false;
        }
    }

    public function signUpComponents()
    {
        $sign_up_module_id = 18;

        return Submodule::with('module')->where(['module_id' => $sign_up_module_id, 'is_active' => 1])->get();
    }
}
