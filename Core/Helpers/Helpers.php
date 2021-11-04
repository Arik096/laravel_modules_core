<?php


namespace Modules\Core\Helpers;


use Illuminate\Support\Facades\Facade;

class Helpers extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'HelpersFacade';
    }

}
