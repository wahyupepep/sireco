<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{BannerController, HomeController, CategoryController, MenuController, PartnerController, RoleController, SettingController, SliderController, UserController, SeatController, PaymentController, SaleController, MemberController, VerificationController};
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
   
    // Route::group(['prefix' => 'menu', 'as' => 'menu.'], function () {
    //     Route::get('/', [MenuController::class, 'index'])->name('index');
    //     Route::get('add', [MenuController::class, 'add'])->name('add');
    //     Route::post('store', [MenuController::class, 'store'])->name('store');
    //     Route::post('store/image', [MenuController::class, 'storeImage'])->name('store-image');
    //     Route::get('edit/{id}', [MenuController::class, 'edit'])->name('edit');
    //     Route::put('update/{id}', [MenuController::class, 'update'])->name('update');
    //     Route::post('delete/image', [MenuController::class, 'deleteImage'])->name('delete-image');
    //     Route::post('delete', [MenuController::class, 'delete'])->name('delete');
    //     Route::get('search/category', [MenuController::class, 'searchCategory'])->name('searchcategory');
    // });
    // Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
    //     Route::get('/', [BannerController::class, 'index'])->name('index');
    //     Route::get('add', [BannerController::class, 'add'])->name('add');
    //     Route::post('store', [BannerController::class, 'store'])->name('store');
    //     Route::post('store/image', [BannerController::class, 'storeImage'])->name('store-image');
    //     Route::get('edit/{id}', [BannerController::class, 'edit'])->name('edit');
    //     Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
    //     Route::post('delete/image', [BannerController::class, 'deleteImage'])->name('delete-image');
    //     Route::post('delete', [BannerController::class, 'delete'])->name('delete');
    // });
    // Route::group(['prefix' => 'slider', 'as' => 'slider.'], function () {
    //     Route::get('/', [SliderController::class, 'index'])->name('index');
    //     Route::get('add', [SliderController::class, 'add'])->name('add');
    //     Route::post('store', [SliderController::class, 'store'])->name('store');
    //     Route::post('store/image', [SliderController::class, 'storeImage'])->name('store-image');
    //     Route::get('edit/{id}', [SliderController::class, 'edit'])->name('edit');
    //     Route::put('update/{id}', [SliderController::class, 'update'])->name('update');
    //     Route::post('delete/image', [SliderController::class, 'deleteImage'])->name('delete-image');
    //     Route::post('delete', [SliderController::class, 'delete'])->name('delete');
    // });
    // Route::group(['prefix' => 'partner', 'as' => 'partner.'], function () {
    //     Route::get('/', [PartnerController::class, 'index'])->name('index');
    //     Route::get('add', [PartnerController::class, 'add'])->name('add');
    //     Route::post('store', [PartnerController::class, 'store'])->name('store');
    //     Route::post('store/image', [PartnerController::class, 'storeImage'])->name('store-image');
    //     Route::post('delete/image', [PartnerController::class, 'deleteImage'])->name('delete-image');
    //     Route::post('delete', [PartnerController::class, 'delete'])->name('delete');
    // });
    // Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
    //     Route::group(['prefix' => 'general', 'as' => 'general.'], function () {
    //         Route::get('/', [SettingController::class, 'index'])->name('index');
    //         Route::post('store', [SettingController::class, 'store'])->name('store');
    //         Route::post('store/image', [SettingController::class, 'storeImage'])->name('store-image');
    //         Route::post('delete/image', [SettingController::class, 'deleteImage'])->name('delete-image');
    //         Route::post('new/upload', [SettingController::class, 'newUpload'])->name('new-upload');
    //         Route::post('preview/image', [SettingController::class, 'previewImage'])->name('preview-image');
    //         Route::post('update', [SettingController::class, 'edit'])->name('update');
    //     });
    // });
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
