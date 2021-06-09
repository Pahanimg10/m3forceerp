@extends('layouts.print')

@section('title')
<title>M3Force | Print Repair</title>
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
    #detail_table tfoot th:nth-child(1),
    #detail_table tfoot th:nth-child(2){
        text-align: center;
        padding: 5px;
        white-space: nowrap;
    }
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tfoot th,
    #detail_table tfoot td{
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

@if ($repair)
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Repair Details</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Repair No</td>
                <td>{{ $repair->repair_no }}</td>
                <td>Item Code</td>
                <td>{{ $repair->Item ? $repair->Item->code : '' }}</td>
            </tr>
            <tr>
                <td>Repair Date & Time</td>
                <td>{{ $repair->repair_date_time }}</td>
                <td>Item Name</td>
                <td>{{ $repair->Item ? $repair->Item->name : '' }}</td>
            </tr>
            <tr>
                <td>Repair Type / Document No</td>
                <td>
                <?php
                    $document_no = $customer_name = $customer_address = $customer_contact_no = '';
                    if($repair->repair_type_id == 1){
                        $document_no = $repair->Job ? $repair->Job->job_no : '';
                        $customer_name = $repair->Job && $repair->Job->Inquiry && $repair->Job->Inquiry->Contact ? $repair->Job->Inquiry->Contact->name : '';
                        $customer_address = $repair->Job && $repair->Job->Inquiry && $repair->Job->Inquiry->Contact ? $repair->Job->Inquiry->Contact->address : '';
                        $customer_contact_no = $repair->Job && $repair->Job->Inquiry && $repair->Job->Inquiry->Contact ? $repair->Job->Inquiry->Contact->contact_no : '';
                    } else if($repair->repair_type_id == 2){
                        $document_no = $repair->TechResponse ? $repair->TechResponse->tech_response_no : '';
                        $customer_name = $repair->TechResponse && $repair->TechResponse->Contact ? $repair->TechResponse->Contact->name : '';
                        $customer_address = $repair->TechResponse && $repair->TechResponse->Contact ? $repair->TechResponse->Contact->address : '';
                        $customer_contact_no = $repair->TechResponse && $repair->TechResponse->Contact ? $repair->TechResponse->Contact->contact_no : '';
                    } 
                    
                    if($document_no != ''){
                        echo $repair->RepairType ? $repair->RepairType->name.' / '.$document_no : $document_no;
                    } else{
                        echo $repair->RepairType ? $repair->RepairType->name : '';
                    }
                ?>
                </td>
                <td>Item Model No</td>
                <td>{{ $repair->model_no }}</td>
            </tr>
            <tr>
                <td>Customer Name</td>
                <td>{{ $customer_name }}</td>
                <td>Item Brand</td>
                <td>{{ $repair->brand }}</td>
            </tr>
            <tr>
                <td>Customer Address</td>
                <td>{{ $customer_address }}</td>
                <td>Item Serial No</td>
                <td>{{ $repair->serial_no }}</td>
            </tr>
            <tr>
                <td>Customer Contact No</td>
                <td>{{ $customer_contact_no }}</td>
                <td>Repair Remarks</td>
                <td>{{ $repair->remarks }}</td>
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
                    <th>Update Date & Time</th>
                    <th>Repair Status</th>
                    <th>Handed Over / Taken Over</th>
                    <th>Update Remarks</th>
                    <th>Logged User</th>
                </tr>
            </thead>
            <tbody>
            {{--*/ $s = 1 /*--}}
            @if ($repair->RepairDetails)
                @foreach ($repair->RepairDetails as $index => $value)
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                <tr>
                    <td style="{{ $style }}">{{ $index+1 }}</td>
                    <td style="{{ $style }}">{{ $value->update_date_time }}</td>
                    <td style="{{ $style }}">{{ $value->RepairStatus['name'] }}</td>
                    <td style="{{ $style }}">{{ $value->handed_over_taken_over }}</td>
                    <td style="{{ $style }}">{{ $value->remarks }}</td>
                    <td style="{{ $style }}">{{ $value->User['first_name'] }}</td>
                </tr>
                {{--*/ $s++ /*--}}
                @endforeach
            @endif
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
                <td style="width: 40%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 5%;"></td>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 25%; text-align: center;">Handed Over</td>
                <td style="width: 40%;"></td>
                <td style="width: 25%; text-align: center;">Taken Over</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>
@endif
@endsection