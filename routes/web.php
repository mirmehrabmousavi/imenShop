<?php

use App\Http\Controllers\Home\AuthController;
use App\Http\Controllers\Home\SitemapController;
use Illuminate\Support\Facades\Route;
use App\Models\Setting;
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

Route::group(['prefix' => 'admin'] , function (){
    Route::get('/',  [App\Http\Controllers\Admin\PanelController::class, 'index'])->middleware(['permission:نمایش داشبورد']);
    Route::get('/logout',  [App\Http\Controllers\Admin\PanelController::class, 'logout']);

    ///////////////////////////////////// gallery
    Route::match(['get', 'post'],'/gallery',  [App\Http\Controllers\Admin\GalleryController::class, 'index'])->middleware(['permission:نمایش همه تصویر ها|حذف تصویر|ویرایش تصویر|نمایش تصویر های خودش|افزودن تصویر']);
    Route::post('/gallery/create',  [App\Http\Controllers\Admin\GalleryController::class, 'create'])->middleware(['permission:افزودن تصویر']);
    Route::post('/gallery/create-image',  [App\Http\Controllers\Admin\GalleryController::class, 'createImage'])->middleware(['permission:افزودن تصویر']);
    Route::post('/gallery/remove',  [App\Http\Controllers\Admin\GalleryController::class, 'destroy'])->middleware(['permission:حذف تصویر']);
    Route::post('/gallery/show',  [App\Http\Controllers\Admin\GalleryController::class, 'show']);
    Route::post('/gallery/crop-image',  [App\Http\Controllers\Admin\GalleryController::class, 'cropImage']);
    Route::get('/get-image',  [App\Http\Controllers\Admin\GalleryController::class, 'getImage']);

    ///////////////////////////////////// setting
    Route::get('/setting/setting-comment',  [App\Http\Controllers\Admin\SettingController::class, 'comment'])->middleware(['permission:تنظیمات دیدگاه']);
    Route::put('/setting/setting-comment',  [App\Http\Controllers\Admin\SettingController::class, 'storeComment']);
    Route::get('/setting/setting-manage',  [App\Http\Controllers\Admin\SettingController::class, 'manage'])->middleware(['permission:تنظیمات سایت']);
    Route::put('/setting/setting-manage',  [App\Http\Controllers\Admin\SettingController::class, 'storeManage']);
    Route::get('/setting/seo',  [App\Http\Controllers\Admin\SettingController::class, 'seo'])->middleware(['permission:تنظیمات سئو']);
    Route::put('/setting/seo',  [App\Http\Controllers\Admin\SettingController::class, 'storeSeo']);
    Route::get('/setting/setting-category',  [App\Http\Controllers\Admin\SettingController::class, 'settingCategory'])->middleware(['permission:تنظیمات دسته بندی ها']);
    Route::put('/setting/setting-category',  [App\Http\Controllers\Admin\SettingController::class, 'storeCategory']);
    Route::match(['post','get'],'/setting/setting-design',  [App\Http\Controllers\Admin\VidgetController::class, 'template'])->middleware(['permission:تنظیمات قالب سایت']);;
    Route::match(['post','get'],'/setting/setting-pay',  [App\Http\Controllers\Admin\SettingController::class, 'pay'])->middleware(['permission:تنظیمات درگاه']);;

    /////////////////////////////////////// user
    Route::match(['get', 'post'],'/user',  [App\Http\Controllers\Admin\UserController::class, 'index'])->middleware(['permission:نمایش همه کاربر ها|حذف کاربر|نمایش کاربر های خودش|ویرایش کاربر|افزودن کاربر']);
    Route::post('/user/create',  [App\Http\Controllers\Admin\UserController::class, 'store'])->middleware(['permission:افزودن کاربر']);
    Route::get('/user/{user}/edit',  [App\Http\Controllers\Admin\UserController::class, 'edit'])->middleware(['permission:ویرایش کاربر']);
    Route::put('/user/{user}',  [App\Http\Controllers\Admin\UserController::class, 'update'])->middleware(['permission:ویرایش کاربر']);
    Route::post('/user/remove',  [App\Http\Controllers\Admin\UserController::class, 'removeArray'])->middleware(['permission:حذف کاربر']);
    Route::get('/user/{user}/show',  [App\Http\Controllers\Admin\UserController::class, 'show'])->middleware(['permission:نمایش همه کاربر ها|نمایش کاربر های خودش']);

    /////////////////////////////////////// search
    Route::post('/search-gallery',  [App\Http\Controllers\Admin\SearchController::class, 'searchGallery']);
    Route::post('/search-tax',  [App\Http\Controllers\Admin\SearchController::class, 'tax']);
    Route::post('/search-product',  [App\Http\Controllers\Admin\SearchController::class, 'product']);
    Route::post('/tax/create',  [App\Http\Controllers\Admin\SearchController::class, 'createTax']);

    /////////////////////////////////////// role
    Route::match(['get', 'post'],'/role',  [App\Http\Controllers\Admin\RoleController::class, 'index'])->middleware(['permission:نمایش مقام|حذف مقام|ویرایش مقام|افزودن مقام']);
    Route::match(['get', 'post'],'/user/role',  [App\Http\Controllers\Admin\RoleController::class, 'user'])->middleware(['permission:نمایش کارمندان']);

    /////////////////////////////////////// view
    Route::match(['get', 'post'],'/view',  [App\Http\Controllers\Admin\ViewController::class, 'index'])->middleware(['permission:نمایش بازدید|حذف بازدید']);

    /////////////////////////////////////// tax
    Route::match(['get', 'post'],'/category',  [App\Http\Controllers\Admin\CategoryController::class, 'index'])->middleware(['permission:نمایش همه دسته ها|حذف دسته|ویرایش دسته|نمایش دسته های خودش|افزودن دسته']);
    Route::match(['get', 'post'],'/tag',  [App\Http\Controllers\Admin\TagController::class, 'index'])->middleware(['permission:نمایش همه برچسب ها|نمایش برچسب های خودش|حذف برچسب|ویرایش برچسب|افزودن برچسب']);
    Route::match(['get', 'post'],'/brand',  [App\Http\Controllers\Admin\BrandController::class, 'index'])->middleware(['permission:نمایش همه برند ها|نمایش برند های خودش|حذف برند|ویرایش برند|افزودن برند']);
    Route::match(['get', 'post'],'/guarantee',  [App\Http\Controllers\Admin\GuaranteeController::class, 'index'])->middleware(['permission:نمایش همه برند ها|نمایش برند های خودش|حذف برند|ویرایش برند|افزودن برند']);
    Route::match(['get', 'post'],'/page',  [App\Http\Controllers\Admin\PageController::class, 'index'])->middleware(['permission:نمایش برگه|حذف برگه|ویرایش برگه|افزودن برگه']);

    /////////////////////////////////////// ticket
    Route::match(['get', 'post'],'/ticket',  [App\Http\Controllers\Admin\TicketController::class, 'index'])->middleware(['permission:نمایش درخواست|حذف درخواست|ویرایش درخواست']);
    Route::get('/ticket/{ticket}/edit',  [App\Http\Controllers\Admin\TicketController::class, 'edit'])->middleware(['permission:ویرایش درخواست']);
    Route::put('/ticket/{ticket}',  [App\Http\Controllers\Admin\TicketController::class, 'update'])->middleware(['permission:ویرایش درخواست']);
    Route::post('/ticket/remove',  [App\Http\Controllers\Admin\TicketController::class, 'removeArray'])->middleware(['permission:حذف درخواست']);
    Route::post('/mail',  [App\Http\Controllers\Admin\MailController::class, 'email'])->middleware(['permission:فرستادن ایمیل']);

    /////////////////////////////////////// post
    Route::match(['get', 'post'],'/post',  [App\Http\Controllers\Admin\PostController::class, 'index'])->middleware(['permission:نمایش همه کالا ها|نمایش کالا های خودش|حذف کالا|ویرایش کالا|افزودن کالا']);
    Route::match(['get' , 'post'],'/post/create',  [App\Http\Controllers\Admin\PostController::class, 'create'])->middleware(['permission:افزودن کالا']);
    Route::match(['post','get'],'/post/{post}/edit',  [App\Http\Controllers\Admin\PostController::class, 'edit'])->middleware(['permission:ویرایش کالا']);
    Route::match(['post','get'],'/post/{post}/show',  [App\Http\Controllers\Admin\PostController::class, 'show'])->middleware(['permission:نمایش کالا']);

    /////////////////////////////////////// file
    Route::match(['get', 'post'],'/file',  [App\Http\Controllers\Admin\FileController::class, 'index'])->middleware(['permission:نمایش همه کالا ها|نمایش کالا های خودش|حذف کالا|ویرایش کالا|افزودن کالا']);
    Route::match(['get' , 'post'],'/file/create',  [App\Http\Controllers\Admin\FileController::class, 'create'])->middleware(['permission:افزودن کالا']);
    Route::match(['post','get'],'/file/{post}/edit',  [App\Http\Controllers\Admin\FileController::class, 'edit'])->middleware(['permission:ویرایش کالا']);
    Route::match(['post','get'],'/file/{post}/show',  [App\Http\Controllers\Admin\FileController::class, 'show'])->middleware(['permission:نمایش کالا']);

    /////////////////////////////////////// inventory
    Route::match(['get', 'post'],'/inventory',  [App\Http\Controllers\Admin\InventoryController::class, 'index'])->middleware(['permission:نمایش انبارداری']);

    /////////////////////////////////////// variety
    Route::match(['get', 'post'],'/variety',  [App\Http\Controllers\Admin\VarietyController::class, 'index'])->middleware(['permission:نمایش تنوع|حذف تنوع|ویرایش تنوع']);
    Route::match(['get' , 'post'],'/variety/{post}/create',  [App\Http\Controllers\Admin\VarietyController::class, 'create'])->middleware(['permission:افزودن تنوع']);
    Route::match(['post','get'],'/variety/{post}/edit',  [App\Http\Controllers\Admin\VarietyController::class, 'edit'])->middleware(['permission:ویرایش تنوع']);
    Route::match(['post','get'],'/variety/{post}/show',  [App\Http\Controllers\Admin\VarietyController::class, 'show'])->middleware(['permission:ویرایش تنوع']);

    /////////////////////////////////////// news
    Route::match(['get', 'post'],'/news',  [App\Http\Controllers\Admin\NewsController::class, 'index'])->middleware(['permission:نمایش همه خبر ها|نمایش خبر های خودش|حذف خبر|ویرایش خبر|افزودن خبر']);
    Route::match(['get' , 'post'],'/news/create',  [App\Http\Controllers\Admin\NewsController::class, 'create'])->middleware(['permission:افزودن خبر']);
    Route::match(['post','get'],'/news/{news}/edit',  [App\Http\Controllers\Admin\NewsController::class, 'edit'])->middleware(['permission:ویرایش خبر']);

    ////////////////////////////////////////////////////////////////////////////////////// pay
    Route::match(['get', 'post'],'/pay',  [App\Http\Controllers\Admin\PayController::class, 'index'])->middleware(['permission:نمایش پرداختی|حذف پرداختی']);
    Route::match(['get', 'post'],'/pay/chart',  [App\Http\Controllers\Admin\PayController::class, 'chart'])->middleware(['permission:نمایش پرداختی|حذف پرداختی']);
    Route::match(['get', 'post'],'/pay/create',  [App\Http\Controllers\Admin\PayController::class, 'create'])->middleware(['permission:نمایش پرداختی|حذف پرداختی']);
    Route::get('/pay/{pay}',  [App\Http\Controllers\Admin\PayController::class, 'show'])->middleware(['permission:نمایش پرداختی']);
    Route::match(['get', 'post'],'/show-pay/{pay}',  [App\Http\Controllers\Admin\PayController::class, 'showPay'])->middleware(['permission:نمایش پرداختی']);
    Route::get('/pay/invoice/{pay}',  [App\Http\Controllers\Admin\PayController::class, 'invoice'])->name('invoice')->middleware(['permission:نمایش پرداختی']);

    /////////////////////////////////////// comment
    Route::match(['get', 'post'],'/comment',  [App\Http\Controllers\Admin\CommentController::class, 'index'])->middleware(['permission:ویرایش دیدگاه|حذف دیدگاه']);
    Route::match(['get', 'post'],'/comment/{comment}',  [App\Http\Controllers\Admin\CommentController::class, 'edit'])->middleware(['permission:ویرایش دیدگاه']);

    ///////////////////////////////////////// notification
    Route::match(['get', 'post'],'/notification/sms',  [App\Http\Controllers\Admin\NotificationController::class, 'sms'])->middleware(['permission:نمایش همه اطلاع رسانی ها|حذف اطلاع رسانی|نمایش اطلاع رسانی های خودش|افزودن اطلاع رسانی']);
    Route::match(['get', 'post'],'/notification/email',  [App\Http\Controllers\Admin\NotificationController::class, 'email'])->middleware(['permission:نمایش همه اطلاع رسانی ها|حذف اطلاع رسانی|نمایش اطلاع رسانی های خودش|افزودن اطلاع رسانی']);

    ///////////////////////////////////////// robot
    Route::match(['get', 'post'],'/robot',  [App\Http\Controllers\Admin\RobotController::class, 'index'])->middleware(['permission:نمایش همه رباط ها|حذف رباط|نمایش رباط های خودش|افزودن رباط']);

    ///////////////////////////////////////// charge
    Route::match(['get', 'post'],'/charge',  [App\Http\Controllers\Admin\ChargeController::class, 'index'])->middleware(['permission:نمایش شارژ|حذف شارژ|ویرایش شارژ|افزودن شارژ']);

    ///////////////////////////////////////// score
    Route::match(['get', 'post'],'/score',  [App\Http\Controllers\Admin\ScoreController::class, 'index'])->middleware(['permission:نمایش شارژ|حذف شارژ|ویرایش شارژ|افزودن شارژ']);
    Route::match(['get', 'post'],'/rank',  [App\Http\Controllers\Admin\ScoreController::class, 'rank'])->middleware(['permission:رتبه بندی']);

    ///////////////////////////////////////// carrier
    Route::match(['get', 'post'],'/carrier',  [App\Http\Controllers\Admin\CarrierController::class, 'index'])->middleware(['permission:نمایش همه رباط ها|حذف رباط|نمایش رباط های خودش|افزودن رباط']);

    ///////////////////////////////////////// time
    Route::match(['get', 'post'],'/time',  [App\Http\Controllers\Admin\TimeController::class, 'index'])->middleware(['permission:نمایش همه رباط ها|حذف رباط|نمایش رباط های خودش|افزودن رباط']);

    ///////////////////////////////////////// seller
    Route::match(['get', 'post'],'/seller',  [App\Http\Controllers\Admin\SellerController::class, 'index'])->middleware(['permission:نمایش فروشنده']);

    ///////////////////////////////////////// checkout
    Route::match(['get', 'post'],'/checkout',  [App\Http\Controllers\Admin\CheckoutController::class, 'index'])->middleware(['permission:نمایش تسویه حساب ها']);

    ///////////////////////////////////////// discount
    Route::match(['get', 'post'],'/discount',  [App\Http\Controllers\Admin\DiscountController::class, 'index'])->middleware(['permission:نمایش پرداختی']);

    ///////////////////////////////////////// document
    Route::match(['get', 'post'],'/document',  [App\Http\Controllers\Admin\DocumentController::class, 'index'])->middleware(['permission:نمایش مدارک']);

    ///////////////////////////////////////// question
    Route::match(['get', 'post'],'/question',  [App\Http\Controllers\Admin\QuestionController::class, 'index'])->middleware(['permission:نمایش پرسش و پاسخ|حذف پرسش و پاسخ|ویرایش پرسش و پاسخ']);

    ///////////////////////////////////////// event
    Route::match(['get', 'post'],'/event',  [App\Http\Controllers\Admin\EventController::class, 'index'])->middleware(['permission:نمایش رویداد|حذف رویداد']);
    Route::match(['get', 'post'],'/report/feedback',  [App\Http\Controllers\Admin\ReportController::class, 'feedback'])->middleware(['permission:نمایش بازخورد|حذف بازخورد']);
    Route::match(['get', 'post'],'/report/notification',  [App\Http\Controllers\Admin\ReportController::class, 'notification'])->middleware(['permission:نمایش اطلاع پست|حذف اطلاع پست']);

    ///////////////////////////////////////// excel
    Route::match(['get', 'post'],'/import/product',  [App\Http\Controllers\Admin\ExcelController::class, 'createProduct'])->middleware(['permission:ورودی اکسل']);
    Route::match(['get', 'post'],'/excel/import',  [App\Http\Controllers\Admin\ExcelController::class, 'import'])->middleware(['permission:ورودی اکسل']);
    Route::match(['get', 'post'],'/excel',  [App\Http\Controllers\Admin\ExcelController::class, 'index'])->middleware(['permission:خروجی اکسل']);
    Route::get('/get-excel/{data}',  [App\Http\Controllers\Admin\ExcelController::class, 'getExcel'])->middleware(['permission:خروجی اکسل']);
    Route::post('/import-excel',  [App\Http\Controllers\Admin\ExcelController::class, 'importExcel'])->middleware(['permission:ورودی اکسل']);
    Route::post('/excel/change-price',  [App\Http\Controllers\Admin\ExcelController::class, 'changePrice'])->middleware(['permission:ورودی اکسل']);

});
Route::group(['prefix' => '/'] , function (){
    Route::match(['get', 'post'],'/',  [App\Http\Controllers\Home\IndexController::class, 'index']);
    Route::post('/show-fast',  [App\Http\Controllers\Home\IndexController::class, 'showFast']);
    Route::post('/show-compares',  [App\Http\Controllers\Home\IndexController::class, 'showCompares']);
    Route::post('/get-sugest-index',  [App\Http\Controllers\Home\IndexController::class, 'getSugest']);

    /////////////////////////////////////////////// like
    Route::get('/get-like',  [App\Http\Controllers\Home\LikeController::class, 'getLike']);
    Route::post('/like',  [App\Http\Controllers\Home\LikeController::class, 'store']);

    /////////////////////////////////////////////// bookmark
    Route::get('/get-bookmark',  [App\Http\Controllers\Home\BookmarkController::class, 'getBookmark']);
    Route::post('/bookmark',  [App\Http\Controllers\Home\BookmarkController::class, 'store']);

    /////////////////////////////////////////////// search
    Route::post('/search-cat',  [App\Http\Controllers\Home\SearchController::class, 'searchCat']);
    Route::post('/search-brand',  [App\Http\Controllers\Home\SearchController::class, 'searchBrand']);
    Route::post('/search-advance',  [App\Http\Controllers\Home\SearchController::class, 'searchAdvance']);

    //////////////////////////////////////////////// cart
    Route::post('/add-cart',  [App\Http\Controllers\Home\CartController::class, 'addCart']);
    Route::post('/add-cart2',  [App\Http\Controllers\Home\CartController::class, 'addCart2']);
    Route::get('/get-carts',  [App\Http\Controllers\Home\CartController::class, 'getCarts']);
    Route::put('/change-carts/{cart}',  [App\Http\Controllers\Home\CartController::class, 'changeCarts']);
    Route::delete('/delete-cart/{cart}',  [App\Http\Controllers\Home\CartController::class, 'deleteCart']);
    Route::get('/cart',  [App\Http\Controllers\Home\CartController::class, 'cart']);
    Route::post('/change-carrier',  [App\Http\Controllers\Home\CartController::class, 'changeCarrier']);
    Route::post('/change-time-delivery',  [App\Http\Controllers\Home\CartController::class, 'changeTimeDelivery']);
    Route::post('/check-discount',  [App\Http\Controllers\Home\CartController::class, 'checkDiscount']);

    ///////////////////////////////////////////////////// user
    Route::get('/logout',  [App\Http\Controllers\Home\UserController::class, 'logout']);
    Route::get('/user-info-cart',  [App\Http\Controllers\Home\UserController::class, 'userInfoCart']);
    Route::put('/change-user-info',  [App\Http\Controllers\Home\UserController::class, 'ChangeUserInfo']);
    Route::get('/profile',  [App\Http\Controllers\Home\UserController::class, 'profile']);
    Route::match(['get', 'post'],'/profile/pay',  [App\Http\Controllers\Home\UserController::class, 'pay']);
    Route::get('/profile/like',  [App\Http\Controllers\Home\UserController::class, 'like']);
    Route::get('/profile/bookmark',  [App\Http\Controllers\Home\UserController::class, 'bookmark']);
    Route::match(['get', 'post'],'/profile/comment',  [App\Http\Controllers\Home\UserController::class, 'comment']);
    Route::match(['get', 'post'],'/profile/ticket',  [App\Http\Controllers\Home\UserController::class, 'ticket']);
    Route::get('/profile/recently',  [App\Http\Controllers\Home\UserController::class, 'recently']);
    Route::get('/profile/personal-info',  [App\Http\Controllers\Home\UserController::class, 'personalInfo']);
    Route::put('/change-all-user-info/{user}',  [App\Http\Controllers\Home\UserController::class, 'ChangeAllUserInfo']);
    Route::post('/profile/check-code',  [App\Http\Controllers\Home\UserController::class, 'checkCode']);
    Route::post('/profile/check-email',  [App\Http\Controllers\Home\UserController::class, 'checkEmail']);
    Route::match(['get', 'post'],'/profile/checkout',  [App\Http\Controllers\Home\UserController::class, 'checkout']);
    Route::get('/pay/invoice/{PayId}',  [App\Http\Controllers\Home\UserController::class, 'invoice'])->name('invoice');
    Route::get('/charge-increase',  [App\Http\Controllers\Home\ChargeController::class, 'chargeIncrease']);

    ///////////////////////////////////////////////////// single
    Route::get('/product/{PostSlug}',  [App\Http\Controllers\Home\SingleController::class, 'single']);
    Route::get('/download-product/{DownloadSlug}',  [App\Http\Controllers\Home\SingleController::class, 'downloadProduct']);
    Route::get('/product/{DownloadSlug}/download',  [App\Http\Controllers\Home\SingleController::class, 'download']);
    Route::post('/send-report',  [App\Http\Controllers\Home\ReportController::class, 'sendReport']);
    Route::post('/send-question',  [App\Http\Controllers\Home\QuestionController::class, 'sendQuestion']);
    Route::post('/send-call',  [App\Http\Controllers\Home\QuestionController::class, 'sendCall']);
    Route::get('/news/{NewsSlug}',  [App\Http\Controllers\Home\SingleController::class, 'news']);

    ///////////////////////////////////////////////////// rate
    Route::post('/rate',  [App\Http\Controllers\Home\RateController::class, 'store']);
    Route::post('/get-rate',  [App\Http\Controllers\Home\RateController::class, 'getRate']);

    ///////////////////////////////////////////////////// view
    Route::post('/view',  [App\Http\Controllers\Home\ViewController::class, 'view']);

    ///////////////////////////////////////////////////// address
    Route::post('/add-address',  [App\Http\Controllers\Home\AddressController::class, 'create']);
    Route::get('/get-all-address',  [App\Http\Controllers\Home\AddressController::class, 'getAll']);
    Route::post('/edit-address',  [App\Http\Controllers\Home\AddressController::class, 'editAddress']);
    Route::post('/select-address',  [App\Http\Controllers\Home\AddressController::class, 'selectAddress']);
    Route::post('/delete-address',  [App\Http\Controllers\Home\AddressController::class, 'deleteAddress']);
    Route::post('/edit-user-address',  [App\Http\Controllers\Home\AddressController::class, 'editUserAddress']);

    ///////////////////////////////////////////////////// comment
    Route::post('/send-comment',  [App\Http\Controllers\Home\CommentController::class, 'sendComment']);
    Route::post('/get-comment',  [App\Http\Controllers\Home\CommentController::class, 'getComment']);
    Route::post('/send-reply',  [App\Http\Controllers\Home\CommentController::class, 'sendReply']);

    ///////////////////////////////////////////////////// shop
    Route::get('/order',  [App\Http\Controllers\Home\ShopController::class, 'order']);
    Route::get('/order/zibal',  [App\Http\Controllers\Home\ShopController::class, 'zibal']);
    Route::get('/order/nextPay',  [App\Http\Controllers\Home\ShopController::class, 'nextPay']);
    Route::get('/order/idpay',  [App\Http\Controllers\Home\ShopController::class, 'idpay']);
    Route::get('/charge/order',  [App\Http\Controllers\Home\ShopController::class, 'chargeOrder']);
    Route::get('/charge/order/zibal',  [App\Http\Controllers\Home\ShopController::class, 'chargeZibal']);
    Route::get('/charge/order/nextPay',  [App\Http\Controllers\Home\ShopController::class, 'chargeNextPay']);
    Route::get('/charge/order/idpay',  [App\Http\Controllers\Home\ShopController::class, 'chargeIdpay']);
    Route::get('/shop',  [App\Http\Controllers\Home\ShopController::class, 'add_order']);
    Route::match(['get', 'post'],'/charge/shop',  [App\Http\Controllers\Home\ShopController::class, 'addCharge']);
    Route::get('/shop/wallet',  [App\Http\Controllers\Home\ShopController::class, 'shopWallet']);
    Route::get('/payment-spot',  [App\Http\Controllers\Home\ShopController::class, 'paymentSpot']);
    Route::get('/spot/order',  [App\Http\Controllers\Home\ShopController::class, 'spotOrder']);
    Route::get('/spot/order/zibal',  [App\Http\Controllers\Home\ShopController::class, 'spotZibal']);
    Route::get('/spot/order/nextPay',  [App\Http\Controllers\Home\ShopController::class, 'spotNextPay']);
    Route::get('/spot/order/idpay',  [App\Http\Controllers\Home\ShopController::class, 'spotIdpay']);

    ////////////////////////////////////////////////////////// shop download
    Route::get('/download/order',  [App\Http\Controllers\Home\ShopDownloadController::class, 'order']);
    Route::get('/download/order/zibal',  [App\Http\Controllers\Home\ShopDownloadController::class, 'zibal']);
    Route::get('/download/order/nextPay',  [App\Http\Controllers\Home\ShopDownloadController::class, 'nextPay']);
    Route::get('/download/order/idpay',  [App\Http\Controllers\Home\ShopDownloadController::class, 'idpay']);
    Route::get('/download/shop',  [App\Http\Controllers\Home\ShopDownloadController::class, 'add_order']);

    ///////////////////////////////////////////////////// archive
    Route::match(['get', 'post'],'/archive/category/{CategorySlug}',  [App\Http\Controllers\Home\ArchiveController::class, 'category']);
    Route::match(['get', 'post'],'/archive/brand/{BrandSlug}',  [App\Http\Controllers\Home\ArchiveController::class, 'brand']);
    Route::match(['get', 'post'],'/archive/search',  [App\Http\Controllers\Home\ArchiveController::class, 'search']);
    Route::match(['get', 'post'],'/archive/products/{VidgetSlug}',  [App\Http\Controllers\Home\ArchiveController::class, 'vidget']);
    Route::post('/search-nav',  [App\Http\Controllers\Home\ArchiveController::class, 'searchNav']);
    Route::get('/archive/news',  [App\Http\Controllers\Home\ArchiveController::class, 'news']);
    Route::get('/news/archive/category/{CategoryNewsSlug}',  [App\Http\Controllers\Home\ArchiveController::class, 'newsCategory']);
    Route::get('/news/archive/tag/{TagSlug}',  [App\Http\Controllers\Home\ArchiveController::class, 'newsTag']);
    Route::get('/rank/suggest',  [App\Http\Controllers\Home\ArchiveController::class, 'rank']);

    ///////////////////////////////////////////////////// product
    Route::match(['get', 'post'],'/profile/product/create/',  [App\Http\Controllers\Home\ProductController::class, 'create']);
    Route::match(['get', 'post'],'/profile/product',  [App\Http\Controllers\Home\ProductController::class, 'index']);
    Route::match(['get', 'post'],'/profile/product/{MyPostSlug}/edit',  [App\Http\Controllers\Home\ProductController::class, 'edit']);
    Route::match(['get', 'post'],'/profile/product/{MyPostSlug}/show',  [App\Http\Controllers\Home\ProductController::class, 'show']);
    Route::match(['get', 'post'],'/profile/product/pay',  [App\Http\Controllers\Home\ProductController::class, 'pays']);
    Route::match(['get', 'post'],'/profile/product/pay/{payMeta}',  [App\Http\Controllers\Home\ProductController::class, 'payShow']);
    Route::match(['get', 'post'],'/profile/all-products',  [App\Http\Controllers\Home\ProductController::class, 'allProducts']);
    Route::match(['get', 'post'],'/profile/add-variety/{PostSlug}',  [App\Http\Controllers\Home\ProductController::class, 'addVariety']);
    Route::match(['get', 'post'],'/profile/gallery',  [App\Http\Controllers\Home\ProductController::class, 'allGallery']);

    ///////////////////////////////////////////////////// seller
    Route::match(['get', 'post'],'/become-seller',  [App\Http\Controllers\Home\SellerController::class, 'becomeSeller'])->middleware(['web', 'auth']);

    ///////////////////////////////////////////////////// pay
    Route::get('/show-pay/{PayProperty}',  [App\Http\Controllers\Home\PayController::class, 'show']);

    ///////////////////////////////////////////////////// page
    Route::get('/page/{PageSlug}',  [App\Http\Controllers\Home\IndexController::class, 'page']);

    ////////////////////////////////////////////////////// google
    Route::get('/google-login', [App\Http\Controllers\Home\GoogleAuthController::class, 'redirectToProvider']);
    Route::get('/callback', [App\Http\Controllers\Home\GoogleAuthController::class, 'handleProviderCallback']);

    ///////////////////////////////////////////////////// ticket
    Route::post('/ticket',  [App\Http\Controllers\Home\TicketController::class, 'store']);

    ///////////////////////////////////////////////////// robot
    Route::get('/send-robot/{RobotId}',  [App\Http\Controllers\Home\IndexController::class, 'sendRobot']);

    Route::feeds();
    ///////////////////////////////////////////////////// sitemap
    Route::get('/sitemap.xml' , [SitemapController::class , 'index']);
    Route::get('/sitemap-products' , [SitemapController::class , 'products']);
    Route::get('/sitemap-downloadable' , [SitemapController::class , 'downloadable']);
    Route::get('/sitemap-categories' , [SitemapController::class , 'categories']);
    Route::get('/sitemap-brands' , [SitemapController::class , 'brands']);
    Route::get('/sitemap-tags' , [SitemapController::class , 'tags']);

    ///////////////////////////////////////////////////// upload
    Route::post('/upload-image',  [App\Http\Controllers\Home\ProductController::class, 'image']);
    Route::post('/change-profile',  [App\Http\Controllers\Home\ProductController::class, 'changeProfile']);

    ////////////////////////////////////////////////////////////// auth
    Route::get('/login',  [AuthController::class, 'loginPage'])->name('login');
    Route::get('/register',  [AuthController::class, 'loginPage'])->name('register');
    /**/// number
    Route::post('/check-auth',  [AuthController::class, 'check']);
    Route::post('/check-code',  [AuthController::class, 'checkCode']);
    Route::post('/login-auth',  [AuthController::class, 'login']);
    Route::get('/logout-auth',  [AuthController::class, 'logout']);
    Route::post('/make-user',  [AuthController::class, 'makeUser']);
    Route::post('/change-password',  [AuthController::class, 'changePassword']);

    /**/// email
    Route::post('/check-email',  [AuthController::class, 'checkEmail']);
    Route::post('/check-email-code',  [AuthController::class, 'checkEmailCode']);
    Route::post('/login-email',  [AuthController::class, 'loginEmail']);
    Route::post('/change-email-password',  [AuthController::class, 'changeEmailPassword']);
    Route::post('/make-user-email',  [AuthController::class, 'makeUserEmail']);
});
