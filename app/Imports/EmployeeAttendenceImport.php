<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeAttendenceImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // Process the data as needed
        foreach ($collection as $row) {


            // Access data using the headers as keys
            $lastMonthAttStatus = $row['Last Month Att. Status'];
            $emp_code = $row['Code'];
            $companyCode = $row['Company Code'];
            $emp_name = $row['Name'];
            $fatherName = $row['Father Name'];
            $doj = $row['DOJ'];
            $designation = $row['Designation'];
            $department = $row['Department'];
            $actualDays = $row['Actual Days'];
            $wo = $row['WO'];
            $hol = $row['HOL'];
            $absent = $row['Absent'];
            $cl = $row['CL'];
            $pl = $row['PL'];
            $blendDays = $row['Blend Days'];
            $ot = $row['OT'];
            $incentive = $row['Incentive'];
            $joiningLeave = $row['Joining Leave'];
            // ... and so on for other headers

        }
    }
}
