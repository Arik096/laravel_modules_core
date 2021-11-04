<?php


namespace Modules\Core\Repositories\User;


use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\User\RetailUser;
use Modules\Core\Repositories\Contracts\RetailUserRepositoryInterface;

class RetailUserRepository implements RetailUserRepositoryInterface
{
    public $Error;


    public function allForPublic($prefix,$flag)
    {
        return $users = RetailUser::withoutTrashed()
            ->where(['flag'=>$flag,'is_active'=>1])
            ->get();
    }



    public function all($prefix)
    {
        return $users = DB::table($prefix . '_users')
            ->join($prefix . '_roles', $prefix . '_users.role_id', '=', $prefix . '_roles.id')
            ->select($prefix . '_users.*', $prefix . '_roles.name as role_name')
            ->paginate(100);
    }

    public function find($prefix, $id)
    {
        $obj = new RetailUser();
        $obj->setTable($prefix . '_users');
        //return $obj->with('role')->findOrFail($id);
        return DB::table($prefix . '_users')
            ->join($prefix . '_roles', $prefix . '_users.role_id', '=', $prefix . '_roles.id')
            ->select($prefix . '_users.*', $prefix . '_roles.name as role_name')->
            where([$prefix . '_users.id' => $id])->paginate(100);
    }

    public function store($request, $prefix)
    {
        try {

            /*****************************************************
             * Genarate username
             *******************************************************/
            //last Id
            $last = RetailUser::orderBy('id', 'desc')->paginate(1);
            $last_id = 1;
            if (count($last) > 0) {
                $last_id = $last->first()->id;
                $last_id++;
            }
            $username = 'User' . $last_id;
            /*****************************************************
             * Genarate username
             *******************************************************/

            $ex = explode(":", $request->role_id);
            $obj = new RetailUser();
            $obj->setTable($prefix . '_users');
            $obj->name = $request->name;
            $obj->username = $username;
            $obj->email = $request->email;
            $obj->password = Hash::make($request->password);
            $obj->mobile = $request->mobile;
            $obj->division_id = $request->division_id;
            $obj->district_id = $request->district_id;
            $obj->upazila_id = $request->upazila_id;
            $obj->union_id = $request->union_id;
            $obj->village_id = $request->village_id;
            $obj->registration_type = "registration_by_retail";
            $obj->role_id = $ex[0];
            $obj->flag = $ex[1];
            $obj->is_active = 1;
            $obj->save();
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }
    }

    public function update($request, $prefix, $id)
    {

        try {
            $ex = explode(":", $request->role_id);
            $pram = new RetailUser();
            $pram->setTable($prefix . '_users');
            $obj = $pram->find($id);
            $obj->name = $request->name;
            $obj->email = $request->email;
            $obj->password = Hash::make($request->password);
            $obj->mobile = $request->mobile;
            $obj->division_id = $request->division_id;
            $obj->district_id = $request->district_id;
            $obj->upazila_id = $request->upazila_id;
            $obj->union_id = $request->union_id;
            $obj->village_id = $request->village_id;
            $obj->is_active = 1;
            $obj->role_id = $ex[0];
            $obj->flag = $ex[1];
            $obj->update();
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }


    }


    public function isActive($prefix, $status, $id)
    {
        try {
            $obj = new RetailUser();
            $obj->setTable($prefix . '_users');
            $data = $obj->find($id);
            if ($status === "yes") {
                $data->is_active = 1;
            } else {
                $data->is_active = 0;
            }
            $data->update();
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }

    }


    public function search($request, $prefix)
    {

        //dd($request->search);

        $search = trim($request->search);

        $users = DB::table($prefix . '_users')
            ->select($prefix . '_users.*', $prefix . '_roles.name as role_name')
            ->join($prefix . '_roles', $prefix . '_users.role_id', '=', $prefix . '_roles.id');

        if ($search != "") {

            $users->where($prefix . '_users.name', 'LIKE', '%' . $search . '%')
                ->orWhere($prefix . '_users.email', 'LIKE', '%' . $search . '%')
                ->orWhere($prefix . '_users.mobile', 'LIKE', '%' . $search . '%');
        }

        if ($request->role_id != "") {
            $users->where('role_id', $request->role_id);
        }

        if ($request->activation != "") {
            $users->where($prefix . '_users.is_active', $request->activation);
        }

        return $users->paginate(100);

    }


    public function delete($prefix, $id)
    {
        try {
            $obj = new RetailUser();
            $obj->setTable($prefix . '_users');
            $pram = $obj->find($id);
            $pram->delete();
            return true;
        } catch (\Exception $ex) {
            $this->Error = $ex->getMessage();
            return false;
        }

    }


}
