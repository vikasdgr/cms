<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Attachment\SharedResource;
use App\Models\Masters\Brand;
use App\Models\Masters\MachineModel;
use App\Models\Masters\MachineType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MachineModelController extends Controller
{
    public function index(){
        if (Gate::denies('machine-models')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/MachineModel/MachineModelList', [
        ]);
    }

    public function machineModelList (Request $request){
        if (Gate::denies('machine-models')) {
            return deny();
        }
        $count = MachineModel::count();
        $filteredCount = $count;

        $machine_model = MachineModel::leftJoin('brands', 'machine_models.brand_id', '=', 'brands.id')
        ->leftJoin('machine_types', 'machine_models.machine_type_id', '=', 'machine_types.id');

        if ($searchStr = $request->input('search.value')) {
            $machine_model = $machine_model->where(function ($query) use ($searchStr) {
                $query->orWhere('brands.name', 'LIKE', '%' . $searchStr . '%')
                ->orWhere('machine_models.model_no', 'LIKE', '%' . $searchStr . '%')
                ->orWhere('machine_types.name', 'LIKE', '%' . $searchStr . '%');
            });
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $machine_model = $machine_model->orderBy($field_name, $asc_desc);
        }
        else{
            $machine_model = $machine_model->orderBy('id','DESC');

        }

        $machine_model = $machine_model->take($request->length);
        $filteredCount = $machine_model->count();
        if ($request->start > 0) {
            $machine_model->skip($request->start);
        }
        $machine_model = $machine_model->select(['machine_models.*','brands.name as brand_name',
        'machine_types.name as machine_type'
        ])->with('resources')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $machine_model,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('machine-models-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $machine_model = MachineModel::findOrNew($request->form_id);
        $machine_model->fill($request->all());
        $old_ids = $machine_model->resources()->pluck('id')->toArray();

        $resources = new Collection();
        foreach($request->resources as $resource){
            $resource_d = SharedResource::findOrNew($resource['id']);
            $resource_d->resourceable_type = MachineModel::class;
            $resource_d->fill($resource);
            $resources->add($resource_d);
        }

        $new_ids = $resources->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);

        DB::beginTransaction();
            $machine_model->save();
            $machine_model->resources()->saveMany($resources);
            SharedResource::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true, [
            'machine_model' => $machine_model
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'model_no' => 'required|string|max:100|unique:machine_models,model_no,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('machine-models-modify')) {
            return deny();
        }
        $machine_model = MachineModel::whereId($id)->with(['brand','machine_type','resources'])->first();
        return reply(true, [
            'machine_model' => $machine_model,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('machine-models-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

}
