<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});


// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('/faq', 'faq')->name('faq');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode','maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

// product route
Route::controller('ProductController')->prefix('product')->name('product.')->group(function () {
    Route::get('catalogs/all', 'allCatalogCategory')->name('all.catalogs');
    Route::get('catalogs/{categorySlug}', 'catalogs')->name('catalogs');
    Route::get('details/{slug}', 'productDetails')->name('details');
    Route::get('design/{slug}/{variantId?}', 'productDesign')->name('design');
    Route::get('cart/design/{id}', 'cartProductDesign')->name('cart.design');
    Route::get('catalog/{categorySlug}/{catalogSlug}', 'products')->name('list');
    Route::get('vendor/{username}', 'vendorProducts')->name('vendor');
    Route::get('reviews/{slug}', 'productReviews')->name('reviews');
});

Route::controller('CartController')->prefix('product')->name('product.')->group(function () {
    Route::post('add/to/cart', 'addToCart')->name('add.to.cart');
    Route::post('update/cart', 'updateCart')->name('update.cart');
    Route::post('delete/cart', 'deleteCart')->name('delete.cart');
    Route::get('carts', 'carts')->name('carts');
    Route::get('cart/clear', 'clearCart')->name('cart.clear');
    Route::post('buy/now', 'buyNow')->name('buy.now');
    Route::get('checkout', 'checkout')->name('checkout')->middleware('auth');
    Route::post('order', 'order')->name('order')->middleware('auth');
});
