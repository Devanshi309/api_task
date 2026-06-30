<?php 
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\groceryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\deliveryController;

Route::post('admin/login',[AdminAuthController::class,'login']);
Route::post('customer/login',[CustomerAuthController::class,'login']);
Route::post('customer/register',[CustomerAuthController::class,'register']);
Route::middleware(['auth:sanctum','admin'])->group(function () {


Route::post('grocery/add',[groceryController::class,'store']);
Route::post('grocery/update/{id}',[groceryController::class,'update']);
Route::delete('grocery/delete/{id}',[groceryController::class,'delete']);
Route::get('grocery/list',[groceryController::class,'list']);
Route::post('grocery/detail/{id}',[groceryController::class,'detail']);


Route::get('admin/orderlist',[OrderController::class,'orderlist']);
Route::post('admin/orderdetail/',[OrderController::class,'orderdetail']);
Route::post('admin/order/status/update/{id}',[OrderController::class,'statusupdate']);
Route::post('admin/delivery/assign/{id}',[deliveryController::class,'assigndelivery']);
Route::post('admin/delivery/daily/',[deliveryController::class,'dailydelivery']);
Route::patch('admin/delivery/status/{id}',[deliveryController::class,'updatestatus']);

Route::get('admin/dashboard/',[CartController::class,'dashboardapi']);
Route::post('admin/dashboard/filter/',[CartController::class,'filter']);

Route::get('/admin/notifications', [NotificationController::class, 'notifications']);

Route::get('/admin/daily-sales-report',[deliveryController::class,'dailyreport']);
Route::get('/admin/monthly-sales-report',[deliveryController::class,'monthreport']);
Route::get('/admin/customer-purchase-report/{id}',[deliveryController::class,'customerpurchasereport']);

});

Route::middleware(['auth:sanctum'])->group(function () {

Route::post('customer/logout',[CustomerAuthController::class,'logout']);
Route::get('customer/profile/{id}',[CustomerAuthController::class,'profile']);

Route::post('grocery/cart/add',[CartController::class,'addtocart']);
Route::post('grocery/cart/update/{id}',[CartController::class,'updatecart']);
Route::delete('grocery/cart/delete/{id}',[CartController::class,'removecart']);

Route::post('customer/place-order',[OrderController::class,'placeorder']);
Route::get('grocery/view',[CartController::class,'viewproduct']);

});




