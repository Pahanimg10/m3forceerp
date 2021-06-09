@extends('layouts.print')

@section('title')
<title>M3Force | Print Company Contact Details Document</title>
@endsection

@section('content')
<style>
    #detail_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 18px;
    }
    #detail_table tr{
        page-break-inside: avoid;
    }
    #detail_table tfoot{
        display: table-row-group;
    }
    #detail_table td:nth-child(1){
        font-weight: bold;
    }
    #detail_table td{
        padding-top: 10px;
        padding-bottom: 10px;
    }
</style>

<div class="panel panel-default" style=" margin-top: 100px;">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; text-align: center; text-decoration: underline; font-family: Verdana, Geneva, sans-serif; font-size: 36px;">24 HOURS SERVICE CENTER</h4>
    </div>
    <div class="panel-body">
        <table id="detail_table" style="margin-top: 50px;">
            <tr>
                <td>Company Name</td>
                <td>M3Force (Pvt) Ltd</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>No. 445, Nawala Road, Rajagiriya, Sri Lanka</td>
            </tr>
            <tr>
                <td>Contact No.</td>
                <td>+94 112 794 646</td>
            </tr>
            <tr>
                <td>Hot Line</td>
                <td>+94 704 599 304, +94 716 289 289</td>
            </tr>
        </table>
    </div>
</div>
@endsection