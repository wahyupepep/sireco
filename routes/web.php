<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HomeController, RoleController, SettingController, UserController, SeatController, PaymentController, SaleController, MemberController, VerificationController, FdseatController, PaymentMethodController, RoomController};
use App\Http\Controllers\Auth\LoginController;

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

// Route::get('/', function () {
//     return view('auth.login');
// })->name('login');
Route::get('/', [LoginController::class, 'index'])->name('manage.login');
Route::post('/manage/login', [LoginController::class, 'checkLogin'])->name('manage.checklogin');


// Auth::routes();

Route::group([

        'middleware'    => ['auth'],
        'prefix'        => 'admin',
        'namespace'     => 'admin',

], function () {
        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::post('/manage/logout', [LoginController::class, 'logout'])->name('manage.logout');
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::get('add', [CategoryController::class, 'add'])->name('add');
                Route::post('store', [CategoryController::class, 'store'])->name('store');
                Route::post('store/image', [CategoryController::class, 'storeImage'])->name('store-image');
                Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
                Route::put('update/{id}', [CategoryController::class, 'update'])->name('update');
                Route::post('delete/image', [CategoryController::class, 'deleteImage'])->name('delete-image');
                Route::post('delete', [CategoryController::class, 'delete'])->name('delete');
        });
        Route::group(['prefix' => 'fdseat', 'as' => 'fdseat.'], function () {
                Route::get('/', [FdseatController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => 'seat', 'as' => 'seat.'], function () {
                Route::get('/', [SeatController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => 'sale', 'as' => 'sale.'], function () {
                Route::get('/', [SaleController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => 'member', 'as' => 'member.'], function () {
                Route::get('/', [MemberController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => 'verification', 'as' => 'verification.'], function () {
                Route::get('/', [VerificationController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
                Route::get('profile', [SettingController::class, 'profile'])->name('profile');
                Route::get('password', [SettingController::class, 'password'])->name('password');
        });

        Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
                Route::group(['prefix' => 'payment_method', 'as' => 'payment_method.'], function () {
                        Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
                        Route::get('/add', [PaymentMethodController::class, 'add'])->name('add');
                        Route::post('/store', [PaymentMethodController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [PaymentMethodController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [PaymentMethodController::class, 'update'])->name('update');
                        Route::delete('/delete', [PaymentMethodController::class, 'delete'])->name('delete');
                });
                Route::group(['prefix' => 'room', 'as' => 'room.'], function () {
                        Route::get('/', [RoomController::class, 'index'])->name('index');
                        Route::get('/add', [RoomController::class, 'add'])->name('add');
                        Route::post('/store', [RoomController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [RoomController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [RoomController::class, 'update'])->name('update');
                        Route::delete('/delete', [RoomController::class, 'delete'])->name('delete');
                });
        });
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('add', [UserController::class, 'add'])->name('add');
                Route::post('store', [UserController::class, 'store'])->name('store');
                Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
                Route::put('reset', [UserController::class, 'resetPassword'])->name('reset');
                Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
                Route::delete('delete', [UserController::class, 'delete'])->name('delete');
        });
        Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::get('/add', [RoleController::class, 'add'])->name('add');
                Route::post('store', [RoleController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
                Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
                Route::delete('/delete', [RoleController::class, 'delete'])->name('delete');
        });
});
