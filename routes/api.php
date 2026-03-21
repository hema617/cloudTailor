<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TailorController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ChatController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Route::middleware('auth:sanctum')->group(function () {

//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/profile', [AuthController::class, 'profile']);
//     Route::put('/profile/update', [AuthController::class, 'updateProfile']);
//     Route::post('/change-password', [AuthController::class, 'changePassword']);
// });

Route::group(
    [
        'where'         =>  ['routePrefix' => 'admin'],
        'prefix'        =>  '/{routePrefix}',
        'middleware'    =>  ['multiroleauth'],
    ],
    function () {
        Route::get('users', [AdminController::class, 'customers']);
        Route::get('users/{id}', [AdminController::class, 'changeUserStatus']);
        Route::get('/categories', [DesignController::class, 'categories']); // all category according to category
    }
);
// Route::group(
//     [
//         'where'         =>  ['routePrefix' => 'customer|admin|tailor'],
//         'prefix'        =>  '/{routePrefix}',
//         'middleware'    =>  ['multiroleauth'],
//     ],
//     function () {
//         Route::get('profile', [AuthController::class, 'profile']);
//     }
// );

Route::group(
    [
        'where'         =>  ['routePrefix' => 'tailor'],
        'prefix'        =>  '/{routePrefix}',
        'middleware'    =>  ['multiroleauth'],
    ],
    function () {
        Route::get('profile', [AuthController::class, 'profile']);

        Route::post('design/add', [DesignController::class, 'addDesign']);

        Route::put('/design/update/{id}', [DesignController::class, 'updateDesign']);
        Route::put('/design/status/{id}', [DesignController::class, 'updateDesignStatus']);
        Route::delete('/design/delete/{id}', [DesignController::class, 'deleteDesign']);

        Route::get('/tailor/availability', [TailorController::class, 'availability']);
        Route::post('/tailor/availability/add', [TailorController::class, 'addAvailability']);
        Route::put('/tailor/availability/update/{id}', [TailorController::class, 'updateAvailability']);
        Route::get('/tailor/availability/status/{id}', [TailorController::class, 'updateAvailabilityStatus']);

        Route::put('/order/status/{id}', [OrderController::class, 'updateOrderStatus']);

    }
);

Route::group(
    [
        'where'         =>  ['routePrefix' => 'customer|admin|tailor'],
        'prefix'        =>  '/{routePrefix}',
        'middleware'    =>  ['multiroleauth'],
    ],
    function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::get('/order/list', [OrderController::class, 'Orders']);
    }
);
Route::group(
    [
        'where'         =>  ['routePrefix' => 'customer'],
        'prefix'        =>  '/{routePrefix}',
        'middleware'    =>  ['multiroleauth'],
    ],
    function () {
        Route::post('/order/place', [OrderController::class, 'placeOrder']);
        Route::post('/cart/add', [CartController::class, 'addToCart']);
    }
);
// open apis
Route::group(
    [
        'where'         =>  ['routePrefix' => 'customer|admin|tailor'],
        'prefix'        =>  '/{routePrefix}',
    ],
    function () {
        Route::get('/categories', [DesignController::class, 'categories']);
        Route::get('designs', [DesignController::class, 'tailorDesign']);
        Route::get('tailor', [TailorController::class, 'getActiveTailorList']);
    }
);

Route::middleware('auth:sanctum')->group(function () {

    // Address
    Route::get('/user/addresses', [UserController::class, 'addresses']);
    Route::post('/user/address/add', [UserController::class, 'addAddress']);
    Route::put('/user/address/update/{id}', [UserController::class, 'updateAddress']);
    Route::delete('/user/address/delete/{id}', [UserController::class, 'deleteAddress']);
    Route::get('/user/address/{id}', [UserController::class, 'getAddress']);

    // Wishlist
    Route::get('/wishlist', [UserController::class, 'wishlist']);
    Route::post('/wishlist/add', [UserController::class, 'addWishlist']);
    Route::delete('/wishlist/remove/{design_id}', [UserController::class, 'removeWishlist']);

    // Notifications
    Route::get('/notifications', [UserController::class, 'notifications']);
    Route::post('/notification/read/{id}', [UserController::class, 'readNotification']);
    Route::delete('/notification/delete/{id}', [UserController::class, 'deleteNotification']);

    // Location
    Route::get('/cities', [UserController::class, 'cities']);
    Route::get('/city/{id}', [UserController::class, 'city']);
    Route::get('/delivery-charge/{city_id}', [UserController::class, 'deliveryCharge']);


    // Tailor
    Route::get('/tailors', [TailorController::class, 'tailors']);
    Route::get('/tailor/{id}', [TailorController::class, 'tailor']);
    Route::post('/tailor/register', [TailorController::class, 'registerTailor']);
    Route::put('/tailor/update/{id}', [TailorController::class, 'updateTailor']);

    // Services
    Route::get('/tailor/services/{tailor_id}', [TailorController::class, 'tailorServices']);
    Route::post('/tailor/service/add', [TailorController::class, 'addService']);
    Route::put('/tailor/service/update/{id}', [TailorController::class, 'updateService']);
    Route::delete('/tailor/service/delete/{id}', [TailorController::class, 'deleteService']);

    // Portfolio
    Route::get('/tailor/portfolio/{tailor_id}', [TailorController::class, 'portfolio']);
    Route::post('/tailor/portfolio/upload', [TailorController::class, 'uploadPortfolio']);

    // Availability
    Route::get('/tailor/availability/{tailor_id}', [TailorController::class, 'availability']);
    Route::post('/tailor/availability/add', [TailorController::class, 'addAvailability']);
    Route::put('/tailor/availability/update/{id}', [TailorController::class, 'updateAvailability']);
    Route::delete('/tailor/availability/delete/{id}', [TailorController::class, 'deleteAvailability']);


    // Categories
    // Route::get('/categories', [DesignController::class, 'categories']);
    Route::get('/categories/{id}', [DesignController::class, 'category']);
    Route::get('/subcategories/{category_id}', [DesignController::class, 'subcategories']);
    // Route::get('/subcategory/{id}', [DesignController::class, 'subcategory']);

    // Designs
    Route::get('/designs', [DesignController::class, 'designs']);
    Route::get('/design/{id}', [DesignController::class, 'design']);
    Route::get('/designs/category/{category_id}', [DesignController::class, 'designsByCategory']);
    Route::get('/designs/subcategory/{subcategory_id}', [DesignController::class, 'designsBySubcategory']);
    Route::get('/designs/tailor/{tailor_id}', [DesignController::class, 'designsByTailor']);

    // Design Images
    Route::post('/design/image/upload', [DesignController::class, 'uploadDesignImage']);
    Route::delete('/design/image/delete/{id}', [DesignController::class, 'deleteDesignImage']);

    // Options
    Route::get('/design/options/{design_id}', [DesignController::class, 'designOptions']);
    Route::post('/design/option/add', [DesignController::class, 'addOption']);
    Route::put('/design/option/update/{id}', [DesignController::class, 'updateOption']);
    Route::delete('/design/option/delete/{id}', [DesignController::class, 'deleteOption']);




    Route::get('/cart/{customer_id}', [CartController::class, 'cart']);

    // Route::post('/cart/add', [CartController::class, 'addToCart']);

    Route::put('/cart/update/{id}', [CartController::class, 'updateCart']);

    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart']);

    Route::delete('/cart/clear/{customer_id}', [CartController::class, 'clearCart']);

    Route::get('/cart/count/{customer_id}', [CartController::class, 'cartCount']);

    Route::get('/cart/total/{customer_id}', [CartController::class, 'cartTotal']);


    // Route::post('/order/place', [OrderController::class, 'placeOrder']);

    Route::get('/orders/customer/{customer_id}', [OrderController::class, 'customerOrders']);

    Route::get('/orders/tailor/{tailor_id}', [OrderController::class, 'tailorOrders']);

    Route::get('/order/{id}', [OrderController::class, 'orderDetails']);

    Route::put('/order/cancel/{id}', [OrderController::class, 'cancelOrder']);

    Route::put('/order/status/{id}', [OrderController::class, 'updateStatus']);

    Route::put('/order/payment/{id}', [OrderController::class, 'updatePayment']);

    Route::post('/order/measurement', [OrderController::class, 'addMeasurement']);

    Route::get('/order/measurement/{order_id}', [OrderController::class, 'measurement']);

    Route::delete('/order/delete/{id}', [OrderController::class, 'deleteOrder']);


    Route::get('/wishlist/{customer_id}', [WishlistController::class, 'wishlist']);

    Route::post('/wishlist/add', [WishlistController::class, 'addWishlist']);

    Route::delete('/wishlist/remove/{id}', [WishlistController::class, 'removeWishlist']);


    Route::get('/reviews/{design_id}', [ReviewController::class, 'reviews']);

    Route::post('/review/add', [ReviewController::class, 'addReview']);

    Route::delete('/review/delete/{id}', [ReviewController::class, 'deleteReview']);



    Route::post('/payment', [PaymentController::class, 'payment']);

    Route::get('/payment/{order_id}', [PaymentController::class, 'paymentDetails']);

    Route::put('/payment/refund/{id}', [PaymentController::class, 'refund']);


    //Admin


    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

    // Route::get('/admin/customers', [AdminController::class, 'customers']);

    Route::delete('/admin/customer/{id}', [AdminController::class, 'deleteCustomer']);

    Route::get('/admin/tailors', [AdminController::class, 'tailors']);

    Route::put('/admin/tailor/verify/{id}', [AdminController::class, 'verifyTailor']);

    Route::delete('/admin/tailor/{id}', [AdminController::class, 'deleteTailor']);

    Route::get('/admin/orders', [AdminController::class, 'orders']);

    Route::put('/admin/order/status/{id}', [AdminController::class, 'updateOrderStatus']);


    // chat controller



    Route::post('/chat/conversation', [ChatController::class, 'createConversation']);

    Route::get('/chat/conversations/{user_id}', [ChatController::class, 'conversations']);

    Route::get('/chat/messages/{conversation_id}', [ChatController::class, 'messages']);

    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    Route::delete('/chat/message/{id}', [ChatController::class, 'deleteMessage']);
});
