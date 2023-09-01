<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MonthlyReport
 *
 * @author Ani
 */

namespace App\Printings;

use App\Models\Accounts\Account;
use App\Models\Masters\Packing;
use App\Models\Masters\TermCondition;
use App\Models\Yearly\service;
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDispatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicePrint extends PrintPdf
{
    protected $testpdf = null;
    protected $pageno = 0;
    protected $nextpage = '';
    protected $copyno = 0;
    protected $secHeight = 0;
    protected $hight = 0;
    protected $footerHeight = 0;
    protected $detailHeight = 0;
    protected $border_gap = 0;
    protected $detHeight = 0;
    protected $machine = null;
    protected $case = null;
    protected $service = null;
    protected $det_cols =[];

    public function makepdf($service)
    {
        $this->service = $service;
        $this->service->load([
            'case.machine.brand',
            'case.machine.machine_model.machine_image.attachment',
            'case.machine.machine_type',
            'case.machine.department',
            'case.machine.area',
            'case.generated_user',
            'service_maintenances.maintenance',
            'service_problems.problem',
            'service_resolving_actions.resolving_action',
        ]);
        $this->machine = $this->service['case']['machine'];
        $this->case = $this->service['case'];
        $this->testpdf = new TestPdf();
        $this->testpdf->makePdf("P", "A4");
        $this->main();
        return $this->pdf;
    }

    private function setCopy($copyno)
    {
        $pdf = $this->pdf;
        $this->copyno = $copyno;
        $this->header();
        $this->details();
        $this->footer("E");
    }

    private function main($title = '')
    {
        $pdf = $this->pdf;
        $this->pageno = 0;
        $this->footerHeight = $this->getFooterHight();
        do {
            $this->pageno++;
            $this->nextpage = "N";
            $this->addNewPage("P", "A4");
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->setCopy(1);
        } while ($this->nextpage == "Y");
    }

    private function header()
    {
        $pdf = $this->pdf;
        $this->lineHeight = 5;
        $leftSideYPos = 0;
        $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
        $this->GetYPos(true);
        // $this->pdf->ln(4);
        $width = 0;

        $title = $this->service['service_type'] =='R' ? 'Repair' :
        ($this->service['service_type'] =='I' ? 'Installation':
        ($this->service['service_type'] =='M' ? 'Maintenance':''))
        ;
        $this->addCell($width, 0, "CLEANING MACHINE"  , 0, 'C', 1, '', 12, 'B', false, false, true);
        $this->addHLine();
        $service_no = $this->service['service_no'];

        $this->addCell($width, 0,$title  ." ( ".$service_no." )"  , 0, 'C', 1, '', 10, 'B', false, false, true);
        $this->addHLine();
        // $this->addCell($width, 0,strtoupper($this->machine['serial_no']), 0, 'C', 1, '', 12, 'B', false, false, true);


        $this->addCols([30,70,30,50,20]);

        $this->addCell($this->getColW(1), 0, 'Department : ', 0, 'L', 0, 'T', 11, 'B');
        $this->addCell($this->getColW(2), 0,$this->machine['department']['name'], 0, 'L', 0, 'T', 11, 'B');

        $this->addCell($this->getColW(3), 0, 'Area: ', 0, 'L', 0, 'T', 11, 'B');
        $this->addCell($this->getColW(4), 0,$this->machine['area'] ? $this->machine['area']['name']:'', 0, 'L', 1, 'T', 11, 'B');


        if( $this->machine['machine_model'] &&  $this->machine['machine_model']['machine_image']  &&  $this->machine['machine_model']['machine_image']['attachment']){
            $attachment = $this->machine['machine_model']['machine_image']['attachment'];
            $path = storage_path('/app' . '/'.sharedAttachmentPath().'/' . $attachment->id . '.' . $attachment->file_ext);
            $this->pdf->Image($path,185, 15, 20, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $this->pdf->ln(1);
            $this->GetYPos(true);
        }
        $this->addCell($this->getColW(1), 0, 'Serial No : ', 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(2), 0,$this->machine['serial_no'], 0, 'L', 0, 'T', 10, '');

        $this->addCell($this->getColW(3), 0, 'Machine Name: ', 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(4), 0,$this->machine['name_code_no'], 0, 'L', 1, 'T', 10, '');

        $this->addCell($this->getColW(1), 0, 'Machine Type : ', 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(2), 0,$this->machine['machine_type']['name'], 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3), 0, 'Machine Model No. : ', 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(4), 0,$this->machine['machine_model']['model_no'], 0, 'L', 1, 'T', 10, '');



        $this->pdf->ln(2);
        $this->addHLine();
        // $style = array(
        //     'border' => 1,
        //     'vpadding' => '1',
        //     'hpadding' => '1',
        //     'fgcolor' => array(0,0,0),
        //     // 'bgcolor' => false, //array(255,255,255)
        //     'module_width' => 1, // width of a single module in points
        //     'module_height' => 1 // height of a single module in points
        // );
        //        // QRCODE,L : QR-CODE Low error correction
        // $this->pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,L', 167, 20.5, 40, 40, $style);


        $status = $this->service['status'] =='C' ?'Complete & Closed':(
            $this->service['status'] =='F' ?'Required Follow up':''
        );

        $case_status = $this->case['status'] =='C' ?'Complete & Closed':(
            $this->case['status'] =='F' ?'Required Follow up':''
        );

        $this->GetYPos(true);
        $cols = [25,75,25,75];
        $this->addCols($cols);
        $this->addMCell($this->getColW(1,2), 0, 'Case Details', 0, 'C', 0, 'T', 10, 'B');
        $this->addMCell($this->getColW(3,4), 0, 'Service Details ', 0, 'C', 1, 'T', 10, 'B');
        $this->addHLine();
        $this->pdf->ln( 1);
        $this->addMCell($this->getColW(1), 0, 'Case No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ': '.$this->case['case_no'], 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Service No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$this->service['service_no']  , 0, 'L', 1, 'T', 10, '');

        $this->addMCell($this->getColW(1), 0, 'Open Date ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ': '.$this->case['open_date'], 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Service Date ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$this->service['service_date']  , 0, 'L', 1, 'T', 10, '');


        $this->addMCell($this->getColW(1), 0, 'Closed Date', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ': '.$this->case['closed_date'], 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Time ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$this->service['service_time']  , 0, 'L', 1, 'T', 10, '');


        $this->addMCell($this->getColW(1), 0, 'Work Type', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ': '.$this->case['work_types'], 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Technician  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$this->service['technician_name']  , 0, 'L', 1, 'T', 10, '');

        $this->addMCell($this->getColW(1), 0, 'Generated By', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ': '.$this->case['generated_user']['name'], 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Service Status  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$status  , 0, 'L', 1, 'T', 10, '');


        $this->addMCell($this->getColW(1), 0, 'Current Status', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ': '.$case_status, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, ' ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ''  , 0, 'L', 1, 'T', 10, '');


        $this->addMCell($this->getColW(1), 0, 'Remarks', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0, ': '.$this->case['remarks'] , 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Remarks', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$this->service['remarks']  , 0, 'L', 1, 'T', 10, '');

        $this->addVLine($this->lm + 100);
        // $this->GetYPos(true);


        // $this->addMCell($this->getColW(1), $this->lineHeight,  ' '.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        // $this->addMCell($this->getColW(2), $this->lineHeight,  'Description ' .chr(10)  .chr(10) , 1, 'C', 1, 'T', 9,'B',1);
        // // $this->pdf->ln($this->detHeight);
        $this->addHLine();
        $this->GetYPos(true);
        $this->hight = $this->getNetHeight();
        $this->detailHeight = $this->hight - $pdf->getY() - $this->footerHeight;
        $this->secHeight = 0;

    }

    private function details($title = '')
    {

        $count = 1;
        if(count($this->service['service_problems'] )>0){
            $this->addCell(0, 0, "Detected Issues"  , 0, 'L', 1, '', 12, 'B', false, false, true);
            $this->addHLine();
            // $this->addCols([10,190]);
            foreach ($this->service['service_problems'] as $key=>$service_detail) {
                $last = $key+1 == count($this->service['service_problems'] );
                $this->addDetail($service_detail,'problem', $count,$last );

                $count++;
            }
            // $this->GetYPos(true);
            // $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();
        }
        $count = 1;

        if(count($this->service['service_maintenances']) > 0){
            $this->addCell(0, 0, "Maintenance Procedures "  , 0, 'L', 1, '', 12, 'B', false, false, true);
            $this->addHLine();
            // $this->addCols([10,190]);
            foreach ($this->service['service_maintenances'] as $key=>$service_detail) {
                $last = $key+1 == count($this->service['service_maintenances'] );
                $this->addDetail($service_detail,'maintenance', $count,$last );
                $count++;
            }
            // $this->GetYPos(true);
            // $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();
        }

        $count = 1;

        if(count($this->service['service_resolving_actions']) > 0){
            $this->addCell(0, 0, "Tackling Procedures"  , 0, 'L', 1, '', 12, 'B', false, false, true);
            $this->addHLine();
            // $this->addCols([10,190]);
            foreach ($this->service['service_resolving_actions'] as $key=>$service_detail) {
                $last = $key+1 == count($this->service['service_resolving_actions'] );
                $this->addDetail($service_detail,'resolving_action', $count,$last );
                $count++;
            }
            // $this->GetYPos(true);
            $this->addHLine();
            // $this->drawBox($this->pdf->getY(), false);
        }

        //   $this->drawBox($this->pdf->getY(), false);
        //     $this->addHLine();

        //     $this->GetYPos(true);


    }

    private function addDetail($service_det,$type,$count,$is_last=false)
    {
        $this->detHeight = 0;
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $service_det['is_remarks'] =='Y'?$service_det[$type]['name'] .$service_det['remarks'] :$service_det[$type]['name'] , 0, 'L', 0, 'T', 0, '');

        if($is_last && ($this->secHeight + $this->testpdf->testHeight)  > $this->detailHeight){
            $this->startNewPage();
        }
        else if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight){
            $this->startNewPage();
        }
        $this->addMCell($this->getColW(1), 0, $count, 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(2,4), 0, $service_det[$type]['name'], 0, 'L', 1, 'L', 9, 'B');

        if( $service_det['is_remarks'] == 'Y'){
            $this->addMCell($this->getColW(1), 0, '', 0, 'C', 0, 'T', 9, '');
            $this->addMCell($this->getColW(2,4), 0, $service_det['remarks'], 0, 'L', 1, 'L', 9, '');
        }
        $this->pdf->ln(1);

        // $this->pdf->ln($this->detHeight+1);
        $this->secHeight += $this->detHeight+1;

    }

    private function footer($type = "R")
    {
        $this->GetYPos(true);
        $service = $this->service;
        if ($this->secHeight > $this->detailHeight) {
            $this->drawBox($this->pdf->getY(), false);
            $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
            $this->startNewPage('N');
        }


        if ($this->detailHeight - $this->secHeight > 0) {
            $this->pdf->ln($this->detailHeight - $this->secHeight);
        }
        // if(strlen($this->service['remarks']) > 0){
        //     $this->addMCell($this->getColW(1), $this->lineHeight, '', 0, 'L',0, 'T', 8, 'B');
        //     $this->addMCell(200-$this->getColW(1), $this->lineHeight, $this->service['remarks'], 0, 'L', 1, 'T', 8, 'B');
        // }

        // $this->drawBox($this->pdf->getY(), false);
        // $this->addHLine();


        if ($type == "E") {
            // $this->checkEndFooterHeight();
            // $this->GetYPos(true);
            // $this->addCols([80,20,20,20,20,20,20]);
            // $this->addMCell($this->getColW(1), $this->lineHeight, 'Total', 0, 'C', 0, 'T', 8, 'B');
            // $this->addCell($this->getColW(2), $this->lineHeight, myRound($this->total_qty), 0, 'R', 0, 'T', 8, 'B');
            // $this->addCell($this->getColW(3), $this->lineHeight, myRound($this->total_weight,3), 0, 'R', 0, 'T', 8, 'B');
            // $this->addCell($this->getColW(4), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            // $this->addCell($this->getColW(5), $this->lineHeight, myRound($this->total_amount ? $this->total_amount :'0.00'), 0, 'R', 0, 'T', 8, 'B');
            // $this->addCell($this->getColW(6), $this->lineHeight,myRound( $this->total_discount ?$this->total_discount:'0.00') , 0, 'R', 0, 'T', 8, 'B');
            // $this->addCell($this->getColW(7), $this->lineHeight, myRound($this->amt_without_gst), 0, 'R', 1, 'T', 8, 'B');

            // $this->drawBox($this->pdf->getY(), false);
            // $this->addHLine();

            $this->GetYPos(true);


            // $term_f = $this->service['freight'] > 0 ? 'Add ' :'Less ';
            // if(abs($this->service['freight']) > 0){
            //     $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(3), $this->lineHeight, $term_f .'Freight :', 0, 'R', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->service['freight']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            // }

            // $term_d = $this->service['discount_amt'] > 0 ? 'Add ' :'Less ';
            // if(abs($this->service['discount_amt']) > 0){
            //     $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(3), $this->lineHeight, $term_d .'Discount :', 0, 'R', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->service['discount_amt']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            // }
            // $term_e = $this->service['export_fee'] > 0 ? 'Add ' :'Less ';
            // if(abs($this->service['export_fee']) > 0){
            //     $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(3), $this->lineHeight,$term_e. 'Export Fee :', 0, 'R', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->service['export_fee']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            // }
            // foreach($this->gst_rates as $key => $gst){
            //     $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(3), $this->lineHeight, 'Add '.$key.'% On '.myround($gst['gst_on']) .' :', 0, 'R', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(4), $this->lineHeight, myround($gst['gst_value']) , 0, 'R', 1, 'T', 8, 'B');
            // }
            //  if(abs($this->service['tcs_per']) > 0){
            //     $this->addMCell($this->getColW(1), $this->lineHeight,'', 0, 'R', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');

            //     $this->addMCell($this->getColW(3), $this->lineHeight, $this->service['tcs_per'] ?  'TCS @' .$this->service['tcs_per'] .' %' : 'TCS @ 0.00%'  , 0, 'R', 0, 'T', 8, 'B');
            //     $this->addMCell($this->getColW(4), $this->lineHeight, number_format($this->service['tcs_amount'], 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            // }
            // $this->addMCell($this->getColW(1), $this->lineHeight, 'Bank Name : ', 0, 'L', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(2), $this->lineHeight,$this->service['ifsc'] &&  $this->service['ifsc']['bank'] ? $this->service['ifsc']['bank']['name'] :'', 0, 'L', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(3), $this->lineHeight, 'Round Off : ', 0, 'R', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(4), $this->lineHeight, number_format($this->service['round_off'], 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');

            // $this->addMCell($this->getColW(1), $this->lineHeight, 'Bank Account Number : ', 0, 'L', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(2), $this->lineHeight, $this->service['bank_account_number'] , 0, 'L', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(3), $this->lineHeight, 'Bill Amount :', 0, 'R', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(4), $this->lineHeight,$this->service['net_amt'] , 0, 'R', 1, 'T', 8, 'B');

            // $this->addMCell($this->getColW(1), $this->lineHeight, 'Bank Branch IFSC : ', 0, 'L', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(2), $this->lineHeight, $this->service['ifsc']  ? $this->service['ifsc']['ifsc_code'] :'', 0, 'L', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(3), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            // $this->addMCell($this->getColW(4), $this->lineHeight,'' , 0, 'R', 1, 'T', 8, 'B');
            // $this->addHLine();
            $this->GetYPos(true);

            $this->pdf->ln($this->detHeight);
            // $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();
            $this->GetYPos(true);
            $this->addCols([200]);


            $this->pdf->SetY(-15);
            // $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'] , 0, 'R', 1, 'T', 7, 'B');
            // $this->pdf->ln(15);
            $this->GetYPos(true);
            $this->addCols([66.6,66.6,66.6]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Prepared By: ", 0, 'L', 0, 'T', 7, 'B');
            $this->addCell($this->getColW(2), $this->lineHeight, "Checked By", 0, 'C', 0, 'T', 7, 'B');
            $this->addCell($this->getColW(3), $this->lineHeight, 'Authorised Signatory', 0, 'R', 1, 'T', 7, 'B');
            if ($this->pageno != 1) {
                $this->drawBorder();
            }
        } else {
            $this->addCols([170]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Continued.....", 0, 'R', 1, 'T', 10, '');
        }

        // $this->addHLine();
        $this->addCols([20]);
        //$this->pdf->ln(10);
        // dd($vo->updatedby);

        // $this->pdf->ln(40);
        // $this->addCell($this->getColW(1), $this->lineHeight, "Prepared By: ", 0, 'L', 0, 'T', 10, 'B');
        // $this->addCell($this->getColW(2), $this->lineHeight, $service->updatedby ? $service->updatedby->name :'', 0, 'L', 0, 'T', 10, '');
        // $this->addCell($this->getColW(3), $this->lineHeight, "", 0, 'L', 0, 'T', 10, 'B');
        // $this->addCell($this->getColW(4), $this->lineHeight, 'Authorised Signatory', 0, 'R', 0, 'T', 10, 'B');
        //$this->pdf->ln(5);
        // $this->pdf->SetY(-10);
        // $this->pdf->ln(2);
        $currentPage = $this->pdf->getPage();
        $totalPages = $this->pdf->getNumPages();
        // $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'], 0, 'R', 1, 'T', 7, 'B');
        $footerText = "Page {$currentPage} of {$totalPages}";
        $this->pdf->SetY(-10);
        $this->addCell(0, $this->lineHeight,  $footerText , 0, 'C', 0, 'T', 7, 'B');
        $this->drawBorder($this->border_gap);
    }





    private function startNewPage($footer = 'Y')
    {
        // $this->footerHeight = 20;
        if ($footer == 'Y')
            $this->footer();
        $this->secHeight = 0;
        $this->pageno++;
        $this->addNewPage("P", "A4");
        $this->header();

    }

    private function getFooterHight() {
        $hight = 35;
        $hight += $this->service['ethanol_bill'] == 'Y' ? 25:15;
        $hight += $this->getHight(abs($this->service['freight']));
        $hight += $this->getHight(abs($this->service['discount_amt']));
        $hight += $this->getHight(abs($this->service['export_fee']));
        $hight += $this->getHight(abs($this->service['tcs_amount']));
        return $hight;
    }

    private function getHight($amount) {
        $hight = 0;
        if($amount > 0) {
            $hight = 5;
        }
        return $hight;
    }


}
