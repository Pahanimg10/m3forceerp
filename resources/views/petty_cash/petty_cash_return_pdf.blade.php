@extends('layouts.print')

@section('title')
<title>M3Force | Print Petty Cash Return</title>
@endsection

@section('content')
<style>
    #header_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #header_table tr{
        page-break-inside: avoid;
    }
    #header_table tfoot{
        display: table-row-group;
    }
    #header_table,
    #header_table thead th,
    #header_table tbody td,
    #header_table tbody th,
    #header_table tfoot th{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
    }
    #header_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #header_table tbody td,
    #header_table tbody th{
        padding: 5px;
    }
    #header_table td:nth-child(1){
        width: 25%;
        vertical-align: middle;
        white-space: nowrap;
    }
    #header_table td:nth-child(2){
        vertical-align: middle;
    }
    #header_table thead th,
    #header_table tbody td,
    #header_table tfoot th,
    #header_table tfoot td{
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
    if($petty_cash_return){        
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Petty Cash Return</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <?php 
                $s = 0;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Petty Cash Issue No</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_return->PettyCashIssue->petty_cash_issue_no; ?></td>
            </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
                
                $customer = '';
                $customer = $petty_cash_return->PettyCashIssue->petty_cash_issue_type_id == 1 ? $petty_cash_return->PettyCashIssue->Job->Inquiry->Contact->name : $customer;
                $customer = $petty_cash_return->PettyCashIssue->petty_cash_issue_type_id == 2 ? $petty_cash_return->PettyCashIssue->TechResponse->Contact->name : $customer;
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Issued To</td>
                <td style="<?php echo $style; ?>"><?php echo $customer != '' ? $customer.' : '.$petty_cash_return->PettyCashIssue->issued_to : $petty_cash_return->PettyCashIssue->issued_to; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Returned Date & Time</td>
                <td style="<?php echo $style; ?>"><?php echo date('Y-m-d h:i A', strtotime($petty_cash_return->petty_cash_return_date_time)); ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Petty Cash Return No</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_return->petty_cash_return_no; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Petty Cash Return Value</td>
                <td style="<?php echo $style; ?>"><?php echo 'Rs '. number_format($petty_cash_return->petty_cash_return_value, 2); ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Remarks</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_return->remarks; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default" style="margin-top: 50px;">
    <div class="panel-body">
        <table id="signature_table">
            <tr>
                <td style="width: 5%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 40%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 5%;"></td>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 25%; text-align: center;">Cash Returner</td>
                <td style="width: 40%;"></td>
                <td style="width: 25%; text-align: center;">Accountant</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>
<?php } ?>
@endsection