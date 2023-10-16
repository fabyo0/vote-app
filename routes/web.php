<?php

use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/user', function (\App\Models\User $user) {
    return response()->json([
       'user' => \App\Models\User::with('ideas.comments')->get()
    ]);
});

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    dd('Application cache has been cleared');
});

Route::get('/', [IdeaController::class, 'index'])->name('idea.index');

Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('idea.show');

require __DIR__ . '/auth.php';
