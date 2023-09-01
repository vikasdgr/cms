<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Services\MachineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServiceReportController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('report') && $request->has('report') ==true){
            $count = MachineService::count();
            $filteredCount = $count;

            $services = MachineService::leftJoin("cases", "cases.id", '=', 'services.case_id')
                    ->leftJoin('machines', 'machines.id', '=', 'cases.machine_id')
                    ->leftJoin('machine_models', 'machine_models.id', '=', 'machines.model_id')
                    ->leftJoin('machine_types', 'machine_types.id', '=', 'machines.machine_type_id')
                    ->leftJoin('brands', 'brands.id', '=', 'machines.brand_id')
                    ->leftJoin('service_captured_problems', 'service_captured_problems.service_id', '=', 'services.id')
                    ->leftJoin('departments', 'machines.department_id', '=', 'departments.id')
                    ->leftJoin('services as closed_service', function ($join) {
                        $join->on('closed_service.case_id', '=', 'services.case_id')
                             ->where('closed_service.status', 'C');
                    });

            if ($searchStr = $request->input('search.value')) {
                $services = $services->where(function ($query) use ($searchStr) {
                    $query->where('machines.name_code_no', 'LIKE', '%' . $searchStr . '%')
                        ->where('machines.serial_no', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('brands.name', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('machine_models.model_no', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('machine_types.name', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('departments.name', 'LIKE', '%' . $searchStr . '%');
                });
            }
            $asc_desc = $request->order[0]['dir'];
            $field_name = $request->columns[$request->order[0]['column']]['data'];
            if($field_name){
                $services = $services->orderBy($field_name, $asc_desc);
            }
            else{
                $services = $services->orderBy('id','DESC');
            }

            if($request->model_id > 0){
                $services = $services->where('machines.model_id', $request->model_id);
            }
            if($request->machine_type_id > 0){
                $services = $services->where('machines.machine_type_id', $request->machine_type_id);
            }
            if($request->department_id > 0){
                $services = $services->where('machines.department_id', $request->department_id);
            }

            $services = $services->select([
                'services.*',
                'brands.name as brand_name',
                'cases.case_no',
                'machine_models.model_no',
                'machine_types.name as machine_type',
                'departments.name as department_name',
                'closed_service.service_no as closed_service_no',
                DB::raw("SUM(CASE when isnull(service_captured_problems.id) then 0 else 1 end) as no_issues")
            ])
            ->groupBy(
                'services.id',
                'brands.name',
                'cases.case_no',
                'machine_models.model_no',
                'machine_types.name',
                'departments.name',
                'closed_service.service_no'
            );

            // $services = $services->select(['services.*','brands.name as brand_name','cases.case_no',
            //     'machine_models.model_no',
            //     'machine_types.name as machine_type','departments.name as department_name',
            //     'closed_service.service_no as closed_service_no',
            //     DB::raw("SUM(CASE when isnull(service_captured_problems.id) then 0 else 1 end) as no_issues"),
            // ])->groupBy('services.id');

            $services = getQryObject($services,'a1','Y');
            $services = $services->select('a1.*');
            $count = $services->count();
            $services = $services->take($request->length);
            if ($request->start > 0) {
                $services->skip($request->start);
            }
            $filteredCount = $count;

            $services=$services->get();
            return [
                'draw' => intval($request->draw),
                'start' => $request->start,
                'data' => $services,
                'recordsTotal' => $count,
                'recordsFiltered' => $filteredCount,
            ];

        }
        return Inertia::render('ProjectComponents/Reports/Service/ServiceReport');
    }
}
