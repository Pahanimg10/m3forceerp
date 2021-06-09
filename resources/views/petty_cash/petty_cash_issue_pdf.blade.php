@extends('layouts.print')

@section('title')
<title>M3Force | Print Petty Cash Issue</title>
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
    if($petty_cash_issue){        
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Petty Cash Issue</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <?php 
                $s = 0;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Petty Cash Issue Type</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->ItemIssueType->name; ?></td>
            </tr>
            <?php
                if($petty_cash_issue->petty_cash_issue_type_id != 3){
            ?>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Document No</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->petty_cash_issue_type_id == 1 ? $petty_cash_issue->Job->job_no : $petty_cash_issue->TechResponse->tech_response_no; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Customer</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->petty_cash_issue_type_id == 1 ? $petty_cash_issue->Job->Inquiry->Contact->name : $petty_cash_issue->TechResponse->Contact->name; ?></td>
            </tr>
            <?php
                }
            ?>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Issued To</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->issued_to; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Petty Cash Issue No</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->petty_cash_issue_no; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Petty Cash Issue Value</td>
                <td style="<?php echo $style; ?>"><?php echo 'Rs '. number_format($petty_cash_issue->petty_cash_issue_value, 2); ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Remarks</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->remarks; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Requested</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->LoggedUser->first_name; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Requested Date & Time</td>
                <td style="<?php echo $style; ?>"><?php echo date('Y-m-d h:i A', strtotime($petty_cash_issue->petty_cash_request_date_time)); ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Issued</td>
                <td style="<?php echo $style; ?>"><?php echo $petty_cash_issue->PostedUser->first_name; ?></td>
            </tr>
            <?php 
                $s++;
                $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?>">Issued Date & Time</td>
                <td style="<?php echo $style; ?>"><?php echo date('Y-m-d h:i A', strtotime($petty_cash_issue->petty_cash_issue_date_time)); ?></td>
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
                <td style="width: 25%; text-align: center;">Cash Receiver</td>
                <td style="width: 40%;"></td>
                <td style="width: 25%; text-align: center;">Accountant</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>
<?php } ?>
@endsection