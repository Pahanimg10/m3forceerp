@extends('layouts.print')

@section('title')
<title>M3Force | Print Item Issue</title>
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
    #item_detail_table tbody td:nth-child(5),
    #item_detail_table tbody td:nth-child(6),
    #item_detail_table tbody td:nth-child(8){
        text-align: center;
        white-space: nowrap;
    }
    #item_detail_table tbody td:nth-child(7),
    #item_detail_table tbody td:nth-child(9){
        text-align: right;
        white-space: nowrap;
    }
    #item_detail_table tfoot th{
        padding: 5px;
        white-space: nowrap;
    }
    #item_detail_table tfoot th:nth-child(1),
    #item_detail_table tfoot th:nth-child(2){
        text-align: center;
    }
    #item_detail_table tfoot th:nth-child(3){
        text-align: right;
    }
    #item_detail_table thead th,
    #item_detail_table tbody td,
    #item_detail_table tbody th,
    #item_detail_table tfoot th{
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
if ($item_issue) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Item Issue</h4>
        </div>
        <div class="panel-body">
            <table id="header_table">
                <tr>
                    <?php
                    $customer = '';
                    $customer = $item_issue->item_issue_type_id == 1 ? $item_issue->Job->Inquiry->Contact->name : $customer;
                    $customer = $item_issue->item_issue_type_id == 2 ? $item_issue->TechResponse->Contact->name : $customer;
                    ?>
                    <td>Issued To</td>
                    <td><?php echo $customer != '' ? $customer . ' : ' . $item_issue->issued_to : $item_issue->issued_to; ?></td>
                    <td>Document No</td>
                    <td>
                        <?php
                        $document_no = '';
                        $document_no = $item_issue->item_issue_type_id == 1 ? $item_issue->Job->job_no : $document_no;
                        $document_no = $item_issue->item_issue_type_id == 2 ? $item_issue->TechResponse->tech_response_no : $document_no;
                        echo $document_no;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Item Issue No</td>
                    <td><?php echo $item_issue->item_issue_no; ?></td>
                    <td>Date & Time</td>
                    <td><?php echo $item_issue->item_issue_date_time; ?></td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3"><?php echo $item_issue->remarks; ?></td>
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
                        <th>Unit Type</th>
                        <th>Warranty</th>
                        <th>Rate</th>
                        <th>Quantity</th>
                        <th>Value</th>
                        <th>Location</th>
                        <th>Return</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $s = 1;
                    $total_quantity = $total_value = 0;
                    if ($item_issue->ItemIssueDetails) {
                        foreach ($item_issue->ItemIssueDetails as $index => $value) {
                            $style = $s % 2 == 0 ? ' background-color: #e3e0dd;' : '';

                            $item_total = 0;
                            $grn_detail_ids = [];
                            foreach ($value->ItemIssueBreakdown as $item_issue_breakdown) {
                                if($item_issue_breakdown->type == 1){
                                    $item_total += $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                                    if(!in_array($item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->id, $grn_detail_ids)){
                                        array_push($grn_detail_ids, $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->id);
                                    }
                                } else{
                                    $item_total += $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                                    if(!in_array($item_issue_breakdown->GoodReceiveDetails->id, $grn_detail_ids)){
                                        array_push($grn_detail_ids, $item_issue_breakdown->GoodReceiveDetails->id);
                                    }
                                }
                            }

                            $location = '';
                            for($i = 0; $i < count($grn_detail_ids); $i++){
                                $good_receive_detail = \App\Model\GoodReceiveDetails::find($grn_detail_ids[$i]);
                                $location .= $location == '' ? $good_receive_detail->location : ' | '.$good_receive_detail->location;
                            }

                            $total_quantity += $value->quantity;
                            $total_value += $item_total;
                            ?>
                            <tr>
                                <td style="<?php echo $style; ?>"><?php echo $index + 1; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $value->Item['code']; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $value->Item['name']; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $value->Item['model_no']; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $value->Item->UnitType['code']; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $value->warranty; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo number_format($item_total / $value->quantity, 2); ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $value->quantity; ?></td>
                                <td style="<?php echo $style; ?>"><?php echo number_format($item_total, 2); ?></td>
                                <td style="<?php echo $style; ?>"><?php echo $location; ?></td>
                                <td style="<?php echo $style; ?>"></td>
                            </tr>
                            <?php
                            $s++;
                        }
                    }
                    $style = $s % 2 == 0 ? ' background-color: #e3e0dd;' : '';
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" style="<?php echo $style; ?>">Total</th>
                        <th style="<?php echo $style; ?> border-top: 1px double black; border-bottom: 3px double black;"><?php echo $total_quantity; ?></th>
                        <th style="<?php echo $style; ?> border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_value, 2); ?></th>
                        <th style="<?php echo $style; ?>"></th>
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
                    <td style="width: 25%; text-align: center;">Good Receiver</td>
                    <td style="width: 7.5%;"></td>
                    <td style="width: 25%; text-align: center;">Stores Manager</td>
                    <td style="width: 7.5%;"></td>
                    <td style="width: 25%; text-align: center;">Return Receiver</td>
                    <td style="width: 5%;"></td>
                </tr>
            </table>       
        </div> 
    </div>

    <?php
    if ($item_issue->ItemIssueDetails) {
        $main_serails = [];
        foreach ($item_issue->ItemIssueDetails as $index => $value) {
            foreach ($value->ItemIssueBreakdown as $detail) {
                if ($detail['type'] == 1) {
                    $row = [
                        'id' => $detail['GoodReceiveBreakdown']['id'],
                        'serial_no' => $detail['GoodReceiveBreakdown']['serial_no']
                    ];
                    array_push($main_serails, $row);
                }
            }
        }
        if (count($main_serails) > 0) {
            ?>
            <!--<p style="page-break-after: always;"></p>-->

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
                            foreach ($item_issue->ItemIssueDetails as $index => $value) {
                                $style = $s % 2 == 0 ? ' background-color: #e3e0dd;' : '';
                                $main_serails = [];
                                foreach ($value->ItemIssueBreakdown as $detail) {
                                    if ($detail['type'] == 1) {
                                        $row = [
                                            'id' => $detail['GoodReceiveBreakdown']['id'],
                                            'serial_no' => $detail['GoodReceiveBreakdown']['serial_no']
                                        ];
                                        array_push($main_serails, $row);
                                    }
                                }

                                if (count($main_serails) > 0) {
                                    ?>
                                    <tr>
                                        <td rowspan="<?php echo count($main_serails); ?>" style="<?php echo $style; ?> white-space: nowrap;"><?php echo $value->Item['code']; ?></td>
                                        <?php
                                        foreach ($main_serails as $key => $detail) {
                                            if ($key == 0) {
                                                ?>
                                                <td style="<?php echo $style; ?> white-space: nowrap;"><?php echo $detail['serial_no']; ?></td>
                                                <td style="<?php echo $style; ?>">
                                                    <?php
                                                    $sub_serials = '';
                                                    $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('main_id', $detail['id'])
                                                            ->where('id', '!=', $detail['id'])
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                    foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                                                        $sub_serials .= $sub_serials != '' ? ' | ' . $good_receive_breakdown->serial_no : $good_receive_breakdown->serial_no;
                                                    }
                                                    echo $sub_serials;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            $s++;
                                            $style = $s % 2 == 0 ? ' background-color: #e3e0dd;' : '';
                                            ?>
                                            <tr>
                                                <td style="<?php echo $style; ?> white-space: nowrap;"><?php echo $detail['serial_no']; ?></td>
                                                <td style="<?php echo $style; ?>">
                                                    <?php
                                                    $sub_serials = '';
                                                    $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('main_id', $detail['id'])
                                                            ->where('id', '!=', $detail['id'])
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                    foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                                                        $sub_serials .= $sub_serials != '' ? ' | ' . $good_receive_breakdown->serial_no : $good_receive_breakdown->serial_no;
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
                            ?>
                        </tbody>
                    </table>        
                </div> 
            </div>
            <?php
        }
    }
} 
?>
@endsection