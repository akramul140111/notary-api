<?php

use App\Http\Controllers\Api\v1\ApplicationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/profile-data/{id}", [ProfileController::class, 'profileEdit'])->name('profile.edit');
Route::post("/profile-data/{id}", [ProfileController::class, 'profileUpdate'])->name('profile.update');

Route::get('/application-list', [ApplicationController::class, 'index'])->name('application.list');
Route::post('/application-store', [ApplicationController::class, 'store'])->name('application.store');
Route::get('/application-show/{application}', [ApplicationController::class, 'show'])->name('application.show');
Route::get('/application-edit/{application}', [ApplicationController::class, 'edit'])->name('application.edit');
Route::put('/application-update/{application}', [ApplicationController::class, 'update'])->name('application.update');
Route::delete('/application-delete/{application}', [ApplicationController::class, 'destroy'])->name('application.delete');