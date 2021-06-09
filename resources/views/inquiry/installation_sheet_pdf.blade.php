@extends('layouts.print')

@section('title')
<title>M3Force | Print Installation Sheet</title>
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
    #detail_table tbody td{
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

@if ($installation_sheet)
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Installation Sheet</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td>{{ $installation_sheet->Inquiry->Contact->name }}</td>
                <td>Installation Sheet No</td>
                <td>{{ $installation_sheet->installation_sheet_no }}</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>{{ $installation_sheet->Inquiry->Contact->address }}</td>
                <td>Date & Time</td>
                <td>{{ $installation_sheet->installation_sheet_date_time }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $installation_sheet->Inquiry->Contact->email }}</td>
                <td>Remarks</td>
                <td>{{ $installation_sheet->remarks }}</td>
            </tr>
            <tr>
                <td>Contact No</td>
                <td>{{ $installation_sheet->Inquiry->Contact->contact_no }}</td>
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
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
            {{--*/ $s = 1 /*--}}
            {{--*/ $total_quantity = 0 /*--}}
            {{--*/ $total_value = 0 /*--}}
            @if ($installation_sheet->InstallationSheetDetails)
                @foreach ($installation_sheet->InstallationSheetDetails as $index => $value)
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                {{--*/ $total_quantity += $value->quantity /*--}}
                {{--*/ $total_value += $value->rate*$value->quantity /*--}}
                <tr>
                    <td style="{{ $style }}">{{ $index+1 }}</td>
                    <td style="{{ $style }}">{{ $value->Item['code'] }}</td>
                    <td style="{{ $style }}">{{ $value->Item['name'] }}</td>
                    <td style="{{ $style }}">{{ $value->Item['model_no'] }}</td>
                    <td style="{{ $style }}">{{ $value->Item->UnitType['code'] }}</td>
                    <td style="{{ $style }}">{{ number_format($value->rate, 2) }}</td>
                    <td style="{{ $style }}">{{ $value->quantity }}</td>
                    <td style="{{ $style }}">{{ number_format($value->rate*$value->quantity, 2) }}</td>
                    <td style="{{ $style }}">{{ $value->Item['stock'] }}</td>
<!--                    <td style="{{ $style }}">
                        <?php
//                            $good_receive_details = \App\Model\GoodReceiveDetails::with(array('GoodReceive' => function($query) {
//                                        $query->where('is_posted', 1)->where('is_delete', 0)->orderBy('good_receive_date_time', 'asc');
//                                    }))
//                                    ->where('item_id', $value->Item['id'])
//                                    ->where('available_quantity', '>', 0)
//                                    ->where('is_delete', 0)
//                                    ->get();
//                            $location = '';
//                            foreach ($good_receive_details as $index => $value){
//                                $location .= $index == 0 ? $value->location : ' '.$value->location;
//                            }
//                            echo $location;
                        ?>
                    </td>-->
                </tr>
                {{--*/ $s++ /*--}}
                @endforeach
            @endif
            </tbody>
            <tfoot>
                {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
                <tr>
                    <th colspan="6" style="{{ $style }}">Total</th>
                    <th style="{{ $style }} border-top: 1px double black; border-bottom: 3px double black;">{{ $total_quantity != 0 ? $total_quantity : null }}</th>
                    <th style="{{ $style }} border-top: 1px double black; border-bottom: 3px double black;">{{ $total_value != 0 ? number_format($total_value, 2) : null }}</th>
                    <th style="{{ $style }}"></th>
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