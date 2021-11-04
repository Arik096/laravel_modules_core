<?php


namespace Modules\Core\Repositories\Auth;


use App\Models\Admin\DistributionPlatform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\Platform\Platform;
use Modules\Core\Models\User\RetailUser;

class AuthRepository
{

    /**
     * @param $prefix
     * @return bool
     */
    //for loginForm by prefix
    public function loginForm($prefix)
    {
        $have = Platform::where(['platform_prefix' => $prefix])->first();
        if (empty($have)) {
            return false;
        } else {
            $minutes = 3600 * 24 * 7;
            Cookie::queue('prefix', $prefix, $minutes);
            return true;
        }
    }



    /**
     * @param $prefix
     * @return mixed
     */
    //get plaform information by prefix
    public function getPlateformInfo($prefix)
    {
        return Platform::where(['platform_prefix' => $prefix])->firstOrFail();
    }


    /**
     * @param $request
     * @param $prefix
     * @param $credentials
     * @param $remember
     * @param $fieldType
     * @return bool
     */
    public function login($request, $prefix, $credentials, $remember, $fieldType)
    {
        if (Auth::attempt($credentials, $remember)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $prefix
     * @param $request
     * @param $fieldType
     * @return bool
     */
    public function isActiveAccount($prefix, $request, $fieldType)
    {
        $customCredential = [$fieldType => $request->email, 'is_active' => 1];
        $pram = RetailUser::where($customCredential)->first();
        if (!is_null($pram)) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * @param $request
     * @param $prefix
     * @param $pin
     * @return bool
     */
    public function validPhoneNumber($request, $prefix, $pin)
    {
        $users = RetailUser::where(['mobile' => $request->phone_number])->get()->first();
        if (!is_null($users)) {
            RetailUser::where(['mobile' => $request->phone_number])->update(['pin' => $pin]);
            return true;
        }
        return false;
    }


    /**
     * @param $request
     * @param $prefix
     * @return bool
     */
    public function isPinNumber($request, $prefix)
    {
        $users = RetailUser::where(['pin' => $request->pin_number])->get()->first();
        if (!is_null($users)) {
            return true;
        }
        return false;
    }


    /**
     * @param $prefix
     * @param $request
     * @param $pin_number
     * @return bool
     * @throws \Exception
     */
    public function resetPassword($prefix, $request, $pin_number)
    {
        try {
            RetailUser::where(['pin' => $pin_number])->update(['password' => Hash::make($request->password)]);
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
        return false;
    }


    /**
     * @param $prefix
     * @param $phone_number
     * @param $rand
     */
    public function sendAnotherSms($prefix, $phone_number, $rand)
    {
        sendSms($phone_number, $rand);
        RetailUser::where(['mobile' => $phone_number])->update(['pin' => $rand]);
    }


}
