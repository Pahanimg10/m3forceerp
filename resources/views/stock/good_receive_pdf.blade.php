@extends('layouts.print')

@section('title')
<title>M3Force | Print Good Receive</title>
@endsection

@section('content')
<style>
    #header_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
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
    
    #item_detail_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #item_detail_table tr{
        page-break-inside: avoid;
    }
    #item_detail_table tfoot{
        display: table-row-group;
    }
    #item_detail_table,
    #item_detail_table thead th,
    #item_detail_table tbody td,
    #item_detail_table tbody th,
    #item_detail_table tfoot th{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
    }
    #item_detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #item_detail_table tbody td,
    #item_detail_table tbody th{
        padding: 5px;
    }
    #item_detail_table tbody td:nth-child(1),
    #item_detail_table tbody td:nth-child(7),
    #item_detail_table tbody td:nth-child(8),
    #item_detail_table tbody td:nth-child(10){
        text-align: center;
        white-space: nowrap;
    }
    #item_detail_table tbody td:nth-child(9),
    #item_detail_table tbody td:nth-child(11){
        text-align: right;
        white-space: nowrap;
    }
    #item_detail_table tfoot th,
    #item_detail_table tfoot td{
        text-align: right;
        padding: 5px;
        white-space: nowrap;
    }
    #item_detail_table thead th,
    #item_detail_table tbody td,
    #item_detail_table tbody th,
    #item_detail_table tfoot th,
    #item_detail_table tfoot td{
        vertical-align: middle;
    }
    
    #serial_detail_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #serial_detail_table tr{
        page-break-inside: avoid;
    }
    #serial_detail_table tfoot{
        display: table-row-group;
    }
    #serial_detail_table,
    #serial_detail_table thead th,
    #serial_detail_table tbody td,
    #serial_detail_table tbody th,
    #serial_detail_table tfoot th{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
    }
    #serial_detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #serial_detail_table tbody td,
    #serial_detail_table tbody th{
        padding: 5px;
    }
    #serial_detail_table thead th,
    #serial_detail_table tbody td,
    #serial_detail_table tbody th{
        vertical-align: middle;
    }
    
    #signature_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
    }
    #signature_table tr{
        page-break-inside: avoid;
    }
    #signature_table tfoot{
        display: table-row-group;
    }
</style>

<?php 
    if($good_receive){        
        $nbt_exist = $svat_exist = $vat_exist = false;
        $nbt_description = $svat_description = $vat_description = '';
        $nbt_percentage = $svat_percentage = $vat_percentage = 0;
        foreach ($good_receive->PurchaseOrder->Contact->ContactTax as $detail){
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
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Good Receive</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Supplier</td>
                <td><?php echo $good_receive->PurchaseOrder->Contact->name; ?></td>
                <td>Contact No</td>
                <td><?php echo $good_receive->PurchaseOrder->Contact->contact_no; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><?php echo $good_receive->PurchaseOrder->Contact->address; ?></td>
                <td>Purchase Order No</td>
                <td><?php echo $good_receive->PurchaseOrder->purchase_order_no; ?></td>
            </tr>
            <tr>
                <td>Good Receive No</td>
                <td><?php echo $good_receive->good_receive_no; ?></td>
                <td>Date & Time</td>
                <td><?php echo $good_receive->good_receive_date_time; ?></td>
            </tr>
            <tr>
                <td>Invoice No</td>
                <td><?php echo $good_receive->invoice_no; ?></td>
                <td>Remarks</td>
                <td><?php echo $good_receive->remarks; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-body">
        <table id="item_detail_table">
            <thead>
                <tr>
                    <th>No#</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Model No</th>
                    <th>Brand</th>
                    <th>Origin</th>
                    <th>Unit Type</th>
                    <th>Warranty</th>
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                $total_value = 0;
                if ($good_receive->GoodReceiveDetails){
                    foreach ($good_receive->GoodReceiveDetails as $index => $value){
                        $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
                        $total_value += $value->rate * $value->quantity;
            ?>
                <tr>
                    <td style="<?php echo $style; ?>"><?php echo $index+1; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['name']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->model_no; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->brand; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->origin; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item->UnitType['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->warranty; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($value->rate, 2); ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->quantity; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($value->rate * $value->quantity, 2); ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->location; ?></td>
                </tr>
            <?php
                        $s++;
                    }
                }
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10" style="<?php echo $style; ?>">Total</th>
                    <th style="<?php echo $style; ?> border-top: 1px double black;"><?php echo number_format($total_value, 2); ?></th>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
                <?php
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 

                    if($nbt_exist){
                        $nbt_value = $total_value * $nbt_percentage / 100;
                        $total_value += $nbt_value; 
                ?>
                <tr>
                    <td colspan="10" style="<?php echo $style; ?>"><?php echo $nbt_percentage.'% '.$nbt_description; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($nbt_value, 2); ?></td>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
                <?php
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                ?>
                <tr>
                    <td colspan="10" style="<?php echo $style; ?>"></td>
                    <td style="<?php echo $style; ?> border-top: 1px double black;"><?php echo number_format($total_value, 2); ?></td>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
                <?php
                    }
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                    if($svat_exist){
                        $svat_value = $total_value * $svat_percentage / 100;  
                ?>
                <tr>
                    <td colspan="10" style="<?php echo $style; ?>"><?php echo $svat_percentage.'% '.$svat_description; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($svat_value, 2); ?></td>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
                <?php
                    } 
                    if($vat_exist){
                        $vat_value = $total_value * $vat_percentage / 100;
                        $total_value += $vat_value;   
                ?>
                <tr>
                    <td colspan="10" style="<?php echo $style; ?>"><?php echo $vat_percentage.'% '.$vat_description; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($vat_value, 2); ?></td>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
                <?php
                    }
                    if(!$nbt_exist && !$svat_exist && !$vat_exist){ 
                        $s++;
                        $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
                ?>
                <tr>
                    <td colspan="10" style="<?php echo $style; ?>">VAT EXEMPTED</td>
                    <td style="<?php echo $style; ?>"></td>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
                <?php
                    }
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
                ?>
                <tr>
                    <th colspan="10" style="<?php echo $style; ?>">Grand Total</th>
                    <th style="<?php echo $style; ?> border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_value, 2); ?></th>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
            </tfoot>
        </table>        
    </div> 
</div>

<div class="panel panel-default" style="margin-top: 50px;">
    <div class="panel-body">
        <table id="serial_detail_table">
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Main Serial</th>
                    <th>Sub Serial</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                if ($good_receive->GoodReceiveDetails){
                    foreach ($good_receive->GoodReceiveDetails as $index => $value){
                        if(count($value->GoodReceiveBreakdown) > 0){
                            $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
                            $main_serails = array();
                            foreach ($value->GoodReceiveBreakdown as $detail){
                                if($detail['is_main'] == 1){
                                    $row = array(
                                        'id' => $detail['id'],
                                        'serial_no' => $detail['serial_no']
                                    );
                                    array_push($main_serails, $row);
                                }
                            }
            ?>
                <tr>
                    <td rowspan="<?php echo count($main_serails); ?>" style="<?php echo $style; ?> white-space: nowrap;"><?php echo $value->Item['code']; ?></td>
                <?php
                    foreach ($main_serails as $key => $main_serail){
                        if($key == 0){
                ?>
                    <td style="<?php echo $style; ?> white-space: nowrap;"><?php echo $main_serail['serial_no']; ?></td>
                    <td style="<?php echo $style; ?>">
                        <?php 
                            $sub_serials = '';
                            foreach ($value->GoodReceiveBreakdown as $detail){
                                if($detail['is_main'] == 0 && $detail['main_id'] == $main_serail['id']){
                                    $sub_serials .= $sub_serials != '' ? ' | '.$detail['serial_no'] : $detail['serial_no'];
                                }
                            }
                            echo $sub_serials;
                        ?>
                    </td>
                </tr>
                <?php
                        } else{
                            $s++;
                            $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
                ?>
                <tr>
                    <td style="<?php echo $style; ?> white-space: nowrap;"><?php echo $main_serail['serial_no']; ?></td>
                    <td style="<?php echo $style; ?>">
                        <?php 
                            $sub_serials = '';
                            foreach ($value->GoodReceiveBreakdown as $detail){
                                if($detail['is_main'] == 0 && $detail['main_id'] == $main_serail['id']){
                                    $sub_serials .= $sub_serials != '' ? ' | '.$detail['serial_no'] : $detail['serial_no'];
                                }
                            }
                            echo $sub_serials;
                        ?>
                    </td>
                </tr>
                <?php
                        }
                    }
                ?>
            <?php
                            $s++;
                        }
                    }
                }
            ?>
            </tbody>
        </table>        
    </div> 
</div>

<div class="panel panel-default" style="margin-top: 50px;">
    <div class="panel-body">
        <table id="signature_table">
            <tr>
                <td style="width: 5%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 7.5%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 7.5%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 5%;"></td>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 25%; text-align: center;">Stores Manager</td>
                <td style="width: 7.5%;"></td>
                <td style="width: 25%; text-align: center;">Purchasing Manager</td>
                <td style="width: 7.5%;"></td>
                <td style="width: 25%; text-align: center;">Accountant</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>
<?php } ?>
@endsection