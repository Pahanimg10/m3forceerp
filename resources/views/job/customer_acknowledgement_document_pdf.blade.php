@extends('layouts.print')

@section('title')
<title>M3Force | Print Installation Completion Acknowledgement Document</title>
@endsection

@section('content')
<style>
    #detail_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #detail_table tr{
        page-break-inside: avoid;
    }
    #detail_table tfoot{
        display: table-row-group;
    }
    #detail_table td:nth-child(1){
        font-weight: bold;
        width: 30%;
    }
    #detail_table td:nth-child(2){
        width: 70%;
    }
    #detail_table td{
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 5px;
        border: 1px solid black;
    }
    
    #item_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #item_table tr{
        page-break-inside: avoid;
    }
    #item_table tfoot{
        display: table-row-group;
    }
    #item_table,
    #item_table thead th,
    #item_table tbody td,
    #item_table tbody th{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
        padding: 5px;
    }
    
    #training_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #training_table tr{
        page-break-inside: avoid;
    }
    #training_table tfoot{
        display: table-row-group;
    }
    #training_table td{
        padding: 5px;
    }
    #training_table td:nth-child(2){
        text-align: center;
    }
    #training_table td:nth-child(3){
        text-align: center;
    }
    
    #signature_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
        page-break-inside: avoid;
    }
    #signature_table tr{
        page-break-inside: avoid;
    }
    #signature_table tfoot{
        display: table-row-group;
    }
    #signature_table td{
        padding: 5px;
    }
    #signature_table td:nth-child(1){
        width: 45%;
    }
    #signature_table td:nth-child(2){
        width: 10%;
    }
    #signature_table td:nth-child(3){
        width: 45%;
    }
</style>

<div class="panel panel-default" style=" margin-top: 20px;">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; text-align: center; text-decoration: underline; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">INSTALLATION COMPLETION ACKNOWLEDGEMENT</h4>
    </div>
    <div class="panel-body">
        <table id="detail_table">
            <tr>
                <td>Client Name</td>
                <td><?php echo $inquiry->Contact ? $inquiry->Contact->name : ''; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><?php echo $inquiry->Contact ? $inquiry->Contact->address : ''; ?></td>
            </tr>
            <tr>
                <td>Installation Location (If  different)</td>
                <td></td>
            </tr>
            <tr>
                <td>Installation Completed Date</td>
                <td></td>
            </tr>
        </table>
        
        <p style="font-family: Verdana, Geneva, sans-serif; font-size: 12px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">The following equipment has been installed.</p>
        
        <?php
            $quotations = \App\Model\Quotation::where('inquiry_id', $inquiry->id)
                    ->where('is_confirmed', 1)
                    ->where('is_delete', 0)
                    ->get();
            $job_card_ids = array();
            foreach ($quotations as $quotation){
                foreach ($quotation->QuotationJobCard as $detail){
                    array_push($job_card_ids, $detail['id']);
                }
            }
            
            $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('item_id AS item_id, SUM(quantity) AS quantity')
                    ->whereIn('quotation_job_card_id', $job_card_ids)
                    ->where('is_delete', 0)
                    ->groupBy('item_id')
                    ->get();
            $item_details = array();
            foreach ($job_card_details as $job_card_detail){
                $row = array(
                    'name' => $job_card_detail->Item->name,
                    'unit_type' => $job_card_detail->Item->UnitType->code,
                    'quantity' => $job_card_detail->quantity
                );
                array_push($item_details, $row);
            }
        ?>
        
        <table id="item_table">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">No#</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Name</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Unit Type</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Quantity</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                foreach ($item_details as $item_detail){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $s; ?></td>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $item_detail['name']; ?></td>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $item_detail['unit_type']; ?></td>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $item_detail['quantity']; ?></td>
                </tr>
            <?php
                    $s++;
                }
            ?>
            </tbody>
        </table>
        
        <p style="font-family: Verdana, Geneva, sans-serif; font-size: 12px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">Were you trained on :</p>
        
        <table id="training_table">
            <tr>
                <td style="border: 1px solid black;"><strong>Intruder Detection System : </strong>How to Arm & Disarm the IDS, How to Add User Codes,  How to Activate Fire, Medical , Panic & Duress, How to Use of Remote Controller , Fault Diagnosis, Isolating  Zone ,What action to take during  heavy lightening to protect the panel.</td>
                <td style="border: 1px solid black;">Yes</td>
                <td style="border: 1px solid black;">No</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"><strong>CCTV System : </strong>Live Viewing, Play Back Viewing (including Time & Date search) , PTZ Control, Remote Viewing, Taking Backups ,What action to take during  heavy lightening to protect the System.</td>
                <td style="border: 1px solid black;">Yes</td>
                <td style="border: 1px solid black;">No</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"><strong>Fire Alarm System : </strong>Activating Alarms through MCP , Identifying Zones, Deactivating after Alarm.</td>
                <td style="border: 1px solid black;">Yes</td>
                <td style="border: 1px solid black;">No</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"><strong>Guard Tour System : </strong>Test Tour, Downloading Data to PC, Taking Reports.</td>
                <td style="border: 1px solid black;">Yes</td>
                <td style="border: 1px solid black;">No</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"><strong>Access Control System : </strong>Adding / Deleting Cards, Software Training, Emergency Door release.</td>
                <td style="border: 1px solid black;">Yes</td>
                <td style="border: 1px solid black;">No</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"><strong>Vehicle Tracking System : </strong>Software Training.</td>
                <td style="border: 1px solid black;">Yes</td>
                <td style="border: 1px solid black;">No</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px;"></td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid black; height: 120px; vertical-align: text-top;"><strong>Comments : </strong></td>
            </tr>
        </table>
        
        <table id="signature_table" style="margin-top: 50px;">
            <tr>
                <td>On behalf of <strong>M3Force (Pvt) Ltd</strong></td>
                <td></td>
                <td>On behalf of <strong>Customer</strong></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black; height: 50px;"></td>
                <td></td>
                <td style="border-bottom: 1px solid black; height: 50px;"></td>
            </tr>
            <tr>
                <td>Signature</td>
                <td></td>
                <td>Signature & Company Seal</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black; height: 20px;"></td>
                <td></td>
                <td style="border-bottom: 1px solid black; height: 20px;"></td>
            </tr>
            <tr>
                <td>Name</td>
                <td></td>
                <td>Name</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black; height: 20px;"></td>
                <td></td>
                <td style="border-bottom: 1px solid black; height: 20px;"></td>
            </tr>
            <tr>
                <td>Designation</td>
                <td></td>
                <td>Designation</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black; height: 20px;"></td>
                <td></td>
                <td style="border-bottom: 1px solid black; height: 20px;"></td>
            </tr>
            <tr>
                <td>Date</td>
                <td></td>
                <td>Date</td>
            </tr>
        </table>
    </div>
</div>
@endsection