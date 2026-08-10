<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Member;
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

Route::post('/donation', [\App\Http\Controllers\DonationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('donation.store');

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
| The member's own area
|--------------------------------------------------------------------------
|
| Members authenticate on the `alumni` guard, against their own registration.
| The emailed one-time link the portal opened with is kept for anyone who
| registered before passwords existed — it lands them on "set a password".
|
*/

Route::prefix('my')->name('member.')->group(function () {
    Route::middleware('guest:alumni')->group(function () {
        Route::get('/login', [Member\LoginController::class, 'show'])->name('login');
        Route::post('/login', [Member\LoginController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('login.attempt');

        Route::post('/link', [Member\LoginController::class, 'sendLink'])
            ->middleware('throttle:5,10')
            ->name('link.send');

        Route::get('/forgot-password', [Member\PasswordController::class, 'requestForm'])->name('password.request');
        Route::post('/forgot-password', [Member\PasswordController::class, 'sendResetLink'])
            ->middleware('throttle:5,10')
            ->name('password.email');

        Route::get('/reset-password/{token}', [Member\PasswordController::class, 'resetForm'])->name('password.reset');
        Route::post('/reset-password', [Member\PasswordController::class, 'reset'])
            ->middleware('throttle:6,1')
            ->name('password.update');
    });

    // The signed link itself; exchanges the URL for a session.
    Route::get('/open/{registration:reference}', [Member\LoginController::class, 'openLink'])
        ->middleware('signed')
        ->name('link.open');

    Route::middleware('auth:alumni')->group(function () {
        Route::get('/', [Member\DashboardController::class, 'show'])->name('dashboard');

        Route::patch('/profile', [Member\DashboardController::class, 'update'])->name('profile.update');

        Route::post('/receipt', [Member\DashboardController::class, 'uploadReceipt'])->name('receipt');

        Route::get('/slip/registration', [Member\DashboardController::class, 'registrationSlip'])->name('slip.registration');
        Route::get('/slip/payment', [Member\DashboardController::class, 'paymentSlip'])->name('slip.payment');
        Route::get('/pass', [Member\DashboardController::class, 'pass'])->name('pass');

        Route::get('/password', [Member\PasswordController::class, 'createForm'])->name('password.create');
        Route::post('/password', [Member\PasswordController::class, 'store'])->name('password.store');

        Route::post('/logout', [Member\LoginController::class, 'destroy'])->name('logout');
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

    Route::get('/donations', [Admin\DonationController::class, 'index'])->name('donations.index');
    Route::patch('/donations/{donation}', [Admin\DonationController::class, 'update'])->name('donations.update');
    Route::delete('/donations/{donation}', [Admin\DonationController::class, 'destroy'])->name('donations.destroy');

    // Content CMS — one generic controller drives every simple content model.
    Route::get('/content/{type}', [Admin\ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{type}/create', [Admin\ContentController::class, 'create'])->name('content.create');
    Route::post('/content/{type}', [Admin\ContentController::class, 'store'])->name('content.store');
    Route::get('/content/{type}/{id}/edit', [Admin\ContentController::class, 'edit'])->name('content.edit');
    Route::put('/content/{type}/{id}', [Admin\ContentController::class, 'update'])->name('content.update');
    Route::delete('/content/{type}/{id}', [Admin\ContentController::class, 'destroy'])->name('content.destroy');
});
