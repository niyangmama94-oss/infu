<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});



// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->group(function () {
    Route::get('/', 'supportTicket')->name('ticket');
    Route::get('/new', 'openSupportTicket')->name('ticket.open');
    Route::post('/create', 'storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'replyTicket')->name('ticket.reply');
    Route::post('/close/{ticket}', 'closeTicket')->name('ticket.close');
    Route::get('/download/{ticket}', 'ticketDownload')->name('ticket.download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('/services', 'services')->name('services');
    Route::get('service/tags/{id}/{name}', 'serviceByTag')->name('service.tag');

    Route::get('service/filtered', 'filterService')->name('service.filter');
    Route::get('service/{slug}/{id}/{order_id?}', 'serviceDetails')->name('service.details');

    Route::get('influencers', 'influencers')->name('influencers');
    Route::get('influencers/category/{id}/{name}', 'influencerByCategory')->name('influencer.category');
    Route::get('influencer/filtered', 'filterInfluencer')->name('influencer.filter');

    Route::get('/influencer/profile/{name}/{id}', 'influencerProfile')->name('influencer.profile');


    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
    Route::get('attachment/download/{attachment}/{conversation_id}/{type?}', 'attachmentDownload')->name('attachment.download');
    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode','maintenance')->withoutMiddleware('maintenance')->name('maintenance');
    Route::post('pusher/auth/{socketId}/{channelName}', "pusherAuthentication");
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
