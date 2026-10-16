<?php

use App\Http\Controllers\IdeaController;
use App\Http\Controllers\IdeaImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::redirect('', '/ideas');

Route::get('ideas', [IdeaController::class, 'index'])->name('idea.index')->middleware('auth');
Route::post('ideas', [IdeaController::class, 'store'])->name('idea.store')->middleware('auth');
Route::get('idea/{idea}', [IdeaController::class, 'show'])->name('idea.show')->middleware('auth');
Route::patch('idea/{idea}', [IdeaController::class, 'update'])->name('idea.update')->middleware('auth');
Route::delete('idea/{idea}', [IdeaController::class, 'destroy'])->name('idea.destroy')->middleware('auth');
Route::delete('idea/{idea}/image', [IdeaImageController::class, 'destroy'])->name('idea.image.destroy')->middleware('auth');

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::get('/login', [SessionsController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [SessionsController::class, 'store'])->middleware('guest');

Route::post('/logout', [SessionsController::class, 'destroy'])->middleware('auth');

Route::patch('steps/{step}', [StepController::class, 'update'])->name('step.update')->middleware('auth');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');

Route::get('storage/{path}', function (string $path) {
    if (config('filesystems.disks.public.driver') === 's3') {
        return redirect(Storage::disk('public')->url($path));
    }

    $filePath = storage_path('app/public/' . $path);

    abort_unless(file_exists($filePath), 404);

    return response()->file($filePath);
})->where('path', '.*');

Route::get('/debug-storage', function () {
    try {
        Storage::disk('public')->put('debug.txt', 'test');
        return [
            'status' => 'SUCCESS',
            'url' => Storage::disk('public')->url('debug.txt'),
        ];
    } catch (\Throwable $e) {
        return [
            'status' => 'ERROR',
            'message' => $e->getMessage(),
            'class' => get_class($e),
        ];
    }
});

