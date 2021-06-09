<style>
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
</style>

Dear <?php echo $name; ?>,
<br/><br/>
Please find bellow item issue details for your reference.
<br/><br/>
Issue Type : <strong><?php echo $item_issue->ItemIssueType->name; ?></strong>
<br/>
<?php if($item_issue->remarks != ''){ ?>
Remarks : <strong><?php echo $item_issue->remarks; ?></strong>
<?php } ?>
<br/><br/>
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
                foreach ($value->ItemIssueBreakdown as $item_issue_breakdown) {
                    $item_total += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                }

                $total_quantity += $value->quantity;
                $total_value += $item_total;
                ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center;"><?php echo $index + 1; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['name']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value->Item['model_no']; ?></td>
                    <td style="<?php echo $style; ?> text-align: center;"><?php echo $value->Item->UnitType['code']; ?></td>
                    <td style="<?php echo $style; ?> text-align: center;"><?php echo $value->warranty; ?></td>
                    <td style="<?php echo $style; ?> text-align: right;"><?php echo number_format($item_total / $value->quantity, 2); ?></td>
                    <td style="<?php echo $style; ?> text-align: center;"><?php echo $value->quantity; ?></td>
                    <td style="<?php echo $style; ?> text-align: right;"><?php echo number_format($item_total, 2); ?></td>
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
        </tr>
    </tfoot>
</table>     
<br/><br/>
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
<?php
        }
    }
?>
<br/>
Thank you.
<br/><br/>
Best Regards,<br/>
M3Force (PVT) Ltd.