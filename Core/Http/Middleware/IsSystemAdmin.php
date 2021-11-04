<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;

class IsSystemAdmin
{
    protected $auth;
    protected $route;
    public function __construct(Guard $auth, Route $route) {
        $this->auth = $auth;
        $this->route = $route;
    }

    public function handle($request, Closure $next)
    {
        if($this->auth->user()->is_system_admin != 1){
            return new Response('<h1 style="margin-top: 150px;color:dimgray"><center>401<br>ACCESS DENIED</center></h1>', 401);
        }
        return $next($request);
    }
}
