<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Modules\Core\Services\PermissionAccessService;

class PermissionMiddleware
{
    protected $auth;
    protected $route;
    public function __construct(Guard $auth, Route $route) {
        $this->auth = $auth;
        $this->route = $route;
    }

    public function handle($request, Closure $next)
    {



        if(!PermissionAccessService::canAccess($this->route->getActionName(), $this->auth->user())){

           // dd($this->route->getActionName());

            return new Response('<h1 style="margin-top: 150px;color:dimgray"><center>401<br>ACCESS DENIED</center></h1>', 401);
        }
        return $next($request);
    }


}
