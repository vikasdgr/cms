<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Brand;
use App\Models\Masters\MachineModel;
use App\Models\Masters\MachineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MachineTypeController extends Controller
{
    public function index(){
        if (Gate::denies('machine-types')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/MachineType/MachineTypeList', [
        ]);
    }

    public function machineTypeList (Request $request){
        if (Gate::denies('machine-types')) {
            return deny();
        }
        $count = MachineType::count();
        $filteredCount = $count;

        $machine_types = MachineType::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $machine_types = $machine_types->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $machine_types = $machine_types->orderBy($field_name, $asc_desc);
        }
        else{
            $machine_types = $machine_types->orderBy('id','DESC');

        }

        $machine_types = $machine_types->take($request->length);
        $filteredCount = $machine_types->count();
        if ($request->start > 0) {
            $machine_types->skip($request->start);
        }
        $machine_types = $machine_types->select()->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $machine_types,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('machine-types-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $machine_type = MachineType::findOrNew($request->form_id);
        $machine_type->fill($request->all());
        $machine_type->save();

        return reply(true, [
            'machine_type' => $machine_type
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:machine_types,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('machine-types-modify')) {
            return deny();
        }
        $machine_type = MachineType::whereId($id)->first();
        return reply(true, [
            'machine_type' => $machine_type,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('machine-types-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

}
