<?php

use App\Http\Controllers\Api\GitApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GitHubController;
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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::controller(GitHubController::class)->group(function () {
//     Route::get('/test', 'store');
// });

Route::get('/test',  [GitHubController::class, 'index']);

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/refresh', 'refresh');
});

Route::get('/find',  [GitApiController::class, 'show']);
Route::post('/find',  [GitApiController::class, 'show']);
Route::delete('/find/{id}',  [GitApiController::class, 'delete']);


