<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('core')->group(function () {
    Route::get('/', 'CoreController@index');
});


/**
 * this route use for login and logout in account
 */
Route::prefix('auth/{prefix}')->group(function () {
    Route::get('login', [\Modules\Core\Http\Controllers\Auth\LoginController::class, 'loginView'])->name('core.login.view');
    Route::post('login', [\Modules\Core\Http\Controllers\Auth\LoginController::class, 'loginViewSubmit'])->name('core.login.submit');
    Route::get('logout', [\Modules\Core\Http\Controllers\Auth\LoginController::class, 'logout']);
});


Route::prefix('account/{prefix}')->group(function () {
    Route::get('dashboard', [\Modules\Core\Http\Controllers\Auth\DashboardController::class, 'dashBoard'])->name('core.dashboard');


    //for System Admin
    Route::middleware(['is_system_admin'])->group(function () {

        //Module
        Route::resource('settings/modules', '\Modules\Core\Http\Controllers\Auth\ModuleController', ['as' => 'core']);
        Route::get('settings/modules-filter', ['\Modules\Core\Http\Controllers\Auth\ModuleController', 'filter'])->name('core.modules.filter');
        Route::get('settings/modules/status/{id}', ['\Modules\Core\Http\Controllers\Auth\ModuleController', 'changeStatus'])->name('core.module.changeStatus');

        //Submodule
        Route::resource('settings/components', '\Modules\Core\Http\Controllers\Auth\SubModuleController', ['as' => 'core']);
        Route::get('settings/components-filter', ['\Modules\Core\Http\Controllers\Auth\SubModuleController', 'filter'])->name('core.components.filter');
        Route::get('settings/components/status/{id}', ['\Modules\Core\Http\Controllers\Auth\SubModuleController', 'changeStatus'])->name('core.components.changeStatus');


        //Permission
        Route::resource('settings/permissions', '\Modules\Core\Http\Controllers\Auth\PermissionController', ['as' => 'core']);
        Route::get('settings/permissions-filter', ['\Modules\Core\Http\Controllers\Auth\PermissionController', 'filter'])->name('core.permissions.filter');
        //Route::get('settings/permissions/status/{id}',['\Modules\Core\Http\Controllers\Auth\PermissionController','changeStatus'])->name('core.permissions.changeStatus');


        //Role
        Route::resource('settings/roles', '\Modules\Core\Http\Controllers\Auth\RoleController', ['as' => 'core']);
        Route::get('settings/roles-filter', ['\Modules\Core\Http\Controllers\Auth\RoleController', 'filter'])->name('core.roles.filter');
        // Route::get('settings/roles/status/{id}',['\Modules\Core\Http\Controllers\Auth\RoleController','changeStatus'])->name('core.roles.changeStatus');


        //Submodule
        Route::resource('settings/users', '\Modules\Core\Http\Controllers\Auth\UserController', ['as' => 'core']);
        Route::get('settings/users-filter', ['\Modules\Core\Http\Controllers\Auth\UserController', 'filter'])->name('core.users.filter');
        Route::get('settings/users/status/{id}', ['\Modules\Core\Http\Controllers\Auth\UserController', 'changeStatus'])->name('core.users.changeStatus');

        Route::get('settings/users/module-permission/{user_id}', ['\Modules\Core\Http\Controllers\Auth\UserController', 'userModulePermission']);
        Route::post('settings/users/module-permission/{user_id}', ['\Modules\Core\Http\Controllers\Auth\UserController', 'userModulePermissionSubmit'])->name('core.users.changePermission');

    });


});
