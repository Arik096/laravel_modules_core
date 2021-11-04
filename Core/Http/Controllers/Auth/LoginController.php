<?php

namespace Modules\Core\Http\Controllers\Auth;

use App\Models\RetailNetwork\RetailNetworkRole;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use Modules\Core\Models\Platform\LoginActivity;
use Modules\Core\Models\User\RetailUser;
use Modules\Core\Repositories\Auth\AuthRepository;

class LoginController extends Controller
{
    private $auth;

    public function __construct(AuthRepository $_auth)
    {
        $this->auth = $_auth;
    }

    /**
     * Loading Login View
     * @param $prefix
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     */
    public function loginView($prefix)
    {
        return view("core::auth.login", [
            'prefix' => $prefix
        ]);
    }


    public function loginViewSubmit(Request $request, $prefix)
    {

        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $fieldType = "email";
        } else {
            if (preg_replace('/[^a-z]/i', '', $request->email)) {
                $fieldType = "username";
            } else {
                $fieldType = "mobile";
            }
        }


        /****************************
         * if OTP verify not done then redirect otp page
         *********************************/

        //$fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = array($fieldType => $request->email, 'password' => $request->password);
        $remember = $request->remember;

        if ($this->auth->isActiveAccount($prefix, $request, $fieldType)) {

            Session::put('locale', 'en');

            $minutes = 3600 * 24 * 30;

            Cookie::queue('prefix', $prefix, $minutes);

            /***************************** start Login for General Sign Up *********************/
            $general = RetailUser::where([$fieldType => $request->email])->first();
            if (is_null($general)) {
                return redirect()->back()->with('error', getLangMessage('active_or_not_account'));
            }

            if ($general->role_id == "" && $general->flag == "") {
                if (Auth::attempt($credentials, $remember)) {
                    return redirect()->intended($prefix . '/sign-up/dashboard/');
                } else {
                    Session::flash('error', getLangMessage('username_password_invalid'));
                    return redirect()->back()->withInput($request->only('email'));
                }
            }


            $restrict = array(7, 8, 9, 10, 11);
            if (in_array($general->flag, $restrict)) {
                Session::flash('error', "Your Login Panel is Not Valid.");
                return redirect()->back()->withInput($request->only('email'));
            }

            /***************************** end Login for General Sign Up *********************/


            /***************************** Login for General Sign Up *********************/
            if ($this->auth->login($request, $prefix, $credentials, $remember, $fieldType)) {

                $agent = new Agent();
                $device_info = 'Platform: ' . $agent->platform() . ', Browser:' . $agent->browser();
                $activity = new LoginActivity();
                $activity->user_id = Auth::guard('web')->user()->id;
                $activity->device_info = $device_info;
                $activity->longitude = $request->longitude;
                $activity->latitude = $request->latitude;
                $activity->save();

                $role = RetailNetworkRole::where(['id' => Auth::guard('web')->user()->role_id])->get()->first();

                switch (Auth::guard('web')->user()->flag) {
                    case 1:
                        Session::put("discount_role", "admin");
                        break;
                    case 2:
                        Session::put("discount_role", "se");
                        break;

                    case 3:
                        Session::put("discount_role", "fo");
                        break;
                    case 4:
                        Session::put("discount_role", "wmm");
                        break;
                    case 5:
                        Session::put("discount_role", "offgrid");
                        break;

                    case 6:
                        Session::put("discount_role", "help_seeker");
                        break;
                    default:
                        Session::put("discount_role", "volunteer");
                        break;
                }
                Session::put('role_name', strtolower($role->name));

                return redirect()->intended('account/' . $prefix . '/dashboard');

            } else {
                //dd("invalid");
                //Session::flash('error',getLangMessage('username_password_invalid'));
                Session::flash('error', "Your username and password is invalid");
                return redirect()->back()->withInput($request->only('email'));
            }

        } else {
            Session::flash('error', getLangMessage('active_or_not_account'));
            return redirect()->back();
        }
    }


    public function logout(Request $request, $prefix)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        return redirect('auth/sujog/login');
    }


}
