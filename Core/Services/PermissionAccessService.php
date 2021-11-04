<?php


namespace Modules\Core\Services;


class PermissionAccessService
{
    public static function canAccess($action, $user)
    {
            $permissions = $user->getDirectPermissions()->pluck('action')->toArray();
            //dd($permissions);

            //\Modules\SignUp\Http\Controllers\SignUp\GenderEducationAssetController@destroy

            if(!in_array($action,$permissions)){
                return false;
            }
            return true;
    }

}
