<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CategoryMemberController, HomeController, RoleController, SettingController, UserController, SeatController, PaymentController, SaleController, MemberController, VerificationController, FdseatController, PaymentMethodController, RoomController, DiscountController, NotificationController, RegistrationController};
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
Route::get('manage/registration', [RegistrationController::class, 'registration'])->name('manage.registration');
Route::post('manage/registration', [RegistrationController::class, 'inputRegistration'])->name('manage.inputregistration');
Route::get('verified/{id}', [RegistrationController::class, 'emailVerified'])->name('email.verified');
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Auth::routes();

Route::group([

        'middleware'    => ['auth'],
        'prefix'        => 'admin',
        'namespace'     => 'admin',

], function () {
        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::post('/manage/logout', [LoginController::class, 'logout'])->name('manage.logout');
        // Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        //         Route::get('/', [CategoryController::class, 'index'])->name('index');
        //         Route::get('add', [CategoryController::class, 'add'])->name('add');
        //         Route::post('store', [CategoryController::class, 'store'])->name('store');
        //         Route::post('store/image', [CategoryController::class, 'storeImage'])->name('store-image');
        //         Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        //         Route::put('update/{id}', [CategoryController::class, 'update'])->name('update');
        //         Route::post('delete/image', [CategoryController::class, 'deleteImage'])->name('delete-image');
        //         Route::post('delete', [CategoryController::class, 'delete'])->name('delete');
        // });
        Route::group(['prefix' => 'fdseat', 'as' => 'fdseat.'], function () {
                Route::get('/', [FdseatController::class, 'index'])->name('index');
                Route::post('/save-booking', [FdseatController::class, 'saveBooking'])->name('savebooking');
        });
        Route::group(['prefix' => 'seat', 'as' => 'seat.'], function () {
                Route::get('/', [SeatController::class, 'index'])->name('index');
                Route::post('/list-seat', [SeatController::class, 'listSeat'])->name('list-seat');
                Route::post('/order', [SeatController::class, 'order'])->name('order');
                Route::post('confirm/order', [SeatController::class, 'confirmOrder'])->name('confirm-order');
                Route::get('/order-summary/{id}', [SeatController::class, 'orderSummary'])->name('order-summary');
                Route::get('/list-order', [SeatController::class, 'listOrder'])->name('list-order');
                Route::get('/detail-order/{id}', [SeatController::class, 'detailOrder'])->name('detail-order');
                Route::get('/payment/{id}', [SeatController::class, 'paymentOrder'])->name('payment-order');
                Route::post('upload/payment', [SeatController::class, 'uploadPayment'])->name('upload-payment');
        });

        Route::group(['prefix' => 'sale', 'as' => 'sale.'], function () {
                Route::get('/', [SaleController::class, 'index'])->name('index');
                Route::post('total-income', [SaleController::class, 'getTotalIncome'])->name('total-income');
        });
        Route::group(['prefix' => 'member', 'as' => 'member.'], function () {
                Route::get('/', [MemberController::class, 'index'])->name('index');
                Route::get('data', [MemberController::class, 'memberData'])->name('data');
                Route::post('check-data', [MemberController::class, 'memberCheckData'])->name('checkdata');
                Route::get('detail/{id}', [MemberController::class, 'detail'])->name('detail');
        });
        Route::group(['prefix' => 'verification', 'as' => 'verification.'], function () {
                Route::get('/', [VerificationController::class, 'index'])->name('index');
                Route::get('/complete', [VerificationController::class, 'complete'])->name('complete');
                Route::get('/detail-order/{id}', [VerificationController::class, 'detailOrder'])->name('detail-order');
                Route::get('verified/detail-order/{id}', [VerificationController::class, 'verifiedDetailOrder'])->name('verified-order');
        });
        Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
                Route::get('profile/{id}', [SettingController::class, 'profile'])->name('profile');
                Route::get('password', [SettingController::class, 'password'])->name('password');
                Route::post('change/password', [SettingController::class, 'changePassword'])->name('changepassword');
                Route::put('update/profile/{id}', [SettingController::class, 'updateProfile'])->name('updateprofile');
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

                Route::group(['prefix' => 'category_member', 'as' => 'category_member.'], function () {
                        Route::get('/', [CategoryMemberController::class, 'index'])->name('index');
                        Route::get('/add', [CategoryMemberController::class, 'add'])->name('add');
                        Route::post('/store', [CategoryMemberController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [CategoryMemberController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [CategoryMemberController::class, 'update'])->name('update');
                        Route::delete('/delete', [CategoryMemberController::class, 'delete'])->name('delete');
                });

                Route::group(['prefix' => 'discount', 'as' => 'discount.'], function () {
                        Route::get('/', [DiscountController::class, 'index'])->name('index');
                        Route::get('/add', [DiscountController::class, 'add'])->name('add');
                        Route::post('/store', [DiscountController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [DiscountController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [DiscountController::class, 'update'])->name('update');
                        Route::delete('/delete', [DiscountController::class, 'delete'])->name('delete');
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

        Route::get('/notification', [NotificationController::class, 'getData'])->name('notification');
});
