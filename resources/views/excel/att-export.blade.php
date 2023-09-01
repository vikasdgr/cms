<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Attendance</h3>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>Last Month Att. Status</th>
            <th>Code</th>
            <th>Company Code</th>
            <th>Name</th>
            <th>Father Name</th>
            <th>DOJ</th>
            <th>Designation</th>
            <th>Deptt</th>
            <th>Actual Days</th>
            <th>WO</th>
            <th>HOL</th>
            <th>Absent</th>
            <th>CL</th>
            <th>PL</th>
            <th>Blend Days</th>
            <th>OT</th>
            <th>Incentive</th>
            <th>Joining Leave</th>
        </tr>
        @php $i = 1; @endphp
        @foreach($employees as $emp)
        <tr>
            <td>
                @if($sd = $emp->prvSalDays->first())
                    @if(floatval($sd->saldays) + floatval($sd->ot1) + floatval($sd->ot2))
                        {{ 'P' }}
                    @else
                        {{ 'A' }}
                    @endif
                @else
                    @if(Carbon\Carbon::parse($emp->joining_date)->format('F') != $site_day->smonth || Carbon\Carbon::parse($emp->joining_date)->format('Y') != $site_day->syear)
                        {{ 'A' }}
                    @else
                        {{ 'N' }}
                    @endif
                @endif
            </td>
            <td>{{ $emp->emp_code }}</td>
            <td>{{ $emp->token_id }}</td>
            <td>{{ $emp->emp_name }}</td>
            <td>{{ $emp->father }}</td>
            <td>{{ $emp->joining_date }}</td>
            <td>{{ $emp->designation->designation or '' }}</td>
            <td>{{ $emp->department->department or '' }}</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->days }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->wo }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->hl }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->leave }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->cl }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->pl }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->blend_ext_days }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->ot1 }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->ot2 }}@endif</td>
            <td></td>
        </tr>
        @endforeach

        @foreach($other_employees as $emp)
        <tr>
            <td></td>
            <td>{{ $emp->emp_code }}</td>
            <td>{{ $emp->token_id }}</td>
            <td>{{ $emp->emp_name }}</td>
            <td>{{ $emp->father }}</td>
            <td>{{ $emp->joining_date }}</td>
            <td>{{ $emp->designation->designation or '' }}</td>
            <td>{{ $emp->department->department or '' }}</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->days }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->wo }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->hl }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->leave }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->cl }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->pl }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->blend_ext_days }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->ot1 }}@endif</td>
            <td>@if($sd = $emp->salDays->first()) {{ $sd->ot2 }}@endif</td>
            <td></td>
        </tr>
        @endforeach
     </table>

</div>
  </body>

</html>

