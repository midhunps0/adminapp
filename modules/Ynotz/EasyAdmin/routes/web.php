<?php
use Illuminate\Support\Facades\Route;
use Ynotz\EasyAdmin\Http\Controllers\DashboardController;
use Ynotz\EasyAdmin\Http\Controllers\MasterController;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin'], function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('eaasyadmin/fetch/{service}/{method}', [MasterController::class, 'fetch'])->name('easyadmin.fetch');
});
Route::post('ea/uploadfile', [MasterController::class, 'filepondUpload'])->name('easyadmin.file_upload');
Route::delete('ea/deletefile', [MasterController::class, 'filepondDelete'])->name('easyadmin.file_delete');
?>

