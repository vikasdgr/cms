<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Services\MachineCase;
use App\Models\Services\MachineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class MachineCaseController extends Controller
{
    public function index(){
        if (Gate::denies('schedule-services')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Case/CaseList', [
        ]);
    }

    public function servicesList(Request $request){
        if (Gate::denies('schedule-services')) {
            return deny();
        }
        $count = MachineCase::count();
        $filteredCount = $count;

        $cases = MachineCase::leftJoin('machines', 'cases.machine_id', '=', 'machines.id')
        ->leftJoin('machine_models', 'machines.model_id', '=', 'machine_models.id')
        ->leftJoin('brands', 'machines.brand_id', '=', 'brands.id')
        ->leftJoin('machine_types', 'machines.machine_type_id', '=', 'machine_types.id')
        ->leftJoin('departments', 'machines.department_id', '=', 'departments.id')
        ->leftJoin('areas', 'machines.area_id', '=', 'areas.id');


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
            $cases = $cases->orderBy('id','DESC');
        }


        if($request->model_id > 0){
            $cases = $cases->where('machines.model_id', $request->model_id);
        }
        if($request->status != 'A'){
            $cases = $cases->where('cases.status', $request->status);
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

        // if($request->from_date !=''){
        //     $cases = $cases->where('machines.model_id', $request->model_id);
        // }
        $cases = $cases->take($request->length);
        $filteredCount = $cases->count();
        if ($request->start > 0) {
            $cases->skip($request->start);
        }


        $cases = $cases->groupBy('cases.id')->select(['cases.*','brands.name as brand_name',
        'machine_models.model_no',
        'machine_types.name as machine_type','departments.name as department_name','areas.name as area','machines.serial_no',
        ])->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $cases,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('schedule-services-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {

        $this->checkOpenScheduledService($request);

        $this->validateForm($request, $id);
        $services = MachineCase::findOrNew($request->form_id);
        $services->fill($request->all());

        DB::beginTransaction();
        if($request->form_id == 0){
            $services->status = 'P';
            $services->generated_user_id = auth()->user()->id;
            $no_generate =  'CS -' . getNextSeriesNumber('machine_case');
            $services['case_no']  = $no_generate;
        }
        $services->save();
        DB::commit();
        return reply(true, [
            'services' => $services
        ]);
    }

    private function validateForm(Request $request, $id)
    {

        $rules = [];
        $rules += [
            'machine_id' => 'required|exists:machines,id',
            'open_date' => 'required|date_format:d-m-Y',
            'work_types' => 'required|in:Maintenance,Installation,BreakDown',
            'work_order_types' => 'required|in:Repair,General Service,Installation',
            'description' => 'nullable|max:1000',
        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('schedule-services-modify')) {
            return deny();
        }
        $service = MachineCase::whereId($id)->with(['machine','machine.machine_type','machine.machine_model','machine.department','machine.area'])->first();
        return reply(true, [
            'service' => $service,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('schedule-services-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

    private function checkOpenScheduledService($request){
        $service = MachineService::where('case_id', $request->form_id)->first();
        $case = MachineCase::where('machine_id',$request->machine_id)->whereIn('status',['F','P'])->first();
        if(($case && !$service &&  $request->form_id == 0)|| $service){
            throw ValidationException::withMessages(['gen_msg' => 'Previous service request is already pending, so you cannot make a new request at the moment.']);
        }

    }
}
