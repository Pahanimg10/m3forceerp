@extends('layouts.print')

@section('title')
<title>M3Force | Print Purchase Order</title>
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
    }
    #detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #detail_table tbody td,
    #detail_table tbody th{
        padding: 5px;
    }
    #detail_table tbody td:nth-child(1),
    #detail_table tbody td:nth-child(5),
    #detail_table tbody td:nth-child(7),
    #detail_table tbody td:nth-child(9){
        text-align: center;
    }
    #detail_table tbody td:nth-child(6),
    #detail_table tbody td:nth-child(8){
        text-align: right;
    }
    #detail_table tfoot th,
    #detail_table tfoot td{
        text-align: right;
        padding: 5px;
        white-space: nowrap;
    }
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th,
    #detail_table tfoot td{
        vertical-align: middle;
    }
    
    #sub_detail_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #sub_detail_table tr{
        page-break-inside: avoid;
    }
    #sub_detail_table tfoot{
        display: table-row-group;
    }
    #sub_detail_table,
    #sub_detail_table thead th,
    #sub_detail_table tbody td{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
    }
    #sub_detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #sub_detail_table tbody td,
    #sub_detail_table tbody th{
        padding: 5px;
    }
    #sub_detail_table tbody td:nth-child(1),
    #sub_detail_table tbody td:nth-child(5){
        text-align: center;
    }
    #sub_detail_table tbody td:nth-child(4){
        text-align: right;
    }
    #sub_detail_table thead th,
    #sub_detail_table tbody td{
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
    if($purchase_order){        
        $nbt_exist = $svat_exist = $vat_exist = false;
        $nbt_description = $svat_description = $vat_description = '';
        $nbt_percentage = $svat_percentage = $vat_percentage = 0;
        foreach ($purchase_order->Contact->ContactTax as $detail){
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
        <h4 style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Purchase Order</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Supplier</td>
                <td><?php echo $purchase_order->Contact->name; ?></td>
                <td>Contact No</td>
                <td><?php echo $purchase_order->Contact->contact_no; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><?php echo $purchase_order->Contact->address; ?></td>
                <td>Good Request No</td>
                <td><?php echo $purchase_order->GoodRequest ? $purchase_order->GoodRequest->good_request_no : ''; ?></td>
            </tr>
            <tr>
                <td>Purchase Order No</td>
                <td><?php echo $purchase_order->purchase_order_no; ?></td>
                <td>Date & Time</td>
                <td><?php echo $purchase_order->purchase_order_date_time; ?></td>
            </tr>
            <tr>
                <td>Remarks</td>
                <td colspan="3"><?php echo $purchase_order->remarks; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-body">
        <table id="detail_table">
            <thead>
                <tr>
                    <th>No#</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Model No</th>
                    <th>Unit Type</th>
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                $total_value = 0;
                if ($purchase_order->PurchaseOrderDetails){
                    foreach ($purchase_order->PurchaseOrderDetails as $index => $value){
                        $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
                        $total_value += $value->rate * $value->quantity;
            ?>
                <tr>
                    <td style="<?php echo $style; ?>"><?php echo $index+1; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['name']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['model_no']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item->UnitType['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($value->rate, 2); ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->quantity; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($value->rate * $value->quantity, 2); ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['stock']; ?></td>
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
                    <th colspan="7" style="<?php echo $style; ?>">Total</th>
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
                    <td colspan="7" style="<?php echo $style; ?>"><?php echo $nbt_percentage.'% '.$nbt_description; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($nbt_value, 2); ?></td>
                    <td style="<?php echo $style; ?>"></td>
                </tr>
                <?php
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                ?>
                <tr>
                    <td colspan="7" style="<?php echo $style; ?>"></td>
                    <td style="<?php echo $style; ?> border-top: 1px double black;"><?php echo number_format($total_value, 2); ?></td>
                    <td style="<?php echo $style; ?>"></td>
                </tr>
                <?php
                    }
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                    if($svat_exist){
                        $svat_value = $total_value * $svat_percentage / 100;  
                ?>
                <tr>
                    <td colspan="7" style="<?php echo $style; ?>"><?php echo $svat_percentage.'% '.$svat_description; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($svat_value, 2); ?></td>
                    <td style="<?php echo $style; ?>"></td>
                </tr>
                <?php
                    } 
                    if($vat_exist){
                        $vat_value = $total_value * $vat_percentage / 100;
                        $total_value += $vat_value;   
                ?>
                <tr>
                    <td colspan="7" style="<?php echo $style; ?>"><?php echo $vat_percentage.'% '.$vat_description; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($vat_value, 2); ?></td>
                    <td style="<?php echo $style; ?>"></td>
                </tr>
                <?php
                    }
                    if(!$nbt_exist && !$svat_exist && !$vat_exist){ 
                        $s++;
                        $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                ?>
                <tr>
                    <td colspan="7" style="<?php echo $style; ?>">VAT EXEMPTED</td>
                    <td style="<?php echo $style; ?>"></td>
                    <td style="<?php echo $style; ?>"></td>
                </tr>
                <?php
                    }
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
                ?>
                <tr>
                    <th colspan="7" style="<?php echo $style; ?>">Grand Total</th>
                    <th style="<?php echo $style; ?> border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_value, 2); ?></th>
                    <th style="<?php echo $style; ?>"></th>
                </tr>
            </tfoot>
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
                <td style="width: 25%; text-align: center;">Purchasing Manager</td>
                <td style="width: 7.5%;"></td>
                <td style="width: 25%; text-align: center;">Stores Manager</td>
                <td style="width: 7.5%;"></td>
                <td style="width: 25%; text-align: center;">General Manager</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>

<div class="panel panel-default" style="margin-top: 50px;">
    <div class="panel-body">
        <table id="sub_detail_table">
            <thead>
                <tr>
                    <th>No#</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Last Purchased Price</th>
                    <th>Last Date Of Purchase</th>
                    <th>Invoice No</th>
                    <th>Supplier Name</th>
                    <th>Supplier Address</th>
                    <th>Supplier Contact No</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                if ($purchase_order->PurchaseOrderDetails){
                    foreach ($purchase_order->PurchaseOrderDetails as $index => $value){
                        $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
                        $good_receive_detail = App\Model\GoodReceiveDetails::whereHas('GoodReceive', function($query){
                                    $query->where('is_posted', 1)->orderBy('good_receive_date_time', 'DESC');
                                })
                                ->where('item_id', $value->Item['id'])
                                ->where('is_delete', 0)
                                ->first();
            ?>
                <tr>
                    <td style="<?php echo $style; ?>"><?php echo $index+1; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['name']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo number_format($value->rate, 2); ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $good_receive_detail && $good_receive_detail->GoodReceive ? date('Y-m-d', strtotime($good_receive_detail->GoodReceive->good_receive_date_time)) : ''; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $good_receive_detail ? $good_receive_detail->invoice_no : ''; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $good_receive_detail && $good_receive_detail->PurchaseOrder && $good_receive_detail->PurchaseOrder->Contact ? $good_receive_detail->PurchaseOrder->Contact->name : ''; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $good_receive_detail && $good_receive_detail->PurchaseOrder && $good_receive_detail->PurchaseOrder->Contact ? $good_receive_detail->PurchaseOrder->Contact->address : ''; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $good_receive_detail && $good_receive_detail->PurchaseOrder && $good_receive_detail->PurchaseOrder->Contact ? $good_receive_detail->PurchaseOrder->Contact->contact_no : ''; ?></td>
                </tr>
            <?php
                        $s++;
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>
@endsection