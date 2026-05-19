<?php
// routes/web.php — ajouter ces lignes

use App\Http\Controllers\InvoiceController;

Route::get('/facture', [InvoiceController::class, 'index'])->name('invoice.form');
Route::post('/facture/generer', [InvoiceController::class, 'generate'])->name('invoice.generate');
