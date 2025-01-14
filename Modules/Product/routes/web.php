<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;

// Route::middleware(['web','auth'])->prefix('/product')->name('product.')->group(function(){
//     Route::get('/', [ProductController::class,'index'])->name('index');
// });

Route::group(['middleware' => ['web','auth']], function() {
    Route::resource('admin/products', ProductController::class);

});
