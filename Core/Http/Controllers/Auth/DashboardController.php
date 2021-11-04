<?php

namespace Modules\Core\Http\Controllers\Auth;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
   public function dashBoard($prefix){
       return view('core::dashboard.dashboard',[
            'prefix'=>$prefix
       ]);
   }




}
