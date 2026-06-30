<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomStatusController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionRoomReservationController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['auth', 'checkRole:Super']], function () {
    Route::resource('user', UserController::class);
});

Route::group(['middleware' => ['auth', 'checkRole:Super,Admin']], function () {
    Route::post('/room/{room}/image/upload', [ImageController::class, 'store'])->name('image.store');
    Route::delete('/image/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

    Route::name('transaction.reservation.')->group(function () {
        Route::get('/createIdentity', [TransactionRoomReservationController::class, 'createIdentity'])->name('createIdentity');
        Route::get('/pickFromCustomer', [TransactionRoomReservationController::class, 'pickFromCustomer'])->name('pickFromCustomer');
        Route::post('/storeCustomer', [TransactionRoomReservationController::class, 'storeCustomer'])->name('storeCustomer');
        Route::get('/{customer}/viewCountPerson', [TransactionRoomReservationController::class, 'viewCountPerson'])->name('viewCountPerson');
        Route::get('/{customer}/chooseRoom', [TransactionRoomReservationController::class, 'chooseRoom'])->name('chooseRoom');
        Route::get('/{customer}/{room}/{from}/{to}/confirmation', [TransactionRoomReservationController::class, 'confirmation'])->name('confirmation');
        Route::post('/{customer}/{room}/payDownPayment', [TransactionRoomReservationController::class, 'payDownPayment'])->name('payDownPayment');
    });

    Route::resource('customer', CustomerController::class);
    Route::resource('type', TypeController::class);
    Route::resource('room', RoomController::class);
    Route::resource('roomstatus', RoomStatusController::class);
    Route::resource('transaction', TransactionController::class);
    Route::resource('facility', FacilityController::class);
    Route::resource('payment-account', \App\Http\Controllers\PaymentAccountController::class);
    Route::resource('role', \App\Http\Controllers\RoleController::class)->except(['show']);
    Route::post('/post/upload-image', [PostController::class, 'uploadImage'])->name('post.upload_image');
    Route::resource('post', PostController::class);
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');

    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payment.invoice');

    Route::get('/transaction/{transaction}/payment/create', [PaymentController::class, 'create'])->name('transaction.payment.create');
    Route::post('/transaction/{transaction}/payment/store', [PaymentController::class, 'store'])->name('transaction.payment.store');

    Route::get('/get-dialy-guest-chart-data', [ChartController::class, 'dailyGuestPerMonth']);
    Route::get('/get-dialy-guest/{year}/{month}/{day}', [ChartController::class, 'dailyGuest'])->name('chart.dailyGuest');
});

Route::group(['middleware' => ['auth', 'checkRole:Super,Admin,Customer']], function () {
    Route::get('/activity-log', [ActivityController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/all', [ActivityController::class, 'all'])->name('activity-log.all');
    Route::resource('user', UserController::class)->only([
        'show',
    ]);

    Route::view('/notification', 'notification.index')->name('notification.index');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('notification.markAllAsRead');

    Route::get('/notification-to/{id}', [NotificationsController::class, 'routeTo'])->name('notification.routeTo');

    Route::post('/transaction/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transaction.cancel');
    Route::post('/transaction/{transaction}/upload-receipt', [TransactionController::class, 'uploadReceipt'])->name('transaction.uploadReceipt');
});

// Login routes
Route::view('/login', 'auth.login')->name('login.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Forgot Password routes
Route::group(['middleware' => 'guest'], function () {
    Route::get('/forgot-password', fn () => view('auth.passwords.email'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    // Reset Password routes
    Route::get('/reset-password/{token}', fn (string $token) => view('auth.reset-password', ['token' => $token]))
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::get('/', [PageController::class, 'home'])->name('public.home');
Route::get('/rooms', [PageController::class, 'rooms'])->name('public.rooms');
Route::get('/rooms/{room}', [PageController::class, 'room'])->name('public.room');
Route::post('/subscribe', [PageController::class, 'subscribe'])->name('public.subscribe');

Route::get('/checkout/{room}', [CheckoutController::class, 'index'])->name('public.checkout');
Route::post('/checkout/{room}', [CheckoutController::class, 'process'])->name('public.checkout.process');

Route::get('/blog', [PostController::class, 'publicIndex'])->name('public.blog.index');
Route::get('/blog/{slug}', [PostController::class, 'publicShow'])->name('public.blog.show');
