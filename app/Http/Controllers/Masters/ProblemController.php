<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;


class ProblemController extends Controller
{
    public function index()
    {
        if (Gate::denies('problems')) {
            return deny();
        }

        return Inertia::render('ProjectComponents/Masters/Problem/ProblemList', [
            // You can pass any necessary data to the view
        ]);
    }

    public function problemsList(Request $request)
    {
        if (Gate::denies('problems')) {
            return deny();
        }

        $count = Problem::count();
        $filteredCount = $count;

        $problems = Problem::where('id', '!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $problems = $problems->where('name', 'like', "%{$searchStr}%");
        }

        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];

        if ($field_name) {
            $problems = $problems->orderBy($field_name, $asc_desc);
        } else {
            $problems = $problems->orderBy('id', 'DESC');
        }

        $problems = $problems->skip($request->start)->take($request->length)->get(['id', 'name','description']);
        $filteredCount = $problems->count();

        return [
            'draw' => intval($request->draw),
            'start' => $request->start,
            'data' => $problems,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('problems-modify')) {
            return deny();
        }

        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);

        $problem = Problem::findOrNew($request->form_id);
        $problem->fill($request->all());
        $problem->save();

        return reply(true, [
            'problem' => $problem
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:100|unique:problems,name,' . $request->form_id,
            // Add any other validation rules as needed
        ];

        $this->validate($request, $rules);
    }

    public function edit($id)
    {
        if (Gate::denies('problems-modify')) {
            return deny();
        }

        $problem = Problem::whereId($id)->first();

        return reply(true, [
            'problem' => $problem,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('problems-modify')) {
            return deny();
        }

        return $this->saveForm($request, $id);
    }
}
