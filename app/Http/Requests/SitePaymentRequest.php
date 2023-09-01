<?php

namespace App\Http\Requests;

use App\Models\Site\SitePayment;
use Illuminate\Foundation\Http\FormRequest;

class SitePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */


     protected $site_payment= null;
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [];
        if ($this->site_designation['ot_applicable'] == 'Y') {
            $rules += [
                'site_designation.ot_hours' => 'required',
                'site_designation.ot_26day'=>'required',
                'site_designation.ot_calc' => 'required_without:ot_rate',
                'site_designation.ot_rate' => 'required_without:ot_calc',
                'site_designation.esi_ot' => 'required',
                'site_designation.esi_ot2' => 'required',
            ];
        }

        if ($this->site_designation['ag_comm_applicable'] == 'Y') {
            $rules += [
                'site_designation.ag_comm_type' => 'required',
                'site_designation.ag_comm_ot_type' => 'required',
                'site_designation.ag_comm_amt' => 'nullable|numeric|required_if:ag_comm_type,A,ag_comm_type,M,ag_comm_type,V',
                'site_designation.ag_comm_per' => 'nullable|numeric|required_if:ag_comm_type,P|max:100',
                'site_designation.ag_comm_on' => 'required_if:ag_comm_type,P',
                'site_designation.ag_comm_ot_per' => 'nullable|numeric|required_if:ag_comm_ot_type,P|max:100',
                'site_designation.ag_comm_amt' => 'nullable|numeric|required_if:ag_comm_ot_type,A,ag_comm_ot_type,M,ag_comm_ot_type,V',
            ];
        }
        if ($this->bonus_applicable == 'Y') {
            $rules += [
                'formula_id_bonus' => 'required|integer|not_in:0',
                'bonus_prec' => 'required|numeric',
                'formula_id_bonus_days' => 'required|integer|not_in:0'
            ];
        }
        $rules += [
            'site_id' =>'required|exists:sites,id|unique:site_payments,site_id,'.$this->form_id.',id,designation_id,' . $this->designation_id,
            'designation_id' =>'required|exists:designations,id',
            'basic' => 'required|numeric|min:100',
            'site_pay_allowances.*.allowance_amt' => 'numeric|min:0',
            'site_pay_deductions.*.deduction_amt' => 'numeric|min:0',
        ];

        return $rules;

    }

    public function messages() {

        return [
            'site_id' => 'Check this site!',
            'site_id.unique' => 'Structure for this site & designation is already defined!',
            'site_pay_allowances.*.allowance_amt.numeric' => 'The amount must be a number',
            'site_pay_allowances.*.allowance_amt.min' => 'The amount must be at least 0.',
            'site_pay_deductions.*.deduction_amt.numeric' => 'The amount must be a number',
            'site_pay_deductions.*.deduction_amt.min' => 'The amount must be at least 0.',
            'site_designation.ot_hours.required' => 'OT Hours Must Be Filled.',
            'site_designation.ot_calc.required_without' => 'Either OT Calc Or OT Rate Must be Filled',
            'site_designation.ot_rate.required_without' => 'Either  OT Rate OT Calc Must be Filled',
            'site_designation.esi_ot.required' => 'ESI OT Must Be Filled.',
            'site_designation.esi_ot2.required' => 'ESI OT2 Must Be Filled.',
            'site_designation.ag_comm_type.required' => 'Agency Commision Type Must Be Filled.',
            'site_designation.ag_comm_ot_type.required' => 'Agency Commision Type Must Be Filled.',
            'site_designation.ag_comm_on.required_if' => 'Agency Commision On Must Be Filled.',
            'site_designation.ag_comm_per.required_if' => 'Enter Agency Commision Rate.',
            'site_designation.ag_comm_ot_per.required_if' => 'Enter Agency Commision OT Rate.',
            'site_designation.ag_comm_amt.required_if' => 'Enter Agency Commision Amount.',
            'site_designation.ag_comm_ot_amt.required_if' => 'Enter Agency Commision OT Amount.',
            'formula_id_bonus.required' => ' Bonus setting is required .',
            'bonus_prec.required' => ' Bonus percentage is required .',
            'formula_id_bonus_days.required' => ' Bonus setting days is required .',
        ];
      }

    public function save()
    {

        $this->site_payment = SitePayment::updateStructure($this);
        return reply(true,[

        ]);
    }
}
