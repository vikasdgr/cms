<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Area;
use App\Models\Masters\City;
use App\Models\Masters\Department;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AreaController extends Controller
{
    public function index(){
        if (Gate::denies('areas')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Area/AreaList', [
            'departments' => Department::all()->map(function ($department) {
                return [
                    'id' => $department->id,
                    'text' => $department->name,
                ];
            }),
        ]);
    }

    public function areasList (Request $request){
        if (Gate::denies('areas')) {
            return deny();
        }
        $count = Area::count();
        $filteredCount = $count;

        $areas = Area::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $areas = $areas->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $areas = $areas->orderBy($field_name, $asc_desc);
        }
        else{
            $areas = $areas->orderBy('id','DESC');

        }

        $areas = $areas->take($request->length);
        $filteredCount = $areas->count();
        if ($request->start > 0) {
            $areas->skip($request->start);
        }
        $areas = $areas->select()->with('department','area_user')->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $areas,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('areas-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $area = Area::findOrNew($request->form_id);
        $area->fill($request->all());
        $area->save();

        return reply(true, [
            'area' => $area
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:areas,name,' . $request->form_id,
            'department_id' => 'required|exists:departments,id',
            'person_contact_no' =>'nullable|numeric|digits:10'
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('areas-modify')) {
            return deny();
        }
        $area = Area::whereId($id)->first()->load(['department','area_user']);
        return reply(true, [
            'area' => $area,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('areas-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

}
