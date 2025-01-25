<?php

use App\Http\Controllers\BagController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OtherRegister;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('User.login');
});
Route::get("/test",function(){
    return view("Layout/test");
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(Dashboard::class)->group(function(){
        Route::get('/home',"home")->name("home");
        Route::get("dashboard/loop-status","loopStatus")->name("dashboard.loop.status");
    });
    Route::controller(MasterController::class)->group(function(){
        Route::get("master/fare/list","fareList")->name("master.fare.list");
        Route::post("master/fare/add","addFare")->name("master.fare.add");
        Route::get("master/fare/dtl/{id?}","fareDtl")->name("master.fare.dtl");
        Route::post("master/fare/deactivate/{id}","deactivateFare")->name("master.fare.deactivate");

        Route::get("master/stereo/list","stereoList")->name("master.stereo.list");
        Route::post("master/stereo/add","addStereo")->name("master.stereo.add");
        Route::get("master/stereo/dtl/{id?}","stereoDtl")->name("master.stereo.dtl");
        Route::post("master/stereo/deactivate/{id}","deactivateStereo")->name("master.stereo.deactivate");

        Route::get("master/grade/list","gradeList")->name("master.grade.list");
        Route::post("master/grade/add","addGrade")->name("master.grade.add");
        Route::get("master/grade/dtl/{id?}","gradeDtl")->name("master.grade.dtl");
        Route::post("master/grade/deactivate/{id}","deactivateGrade")->name("master.grade.deactivate");

        Route::get("master/roll-quality/list","rollQualityList")->name("master.roll.quality.list");
        Route::post("master/roll-quality/add","addRollQuality")->name("master.roll.quality.add");
        Route::get("master/roll-quality/dtl/{id?}","rollQualityDtl")->name("master.roll.quality.dtl");

        Route::get("master/grade-map/list","gradeListMap")->name("master.grade.map.list");
        Route::get("master/quality-vender-map/list/{venderId}","rollVenderListMap")->name("master.quality.vender.map.list");

        Route::get("master/rate-type/list","rateTypeList")->name("master.rate.type.list");
        Route::post("master/rate-type/add","addRateType")->name("master.rate.type.add");
        Route::get("master/rate-type/dtl/{id?}","rateTypeDtl")->name("master.rate.type.dtl");
        Route::post("master/rate-type/deactivate/{id}","deactivateRateType")->name("master.rate.type.deactivate");

        Route::get("master/user-type/list","userTypeList")->name("master.user.type.list");
        Route::post("master/user-type/add","addUserType")->name("master.user.type.add");
        Route::get("master/user-type/dtl/{id?}","userTypeDtl")->name("master.user.type.dtl");

        Route::get("master/loop-stock/list","loopStockList")->name("master.loop.stock.list");
        Route::post("master/loop-stock/add-edit","loopStockAddEdit")->name("master.loop.stock.add.edit");
        Route::get("master/loop-stock/dtl/{id?}","loopStockDtl")->name("master.loop.stock.dtl");
        Route::post("master/loop-stock/deactivateLoopStock/{id}","deactivateLoopStock")->name("master.loop-stock.deactivate");
        Route::post("master/loop-stock-booking/test","loopStockTestBooking")->name("master.loop.stock.booking.test");

        Route::get("master/broker/list","brokerList")->name("master.broker.list");
        Route::post("master/broker/add-edit","brokerAddEdit")->name("master.broker.add.edit");
        Route::get("master/broker/dtl/{id?}","brokerDtl")->name("master.broker.dtl");
        Route::post("master/broker/deactivateLoopStock/{id}","activeDeactivateBroker")->name("master.broker.deactivate");

        Route::get("master/state/list","stateList")->name("master.state.list");
        Route::post("master/state/add-edit","stateAddEdit")->name("master.state.add.edit");
        Route::get("master/state/dtl/{id?}","stateDtl")->name("master.state.dtl");
        Route::post("master/state/deactivateLoopStock/{id}","activeDeactivateState")->name("master.state.deactivate");
        Route::match(["get","post"],"master/state-city/import","importStateAndCity");


        Route::get("master/city/list","cityList")->name("master.city.list");
        Route::post("master/city/add-edit","cityAddEdit")->name("master.city.add.edit");
        Route::get("master/city/dtl/{id?}","cityDtl")->name("master.city.dtl");
        Route::post("master/city/deactivateLoopStock/{id}","activeDeactivateCity")->name("master.city.deactivate");
        Route::get("master/city-by-state/{id}","getCityListByState")->name("master.city.by.state");

        
    });

    Route::controller(MenuController::class)->group(function(){
        Route::get("/menu-list","getMenuList")->name("menu-list");
        Route::get("/menu/update/user-list","updateMenuUserTypeList")->name("menu.update.user.list");
        Route::post("/menu/update/user-type","updateMenuByUserType")->name("menu.update.user.type");
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
        Route::get("user/list","userList")->name("user.list");
        Route::get("user/{id}","userDtl")->name("user.id");
        Route::post("user/add","createUser")->name("user.add");
        Route::post("user/edit","editUser")->name("user.edit");
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
        Route::get("client/delete/{id}","deactivate")->name("client.delete");
    });

    Route::controller(SectorController::class)->group(function(){
        Route::get("sector/list","sectorList")->name("sector.list");
        Route::post("sector/add-edit","addEditSector")->name("sector.add.edit");
        Route::get("sector/dtl/{id}","getSectorDtl")->name("sector.dtl");
        Route::post("sector/deactivate/{id}","deactivate")->name("sector.deactivate");
    });

    Route::controller(BagController::class)->group(function(){
        Route::get("bag/list","bagList")->name("bag.list");
        Route::post("bag/add","addBag")->name("bag.add");
        Route::get("bag/edit/{id}","getBagDtl")->name("bag.edit");
    });

    Route::controller(ColorController::class)->group(function(){
        Route::get("roll/color/list","rollColorList")->name("roll.color.list");
        Route::post("roll/color/add","addRollColor")->name("roll.color.add");
        Route::get("roll/color/edit/{id}","rollColorDtl")->name("roll.color.edit");
        Route::get("roll/color/delete/{id}","deactivateRollColor")->name("client.color.delete");

        Route::get("color/list","colorList")->name("color.list");
        Route::post("color/add","addColor")->name("color.add");
        Route::get("color/edit/{id}","colorDtl")->name("color.edit");
        Route::get("color/delete/{id}","deactivateColor")->name("color.delete");

    });

    Route::controller(TransporterController::class)->group(function(){
        Route::get("transporter/auto/list","autoList")->name("transporter.auto.list");
        Route::post("transporter/auto/add","addAuto")->name("transporter.auto.add");
        Route::get("transporter/auto/dtl/{id}","autoDtl")->name("transporter.auto.dtl");
        Route::post("transporter/auto/deactivate/{id}","deactivateAuto")->name("transporter.auto.deactivate");

        Route::get("transporter/transporter/list","transporterList")->name("transporter.transporter.list");
        Route::post("transporter/transporter/add","addTransporter")->name("transporter.transporter.add");
        Route::get("transporter/transporter/dtl/{id}","transporterDtl")->name("transporter.transporter.dtl");
        Route::post("transporter/transporter/deactivate/{id}","deactivateTransporter")->name("transporter.transporter.deactivate");
    });

    Route::controller(RollController::class)->group(function(){
        Route::get("roll/order/punches","orderPunches")->name("order.punches");
        Route::post("roll/order/punches/save","orderPunchesSave")->name("order.punches.save");        
        Route::post("roll/order/edit","orderPunchesEdit")->name("order.punches.edit");
        Route::post("roll/client/old/order","oldOrderOfClient")->name("client.old.order");
        Route::post("roll/client/order/suggestion","orderSuggestionClient")->name("client.order.suggestion");
        Route::post("roll/order/re-book-suggestion","rebookOrder")->name("order.rebook");
        Route::post("roll/order/disabused","disbursedOrder")->name("order.disabused");
        Route::post("roll/order/deactivate","deactivateOrder")->name("order.deactivate");
        Route::get("roll/order/disabused/register","disburseRegister")->name("order.disabused.register");
        Route::post("roll/order/test","rollTestByOrder")->name("roll.order.test");
        Route::post("roll/delete/transit","deleteTransit")->name("roll.delete.transit");
        Route::post("roll/order/to/book","bookRollToOrder")->name("roll.order.to.book");
        Route::post("roll/order/remove/booking","removeBookedRoll")->name("roll.order.remove.booking");

        Route::match(["get","post"],"roll/order/book","bookedOrder")->name("order.book");
        Route::match(["get","post"],"roll/order/book-delivered","bookedOrderDelivered")->name("order.book.delivered");
        Route::match(["get","post"],"roll/order/unbook","unBookedOrder")->name("order.unbook");

        Route::get("roll/transit/list","transitList")->name("roll.transit");
        Route::get("roll/transit/dtl/{vendor_id?}","transitDtl")->name("roll.transit.dtl");
        Route::post("roll/transit/add","addTransitRoll")->name("roll.transit.add");
        Route::post("roll/transit/book","rollTransitBook")->name("roll.transit.book");
        Route::post("roll/transit/roll/stock","rollTransitRollStock")->name("roll.transit.rll.stock");

        Route::get("roll/list/{flag?}","rollList")->name("roll.list");
        Route::post("roll/add","addRoll")->name("roll.add");
        Route::post("roll/import","importRoll")->name("roll.import");
        Route::get('roll/import-template', 'downloadCsvTemplate')->name('download.roll.import.template')->withoutMiddleware("auth:sanctum");
        Route::post("roll/book","rollBook")->name("roll.book");
        Route::get("roll/dtl/{id}","rollDtl")->name("roll.dtl");
        Route::get("roll/dtl-full/{id}","rollDtlFull")->name("roll.dtl.full");
        Route::post("roll/schedule/printing","rollPrintingSchedule")->name("roll.printing.schedule");
        Route::post("roll/printing/update","rollPrintingUpdate")->name("roll.printing.update");
        Route::post("roll/schedule/cutting","rollCuttingSchedule")->name("roll.cutting.schedule");
        Route::post("roll/cutting/update","rollCuttingUpdate")->name("roll.cutting.update");

        Route::get("roll/register","rollRegister")->name("roll.register");
        Route::get("roll/loop-register","loopRegister")->name("roll.loop.register");
        Route::get("roll/register/printing/{machineId?}","rollRegisterPrinting")->name("roll.register.printing");
        Route::get("roll/register/cutting/{machineId?}","rollRegisterCutting")->name("roll.register.cutting");
        Route::get("roll/schedule/{flag?}","rollSchedule")->name("roll.schedule");
        Route::post("roll/schedule-set/{flag}","rollScheduleSet")->name("roll.schedule.set");
        Route::get("roll/production/printing/{machineId?}","rollProduction")->name("roll.production.printing");
        Route::get("roll/production/cutting/{machineId?}","rollProductionCutting")->name("roll.production.cutting");

        Route::post("roll/search/printing","rollSearchPrinting")->name("roll.search.printing");
        Route::post("roll/search/cutting","rollSearchCutting")->name("roll.search.cutting");
        Route::post("roll/update/edit","rollUpdate")->name("roll.update.edit");
    });

    Route::controller(PackingController::class)->group(function(){
        Route::get("packing/entry","packingEnter")->name("packing.entry");
        Route::get("packing/wip","WIPVerification")->name("packing.wip");
        Route::post("packing/entry-wip/add","packingEnterWipAdd")->name("packing.entry.wip.add");
        Route::post("packing/entry/search","searchRoll")->name("packing.entry.search");
        Route::post("packing/entry/add","packingEnterAdd")->name("packing.entry.add");
        Route::match(["get","post"],"packing/stock","bagStock")->name("packing.stock");
        Route::match(["get","post"],"packing/godown","bagGodown")->name("packing.godown");
        Route::get("packing/inTransport","addBagInTransport")->name("packing.inTransport");
        Route::get("packing/transport/for/{flag}","bagTransport")->name("packing.transport.for");
        Route::get("packing/transport/stock","bagStockToGodown")->name("packing.transport.stock");
        Route::post("packing/transport/search","searchPackingForTransport")->name("packing.transport.search");
        Route::post("packing/transport/save","transportAdd")->name("packing.transport.save");
        Route::match(["get","post"],"packing/godown/reiving","reivingGodown")->name("packing.godown.reiving");
        Route::post("packing/godown/transport","reivingTransport")->name("packing.godown.transport");
        Route::post("packing/godown/add","addInGodown")->name("packing.godown.add");
        Route::get("packing/transport/register","transportRegister")->name("packing.transport.register");
    });

    Route::controller(OtherRegister::class)->group(function(){
        Route::get("register/accept-garbage","acceptGarbage")->name("register.accept.garbage");
    });

});
