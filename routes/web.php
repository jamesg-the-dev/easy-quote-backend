<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

// Invoice routes
Route::get('/invoices/{quoteNumber}', [InvoiceController::class, 'show'])->name('invoice.show');

