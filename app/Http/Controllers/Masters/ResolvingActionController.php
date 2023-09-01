<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\ResolvingAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ResolvingActionController extends Controller
{
    public function index()
    {
        if (Gate::denies('resolving-actions')) {
            return deny();
        }

        return Inertia::render('ProjectComponents/Masters/ResolvingAction/ResolvingActionList', [
            // You can pass any necessary data to the view
        ]);
    }

    public function resolvingActionList(Request $request)
    {
        if (Gate::denies('resolving-actions')) {
            return deny();
        }

        $count = ResolvingAction::count();
        $filteredCount = $count;

        $resolvingActions = ResolvingAction::where('id', '!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $resolvingActions = $resolvingActions->where('name', 'like', "%{$searchStr}%");
        }

        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];

        if ($field_name) {
            $resolvingActions = $resolvingActions->orderBy($field_name, $asc_desc);
        } else {
            $resolvingActions = $resolvingActions->orderBy('id', 'DESC');
        }

        $resolvingActions = $resolvingActions->skip($request->start)->take($request->length)->get(['id', 'name','description']);
        $filteredCount = $resolvingActions->count();

        return [
            'draw' => intval($request->draw),
            'start' => $request->start,
            'data' => $resolvingActions,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('resolving-actions-modify')) {
            return deny();
        }

        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);

        $resolvingAction = ResolvingAction::findOrNew($request->form_id);
        $resolvingAction->fill($request->all());
        $resolvingAction->save();

        return reply(true, [
            'resolvingAction' => $resolvingAction
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:100|unique:resolving_actions,name,' . $request->form_id,
            // Add any other validation rules as needed
        ];

        $this->validate($request, $rules);
    }

    public function edit($id)
    {
        if (Gate::denies('resolving-actions-modify')) {
            return deny();
        }

        $resolvingAction = ResolvingAction::whereId($id)->first();

        return reply(true, [
            'resolvingAction' => $resolvingAction,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('resolving-actions-modify')) {
            return deny();
        }

        return $this->saveForm($request, $id);
    }
}
