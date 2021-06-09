@extends('layouts.print')

@section('title')
<title>M3Force | Print Inventory Issue</title>
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
    #detail_table tbody td:nth-child(1){
        text-align: center;
    }
    #detail_table tbody td:nth-child(9){
        text-align: right;
    }
    #detail_table tfoot th:nth-child(1),
    #detail_table tfoot th:nth-child(2){
        text-align: right;
        padding: 5px;
        white-space: nowrap;
    }
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tfoot th{
        vertical-align: middle;
    }
</style>

@if ($inventory_issue)
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Inventory Issue</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Inventory Issue No</td>
                <td>{{ $inventory_issue->inventory_issue_no }}</td>
                <td>Issued To</td>
                <td>{{ $inventory_issue->issued_to }}</td>
            </tr>
            <tr>
                <td>Date & Time</td>
                <td>{{ $inventory_issue->inventory_issue_date_time }}</td>
                <td>Remarks</td>
                <td>{{ $inventory_issue->remarks }}</td>
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
                    <th>Inventory Location</th>
                    <th>Inventory Type</th>
                    <th>Name</th>
                    <th>Model No</th>
                    <th>IMEI</th>
                    <th>Serial No</th>
                    <th>Credit Limit</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            {{--*/ $s = 1 /*--}}
            {{--*/ $total_value = 0 /*--}}
            @if ($inventory_issue->InventoryIssueDetails)
                @foreach ($inventory_issue->InventoryIssueDetails as $index => $value)
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                {{--*/ $total_value += $value->InventoryRegister['credit_limit'] /*--}}
                <tr>
                    <td style="{{ $style }}">{{ $index+1 }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['code'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister->InventoryLocation['name'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister->InventoryType['name'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['name'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['model_no'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['imei'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['serial_no'] }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['credit_limit'] != 0 ? number_format($value->InventoryRegister['credit_limit'], 2) : '' }}</td>
                    <td style="{{ $style }}">{{ $value->InventoryRegister['remarks'] }}</td>
                </tr>
                {{--*/ $s++ /*--}}
                @endforeach
            @endif
            </tbody>
            <tfoot>
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                <tr>
                    <th colspan="8" style="{{ $style }}">Total</th>
                    <th style="{{ $style }} border-top: 1px double black; border-bottom: 3px double black;">{{ $total_value != 0 ? number_format($total_value, 2) : null }}</th>
                    <th style="{{ $style }}"></th>
                </tr>
            </tfoot>
        </table>        
    </div> 
</div>
@endif
@endsection