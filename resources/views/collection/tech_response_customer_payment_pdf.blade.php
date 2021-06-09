@extends('layouts.print')

@section('title')
<title>M3Force | Print Tech Response Customer Payment</title>
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
    #detail_table tbody td{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
    }
    #detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #detail_table tbody td{
        padding: 5px;
    }
    #detail_table thead th,
    #detail_table tbody td{
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

<?php if($tech_response_customer_payment){ ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Tech Response Customer Payment</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td><?php echo $tech_response_customer_payment->TechResponseCustomer->Contact->name; ?></td>
                <td>Receipt No</td>
                <td><?php echo $tech_response_customer_payment->receipt_no; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><?php echo $tech_response_customer_payment->TechResponseCustomer->Contact->address; ?></td>
                <td>Date & Time</td>
                <td><?php echo $tech_response_customer_payment->receipt_date_time; ?></td>
            </tr>
            <tr>
                <td>Contact No</td>
                <td><?php echo $tech_response_customer_payment->TechResponseCustomer->Contact->contact_no; ?></td>
                <td>Email</td>
                <td><?php echo $tech_response_customer_payment->TechResponseCustomer->Contact->email; ?></td>
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
                    <th>Payment Mode</th>
                    <th>Amount</th>
                    <?php if($tech_response_customer_payment->payment_mode_id == 1){ ?>
                    <th>Cheque No</th>
                    <?php 
                        } else if($tech_response_customer_payment->payment_mode_id == 3){
                    ?>
                    <th>Bank</th>
                    <?php                     
                        } 
                        if($tech_response_customer_payment->payment_mode_id == 1){
                    ?>
                    <th>Realize Date</th>
                    <?php } ?>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;"><?php echo $tech_response_customer_payment->PaymentMode->name; ?></td>
                    <td style="text-align: right;"><?php echo number_format($tech_response_customer_payment->amount, 2); ?></td>
                    <?php if($tech_response_customer_payment->payment_mode_id == 1){ ?>
                    <td style="text-align: center;"><?php echo $tech_response_customer_payment->cheque_no; ?></td>
                    <?php 
                        } else if($tech_response_customer_payment->payment_mode_id == 3){
                    ?>
                    <td style="text-align: center;"><?php echo $tech_response_customer_payment->bank; ?></td>
                    <?php                     
                        } 
                        if($tech_response_customer_payment->payment_mode_id == 1){
                    ?>
                    <td style="text-align: center;"><?php echo $tech_response_customer_payment->realize_date; ?></td>
                    <?php } ?>
                    <td><?php echo $tech_response_customer_payment->remarks; ?></td>
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
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">This is a computer generated Receipt and bears no Signature.</li>
        </ul>
    </div> 
</div>
<?php } ?>
@endsection