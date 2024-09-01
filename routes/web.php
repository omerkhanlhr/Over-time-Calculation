<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceBreakdownController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LabourTypeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WorkHourController;
use App\Models\Designation;
use App\Models\Salary;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class, 'login'])->name('admin.login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/export/employee', [AdminController::class, 'export_employee'])->name('export.employee');
    Route::get('/import/employee', [AdminController::class, 'import_employee'])->name('import.employee');
    Route::post('/import/employee/data', [AdminController::class, 'save_import_employee'])->name('save.import.employee');
    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminController::class, 'update_profile'])->name('update.admin_profile');
    Route::get('/admin/password/change', [AdminController::class, 'change_password'])->name('admin.change_password');
    Route::post('/admin/password/update', [AdminController::class, 'update_password'])->name('update.admin_password');
    Route::post('/store/user', [AdminController::class, 'saveUser'])->name('save.user');
    Route::get('/add/user', [AdminController::class, 'addUser'])->name('add.user');
    Route::get('/all/users', [AdminController::class, 'allUsers'])->name('all.users');
    Route::get('/edit/user/{id}', [AdminController::class, 'editUser'])->name('edit.user');
    Route::post('/update/user', [AdminController::class, 'updateUser'])->name('update.user');
    Route::get('/delete/user/{id}', [AdminController::class, 'deleteUser'])->name('delete.user');
    Route::get('/single/user/{id}', [AdminController::class, 'singleUser'])->name('single.user');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('/add/employee', 'add_employee')->name('add.employee');
        Route::get('/employee/{id}', 'single_employee')->name('single.employee');
        Route::get('/all/employees', 'all_employees')->name('all.employee');
        Route::post('/store/employee', 'store_employee')->name('save.employee');
        Route::get('/edit/employee/{id}', 'edit_employee')->name('edit.employee');
        Route::put('/update/employee/{id}', 'update_employee')->name('update.employee');
        Route::get('/delete/employee/{id}', 'delete_employee')->name('delete.employee');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::controller(ClientController::class)->group(function () {

        Route::get('/get-client-address/{clientId}',  'getClientAddress');

        Route::get('/add/client', 'Addclient')->name('add.client');
        Route::get('/edit/client/{id}', 'Editclient')->name('edit.client');
        Route::get('/delete/client/{id}', 'Deleteclient')->name('delete.client');
        Route::get('/single/client/{id}', 'Singleclient')->name('single.client');
        Route::get('/all/clients', 'allclient')->name('all.clients');
        Route::post('/store/client', 'Saveclient')->name('save.client');
        Route::post('/update/client/{id}', 'Updateclient')->name('update.client');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::controller(WorkHourController::class)->group(function () {
        Route::get('/import/workhour',  'import_workhour')->name('import.workhour');
        Route::post('/import/workhour/data',  'save_import_workhour')->name('save.import.workhour');
        Route::get('add-work-hours', 'create_workhour')->name('add.work.hours');
        Route::get('calculate-overtime/{id}', 'calculate_overtime')->name('calculate.overtime');
        Route::get('single-work-hours-details/{id}', 'single_Workdetails')->name('single.work.hours.details');
        Route::post('store-work-hours', 'store_workhour')->name('store.work.hours');
        Route::get('/search-clients', 'searchClients')->name('search.clients');
        Route::get('/work-details', 'display')->name('display.work.hours');
        Route::get('/search-employees',  'searchEmployees')->name('search.employees');
        Route::get('/workhours/{id}/edit', 'edit')->name('edit.work.hours');
        Route::put('/workhours/{id}', 'update')->name('update.work.hours');
        Route::get('/delete/workhour/{id}', 'delete_workhour')->name('delete.work.hour');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::controller(StatsController::class)->group(function () {

        Route::get('add-stats-hours', 'create_statshour')->name('add.stats.hours');
        Route::get('single-stats-hours-details/{id}', 'single_statsdetails')->name('single.stats.hours.details');
        Route::post('store-stats-hours', 'store_statshour')->name('store.stats.hours');
        Route::get('/stats-details', 'display')->name('display.stats.hours');
        Route::get('/statshours/{id}/edit', 'edit')->name('edit.stats.hours');
        Route::put('/statshours/{id}', 'update')->name('update.stats.hours');
        Route::get('/delete/statshour/{id}', 'delete_statshour')->name('delete.statshour');
        Route::get('/move/statshour/{id}', 'moveToWorkHours')->name('move.To.Workhours');
    });
});

Route::middleware('auth', 'role:admin')->group(function () {
    Route::controller(LabourTypeController::class)->group(function () {
        Route::get('/add/type/labour', 'add_type')->name('add.type');
        Route::get('/all/type', 'all_type')->name('all.type');
        Route::post('/store/type', 'store_type')->name('save.type');
        Route::get('/edit/type/{id}', 'edit_type')->name('edit.type');
        Route::post('/update/type/{id}', 'update_type')->name('update.type');
        Route::get('/delete/type/{id}', 'delete_type')->name('delete.type');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/delete/invoice/{id}', 'delete_invoice')->name('delete.invoice');
        Route::get('/payment/invoice/{id}', 'getpayment')->name('get.payment');
        Route::post('/add/payment/invoice/{id}', 'payment_invoice')->name('payment.invoice');
        Route::get('invoice/{id}', 'show_invoice')->name('show.invoice');
        Route::get('invoices/create' ,'create_invoice')->name('invoices.create');
        Route::post('invoices/store' , 'createInvoice')->name('invoices.store');
        Route::get('/workhours/details', 'getWorkhoursDetails')->name('workhours.details');
        Route::get('invoices' ,'all_Invoices')->name('invoices.show');
        Route::get('invoice/{id}/pdf','generatePdf')->name('invoice.pdf');
        Route::get('/labor-types', 'getLaborTypes')->name('labor.types');
        Route::get('/payments', 'Allpayments')->name('all.payments');
        Route::get('/invoices/{id}/pdfs' , 'showPdfs')->name('invoice.pdfs');
        Route::get('/invoices/{id}/preview/pdf','previewPdf')->name('invoice.previewPdf');
        Route::get('/invoices/{id}/download/pdf','downloadPdf')->name('invoice.downloadPdf');
       Route::get('/invoice/{invoiceId}/breakdown-pdf/{laborType}/preview', 'previewBreakdownPdf')
    ->name('invoice.breakdown.preview.pdf');
       Route::get('/invoice/{invoiceId}/breakdown-pdf/{laborType}/download', 'downloadBreakdownPdf')
    ->name('invoice.breakdown.download.pdf');

        Route::get('invoices/{id}/edit', 'edit')->name('invoices.edit');
    Route::post('invoices/{id}/update','update')->name('invoices.update');

    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::controller(InvoiceBreakdownController::class)->group(function () {
        // Edit Breakdown
Route::get('invoice/{invoiceId}/breakdown/{laborType}/edit',  'editBreakdown')->name('invoice.breakdown.edit');
Route::post('invoice/{invoiceId}/breakdown/{laborType}/update',  'updateBreakdown')->name('invoice.breakdown.update');

    });
});

require __DIR__ . '/auth.php';
