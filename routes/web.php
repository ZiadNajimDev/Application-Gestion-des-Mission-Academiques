<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProofsController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DepartmentHeadController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Enseignant routes
Route::middleware(['auth', 'role:enseignant'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');
    
    Route::get('/missions/create', [MissionController::class, 'create'])->name('missions.create');
    Route::post('/missions', [MissionController::class, 'store'])->name('missions.store');
    Route::get('/missions', [MissionController::class, 'index'])->name('missions.index');
    Route::get('/missions/{mission}', [MissionController::class, 'show'])->name('missions.show');
    Route::delete('/missions/{mission}', [MissionController::class, 'destroy'])->name('missions.destroy');
    Route::get('/missions/{mission}/edit', [MissionController::class, 'edit'])->name('missions.edit');
    Route::put('/missions/{mission}', [MissionController::class, 'update'])->name('missions.update');
    Route::get('/missions/{mission}/print', [MissionController::class, 'print'])->name('missions.print');

    Route::get('/missions/{mission}/proofs', [ProofsController::class, 'create'])->name('proofs.create');
    Route::post('/proofs', [ProofsController::class, 'store'])->name('proofs.store');
    Route::get('/proofs/{proof}', [ProofsController::class, 'show'])->name('proofs.show');
    Route::delete('/proofs/{proof}', [ProofsController::class, 'destroy'])->name('proofs.destroy');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    // Proof routes (placeholder - create this controller later)
    Route::post('/proofs', [ProofsController::class, 'store'])->name('proofs.store');
});




Route::middleware(['auth', 'role:chef_departement'])->prefix('chef')->name('chef.')->group(function () {
    Route::get('/dashboard', [DepartmentHeadController::class, 'dashboard'])->name('dashboard');
    
    // Mission validation routes
    Route::get('/missions/validate', [DepartmentHeadController::class, 'missionValidationList'])->name('mission_validate');
    Route::get('/missions/{mission}/details', [DepartmentHeadController::class, 'missionDetails'])->name('mission_details');
    Route::post('/missions/{mission}/validate', [DepartmentHeadController::class, 'validateMission'])->name('mission_validate_post');
    
    // Department missions routes
    Route::get('/department/missions', [DepartmentHeadController::class, 'departmentMissions'])->name('department_missions');
    
    // Department statistics routes
    Route::get('/department/statistics', [DepartmentHeadController::class, 'departmentStats'])->name('department_stats');
    Route::get('/settings', [DepartmentHeadController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [DepartmentHeadController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/password', [DepartmentHeadController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::post('/settings/department', [DepartmentHeadController::class, 'updateDepartmentSettings'])->name('settings.updateDepartmentSettings');
    Route::get('/department/statistics', [DepartmentHeadController::class, 'departmentStats'])->name('department_stats');
});

//directeur

Route::middleware(['auth', 'role:directeur'])->prefix('director')->name('director.')->group(function () {
    Route::get('/dashboard', [DirectorController::class, 'dashboard'])->name('dashboard');
    Route::get('/pending-missions', [DirectorController::class, 'pendingMissions'])->name('pending_missions');
    Route::get('/missions/{mission}', [DirectorController::class, 'missionDetails'])->name('mission_details');
    Route::post('/missions/{mission}/process', [DirectorController::class, 'processMission'])->name('process_mission');
    Route::get('/all-missions', [DirectorController::class, 'allMissions'])->name('all_missions');
    Route::get('/statistics', [DirectorController::class, 'statistics'])->name('statistics');
    Route::get('/departments', [DirectorController::class, 'departments'])->name('departments');
    Route::get('/departments/{department}', [DirectorController::class, 'departmentDetails'])->name('department_details');
    Route::put('/departments/{department}', [DirectorController::class, 'updateDepartmentSettings'])->name('update_department');
    Route::post('/departments/allocate-budgets', [DirectorController::class, 'allocateBudgets'])->name('allocate_budgets');
    Route::post('/departments/{department}/update-budget', [DirectorController::class, 'updateDepartmentBudget'])->name('update_department_budget');
    Route::delete('/departments/{id}', [DirectorController::class, 'deleteDepartment'])->name('delete_department');
    Route::post('/departments/create', [DirectorController::class, 'createDepartment'])->name('create_department');
    Route::get('/reports', [DirectorController::class, 'reports'])->name('reports');
    Route::get('/settings', [DirectorController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [DirectorController::class, 'updateProfile'])->name('update_profile');
    Route::post('/settings/password', [DirectorController::class, 'updatePassword'])->name('update_password');
});


// Accountant routes
Route::middleware(['auth', 'role:comptable'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');
    
    // Reservation management
    Route::get('/reservations/pending', [AccountantController::class, 'pendingReservations'])->name('pending_reservations');
    Route::get('/reservations/all', [AccountantController::class, 'allReservations'])->name('all_reservations');
    Route::get('/reservations/{mission}', [AccountantController::class, 'reservationDetails'])->name('reservation_details');
    Route::post('/reservations/{mission}/create', [AccountantController::class, 'createReservation'])->name('create_reservation');
    Route::put('/reservations/{reservation}/update', [AccountantController::class, 'updateReservation'])->name('update_reservation');
    Route::post('/reservations/{reservation}/cancel', [AccountantController::class, 'cancelReservation'])->name('cancel_reservation');
    Route::post('/missions/{mission}/complete', [AccountantController::class, 'completeMission'])->name('complete_mission');
    
    // Other routes can be added later (payments, proof documents, etc.)
    Route::get('/payments/pending', [AccountantController::class, 'pendingPayments'])->name('pending_payments');
    Route::get('/payments/all', [AccountantController::class, 'allPayments'])->name('all_payments');
    Route::get('/proof-documents', [AccountantController::class, 'proofDocuments'])->name('proof_documents');
    Route::get('/settings', [AccountantController::class, 'settings'])->name('settings');
});


// Accountant routes
Route::middleware(['auth', 'role:comptable'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');
    Route::get('/reservations', [AccountantController::class, 'reservations'])->name('reservations');
    Route::post('/reservations', [AccountantController::class, 'storeReservation'])->name('reservations.store');
    Route::put('/reservations/{id}', [AccountantController::class, 'updateReservation'])->name('reservations.update');
    Route::put('/reservations/{id}/complete', [AccountantController::class, 'completeMission'])->name('reservations.complete');
    Route::delete('/reservations/{id}/cancel', [AccountantController::class, 'cancelReservation'])->name('reservations.cancel');
    Route::get('/payments', [AccountantController::class, 'payments'])->name('payments');
    Route::post('/payments', [AccountantController::class, 'storePayment'])->name('payments.store');
    Route::get('/payments/{id}/print', [AccountantController::class, 'printPaymentReceipt'])->name('payments.print');

    Route::get('/proofs', [AccountantController::class, 'proofs'])->name('proofs');
    Route::get('/proofs/{id}', [AccountantController::class, 'showProofDocument'])->name('proofs.show');
    Route::post('/proofs/{id}/process', [AccountantController::class, 'processProofDocument'])->name('proofs.process');
    Route::get('/proofs/{id}/download', [AccountantController::class, 'downloadProofDocument'])->name('proofs.download');

    Route::post('/missions/{id}/complete', [AccountantController::class, 'completeMission'])->name('missions.complete');
    Route::post('/missions/{id}/complete', [AccountantController::class, 'markMissionComplete'])->name('missions.complete');
    
    Route::get('/settings', [AccountantController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [AccountantController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [AccountantController::class, 'updatePassword'])->name('settings.password');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.delete-all');
});