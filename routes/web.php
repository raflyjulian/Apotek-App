<?php

use App\Http\Controllers\MedicineController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::middleware('IsGuest')->group(function(){
    Route::get('/', function () {
        return view('login');
    })->name('login');
    Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');
});


Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/error-permission', function(){
    return view('errors.permission');
})->name('error.permission');

Route::middleware(['IsLogin'])->group(function() {
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/home', function (){
        return view('home');
    })->name('home.page');
});

Route::middleware(['IsLogin', 'IsAdmin'])->group(function() {

    //menu data obat
    Route::prefix('/medicine')->name('medicine.')->group(function() {
        Route::get('/create', [MedicineController::class, 'create'])->name('create');
        Route::post('/store', [MedicineController::class, 'store'])->name('store');
        Route::get('/', [MedicineController::class, 'index'])->name('home');
        Route::get('/edit/{id}', [MedicineController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MedicineController::class, 'destroy'])->name('delete');
        Route::get('/stock', [MedicineController::class, 'stock'])->name('stock');
        Route::get('/data/stock/{id}', [MedicineController::class, 'stockEdit'])->name('stock.edit');
        Route::patch('/data/stock{id}', [MedicineController::class, 'stockUpdate'])->name('stock.update');
    });
    
    //menu kelola akun
    Route::prefix('/users')->name('users.')->group(function() {
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::get('/', [UserController::class, 'index'])->name('home');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::patch('/{id}', [UserController::class, 'update'])->name('update');
    });

    Route::prefix('/order')->name('order.')->group(function() {
        Route::get('/data', [OrderController::class, 'data'])->name('data');    
        Route::get('/export-excel', [OrderController::class, 'exportExcel'])->name('export-excel');  
        Route::get('/filter', [OrderController::class, 'filterOrdersByDateAdmin'])->name('filter.admin');
        Route::get('download/{id}', [OrderController::class, 'downloadPDF'])->name('download');
        
    });


});

Route::middleware(['IsLogin', 'IsKasir'])->group(function() {
    Route::prefix('/kasir')->name('kasir.')->group(function() {
        Route::prefix('/order')->name('order.')->group(function() {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/store', [OrderController::class, 'store'])->name('store');
            Route::get('/print/{id}', [OrderController::class, 'show'])->name('print');
            Route::get('download/{id}', [OrderController::class, 'downloadPDF'])->name('download');
            Route::get('/filter', [OrderController::class, 'filterOrdersByDate'])->name('filter');
        });
    });
});

