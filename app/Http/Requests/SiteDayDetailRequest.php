<?php

namespace App\Http\Requests;

use App\Models\Site\Site;
use App\Models\Site\SiteDay;
use App\Models\Site\SiteDayDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class SiteDayDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

     protected $site =null;
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'site_day_id' => 'required',
            "g_month_days" => 'numeric',
            'site_day_details.*.emp_id' => 'required|exists:employees,id|min:1',
            "site_day_details.*.totdays" => 'numeric',
            "site_day_details.*.days" => 'numeric',
            "site_day_details.*.wo" => 'numeric',
            "site_day_details.*.leave" => 'numeric',
            "site_day_details.*.hl" => 'numeric',
            "site_day_details.*.cl" => 'numeric',
            "site_day_details.*.pl" => 'numeric',
            "site_day_details.*.ot1" => 'numeric',
            "site_day_details.*.ot2" => 'numeric',
            "site_day_details.*.blend_ext_days" => 'numeric',
        ];

        return $rules;
    }



    public function validateData()
    {
        $rules = [];
        $site_day = SiteDay::findOrFail($this->site_day_id);
        $this->site = Site::find($site_day->site_id);
        $validationErrors = [];

        foreach($this->site_day_details as $key => $site_day_detail){
            $tot_days = floatval($site_day_detail['days']) + floatval($site_day_detail['wo']) + floatval($site_day_detail['leave']) + floatval($site_day_detail['hl']) + floatval($site_day_detail['cl']) + floatval($site_day_detail['pl']) +
            floatval($site_day_detail['blend_ext_days']) + floatval($site_day_detail['leave_joining']) + floatval($site_day_detail['match_days']) + floatval($site_day_detail['leave_termination']);
            if($this->has('s_no') && $this->s_no != ''){
                $key = $this->s_no;
            }
            if ($this->site->ask_month_days == 'N') {
                if (floatval($site_day_detail['totdays']) != $tot_days) {
                    $validationErrors['site_day_details.'.$key .'.totdays'] = 'Total days do not match!!';
                }
                if (floatval($site_day_detail['match_days']) == floatval($site_day_detail->totdays)) {
                    $validationErrors['site_day_details.'.$key .'.totdays'] = 'Match Days cannot be equal to Total Days!!';
                }
            } else {
                if (floatval($this['g_month_days']) != $tot_days) {
                    $validationErrors['site_day_details.'.$key .'.totdays']= 'Month days do not match!!';
                }
                if (floatval($site_day_detail['match_days']) == floatval($this->g_month_days)) {
                    $validationErrors['site_day_details.'.$key .'.totdays']= 'Match Days cannot be equal to Total Days!!';
                }
            }

            if (!empty($validationErrors)) {
                throw ValidationException::withMessages($validationErrors);
            }
        }

    }



    public function save()
    {
        $this->validateData();

        $site_day = SiteDay::findOrFail($this->site_day_id);
        $this->site = Site::find($site_day->site_id);


        $site_day_collection  = new Collection();

        foreach($this->site_day_details as $site_day_detail){
            $site_day_det = SiteDayDetail::firstOrNew([
                'site_day_id' => $site_day['id'],
                'emp_id' => $site_day_detail['emp_id']
            ]);
            $site_day_det->fill($site_day_detail);
            $site_day_det->smonth = $site_day->smonth;
            $site_day_det->syear = $site_day->syear;
            $site_day_det->site_id = $site_day->site_id;
            $site_day_det->monthdays = $this->g_month_days;
            $site_day_det->changed = true;
            $site_day_det->unit_id =0;
            if ($this->site->add_wo == "Y") {
                $site_day_det->saldays = $site_day_detail['days'] + $site_day_detail['cl'] + $site_day_detail['wo'] + $site_day_detail['hl'] + $site_day_detail['pl'] + $site_day_detail['blend_ext_days'];
            } else {
                $site_day_det['saldays'] = $site_day_detail['days'] + $site_day_detail['cl'] + $site_day_detail['hl'] + $site_day_detail['pl'] + $site_day_detail['blend_ext_days'];
            }
            $site_day_collection->add($site_day_det);
        }



        DB::beginTransaction();
            $site_day->site_day_details()->saveMany($site_day_collection);
        DB::commit();

        return reply(true,[]);
    }

}
