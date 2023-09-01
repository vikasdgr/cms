<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Machine\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MachineWiseReportController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('report') && $request->has('report') ==true){
            $count = Machine::count();
            $filteredCount = $count;

            $machines = Machine::leftJoin('brands', 'machines.brand_id', '=', 'brands.id')
            ->leftJoin('machine_models', 'machines.model_id', '=', 'machine_models.id')
            ->leftJoin('machine_types', 'machines.machine_type_id', '=', 'machine_types.id')
            ->leftJoin('departments', 'machines.department_id', '=', 'departments.id')
            ->leftJoin('areas', 'machines.area_id', '=', 'areas.id')
            ->leftJoin("cases", "cases.machine_id", '=', 'machines.id')
            ->leftJoin("services", "services.case_id", '=', 'cases.id');

            if ($searchStr = $request->input('search.value')) {
                $machines = $machines->where(function ($query) use ($searchStr) {
                    $query->where('machines.name_code_no', 'LIKE', '%' . $searchStr . '%')
                        ->where('machines.serial_no', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('brands.name', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('machine_models.model_no', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('machine_types.name', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('departments.name', 'LIKE', '%' . $searchStr . '%')
                        ->orWhere('areas.name', 'LIKE', '%' . $searchStr . '%');
                });
            }
            $asc_desc = $request->order[0]['dir'];
            $field_name = $request->columns[$request->order[0]['column']]['data'];
            if($field_name){
                $machines = $machines->orderBy($field_name, $asc_desc);
            }
            else{
                $machines = $machines->orderBy('id','DESC');
            }

            if($request->brand_id > 0){
                $machines = $machines->where('machines.brand_id', $request->brand_id);
            }
            if($request->model_id > 0){
                $machines = $machines->where('machines.model_id', $request->model_id);
            }
            if($request->machine_type_id > 0){
                $machines = $machines->where('machines.machine_type_id', $request->machine_type_id);
            }
            if($request->department_id > 0){
                $machines = $machines->where('machines.department_id', $request->department_id);
            }

            if($request->area_id > 0){
                $machines = $machines->where('machines.area_id', $request->area_id);
            }

            $machines = $machines->select(['machines.*','brands.name as brand_name',
            'machine_models.model_no',
            'machine_types.name as machine_type','departments.name as department_name','areas.name as area',
            DB::raw("SUM(CASE when isnull(cases.id) then 0 else 1 end) as no_cases"),
            DB::raw("SUM(CASE when isnull(services.id) then 0 else 1 end) as no_services"),

            ])->groupBy('machines.id');


            $machines = getQryObject($machines,'a1','Y');
            $machines = $machines->select('a1.*');
            $count = $machines->count();
            $machines = $machines->take($request->length);
            if ($request->start > 0) {
                $machines->skip($request->start);
            }
            $filteredCount = $count;

            $machines=$machines->get();
            return [
                'draw' => intval($request->draw),
                'start' => $request->start,
                'data' => $machines,
                'recordsTotal' => $count,
                'recordsFiltered' => $filteredCount,
            ];

        }
        return Inertia::render('ProjectComponents/Reports/Machine/MachineReport');
    }
}
