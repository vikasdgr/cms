<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class MyExport implements FromCollection, WithHeadings, ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $headings;

    public function __construct($data,$headings)
    {
        $this->data = $data;
        $this->headings = $headings;

    }

    public function collection()
    {
        // Use the custom data provided in the constructor
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
        // return ['First Name', 'Last Name', 'Email'];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply bold font to the first row (headings)
        $sheet->getStyle('A1:R1')->getFont()->setBold(true);
        // Add additional styling here if needed
    }
}
