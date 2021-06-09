<style>
    #detail_table{
        width: 100%;
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
    #detail_table tbody th{
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
        vertical-align: middle;
    }
</style>

Dear Mr. Deepal,
<br/><br/>
Please find bellow order suggestion to maintain minimum stock level.
<br/><br/>
<table id="detail_table">
    <thead>
        <tr>
            <th>No#</th>
            <th>Purchase Type</th>
            <th>Code</th>
            <th>Description</th>
            <th>Model No</th>
            <th>Unit Type</th>
            <th>Reorder Level</th>
            <th>Current Stock</th>
            <th>To Be Order</th>
        </tr>
    </thead>
    <tbody>
        {{--*/ $s = 1 /*--}}
        @foreach ($details as $index => $value)
        {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
        <tr>
            <td style="text-align: center; {{ $style }}">{{ $index+1 }}</td>
            <td style="text-align: center; {{ $style }}">{{ $value['purchase_type'] }}</td>
            <td style="{{ $style }}">{{ $value['code'] }}</td>
            <td style="{{ $style }}">{{ $value['name'] }}</td>
            <td style="{{ $style }}">{{ $value['model_no'] }}</td>
            <td style="text-align: center; {{ $style }}">{{ $value['unit_type'] }}</td>
            <td style="text-align: right; {{ $style }}">{{ ceil($value['reorder_level']) }}</td>
            <td style="text-align: right; {{ $style }}">{{ ceil($value['current_stock']) }}</td>
            <td style="text-align: right; {{ $style }}">{{ ceil($value['to_be_order']) }}</td>
        </tr>
        {{--*/ $s++ /*--}}
        @endforeach
    </tbody>
</table>
<br/>
Please do the needful.
<br/>
Thank you.
<br/><br/>
Best Regards,<br/>
M3Force (PVT) Ltd.