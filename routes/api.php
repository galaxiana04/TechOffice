<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotTelegramController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\KatalogKomatController;
use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\NewMemoController;
use App\Http\Controllers\JobticketController;
use App\Http\Controllers\NewBOMController;
use App\Http\Controllers\AiCustomController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\ApiUserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('setWebhook', [BotTelegramController::class, 'setWebhook']);
Route::get('fianbot/webhook', [BotTelegramController::class, 'commandHandlerWebhook']);


Route::post('/uploadid', [FileController::class, 'uploadFile']);
Route::get('/katalogkomat/search', [KatalogKomatController::class, 'search'])->name('katalogkomat.search');
Route::get('/kataloglibrary/search', [FileManagementController::class, 'search'])->name('library.search');
Route::get('/new-memo/search', [NewMemoController::class, 'search'])->name('new-memo.search');

Route::get('/jobticket/search', [JobticketController::class, 'search'])->name('jobticket.search');

Route::get('/newbom/search', [NewBOMController::class, 'search'])->name('newbom.search');


Route::get('/aicustom/{keyword}', [AiCustomController::class, 'show']);



Route::get('/project-types', [ProjectTypeController::class, 'jsondata']);


Route::get('/managerspecialfitur', [JobticketController::class, 'managerspecialfitur'])->name('jobticket.managerspecialfitur');
//acc jobticket oleh manager bisa jarak jauh dengan wa
Route::post('jobticket/approveperbaikan/{revision}/{kindposition}', [JobticketController::class, 'revisionapprove'])->name('jobticket.revisionapprove');

Route::get('/users', [ApiUserController::class, 'getUsers']);
Route::get('/users/wa', [ApiUserController::class, 'getWaPhones']);
