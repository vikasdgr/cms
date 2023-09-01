<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index()
    {
        if (Gate::denies('maintenances')) {
            return deny();
        }

        return Inertia::render('ProjectComponents/Masters/Maintenance/MaintenanceList', [
            // You can pass any necessary data to the view
        ]);
    }

    public function maintenancesList(Request $request)
    {
        if (Gate::denies('maintenances')) {
            return deny();
        }

        $count = Maintenance::count();
        $filteredCount = $count;

        $maintenances = Maintenance::where('id', '!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $maintenances = $maintenances->where('name', 'like', "%{$searchStr}%");
        }

        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];

        if ($field_name) {
            $maintenances = $maintenances->orderBy($field_name, $asc_desc);
        } else {
            $maintenances = $maintenances->orderBy('id', 'DESC');
        }

        $maintenances = $maintenances->skip($request->start)->take($request->length)->get(['id', 'name','description']);
        $filteredCount = $maintenances->count();

        return [
            'draw' => intval($request->draw),
            'start' => $request->start,
            'data' => $maintenances,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('maintenances-modify')) {
            return deny();
        }

        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);

        $maintenance = Maintenance::findOrNew($request->form_id);
        $maintenance->fill($request->all());
        $maintenance->save();

        return reply(true, [
            'maintenance' => $maintenance
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:100|unique:maintenances,name,' . $request->form_id,
            // Add any other validation rules as needed
        ];

        $this->validate($request, $rules);
    }

    public function edit($id)
    {
        if (Gate::denies('maintenances-modify')) {
            return deny();
        }

        $maintenance = Maintenance::whereId($id)->first();

        return reply(true, [
            'maintenance' => $maintenance,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('maintenances-modify')) {
            return deny();
        }

        return $this->saveForm($request, $id);
    }
}
