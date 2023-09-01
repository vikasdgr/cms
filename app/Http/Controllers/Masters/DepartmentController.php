<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index(){
        if (Gate::denies('departments')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Department/DepartmentList', [
            'departments' => Department::all()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            }),
        ]);
    }

    public function departmentsList (Request $request){
        if (Gate::denies('departments')) {
            return deny();
        }
        $count = Department::count();
        $filteredCount = $count;

        $departments = Department::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $departments = $departments->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $departments = $departments->orderBy($field_name, $asc_desc);
        }
        else{
            $departments = $departments->orderBy('id','DESC');

        }

        $departments = $departments->take($request->length);
        $filteredCount = $departments->count();
        if ($request->start > 0) {
            $departments->skip($request->start);
        }
        $departments = $departments->select()->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $departments,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('departments-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $department = Department::findOrNew($request->form_id);
        $department->fill($request->all());
        $department->save();

        return reply(true, [
            'department' => $department
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:departments,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('departments-modify')) {
            return deny();
        }
        $department = Department::whereId($id)->first();
        return reply(true, [
            'department' => $department,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('departments-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

}

