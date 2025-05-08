<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/{any}', function () {
    return view('welcome'); // This must match your Blade file name (e.g., resources/views/app.blade.php)
})->where('any', '.*');