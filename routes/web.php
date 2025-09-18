<?php

use App\Mail\HelloMail;
use App\Http\Controllers\Auth\MyWelcomeController;
use App\Http\Controllers\LanguageController;
use App\Services\MPesaHelper;
use Illuminate\Support\Facades\Route;
use Spatie\WelcomeNotification\WelcomesNewUsers;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Admin\PropertyController;

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

Route::get('/', function () {
    //if SHOW_WELCOME_PAGE is true
    if (config('app.show_landing_page')) {
        // Get featured properties for the landing page
        $featuredProperties = \App\Models\Property::with(['address', 'landlord', 'houses'])
            ->where('status', 'active')
            ->where('is_vacant', true)
            ->limit(8)
            ->get();
            
        return view('web.index', compact('featuredProperties'));
    }
    return redirect('/login');
});

// Test route
Route::get('/test-route', function () {
    return 'Test route works!';
});

// Frontend Property Routes
Route::prefix('properties')->name('frontend.properties.')->group(function () {
    Route::get('/', [App\Http\Controllers\Frontend\PropertyController::class, 'index'])->name('index');
    Route::get('/search', [App\Http\Controllers\Frontend\PropertyController::class, 'search'])->name('search');
    Route::get('/{id}', [App\Http\Controllers\Frontend\PropertyController::class, 'show'])->name('show');
});

// Legacy routes for backward compatibility
Route::get('/rent-properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/rent-properties/{id}', [PropertyController::class, 'show'])->name('properties.show');

// Route to display properties for sale
Route::get('/sale-properties', function () {
    return view('web.frontend.sale-properties.list');
});


Route::get('send-mail', [MailController::class, 'index']);

// Route::get('/symlink', function () {
//     $target = '/home/ojajejar/rms.ojajejar.com/storage/app/public';
//     $shortcut = '/home/ojajejar/rms.ojajejar.com/rentals/storage';
//     symlink($target, $shortcut);

//     return 'Symlink created';
// });

Route::get('/symlink', function () {
    $target = '/home/exprnrln/foxes-rentals.techwithrichard.com/storage/app/public';
    $shortcut = '/home/exprnrln/foxes-rentals.techwithrichard.com/public/storage';

    if (file_exists($shortcut)) {
        return 'Symlink already exists.';
    }

    if (symlink($target, $shortcut)) {
        return 'Symlink created successfully.';
    } else {
        return 'Failed to create symlink.';
    }
});


Route::get('/stk', function () {
//    $phone = '254720691181';
//    $amount = '100000000000000';
//    $reference = 'test lease';
//    return MPesaHelper::stkPush($phone, $amount, $reference);
//    return MPesaHelper::generateAccessToken();
});

Route::get('/sms', function () {
    return \App\Helpers\TextSMSGateway::sendSms('254720691181', 'Hello from FOXES RMS');
});
//Route::get('/registerUrls', function () {
//    return MPesaHelper::registerURLS();
//});

//Language Routes
Route::get('lang/{lang}', [LanguageController::class, 'changeLanguage'])->name('lang.switch');


Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('tenant')) {
        return redirect('/portal');
    }
    if ($user->hasRole('landlord')) {
        return redirect('/landlord');
    }
    //if user has role is admin or staff
    if ($user->hasAnyRole(['admin', 'staff'])) {
        return redirect('/admin');
    }


    return redirect('/admin');
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/notifications', function () {
    $user = auth()->user();
    if ($user->hasRole('tenant')) {
        return redirect()->route('tenant.notifications');
    }
    if ($user->hasRole('landlord')) {
        return redirect()->route('landlord.notifications');
    }
    return redirect()->route('admin.notifications');
})
    ->middleware(['auth'])
    ->name('notifications');


require __DIR__ . '/auth.php';
require __DIR__ . '/landlord.php';
require __DIR__ . '/tenant.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/test.php';

Route::group(['middleware' => ['web', WelcomesNewUsers::class,]], function () {
    Route::get('welcome/{user}', [MyWelcomeController::class, 'showWelcomeForm'])->name('welcome');
    Route::post('welcome/{user}', [MyWelcomeController::class, 'savePassword']);
});


Route::controller(PaymentController::class)
->prefix('payments')
->as('payments')
->group(function(){
    Route::get('/token', 'token')->name('token');
    Route::get('/initiatestkpush', 'initiateStkPush')->name('initiatestkpush');

    Route::post('/stkcallback', 'stkCallback')->name('stkcallback');
});

Route::get('/sendemail', function () {
    Mail::to('richardkoech69@gmail.com')
    ->send(new HelloMail());
    return 'Test Email sent!';
});


// Route::get('/send-test-email', [TestEmailController::class, 'send']);

// Include portal routes
require __DIR__ . '/portals.php';

// Profile route for users without specific portals
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        return view('profile', ['user' => auth()->user()]);
    })->name('profile');
});

