<?php

declare(strict_types=1);

use App\Http\Controllers\IdeaController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [IdeaController::class, 'index'])->name('idea.index');

Route::get('/ideas/{idea:slug}', [IdeaController::class, 'show'])->name('idea.show');

Route::get('/@{user}', function (User $user) {
    return view('user-profile-show', ['user' => $user]);
})->name('user.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

require __DIR__ . '/auth.php';
