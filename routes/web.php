<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Private storage routes
Route::middleware(['auth'])->group(function () {

    Route::get('/priv-storage/{filepath}', function ($filepath) {
        return Storage::disk('private')->download($filepath);
    })->where('filepath', '.*')->name('priv-storage');

});


