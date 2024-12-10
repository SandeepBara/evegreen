<?php

use App\Http\Controllers\BagController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get("/test",function(){
    return view("Layout/test");
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(Dashboard::class)->group(function(){
        Route::get('/home',"home")->name("home");
    });
    Route::controller(MenuController::class)->group(function(){
        Route::get("/menu-list","getMenuList")->name("menu-list");
        Route::get("/menu-submenu","getSubMenuList")->name("submenu-list");
        Route::post("/menu-add","add")->name("menu-add");
        Route::get("/menu-edit/{id}","getMenuDtl")->name("menu-edit");
        Route::post("/menu-deactivate/{id}","deactivate")->name("menu-deactivate");
    });
    Route::controller(UserController::class)->group(function(){
        Route::match(["get","post"],"user/login","login")->name("login")->withoutMiddleware("auth:sanctum");
        Route::get("user/logout","logout")->name("logout");
        Route::match(["get","post"],"user/create","createUser")->name("createUser");
        Route::get("user/haspassword/{id?}","hasPassword")->name("haspassword");
        Route::get("user/profile","profile")->name("profile");
        Route::match(["get","post"],"user/change-password","changePassword")->name("change-password");
    });

    Route::controller(VendorController::class)->group(function(){
        Route::get("vender/list","vendorList")->name("vendor.list");
        Route::post("vender/add","addVendor")->name("vendor.add");
        Route::get("vender/edit/{id}","getVenderDtl")->name("vendor.edit");
        Route::post("vender/deactivate/{id}","deactivate")->name("vendor.deactivate");
    });

    Route::controller(ClientController::class)->group(function(){
        Route::get("client/list","clientList")->name("client.list");
        Route::post("client/add","addClient")->name("client.add");
        Route::get("client/edit/{id}","getClientDtl")->name("client.edit");
    });

    Route::controller(ClientController::class)->group(function(){
        Route::get("client/list","clientList")->name("client.list");
        Route::post("client/add","addClient")->name("client.add");
        Route::get("client/edit/{id}","getClientDtl")->name("client.edit");
    });

    Route::controller(BagController::class)->group(function(){
        Route::get("bag/list","bagList")->name("bag.list");
        Route::post("bag/add","addBag")->name("bag.add");
        Route::get("bag/edit/{id}","getBagDtl")->name("bag.edit");
    });

    Route::controller(RollController::class)->group(function(){
        Route::get("roll/list/{flag?}","rollList")->name("roll.list");
        Route::post("roll/add","addRoll")->name("roll.add");
        Route::post("roll/import","importRoll")->name("roll.import");
        Route::post("roll/book","rollBook")->name("roll.book");
        Route::get("roll/dtl/{id}","rollDtl")->name("roll.dtl");
        Route::post("roll/schedule/printing","rollPrintingSchedule")->name("roll.printing.schedule");
    });

});
