<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\AnalysisController;

/**
 * Application Routes
 * 
 * This file contains all the web routes for the bill checker application.
 */

// Home page - displays the upload form
Route::get('/', function () {
    return view('scan');
});

// Upload bill file - handles file upload and OCR processing
Route::post('/bill/upload', [BillController::class, 'store'])
    ->name('bill.upload');

// Show bill details - displays OCR results and analysis status
Route::get('/bill/{bill}', [BillController::class, 'show'])
    ->name('bill.show');

// Analyze bill with AI - processes the bill through AI analysis
Route::post('/bill/{bill}/analyze', [AnalysisController::class, 'analyze'])
    ->name('bill.analyze');
