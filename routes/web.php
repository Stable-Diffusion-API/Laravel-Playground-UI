<?php

use App\Http\Controllers\PlaygroundController;
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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [PlaygroundController::class, 'index']);
Route::post('/stablediffusion', [PlaygroundController::class, 'stableDiffusion'])->name('stablediffusion');
Route::post('/dreambooth', [PlaygroundController::class, 'dreambooth'])->name('dreambooth');
Route::post('/dreambooth-training', [PlaygroundController::class, 'dreamboothTraining'])->name('dreambooth-training');
Route::post('/upload-image', [PlaygroundController::class, 'imageUpload'])->name('upload-image');
Route::post('/allmodels', [PlaygroundController::class, 'publicModels'])->name('public-models');
