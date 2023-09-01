<?php


use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Masters\DepartmentController;
use App\Http\Controllers\Attachment\SharedAttachmentController;
use App\Http\Controllers\Machines\MachineController;
use App\Http\Controllers\Masters\AreaController;
use App\Http\Controllers\Masters\BrandController;
use App\Http\Controllers\Masters\MachineModelController;
use App\Http\Controllers\Masters\MachineTypeController;
use App\Http\Controllers\Masters\MaintenanceController;
use App\Http\Controllers\Masters\ProblemController;
use App\Http\Controllers\Masters\ResolvingActionController;
use App\Http\Controllers\Other\MultiPurposeController;
use App\Http\Controllers\Other\SelectController;
use App\Http\Controllers\Reports\CaseReportController;
use App\Http\Controllers\Reports\MachineSummaryReportController;
use App\Http\Controllers\Reports\MachineWiseReportController;
use App\Http\Controllers\Reports\ServiceReportController;
use App\Http\Controllers\Services\MachineCaseController;
use App\Http\Controllers\Services\MachineServiceController;
use App\Http\Controllers\Users\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return Inertia::render('Dashboard',[
            'csrf' =>csrf_token()        // TO RESOLVE SELECT CSRF ISSUE
        ]);
    } else {
        return Inertia::render('Auth/Login');
    }
})->middleware(['auth']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


// roles
Route::get('roles/{role_id}/permissions', [RoleController::class, 'showPermissions']);
Route::post('roles/{role_id}/permissions', [RoleController::class, 'savePermissions']);

Route::get('users/active-deactive', [UserController::class, 'activeDeactive']);

Route::get('users/{userid}/locations', [UserController::class, 'locations']);
Route::post('users/addlocations', [UserController::class, 'addLocations']);
Route::post('users/rmvlocations', [UserController::class, 'removeLocations']);

Route::get('users/{userid}/departments', [UserController::class, 'departments']);
Route::post('user-departments', [UserController::class, 'UserDepartments']);
Route::get('get-departments',[MultipurposeController::class,'getDepartments']);
Route::get('machine-details/{machine_id}',[MultipurposeController::class,'getMachineDetails']);

Route::get('users-list', [UserController::class, 'userslist']);
Route::resource('users', UserController::class, [
    'names' => [
        'index' => 'users',
    ]
]);
Route::get('roles-list', [RoleController::class, 'roleslist']);
Route::resource('roles', RoleController::class, [
    'names' => [
        'index' => 'roles'
    ]
]);

//Select
Route::post('departments/filtered', [SelectController::class, 'departmentsFiltered']); //CMS
Route::post('roles/filtered',[SelectController::class,'RoleFiltered']);
Route::post('department-users/filtered',[SelectController::class,'departmentUserFiltered']);
Route::post('machine-types/filtered',[SelectController::class,'machineTypesFiltered']);
Route::post('machine-model/filtered',[SelectController::class,'machineModelsFiltered']);
Route::post('areas/filtered',[SelectController::class,'areasFiltered']);
Route::post('brands/filtered',[SelectController::class,'brandsFiltered']);
Route::post('problems/filtered',[SelectController::class,'problemsFiltered']);
Route::post('resolving-actions/filtered',[SelectController::class,'resolvingActionsFiltered']);
Route::post('maintenances/filtered',[SelectController::class,'maintenancesFiltered']);
Route::post('machines/filtered',[SelectController::class,'machinesFiltered']);

//End Selec

// Route::get('categories-list', [CategoryController::class, 'categoriesList']);
Route::get('departments-list', [DepartmentController::class, 'departmentsList']);
Route::get('areas-list', [AreaController::class, 'areasList']);
Route::get('machine-types-list', [MachineTypeController::class, 'machineTypeList']);
Route::get('machine-models-list', [MachineModelController::class, 'machineModelList']);
Route::get('problems-list', [ProblemController::class, 'problemsList']);
Route::get('maintenances-list', [MaintenanceController::class, 'maintenancesList']);
Route::get('resolving-actions-list', [ResolvingActionController::class, 'resolvingActionList']);
Route::get('brands-list', [BrandController::class, 'brandsList']);
Route::get('machines-list', [MachineController::class, 'machinesList']);
Route::get('machine-cases-list', [MachineCaseController::class, 'servicesList']);
Route::post('preferences', [UserController::class,'savePreferences']);

//Multipurpose
Route::delete('shared-attachments/{id}', [SharedAttachmentController::class, 'deleteAttachment']);
Route::get('shared-attachments-thumbnail/{id}', [SharedAttachmentController::class, 'getAttachmentThumbnail']);
Route::get('services-details/{id}',[MachineServiceController::class,'showServices']);
Route::post('change-department',[MultiPurposeController::class,'changeDepartment']);
Route::get('service-print/{service_id}',[MultiPurposeController::class,'servicePrint']);
Route::get('dashboard-data',[MultiPurposeController::class,'getMachinesData']);

Route::resources([
    'shared-attachments'=> SharedAttachmentController::class,
    'users' => UserController::class,
    'roles' => RoleController::class,
    'departments' => DepartmentController::class,
    'areas' => AreaController::class,
    'machine-types' =>MachineTypeController::class,
    'machine-models' =>MachineModelController::class,
    'problems' => ProblemController::class,
    'maintenances' =>MaintenanceController::class,
    'resolving-actions'=>ResolvingActionController::class,
    'brands' => BrandController::class,
    'machines' => MachineController::class,
    'machine-cases'=>MachineCaseController::class,
    'services'=> MachineServiceController::class,
    'cases-report'=> CaseReportController::class,
    'machines-report'=> MachineWiseReportController::class,
    'services-report'=> ServiceReportController::class,
    'machines-summary-report'=> MachineSummaryReportController::class,
]);
// Start Normal Reports //


