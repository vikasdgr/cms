<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Services\MachineCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CaseReportController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('report') && $request->has('report') ==true){
            $count = MachineCase::count();
            $filteredCount = $count;

            $cases = MachineCase::leftJoin('machines', 'cases.machine_id', '=', 'machines.id')
            ->leftJoin('machine_models', 'machines.model_id', '=', 'machine_models.id')
            ->leftJoin('brands', 'machines.brand_id', '=', 'brands.id')
            ->leftJoin('machine_types', 'machines.machine_type_id', '=', 'machine_types.id')
            ->leftJoin('departments', 'machines.department_id', '=', 'departments.id')
            ->leftJoin('areas', 'machines.area_id', '=', 'areas.id')
            ->leftJoin("services", "services.case_id", '=', 'cases.id');


            if ($searchStr = $request->input('search.value')) {
                $cases = $cases->where(function ($query) use ($searchStr) {
                    $query->where('cases.name_code_no', 'LIKE', '%' . $searchStr . '%')
                        ->where('cases.serial_no', 'LIKE', '%' . $searchStr . '%')
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
                $cases = $cases->orderBy($field_name, $asc_desc);
            }
            else{
                $cases = $cases->orderBy('cases.open_date','DESC');
            }


            if($request->model_id > 0){
                $cases = $cases->where('machines.model_id', $request->model_id);
            }
            if($request->machine_type_id > 0){
                $cases = $cases->where('machines.machine_type_id', $request->machine_type_id);
            }
            if($request->department_id > 0){
                $cases = $cases->where('machines.department_id', $request->department_id);
            }

            if ($request->from_date && $request->to_date) {
                $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
                $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
                $cases = $cases->whereBetween('cases.open_date', [$fromDate, $toDate]);
            } elseif ($request->from_date) {
                $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');

                $cases = $cases->where('cases.open_date', '>=', $fromDate);
            } elseif ($request->to_date) {
                $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');

                $cases = $cases->where('cases.open_date', '<=', $toDate);
            }

            $cases = $cases->groupBy('cases.id')->select(['cases.*','brands.name as brand_name',
                'machine_models.model_no',
                DB::raw("SUM(CASE when isnull(services.id) then 0 else 1 end) as no_services"),
                'machine_types.name as machine_type','departments.name as department_name','areas.name as area','machines.serial_no',
            ]);



            $cases = getQryObject($cases,'a1','Y');
            $cases = $cases->select('a1.*');
            $count = $cases->count();
            $cases = $cases->take($request->length);
            if ($request->start > 0) {
                $cases->skip($request->start);
            }
            $filteredCount = $count;

            $cases=$cases->get();
            return [
                'draw' => intval($request->draw),
                'start' => $request->start,
                'data' => $cases,
                'recordsTotal' => $count,
                'recordsFiltered' => $filteredCount,
            ];
        }
        return Inertia::render('ProjectComponents/Reports/Case/CaseReport', [
        ]);
    }
}
