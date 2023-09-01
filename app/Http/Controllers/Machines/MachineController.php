<?php

namespace App\Http\Controllers\Machines;

use App\Http\Controllers\Controller;
use App\Models\Machine\Machine;
use App\Models\Masters\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MachineController extends Controller
{
    public function index(){
        if (Gate::denies('machines')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Machines/Machine/MachineList', [
        ]);
    }

    public function machinesList(Request $request){
        if (Gate::denies('machines')) {
            return deny();
        }
        $count = Machine::count();
        $filteredCount = $count;

        $machines = Machine::leftJoin('brands', 'machines.brand_id', '=', 'brands.id')
        ->leftJoin('machine_models', 'machines.model_id', '=', 'machine_models.id')
        ->leftJoin('machine_types', 'machines.machine_type_id', '=', 'machine_types.id')
        ->leftJoin('departments', 'machines.department_id', '=', 'departments.id')
        ->leftJoin('areas', 'machines.area_id', '=', 'areas.id');


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
        $machines = $machines->take($request->length);
        $filteredCount = $machines->count();
        if ($request->start > 0) {
            $machines->skip($request->start);
        }
        $machines = $machines->select(['machines.*','brands.name as brand_name',
        'machine_models.model_no',
        'machine_types.name as machine_type','departments.name as department_name','areas.name as area'
        ])->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $machines,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('machines-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $machine = Machine::findOrNew($request->form_id);
        $machine->fill($request->all());

        DB::beginTransaction();
        if($request->form_id == 0){
            $machine_brand = Brand::findOrFail($request->brand_id);
            $no_generate = $machine_brand['name'].'-' . getNextSeriesNumber('machines');
            // dd($machine_brand['name'] .'-'. getNextSeriesNumber('machines'));
            $machine['name_code_no']  = $no_generate;
        }
        $machine->save();
        DB::commit();

        return reply(true, [
            'machine' => $machine
        ]);
    }

    private function validateForm(Request $request, $id)
    {

        $rules = [];
        $rules += [
            'serial_no' => 'required|string|max:100|unique:machines,serial_no,' . $request->form_id,
            'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:machine_models,id',
            'machine_type_id' => 'required|exists:machine_types,id',
            'department_id' => 'required|exists:departments,id',
            'buy_date'=>'nullable|date_format:d-m-Y',
            'installation_date'=>'nullable|date_format:d-m-Y',
            'warranty_upto_date'=>'nullable|date_format:d-m-Y'
        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('machines-modify')) {
            return deny();
        }
        $machine = Machine::whereId($id)->with(['brand','machine_type','machine_model','department','area'])->first();
        return reply(true, [
            'machine' => $machine,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('machines-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
