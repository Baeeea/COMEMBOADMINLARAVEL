<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DocumentRequestController;

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

// Complaint Image API Routes
Route::get('/complaint/{id}/image', [ComplaintController::class, 'getImage'])->name('api.complaint.image');
Route::get('/complaint/{id}/image2', [ComplaintController::class, 'getImage2'])->name('api.complaint.image2');
Route::get('/complaint/{id}/image3', [ComplaintController::class, 'getImage3'])->name('api.complaint.image3');

// Document Request Image API Routes (from documentrequest table)
Route::get('/documentrequest/{id}/photo-store', [DocumentRequestController::class, 'getPhotoStore'])->name('api.documentrequest.photoStore');
Route::get('/documentrequest/{id}/photo-current-house', [DocumentRequestController::class, 'getPhotoCurrentHouse'])->name('api.documentrequest.photoCurrentHouse');
Route::get('/documentrequest/{id}/photo-renovation', [DocumentRequestController::class, 'getPhotoRenovation'])->name('api.documentrequest.photoRenovation');
Route::get('/documentrequest/{id}/photo-proof', [DocumentRequestController::class, 'getPhotoProof'])->name('api.documentrequest.photoProof');
Route::get('/documentrequest/{id}/valid-id-front', [DocumentRequestController::class, 'getValidIDFront'])->name('api.documentrequest.validIDFront');
Route::get('/documentrequest/{id}/valid-id-back', [DocumentRequestController::class, 'getValidIDBack'])->name('api.documentrequest.validIDBack');
Route::get('/documentrequest/{id}/image', [DocumentRequestController::class, 'getImage'])->name('api.documentrequest.image');
Route::get('/documentrequest/{id}/image2', [DocumentRequestController::class, 'getImage2'])->name('api.documentrequest.image2');
Route::get('/documentrequest/{id}/image3', [DocumentRequestController::class, 'getImage3'])->name('api.documentrequest.image3');

// Resident ID Images API Routes (from residents table)
Route::get('/documentrequest/{id}/id-front', [DocumentRequestController::class, 'getIdFront'])->name('api.documentrequest.idFront');
Route::get('/documentrequest/{id}/id-back', [DocumentRequestController::class, 'getIdBack'])->name('api.documentrequest.idBack');