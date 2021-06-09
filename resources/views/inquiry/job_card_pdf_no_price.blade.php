@extends('layouts.print')

@section('title')
<title>M3Force | Print Job Card No Price</title>
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
    #detail_table tbody td:nth-child(7){
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

@if ($job_card)
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Job Card</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td>{{ $job_card->Inquiry->Contact->name }}</td>
                <td>Job Card No</td>
                <td>{{ $job_card->job_card_no }}</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>{{ $job_card->Inquiry->Contact->address }}</td>
                <td>Date & Time</td>
                <td>{{ $job_card->job_card_date_time }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $job_card->Inquiry->Contact->email }}</td>
                <td>Remarks</td>
                <td>{{ $job_card->remarks }}</td>
            </tr>
            <tr>
                <td>Contact No</td>
                <td>{{ $job_card->Inquiry->Contact->contact_no }}</td>
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
                    <th>Code</th>
                    <th>Description</th>
                    <th>Model No</th>
                    <th>Unit Type</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
            {{--*/ $s = 1 /*--}}
            {{--*/ $total_quantity = 0 /*--}}
            {{--*/ $total_value = 0 /*--}}
            @if ($job_card->JobCardDetails)
                @foreach ($job_card->JobCardDetails as $index => $value)
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                {{--*/ $total_quantity += $value->quantity /*--}}
                {{--*/ $total_value += $value->rate*$value->quantity /*--}}
                <tr>
                    <td style="{{ $style }}">{{ $index+1 }}</td>
                    <td style="{{ $style }}">{{ $value->Item['code'] }}</td>
                    <td style="{{ $style }}">{{ $value->Item['name'] }}</td>
                    <td style="{{ $style }}">{{ $value->Item['model_no'] }}</td>
                    <td style="{{ $style }}">{{ $value->Item->UnitType['code'] }}</td>
                    <td style="{{ $style }}">{{ $value->quantity }}</td>
                </tr>
                {{--*/ $s++ /*--}}
                @endforeach
            @endif
            </tbody>
            <tfoot>
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                <tr>
                    <th colspan="5" style="{{ $style }}">Total</th>
                    <th style="{{ $style }} border-top: 1px double black; border-bottom: 3px double black;">{{ $total_quantity != 0 ? $total_quantity : null }}</th>
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