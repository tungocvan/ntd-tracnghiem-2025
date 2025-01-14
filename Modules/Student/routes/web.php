<?php

use Illuminate\Support\Facades\Route;
use Modules\Student\Http\Controllers\StudentController;

Route::middleware(['web','auth'])->prefix('/student')->name('student.')->group(function(){
    Route::get('/', [StudentController::class,'index'])->name('index');
});
