@extends('layouts.print')

@section('title')
<title>M3Force | Print Job Done Customer</title>
@endsection

@section('content')
<style>
    #header_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 10px;
    }
    #header_table tr{
        page-break-inside: avoid;
    }
    #header_table tfoot{
        display: table-row-group;
    }
    #header_table td:nth-child(1),
    #header_table td:nth-child(3){
        width: 15%;
        vertical-align: middle;
        white-space: nowrap;
    }
    #header_table td:nth-child(2),
    #header_table td:nth-child(4){
        width: 35%;
        vertical-align: middle;
    }
    
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
    #detail_table,
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
        padding: 5px;
    }
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th,
    #detail_table tfoot td{
        vertical-align: middle;
    }
    
    .panel-body p,
    .panel-body li{
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
    }
</style>

<?php 
    if ($monitoring_customer_invoice){
        $nbt_exist = $svat_exist = $vat_exist = false;
        $nbt_description = $svat_description = $vat_description = '';
        $nbt_percentage = $svat_percentage = $vat_percentage = 0;
        if($monitoring_customer_invoice->MonitoringCustomer->is_group == 0){
            foreach ($monitoring_customer_invoice->MonitoringCustomer->Contact->ContactTax as $detail){
                if ($detail['CTaxType']){
                    if($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 4){
                        $nbt_exist = true;
                        $nbt_description = $detail['CTaxType']['code'];
                        $nbt_percentage = $detail['CTaxType']['percentage'];
                    } else if($detail['CTaxType']['id'] == 2){
                        $svat_exist = true;
                        $svat_description = $detail['CTaxType']['code'];
                        $svat_percentage = $detail['CTaxType']['percentage'];
                    } else if($detail['CTaxType']['id'] == 3){
                        $vat_exist = true;
                        $vat_description = $detail['CTaxType']['code'];
                        $vat_percentage = $detail['CTaxType']['percentage'];
                    }
                }
            }
        } else if($monitoring_customer_invoice->MonitoringCustomer->is_group == 1){
            foreach ($monitoring_customer_invoice->MonitoringCustomer->CGroup->CGroupTax as $detail){
                if ($detail['CTaxType']){
                    if($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 4){
                        $nbt_exist = true;
                        $nbt_description = $detail['CTaxType']['code'];
                        $nbt_percentage = $detail['CTaxType']['percentage'];
                    } else if($detail['CTaxType']['id'] == 2){
                        $svat_exist = true;
                        $svat_description = $detail['CTaxType']['code'];
                        $svat_percentage = $detail['CTaxType']['percentage'];
                    } else if($detail['CTaxType']['id'] == 3){
                        $vat_exist = true;
                        $vat_description = $detail['CTaxType']['code'];
                        $vat_percentage = $detail['CTaxType']['percentage'];
                    }
                }
            }
        }
        
        $invoice_type = $vat_exist ? 'Tax ' : '';
        $invoice_type = $svat_exist ? 'SVAT ' : $invoice_type;
        $invoice_type = !$nbt_exist && !$svat_exist && !$vat_exist ? '' : $invoice_type;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;"><?php echo $invoice_type; ?> Invoice</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td><?php echo $monitoring_customer_invoice->MonitoringCustomer->is_group == 0 ? $monitoring_customer_invoice->MonitoringCustomer->Contact->invoice_name : $monitoring_customer_invoice->MonitoringCustomer->CGroup->invoice_name; ?></td>
                <td>Invoice No</td>
                <td><?php echo $monitoring_customer_invoice->invoice_no; ?></td>
            </tr>
            <tr>
                <td>Customer Address</td>
                <td><?php echo $monitoring_customer_invoice->MonitoringCustomer->is_group == 0 ? $monitoring_customer_invoice->MonitoringCustomer->Contact->invoice_delivering_address : $monitoring_customer_invoice->MonitoringCustomer->CGroup->invoice_delivering_address; ?></td>
                <td>Date</td>
                <td><?php echo $monitoring_customer_invoice->invoice_date; ?></td>
            </tr>
            <tr>
                <td>Location Name</td>
                <td><?php echo $monitoring_customer_invoice->MonitoringCustomer->is_group == 0 ? $monitoring_customer_invoice->MonitoringCustomer->Contact->name : $monitoring_customer_invoice->MonitoringCustomer->CGroup->name; ?></td>
                <td>Customer Email</td>
                <td><?php echo $monitoring_customer_invoice->MonitoringCustomer->is_group == 0 ? $monitoring_customer_invoice->MonitoringCustomer->Contact->invoice_email : $monitoring_customer_invoice->MonitoringCustomer->CGroup->invoice_email; ?></td>
            </tr>
            <tr>
                <td>Customer VAT No</td>
                <td><?php echo $monitoring_customer_invoice->MonitoringCustomer->is_group == 0 ? $monitoring_customer_invoice->MonitoringCustomer->Contact->vat_no : $monitoring_customer_invoice->MonitoringCustomer->CGroup->vat_no; ?></td>
                <td>Customer SVAT No</td>
                <td><?php echo $monitoring_customer_invoice->MonitoringCustomer->is_group == 0 ? $monitoring_customer_invoice->MonitoringCustomer->Contact->svat_no : $monitoring_customer_invoice->MonitoringCustomer->CGroup->svat_no; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-body">
        <table id="detail_table">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd; white-space: nowrap;">No#</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd; white-space: nowrap;">Description</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd; white-space: nowrap;">Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                
                if($monitoring_customer_invoice->MonitoringCustomer->is_group == 0){
                    $description = $monitoring_customer_invoice->MonitoringCustomer->Contact->ServiceMode->name.' Charge for the Month of ';
                } else{
                    $contact = App\Model\Contact::where('group_id', $monitoring_customer_invoice->MonitoringCustomer->contact_id)->where('is_active', 1)->where('is_delete', 0)->first();
                    $description = $contact ? $contact->ServiceMode->name.' Charge for the Month of ' : 'Monitoring & Response Charge for the Month of ';
                }
                
                if($monitoring_customer_invoice->MonitoringCustomer->is_group == 0){
                    $invoice_months = \App\Model\ContactInvoiceMonth::where('contact_id', $monitoring_customer_invoice->MonitoringCustomer->contact_id)->where('is_delete', 0)->count();
                    $description .= $invoice_months == 12 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+1 month', strtotime($monitoring_customer_invoice->invoice_date))) : '';
                    $description .= $invoice_months == 4 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+2 month', strtotime($monitoring_customer_invoice->invoice_date))) : '';
                    $description .= $invoice_months == 2 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+5 month', strtotime($monitoring_customer_invoice->invoice_date))) : '';
                    $description .= $invoice_months == 1 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+11 month', strtotime($monitoring_customer_invoice->invoice_date))) : '';
                } else if($monitoring_customer_invoice->MonitoringCustomer->is_group == 1){
                    $invoice_months = \App\Model\CGroupInvoiceMonth::where('group_id', $monitoring_customer_invoice->MonitoringCustomer->contact_id)->where('is_delete', 0)->count();
                    $description .= $invoice_months == 12 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+1 month', strtotime($monitoring_customer_invoice->invoice_date))).'<br/><br/>' : '';
                    $description .= $invoice_months == 4 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+2 month', strtotime($monitoring_customer_invoice->invoice_date))).'<br/><br/>' : '';
                    $description .= $invoice_months == 2 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+5 month', strtotime($monitoring_customer_invoice->invoice_date))).'<br/><br/>' : '';
                    $description .= $invoice_months == 1 ? date('F Y', strtotime($monitoring_customer_invoice->invoice_date)).' to '.date('F Y', strtotime('+11 month', strtotime($monitoring_customer_invoice->invoice_date))).'<br/><br/>' : '';
                
                    $contacts = App\Model\Contact::where('group_id', $monitoring_customer_invoice->MonitoringCustomer->contact_id)->where('is_active', 1)->where('is_delete', 0)->get();
                    foreach ($contacts as $index => $value){
                        $description .= $index == 0 ? $value->name : ', '.$value->name;
                    }
                }
                
                $invoice_value = $monitoring_customer_invoice->invoice_value;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $s; ?></td>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($invoice_value, 2); ?></td>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
            ?>
                <tr>
                    <th colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"></th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($invoice_value, 2); ?></th>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
        
                if($nbt_exist){
                    $nbt_value = $invoice_value * $nbt_percentage / 100;
                    $invoice_value += $nbt_value; 
            ?>
                <tr>
                    <td colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $nbt_percentage.'% '.$nbt_description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($nbt_value, 2); ?></td>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
            ?>
                <tr>
                    <th colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"></th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($invoice_value, 2); ?></th>
                </tr>
            <?php
                }
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                if($svat_exist){
                    $svat_value = $invoice_value * $svat_percentage / 100;  
            ?>
                <tr>
                    <td colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $svat_percentage.'% '.$svat_description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($svat_value, 2); ?></td>
                </tr>
            <?php
                }
                if($vat_exist){
                    $vat_value = $invoice_value * $vat_percentage / 100;
                    $invoice_value += $vat_value;   
            ?>
                <tr>
                    <td colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $vat_percentage.'% '.$vat_description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($vat_value, 2); ?></td>
                </tr>
            <?php
                }
                if(!$nbt_exist && !$svat_exist && !$vat_exist){  
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
            ?>
                <tr>
                    <td colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">VAT EXEMPTED</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"></td>
                </tr>
            <?php
                }
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
            ?>
                <tr>
                    <th colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Grand Total</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($invoice_value, 2); ?></th>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
            ?>
                <tr>
                    <td colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Payment Received</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($monitoring_customer_invoice->payment_received, 2); ?></td>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
                $invoice_value -= $monitoring_customer_invoice->payment_received;  
            ?>
                <tr>
                    <th colspan="2" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Balance Due</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($invoice_value, 2); ?></th>
                </tr>
            </tbody>
        </table>        
    </div> 
</div>

<div class="panel panel-default" style="margin-top: 30px;">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; font-family: Verdana, Geneva, sans-serif; font-size: 12px;">Special Notes</h4>
    </div>
    <div class="panel-body">
        <ul>
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">Cheques to be drawn in favour of <strong>M3FORCE (PVT) LTD</strong>.</li>
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">People's Bank - Wellawatte (Acc No: 145100140012106)</li>
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">This is a computer generated Invoice and bears no Signature.</li>
        </ul>
    </div> 
</div>
<?php } ?>
@endsection