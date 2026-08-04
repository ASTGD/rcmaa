<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AlumniPortalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/heritage', [PageController::class, 'heritage'])->name('heritage');
Route::get('/our-goal', [PageController::class, 'ourGoal'])->name('our-goal');
Route::get('/faculty', [PageController::class, 'teachers'])->name('teachers');
Route::get('/how-to-apply', [PageController::class, 'howToApply'])->name('how-to-apply');
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/help-center', [PageController::class, 'helpCenter'])->name('help-center');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('terms');

Route::get('/committee/{group?}', CommitteeController::class)->name('committee');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/notice', [NoticeController::class, 'index'])->name('notices.index');
Route::get('/notice/{notice}', [NoticeController::class, 'show'])->name('notices.show');

Route::get('/gallery', GalleryController::class)->name('gallery');
Route::get('/directory', DirectoryController::class)->middleware('directory')->name('directory');
Route::get('/faqs', FaqController::class)->name('faqs');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| Reunion registration
|--------------------------------------------------------------------------
*/

Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [RegistrationController::class, 'create'])->name('create');
    Route::post('/', [RegistrationController::class, 'store'])
        ->middleware('throttle:10,60')
        ->name('store');
    Route::get('/confirmed/{registration:reference}', [RegistrationController::class, 'confirmation'])
        ->name('confirmation');
});

/*
|--------------------------------------------------------------------------
| The registrant's own area — reached by an emailed one-time link
|--------------------------------------------------------------------------
*/

Route::prefix('my')->name('portal.')->group(function () {
    Route::get('/', [AlumniPortalController::class, 'request'])->name('request');
    Route::post('/', [AlumniPortalController::class, 'sendLink'])
        ->middleware('throttle:5,10')
        ->name('send-link');

    // The signed link itself; exchanges the URL for a session.
    Route::get('/open/{registration:reference}', [AlumniPortalController::class, 'open'])
        ->middleware('signed')
        ->name('open');

    Route::middleware('alumni')->group(function () {
        Route::get('/registration', [AlumniPortalController::class, 'show'])->name('show');
        Route::post('/receipt', [AlumniPortalController::class, 'uploadReceipt'])->name('receipt');
        Route::patch('/registration', [AlumniPortalController::class, 'update'])->name('update');
        Route::get('/pass', [AlumniPortalController::class, 'pass'])->name('pass');
        Route::post('/close', [AlumniPortalController::class, 'close'])->name('close');
    });
});

Route::get('/registration-status', [RegistrationController::class, 'statusForm'])->name('registration.status');
Route::post('/registration-status', [RegistrationController::class, 'statusLookup'])
    ->middleware('throttle:10,1')
    ->name('registration.status.lookup');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('/registrations', [Admin\RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/export', [Admin\RegistrationController::class, 'export'])->name('registrations.export');
    Route::get('/registrations/{registration}', [Admin\RegistrationController::class, 'show'])->name('registrations.show');
    Route::get('/registrations/{registration}/edit', [Admin\RegistrationController::class, 'edit'])->name('registrations.edit');
    Route::put('/registrations/{registration}', [Admin\RegistrationController::class, 'updateDetails'])->name('registrations.update-details');
    Route::patch('/registrations/{registration}', [Admin\RegistrationController::class, 'update'])->name('registrations.update');
    Route::delete('/registrations/{registration}', [Admin\RegistrationController::class, 'destroy'])->name('registrations.destroy');

    // Committee accounts and the signed-in user's own profile.
    Route::get('/account', [Admin\UserController::class, 'profile'])->name('account');
    Route::put('/account', [Admin\UserController::class, 'updateProfile'])->name('account.update');
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
    Route::patch('/messages/{message}', [Admin\MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

    // Content CMS — one generic controller drives every simple content model.
    Route::get('/content/{type}', [Admin\ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{type}/create', [Admin\ContentController::class, 'create'])->name('content.create');
    Route::post('/content/{type}', [Admin\ContentController::class, 'store'])->name('content.store');
    Route::get('/content/{type}/{id}/edit', [Admin\ContentController::class, 'edit'])->name('content.edit');
    Route::put('/content/{type}/{id}', [Admin\ContentController::class, 'update'])->name('content.update');
    Route::delete('/content/{type}/{id}', [Admin\ContentController::class, 'destroy'])->name('content.destroy');
});
