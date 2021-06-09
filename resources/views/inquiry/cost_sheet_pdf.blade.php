@extends('layouts.print')

@section('title')
<title>M3Force | Print Cost Sheet</title>
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
    #detail_table tbody td:nth-child(1){
        text-align: center;
    }
    #detail_table tbody td:nth-child(3){
        text-align: right;
    }
    #detail_table tfoot th:nth-child(1){
        text-align: center;
        padding: 5px;
        white-space: nowrap;
    }
    #detail_table tfoot th:nth-child(2){
        text-align: right;
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

<?php
    $rate = \App\Model\Rate::find(1);
?>

@if ($cost_sheet)
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Cost Sheet</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td>{{ $cost_sheet->Inquiry->Contact->name }}</td>
                <td>Cost Sheet No</td>
                <td>{{ $cost_sheet->cost_sheet_no }}</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>{{ $cost_sheet->Inquiry->Contact->address }}</td>
                <td>Date & Time</td>
                <td>{{ $cost_sheet->cost_sheet_date_time }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $cost_sheet->Inquiry->Contact->email }}</td>
                <td>Remarks</td>
                <td>{{ $cost_sheet->remarks }}</td>
            </tr>
            <tr>
                <td>Contact No</td>
                <td>{{ $cost_sheet->Inquiry->Contact->contact_no }}</td>
                <td></td>
                <td></td>
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
                    <th>Description</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
            @if ($cost_sheet->InstallationRate)
                <tr>
                    <td>1</td>
                    <td>{{ $cost_sheet->InstallationRate->name }} X {{ $cost_sheet->meters }} Meters</td>
                    <td>{{ number_format($cost_sheet->InstallationRate->rate*$cost_sheet->meters, 2) }}</td>
                </tr>
                <tr>
                    <td style="background-color: #e3e0dd;">2</td>
                    <td style="background-color: #e3e0dd;">Excavation Work</td>
                    <td style="background-color: #e3e0dd;">{{ number_format($cost_sheet->excavation_work, 2) }}</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Transport</td>
                    <td>{{ number_format($cost_sheet->transport, 2) }}</td>
                </tr>
                <tr>
                    <td style="background-color: #e3e0dd;">4</td>
                    <td style="background-color: #e3e0dd;">Traveling Mandays ( {{ $cost_sheet->traveling_mandays.' X '.number_format($rate->value, 2) }} )</td>
                    <td style="background-color: #e3e0dd;">{{ number_format($cost_sheet->traveling_mandays*$rate->value, 2) }}</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Food</td>
                    <td>{{ number_format($cost_sheet->food, 2) }}</td>
                </tr>
                <tr>
                    <td style="background-color: #e3e0dd;">6</td>
                    <td style="background-color: #e3e0dd;">Accommodation</td>
                    <td style="background-color: #e3e0dd;">{{ number_format($cost_sheet->accommodation, 2) }}</td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>Bata</td>
                    <td>{{ number_format($cost_sheet->bata, 2) }}</td>
                </tr>
                <tr>
                    <td style="background-color: #e3e0dd;">8</td>
                    <td style="background-color: #e3e0dd;">Other Expenses</td>
                    <td style="background-color: #e3e0dd;">{{ number_format($cost_sheet->other_expenses, 2) }}</td>
                </tr>
            @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th style="border-top: 1px double black; border-bottom: 3px double black;">{{ number_format($cost_sheet->cost_sheet_value, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="2" style="height: 10px; border: none;"></th>
                    <th style="height: 10px; border: none;"></th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right; background-color: #e3e0dd;">Installation Value</th>
                    <th style="background-color: #e3e0dd;">{{ number_format($cost_sheet->installation_value, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right;">Labour Value</th>
                    <th style="">
                    <?php
                        $rate = \App\Model\Rate::find(1);
                        echo number_format($cost_sheet->mandays * $rate->value, 2);
                    ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right; background-color: #e3e0dd;">Mandays (1 Person)</th>
                    <th style="background-color: #e3e0dd;">{{ $cost_sheet->mandays }}</th>
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
                <td style="width: 40%;"></td>
                <td style="border-bottom: 1px dotted black; width: 25%; text-align: center;"></td>
                <td style="width: 5%;"></td>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 25%; text-align: center;">Sales Manager</td>
                <td style="width: 40%;"></td>
                <td style="width: 25%; text-align: center;">Technical Manager</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>
@endif
@endsection