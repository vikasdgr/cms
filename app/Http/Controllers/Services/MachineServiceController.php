<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\MachineServiceRequest;
use App\Models\Services\MachineCase;
use App\Models\Services\MachineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;

class MachineServiceController extends Controller
{
    public function show($case_id){
        if (Gate::denies('services-modify')) {
            return deny();
        }
        $case = MachineCase::findOrFail($case_id);
        $case->load([
            'machine.department',
            'machine.brand',
            'machine.machine_model.machine_image',
            'machine.machine_type',
            'machine.area',
        ]);
        return Inertia::render('ProjectComponents/Services/ServiceFormOuter', [
            'case' => $case
        ]);
    }

    public function store(MachineServiceRequest $request){
        if (Gate::denies('services-modify')) {
            return deny();
        }
        return $request->save();
    }

    public function showServices($case_id){
        if (Gate::denies('services')) {
            return deny();
        }
        $case = MachineCase::findOrFail($case_id);
        $case->load([
            'machine.department',
            'machine.brand',
            'machine.machine_model.machine_image',
            'machine.machine_type',
            'machine.area',
        ]);

        $services = MachineService::where('case_id', $case_id)
            ->orderBy(DB::raw('ISNULL(parent_service_id), parent_service_id'), 'asc')
            ->orderBy('created_at', 'asc')
            ->with(
                'service_maintenances.maintenance',
                'service_problems.problem',
                'service_resolving_actions.resolving_action'
            )
            ->get();
        return Inertia::render('ProjectComponents/Services/ServicesList', [
            'case' => $case,
            'services'=>$services
        ]);
    }
}
