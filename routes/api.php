<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\Depkop\DepkopController;
use App\Http\Controllers\API\Honor\HonorController;
use App\Http\Controllers\API\Ministry\MinistryController;
use App\Http\Controllers\API\Note\NoteController;
use App\Http\Controllers\API\PadananData\PadananDataController;
use App\Http\Controllers\API\Param\ParamController;
use App\Http\Controllers\API\PriorityProgram\MonevController;
use App\Http\Controllers\API\PriorityProgram\ProgramActivityController;
use App\Http\Controllers\API\PriorityScale\PriorityScaleController;
use App\Http\Controllers\API\Realization\RealizationController;
use App\Http\Controllers\API\Recap\RecapController;
use App\Http\Controllers\API\Recap\RetireController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/login', LoginController::class);

Route::middleware(['auth:api'])->group(function() {
    Route::post('/auth/logout', LogoutController::class);
    Route::get('/auth/user', UserController::class);

    Route::post('/ministry/import', [MinistryController::class, 'store']);

    
    Route::post('/realization/import', [RealizationController::class, 'store']);

    Route::post('/honor/import', [HonorController::class, 'store']);

    Route::controller(ProgramActivityController::class)->group(function() {
        Route::post('program_activity', 'store');
        Route::patch('program_activity/{program_activity:id}', 'update');
        Route::patch('program_activity/{program_activity:id}/update_recommendation', 'update_recommendation');
        Route::delete('program_activity/{program_activity:id}', 'delete');
    });  

    Route::controller(MonevController::class)->group(function() {
        Route::post('monev', 'store');
        Route::patch('monev/{monev:id}', 'update');
        Route::delete('monev/{monev:id}', 'delete');
    });  

    Route::controller(NoteController::class)->group(function() {
        Route::post('note', 'store');
        Route::patch('note/{note:id}', 'update');
        Route::delete('note/{note:id}', 'delete');
    });  
});

Route::controller(ParamController::class)->group(function () {
    Route::get('param/execution_unit', 'execution_unit');
    Route::get('param/unit', 'unit');
    Route::get('param/unit_dropdown', 'unit_dropdown');
    Route::get('param/deputi_dropdown', 'deputi_dropdown');
    Route::get('param/asdep_dropdown', 'asdep_dropdown');
    Route::get('param/priority_program', 'priority_program');
    Route::get('param/note_unit', 'note_unit');
    Route::get('param/participant', 'participant');
    Route::get('param/padanan_data_category', 'padanan_data_category');
    Route::get('param/padanan_data_source', 'padanan_data_source');
});

Route::get('/ministry', [MinistryController::class, 'index']);

Route::prefix('realization')->group(function () {
    Route::get('/total', [RealizationController::class, 'total']);
    Route::get('/total_by_periode', [RealizationController::class, 'total_by_periode']); 
    Route::get('/all', [RealizationController::class, 'all']);
    
    Route::post('/', [RealizationController::class, 'create']); 
    Route::get('/{id}', [RealizationController::class, 'show']);
    Route::patch('/{id}', [RealizationController::class, 'update']); 
    Route::delete('/{id}', [RealizationController::class, 'destroy']); 
});

// Import route (terpisah karena di dalam auth middleware)
Route::middleware(['auth:api'])->group(function() {
    Route::post('/realization/import', [RealizationController::class, 'store']);
});
Route::get('/honor', [HonorController::class, 'index']);

Route::get('/depkop/cooperative_summary', [DepkopController::class, 'cooperative_summary']);
Route::get('/depkop/rekap_jenis_koperasi', [DepkopController::class, 'rekap_jenis_koperasi']);
Route::get('/depkop/rekap_bentuk_koperasi', [DepkopController::class, 'rekap_bentuk_koperasi']);
Route::get('/depkop/rekap_pengesahan', [DepkopController::class, 'rekap_pengesahan']);
Route::get('/depkop/parameter_date', [DepkopController::class, 'parameter_date']);

Route::get('employee_recap', [RecapController::class, 'index']);
Route::post('employee_recap/upsert', [RecapController::class, 'update_insert']);

Route::get('employee_retire', [RetireController::class, 'index']);
Route::post('employee_retire', [RetireController::class, 'store']);
Route::get('employee_retire/{retire:id}', [RetireController::class, 'show']);
Route::patch('employee_retire/{retire:id}', [RetireController::class, 'update']);
Route::delete('employee_retire/{retire:id}', [RetireController::class, 'destroy']);

Route::get('recap_date', [RecapController::class, 'recap_date']);

Route::controller(ProgramActivityController::class)->group(function() {
    Route::get('program_activity', 'index');
    Route::get('program_activity/{program_activity:id}', 'show');
    Route::get('program_activity/statistics/total', 'total');
});  

Route::controller(MonevController::class)->group(function() {
    Route::get('monev', 'index');
    Route::get('monev/{monev:id}', 'show');
});  

Route::controller(NoteController::class)->group(function() {
    Route::get('note', 'index');
    Route::get('note/{note:id}', 'show');
});  

Route::controller(PriorityScaleController::class)->group(function () {
    Route::get('/priority_scale', 'index');
    Route::post('/priority_scale/upsert', 'upsert');
});

Route::controller(PadananDataController::class)->group(function () {
    Route::get('/padanan_data', 'index');
    Route::post('/padanan_data', 'store');
    Route::get('/padanan_data/{padanan_data}', 'show');
    Route::put('/padanan_data/{padanan_data}', 'update');
    Route::delete('/padanan_data/{padanan_data}', 'destroy');
});