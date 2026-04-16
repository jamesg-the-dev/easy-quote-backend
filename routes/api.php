<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::middleware('api')->group(function () {
    // Quote API endpoints
    Route::post('/quotes/{quoteNumber}/accept', [InvoiceController::class, 'accept']);
    Route::post('/quotes/{quoteNumber}/request-changes', [InvoiceController::class, 'requestChanges']);
    Route::post('/quotes/{quoteNumber}/decline', [InvoiceController::class, 'decline']);
});
