<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'url',
    'as' => 'url.'
], function () {
    Route::get('', [UrlController::class, 'getUrl']);
    Route::get('detail', [UrlController::class, 'getUrlDetails']);
    Route::post('', [UrlController::class, 'createUrl']);
});

Route::group([
    'prefix' => 'urls',
    'as' => 'urls.'
], function () {
    Route::get('', [UrlController::class, 'getUrls']);
    Route::post('updateTitles', [UrlController::class, 'updateTitles']);
});
