Dear All,
<br/><br/>
Please find bellow M3Force Job Confirmation Details.
<br/><br/>
Job Details<br/>
<ul style="padding-left: 20px;">
    <li>Job Date & Time: {{ $job_date_time }}</li>
    <li>Job Type: {{ $job_type }}</li>
    <li>Job No: {{ $job_no }}</li>
    <li>Job Value: Rs. {{ number_format($job_value, 2) }}</li>
    <li>Customer Name: {{ $customer_name }}</li>
    <li>Customer Address: {{ $customer_address }}</li>
    <li>Sales Person: {{ $sales_person }}</li>
    <li>Customer Status: <a href="http://erp.m3force.com/m3force/public/customer_status/get_customer_status_details?id={{ $inquiry_id }}&status_type_id=1">click here</a></li>
</ul>
<br/>
Please do the needful.
<br/>
Thank you.
<br/><br/>
Best Regards,<br/>
M3Force (PVT) Ltd.