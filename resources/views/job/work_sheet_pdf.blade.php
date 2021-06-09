@extends('layouts.print')

@section('title')
<title>M3Force | Print Installation Work Sheet</title>
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
        width: 10%;
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
    }
    #header_table td:nth-child(2),
    #header_table td:nth-child(4){
        width: 40%;
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
        border: 1px solid #000000;
    }
    #detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
    }
    #detail_table tbody td{
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
    #detail_table tfoot th{
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

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Installation Work Sheet</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Technical Name</td>
                <td colspan="3" style="border-bottom: 1px dotted black;"></td>
            </tr>
            <tr>
                <td>EPF No</td>
                <td style="border-bottom: 1px dotted black;"></td>
                <td>Month</td>
                <td style="border-bottom: 1px dotted black;"></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-body">
        <table id="detail_table">
            <thead>
                <tr>
                    <th style="width: 5%;">No#</th>
                    <th style="width: 10%;">Job No</th>
                    <th>Customer Name</th>
                    <?php for($i=1; $i<=15; $i++){ ?>
                    <th style="width: 4%;"></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php
                $count=1;
                for($j=0; $j<10; $j++){
            ?>
                <tr>
                    <td height="25"><?php echo $count; ?></td>
                    <td height="25"></td>
                    <td height="25"></td>
                    <?php for($i=1; $i<=15; $i++){ ?>
                    <td height="25"></td>
                    <?php } ?>
                </tr>
            <?php
                    $count++;
                }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th height="25" colspan="3" >Team Leader Signature</th>
                    <?php for($i=1; $i<=15; $i++){ ?>
                    <th height="25"></th>
                    <?php } ?>
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
                <td style="width: 25%; text-align: center;">Operation Manager</td>
                <td style="width: 40%;"></td>
                <td style="width: 25%; text-align: center;">Technical Manager</td>
                <td style="width: 5%;"></td>
            </tr>
        </table>       
    </div> 
</div>
@endsection