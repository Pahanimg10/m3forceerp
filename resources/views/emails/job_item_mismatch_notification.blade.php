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

Dear All,
<br/><br/>
Please find bellow M3Force Job Item Mismatch Details.
<br/><br/>
Quotation Details<br/>
<ul style="padding-left: 20px;">
    <li>Quotation No: {{ $quotation_no }}</li>
    <li>Quotation Value: Rs. {{ number_format($quotation_value, 2) }}</li>
    <li>Customer Name: {{ $customer_name }}</li>
    <li>Customer Address: {{ $customer_address }}</li>
    <li>Sales Person: {{ $sales_person }}</li>
    <li>Customer Status: <a href="http://erp.m3force.com/m3force/public/customer_status/get_customer_status_details?id={{ $inquiry_id }}&status_type_id=1">click here</a></li>
</ul>
<br/><br/>
Item Mismatch Details<br/>
<table id="detail_table">
    <thead>
        <tr>
            <th>No#</th>
            <th>Code</th>
            <th>Description</th>
            <th>Requested Quantity</th>
            <th>Issued Quantity</th>
            <th>Issued To</th>
            <th>Return Quantity</th>
            <th>Return By</th>
            <th>Pending Quantity</th>
        </tr>
    </thead>
    <tbody>
        {{--*/ $s = 1 /*--}}
        @foreach ($pending_items as $index => $value)
        {{--*/ $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '' /*--}}
        <tr>
            <td style="text-align: center; {{ $style }}">{{ $index+1 }}</td>
            <td style="{{ $style }}">{{ $value['code'] }}</td>
            <td style="{{ $style }}">{{ $value['name'] }}</td>
            <td style="text-align: right; {{ $style }}">{{ $value['requested_quantity'] }}</td>
            <td style="text-align: right; {{ $style }}">{{ $value['issued_quantity'] }}</td>
            <td style="{{ $style }}">{{ $value['issued_issued_to'] }}</td>
            <td style="text-align: right; {{ $style }}">{{ $value['returned_quantity'] }}</td>
            <td style="{{ $style }}">{{ $value['returned_issued_to'] }}</td>
            <td style="text-align: right; {{ $style }}">{{ $value['pending_quantity'] }}</td>
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