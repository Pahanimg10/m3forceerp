Dear All,
<br/><br/>
Please find bellow M3Force Customer Quotation Confirmation Details.
<br/><br/>
Quotation Details<br/>
<ul style="padding-left: 20px;">
    <li>Quotation No: {{ $quotation_no }}</li>
    <li>Quotation Value: Rs. {{ number_format($quotation_value, 2) }}</li>
    <li>Customer Name: {{ $customer_name }}</li>
    <li>Customer Address: {{ $customer_address }}</li>
    <li>Sales Person: {{ $sales_person }}</li>
    <?php
        $job_cards = App\Model\JobCard::where('inquiry_id', $inquiry_id)
                ->where('is_used', 1)
                ->where('is_delete', 0)
                ->get();
        foreach ($job_cards as $job_card){
    ?>
    <li>Job Card: <a href="<?php echo URL::to('/'); ?>/job_card/print_job_card?id=<?php echo $job_card->id; ?>"><?php echo $job_card->job_card_no; ?></a></li>
    <?php
        }
    ?>
    <li>Advance Update: <a href="http://erp.m3force.com/m3force/public/inquiry/update_inquiry?id={{ $inquiry_id }}">click here</a></li>
    <li>Installation Sheet Update: <a href="http://erp.m3force.com/m3force/public/inquiry/installation_sheet?type=0&id={{ $inquiry_id }}">click here</a></li>
    <li>Customer Status: <a href="http://erp.m3force.com/m3force/public/customer_status/get_customer_status_details?id={{ $inquiry_id }}&status_type_id=1">click here</a></li>
</ul>
<br/>
Please do the needful.
<br/>
Thank you.
<br/><br/>
Best Regards,<br/>
M3Force (PVT) Ltd.