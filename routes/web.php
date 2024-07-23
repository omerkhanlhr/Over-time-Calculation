<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Models\Designation;
use App\Models\Salary;
use Illuminate\Support\Facades\Route;

Route::get('/',[AdminController::class,'login'])->name('admin.login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/admin/dashboard',[AdminController::class,'index'])->name('admin.dashboard');

    Route::get('/admin/logout',[AdminController::class,'logout'])->name('admin.logout');
    Route::get('/admin/profile',[AdminController::class,'profile'])->name('admin.profile');
    Route::post('/admin/profile/update',[AdminController::class,'update_profile'])->name('update.admin_profile');
    Route::get('/admin/password/change',[AdminController::class,'change_password'])->name('admin.change_password');
    Route::post('/admin/password/update',[AdminController::class,'update_password'])->name('update.admin_password');
    Route::post('/store/user',[AdminController::class,'saveUser'])->name('save.user');
    Route::get('/add/user',[AdminController::class,'addUser'])->name('add.user');
    Route::get('/all/users',[AdminController::class,'allUsers'])->name('all.users');
    Route::get('/edit/user/{id}',[AdminController::class,'editUser'])->name('edit.user');
    Route::post('/update/user',[AdminController::class,'updateUser'])->name('update.user');
    Route::get('/delete/user/{id}',[AdminController::class,'deleteUser'])->name('delete.user');
    Route::get('/single/user/{id}',[AdminController::class,'singleUser'])->name('single.user');
    Route::get('/export/users',[AdminController::class,'export_users'])->name('export.user');
    Route::get('/export/clients',[AdminController::class,'export_clients'])->name('export.client');
    Route::get('/export/products',[AdminController::class,'export_products'])->name('export.product');
    Route::get('/export/payments',[AdminController::class,'export_payments'])->name('export.payment');
    Route::get('/export/invoices',[AdminController::class,'export_invoices'])->name('export.invoice');



    Route::controller(EmployeeController::class)->group(function() {
        Route::get('/add/employee', 'add_employee')->name('add.employee');
        Route::get('/employee/{id}', 'single_employee')->name('single.employee');
        Route::get('/all/employees', 'all_employees')->name('all.employee');
        Route::post('/store/employee', 'store_employee')->name('save.employee');
        Route::get('/edit/employee/{id}', 'edit_employee')->name('edit.employee');
        Route::put('/update/employee/{id}', 'update_employee')->name('update.employee');
        Route::get('/delete/employee/{id}', 'delete_employee')->name('delete.employee');
        Route::get('/salary/employee', 'showPage')->name('salary.page');
        Route::post('/employee/getdata', 'getEmployeeData')->name('get.employee.data');

    });


    Route::get('/generate-pdf',[PDFController::class,'generatepdf'])->name('generate.pdf');

    Route::view('test_pdf', 'pdf');

require __DIR__.'/auth.php';
