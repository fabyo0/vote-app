<?php

declare(strict_types=1);

use App\Http\Controllers\IdeaController;
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::get('/', [IdeaController::class, 'index'])->name('idea.index');

Route::get('/ideas/{idea:slug}', [IdeaController::class, 'show'])->name('idea.show');

Route::get('/@{user}', function (User $user) {
    return view('user-profile-show', ['user' => $user]);
})->name('user.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/admin/settings', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('admin-settings');
    })->name('admin.settings');
});

require __DIR__ . '/auth.php';
