@extends('layouts.main')

@section('title')
<title>M3Force | Customer Status Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Status Details</a></li>  
</ul>
@endsection

@section('content')
<style>
    .timeline {
        position: relative;
        padding: 21px 0px 10px;
        margin-top: 4px;
        margin-bottom: 30px;
    }

    .timeline .line {
        position: absolute;
        width: 4px;
        display: block;
        background: currentColor;
        top: 0px;
        bottom: 0px;
        margin-left: 30px;
    }

    .timeline .separator {
        border-top: 1px solid currentColor;
        padding: 5px;
        padding-left: 40px;
        font-style: italic;
        font-size: .9em;
        margin-left: 30px;
    }

    .timeline .line::before { top: -4px; }
    .timeline .line::after { bottom: -4px; }
    .timeline .line::before,
    .timeline .line::after {
        content: '';
        position: absolute;
        left: -4px;
        width: 12px;
        height: 12px;
        display: block;
        border-radius: 50%;
        background: currentColor;
    }

    .timeline .panel {
        position: relative;
        /*margin: 10px 0px 21px 70px;*/
        clear: both;
    }

    .timeline .panel::before {
        position: absolute;
        display: block;
        top: 8px;
        left: -24px;
        content: '';
        width: 0px;
        height: 0px;
        border: inherit;
        border-width: 12px;
        border-top-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    }

    .timeline .panel .panel-heading.icon * { font-size: 20px; vertical-align: middle; line-height: 40px; }
    .timeline .panel .panel-heading.icon {
        position: absolute;
        left: -59px;
        display: block;
        width: 40px;
        height: 40px;
        padding: 0px;
        border-radius: 50%;
        text-align: center;
        float: left;
    }

    .timeline .panel-outline {
        border-color: transparent;
        background: transparent;
        box-shadow: none;
    }

    .timeline .panel-outline .panel-body {
        padding: 10px 0px;
    }

    .timeline .panel-outline .panel-heading:not(.icon),
    .timeline .panel-outline .panel-footer {
        display: none;
    }  
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Customer Status Details</strong></h3>
                    <ul class="panel-controls">
                        <li><a href="{{ asset('customer_status')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body"> 
                    <?php if($result){ ?>
                        <!-- Timeline -->
                        <div class="timeline">

                            <!-- Line component -->
                            <div class="line text-muted"></div>

                            <!-- Panel -->
                            <article class="panel panel-success">

                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Customer Details</h2>
                                </div>
                                <!-- /Heading -->
                                
                                <?php if($result->Contact){ ?>
                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <tr>
                                            <th style="width: 25%;">Business Type</th>
                                            <td style="width: 75%;"><?php echo $result->Contact->IBusinessType ? $result->Contact->IBusinessType->name : ''; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Client Type</th>
                                            <td><?php echo $result->Contact->IClientType ? $result->Contact->IClientType->name : ''; ?></td>
                                        </tr> 
                                        <tr>
                                            <th>Code</th>
                                            <td><?php echo $result->Contact->code; ?></td>
                                        </tr> 
                                        <tr>
                                            <th>Name</th>
                                            <td><?php echo $result->Contact->name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td><?php echo $result->Contact->address; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Contact No</th>
                                            <td><?php echo $result->Contact->contact_no; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- /Body -->
                                <?php } ?>

                            </article>
                            <!-- /Panel -->

                            <!-- Panel -->
                            <article class="panel panel-primary">

                                <?php if($status_type == 1){ ?>
                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Inquiry Details</h2>
                                </div>
                                <!-- /Heading -->

                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <tr>
                                            <th style="width: 25%;">Inquiry No</th>
                                            <td style="width: 75%;"><?php echo $result->inquiry_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Inquiry Date & Time</th>
                                            <td style="width: 75%;"><?php echo date('Y-m-d h:i A', strtotime($result->inquiry_date_time)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Mode of Inquiry</th>
                                            <td><?php echo $result->IModeOfInquiry ? $result->IModeOfInquiry->name : ''; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Contact Of</th>
                                            <td><?php echo $result->contact_of; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Inquiry Type</th>
                                            <td><?php echo $result->IInquiryType ? $result->IInquiryType->name : ''; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Sales Person</th>
                                            <td><?php echo $result->SalesTeam ? $result->SalesTeam->name : ''; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Remarks</th>
                                            <td><?php echo $result->remarks; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Logged User</th>
                                            <td><?php echo $result->User->first_name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Logged Date & Time</th>
                                            <td><?php echo date('Y-m-d h:i A', strtotime($result->created_at)); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <?php 
                                    $job = \App\Model\Job::where('inquiry_id', $result->id)->where('is_delete', 0)->first();
                                    if($job){
                                ?>
                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Job Details</h2>
                                </div>
                                <!-- /Heading -->

                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <tr>
                                            <th style="width: 25%;">Job No</th>
                                            <td style="width: 75%;"><?php echo $job->job_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Job Date & Time</th>
                                            <td style="width: 75%;"><?php echo date('Y-m-d h:i A', strtotime($job->job_date_time)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Job Value</th>
                                            <td><?php echo number_format($job->job_value, 2); ?></td>
                                        </tr>
                                        <?php
                                            $advance_value = 0;
                                            $inquiry_details = \App\Model\InquiryDetials::where('inquiry_id', $job->inquiry_id)
                                                    ->where('inquiry_status_id', 16)
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach ($inquiry_details as $inquiry_detail){
                                                $advance_value += $inquiry_detail->advance_payment;
                                            }
                                        ?>
                                        <tr>
                                            <th>Advance Value</th>
                                            <td><?php echo number_format($advance_value, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Mandays</th>
                                            <td><?php echo $job->mandays; ?></td>
                                        </tr>
                                        <?php
                                            $used_mandays = 0;
                                            $job_attendances = \App\Model\JobAttendance::where('job_id', $job->id)
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach ($job_attendances as $job_attendance){
                                                $used_mandays += $job_attendance->mandays;
                                            }
                                        ?>
                                        <tr>
                                            <th>Used Mandays</th>
                                            <td><?php echo $used_mandays; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <?php } ?>
                                <!-- /Body -->
                                <?php } else if($status_type == 2){ ?>
                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Tech Response Details</h2>
                                </div>
                                <!-- /Heading -->

                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <tr>
                                            <th style="width: 25%;">Tech Response No</th>
                                            <td style="width: 75%;"><?php echo $result->tech_response_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Tech Response Date & Time</th>
                                            <td style="width: 75%;"><?php echo date('Y-m-d h:i A', strtotime($result->record_date_time)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Reported Person</th>
                                            <td><?php echo $result->reported_person; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Reported Contact No</th>
                                            <td><?php echo $result->reported_contact_no; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Reported Email</th>
                                            <td><?php echo $result->reported_email; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Remarks</th>
                                            <td><?php echo $result->remarks; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Logged User</th>
                                            <td><?php echo $result->User->first_name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Logged Date & Time</th>
                                            <td><?php echo date('Y-m-d h:i A', strtotime($result->created_at)); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- /Body -->
                                <?php } ?>

                            </article>
                            <!-- /Panel -->

                            <!-- Panel -->
                            <article class="panel panel-info">

                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Status Timeline</h2>
                                </div>
                                <!-- /Heading -->
                                
                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Update Date & Time</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Status</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Remarks</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Logged User</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Logged Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                <?php
                                    if($status_type == 1){
                                        $result_details = App\Model\InquiryDetials::where('inquiry_id', $result->id)->where('is_delete', 0)->get();
                                        foreach ($result_details as $result_detail){
                                            $remarks = $result_detail->remarks;
                                            $remarks .= $result_detail->SalesTeam ? $remarks != '' ? '<br/>Sales Person : '.$result_detail->SalesTeam->name : 'Sales Person : '.$result_detail->SalesTeam->name : '';
                                            $remarks .= $result_detail->site_inspection_date_time && $result_detail->site_inspection_date_time != '' ? $remarks != '' ? '<br/>Site Inspection Date & Time : '.date('Y-m-d h:i A', strtotime($result_detail->site_inspection_date_time)) : 'Site Inspection Date & Time : '.date('Y-m-d h:i A', strtotime($result_detail->site_inspection_date_time)) : '';
                                            $remarks .= $result_detail->PaymentMode ? $remarks != '' ? '<br/>Payment Mode : '.$result_detail->PaymentMode->name : 'Payment Mode : '.$result_detail->PaymentMode->name : '';
                                            $remarks .= $result_detail->PaymentMode ? $remarks != '' ? '<br/>Receipt No : '.$result_detail->receipt_no : 'Receipt No : '.$result_detail->receipt_no : '';
                                            $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id != 4 ? $remarks != '' ? '<br/>Advance Payment : '.number_format($result_detail->advance_payment, 2) : 'Advance Payment : '.number_format($result_detail->advance_payment, 2) : '';
                                            $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id == 1 ? $remarks != '' ? '<br/>Cheque No : '.$result_detail->cheque_no : 'Cheque No : '.$result_detail->cheque_no : '';
                                            $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id == 1 ? $remarks != '' ? '<br/>Realize Date : '.$result_detail->realize_date : 'Realize Date : '.$result_detail->realize_date : '';
                                            $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id == 3 ? $remarks != '' ? '<br/>Bank : '.$result_detail->bank : 'Bank : '.$result_detail->bank : '';
                                ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('Y-m-d h:i A', strtotime($result_detail->update_date_time)); ?></td>
                                                <td style="vertical-align: middle;"><?php echo $result_detail->InquiryStatus ? $result_detail->InquiryStatus->name : ''; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $remarks; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $result_detail->User ? $result_detail->User->first_name : ''; ?></td>
                                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('Y-m-d h:i A', strtotime($result_detail->created_at)); ?></td>
                                            </tr>
                                <?php
                                        }
                                        $result_job_details = App\Model\JobDetails::whereHas('Job', function ($query) use ($result) {
                                            $query->where('inquiry_id', $result->id)->where('is_delete', 0);
                                            })
                                            ->where('is_delete', 0)
                                            ->get();
                                        foreach ($result_job_details as $result_job_detail){
                                            $remarks = $result_job_detail->remarks;
                                            $remarks .= $result_job_detail->job_scheduled_date_time && $result_job_detail->job_scheduled_date_time != '' ? $remarks != '' ? '<br/>Job Scheduled Date & Time : '.date('Y-m-d h:i A', strtotime($result_job_detail->job_scheduled_date_time)) : 'Job Scheduled Date & Time : '.date('Y-m-d h:i A', strtotime($result_job_detail->job_scheduled_date_time)) : '';
                                ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('Y-m-d h:i A', strtotime($result_job_detail->update_date_time)); ?></td>
                                                <td style="vertical-align: middle;"><?php echo $result_job_detail->JobStatus ? $result_job_detail->JobStatus->name : ''; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $remarks; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $result_job_detail->User ? $result_job_detail->User->first_name : ''; ?></td>
                                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('Y-m-d h:i A', strtotime($result_job_detail->created_at)); ?></td>
                                            </tr>
                                <?php
                                        }
                                    } else if($status_type == 2){
                                        $tech_response_details = \App\Model\TechResponseDetails::where('tech_response_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                        foreach ($tech_response_details as $tech_response_detail){
                                            $remarks = $tech_response_detail->remarks;
                                            $remarks .= $tech_response_detail->job_scheduled_date_time && $tech_response_detail->job_scheduled_date_time != '' ? $remarks != '' ? '<br/>Tech Response Scheduled Date & Time : '.date('Y-m-d h:i A', strtotime($tech_response_detail->job_scheduled_date_time)) : 'Tech Response Scheduled Date & Time : '.date('Y-m-d h:i A', strtotime($tech_response_detail->job_scheduled_date_time)) : '';
                                ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('Y-m-d h:i A', strtotime($tech_response_detail->update_date_time)); ?></td>
                                                <td style="vertical-align: middle;"><?php echo $tech_response_detail->TechResponseStatus ? $tech_response_detail->TechResponseStatus->name : ''; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $remarks; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $tech_response_detail->User ? $tech_response_detail->User->first_name : ''; ?></td>
                                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;"><?php echo date('Y-m-d h:i A', strtotime($tech_response_detail->created_at)); ?></td>
                                            </tr>
                                <?php
                                        }
                                    }
                                ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /Body -->

                            </article>
                            <!-- /Panel -->
                            
                            <?php 
                                if($status_type == 1){
                                    $upload_errors = array();
                                    if(!in_array($result->inquiry_type_id, array(3,5,6))){
                                        $drawing_uploaded = \App\Model\InquiryDetials::where('inquiry_id', $result->id)
                                                ->where('inquiry_status_id', 5)
                                                ->where('is_delete', 0)
                                                ->first();
                                        if(!$drawing_uploaded){
                                            array_push($upload_errors, 'Site Drawing required');
                                        }
                                    }
                                    $quotation_confirmed = \App\Model\Quotation::where('inquiry_id', $result->id)
                                            ->where('is_confirmed', 1)
                                            ->where('is_delete', 0)
                                            ->first();
                                    if(!$quotation_confirmed){
                                        array_push($upload_errors, 'Quotation not confirmed');
                                    }
                                    
                                    $job = \App\Model\Job::where('inquiry_id', $result->id)
                                            ->where('is_delete', 0)
                                            ->first();
                                    if($job){
                                        $job_card_ids = array();
                                        $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                                                ->where('is_confirmed', 1)
                                                ->where('is_delete', 0)
                                                ->get();
                                        foreach ($quotations as $quotation){
                                            foreach ($quotation->QuotationJobCard as $detail){
                                                array_push($job_card_ids, $detail['id']);
                                            }
                                        }

                                        $items = array();
                                        $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('SUM(quantity) AS total_quantity, item_id AS item_id')
                                                ->whereIn('quotation_job_card_id', $job_card_ids)
                                                ->where('is_delete', 0)
                                                ->groupBy('item_id')
                                                ->get();
                                        foreach ($job_card_details as $job_card_detail){
                                            $row = array(
                                                'id' => $job_card_detail->item_id,
                                                'quantity' => $job_card_detail->total_quantity
                                            );
                                            array_push($items, $row);
                                        }

                                        $item_issue = false;
                                        foreach ($items as $item){
                                            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use($job){
                                                            $query->where('item_issue_type_id', 1)->where('document_id', $job->id)->where('is_delete', 0);
                                                        })
                                                        ->where('item_id', $item['id'])
                                                        ->where('is_delete', 0)
                                                        ->get();
                                            $total_qunatity = 0;
                                            foreach ($item_issue_details as $item_issue_detail){
                                                $total_qunatity += $item_issue_detail->quantity;
                                            }
                                            if($total_qunatity != $item['quantity']){
                                                $item_issue = true;                                                
                                            }
                                        }
                                        if($item_issue){
                                            array_push($upload_errors, 'Job card items not issued');
                                        }
                                        
                                        if(in_array($job->Inquiry->inquiry_type_id, array(2,4))){
                                            $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                                                    ->where('job_status_id', 8)
                                                    ->where('is_delete', 0)
                                                    ->first();
                                            if(!$job_status){
                                                array_push($upload_errors, 'Remote Monitoring not connected');
                                            }        
                                        }

                                        if(!in_array($job->Inquiry->inquiry_type_id, array(3,6))){
                                            $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                                                    ->where('job_status_id', 9)
                                                    ->where('is_delete', 0)
                                                    ->first();
                                            if(!$job_status){
                                                array_push($upload_errors, 'Handover Document required');
                                            }
                                        }
                                    }
                            ?>

                            <!-- Panel -->
                            <article class="panel panel-danger">

                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Update Errors</h2>
                                </div>
                                <!-- /Heading -->
                                
                                <!-- Body -->
                                <div class="panel-body">
                                    <ul>
                                        <?php 
                                            for($i=0; $i<count($upload_errors); $i++){
                                        ?>
                                        <li><?php echo $upload_errors[$i]; ?></li>
                                        <?php
                                            }
                                        ?>
                                    </ul>
                                </div>
                                <!-- /Body -->

                            </article>
                            <!-- /Panel -->
                            
                            <?php } ?>

                            <!-- Panel -->
                            <article class="panel panel-warning">

                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Documents</h2>
                                </div>
                                <!-- /Heading -->
                                
                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Document Type</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Document Name</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            if($status_type == 1){
                                                $upload_documents = \App\Model\DocumentUpload::where('document_type_id', 1)
                                                        ->where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($upload_documents as $upload_document){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Site Drawing</td>
                                                <td colspan="2" style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/assets/uploads/documents/<?php echo $upload_document->upload_document; ?>" target="_blank"><?php echo $upload_document->document_name; ?></a></td>
                                            </tr>
                                        <?php
                                                }
                                            }
                                            
                                            if($status_type == 1){
                                                $job_cards = App\Model\JobCard::where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($job_cards as $job_card){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Job Card</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/job_card/print_job_card?id=<?php echo $job_card->id; ?>" target="_blank"><?php echo $job_card->job_card_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $job_card->is_used == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            } else if($status_type == 2){
                                                $tech_response_job_cards = App\Model\TechResponseJobCard::where('tech_response_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($tech_response_job_cards as $tech_response_job_card){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Job Card</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/tech_response_job_card/print_tech_response_job_card?id=<?php echo $tech_response_job_card->id; ?>" target="_blank"><?php echo $tech_response_job_card->tech_response_job_card_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $tech_response_job_card->is_approved == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            }
                                            
                                            if($status_type == 1){
                                                $cost_sheets = App\Model\CostSheet::where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($cost_sheets as $cost_sheet){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Cost Sheet</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/cost_sheet/print_cost_sheet?id=<?php echo $cost_sheet->id; ?>" target="_blank"><?php echo $cost_sheet->cost_sheet_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $cost_sheet->is_used == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            }
                                            
                                            if($status_type == 1){
                                                $quotations = App\Model\Quotation::where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($quotations as $quotation){
                                                    $status = '';
                                                    $status = $quotation->is_confirmed == 0 && $quotation->is_revised == 0 ? 'Pending' : $status;
                                                    $status = $quotation->is_confirmed == 1 ? 'Confirmed' : $status;
                                                    $status = $quotation->is_revised == 1 ? 'Revised' : $status;
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Quotation</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/quotation/print_quotation?id=<?php echo $quotation->id; ?>" target="_blank"><?php echo $quotation->quotation_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $status; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            } else if($status_type == 2){
                                                $tech_response_quotations = App\Model\TechResponseQuotation::where('tech_response_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($tech_response_quotations as $tech_response_quotation){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Quotation</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/tech_response_quotation/print_tech_response_quotation?id=<?php echo $tech_response_quotation->id; ?>" target="_blank"><?php echo $tech_response_quotation->tech_response_quotation_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $tech_response_quotation->is_confirmed == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            }
                                            
                                            if($status_type == 1){
                                                $installation_sheets = App\Model\InstallationSheet::where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($installation_sheets as $installation_sheet){
                                                    $status = '';
                                                    $status = $installation_sheet->is_posted == 0 && $installation_sheet->is_approved == 0 ? 'Pending' : $status;
                                                    $status = $installation_sheet->is_posted == 1 ? 'Completed' : $status;
                                                    $status = $installation_sheet->is_approved == 1 ? 'Authorized' : $status;
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Installation Sheet</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/installation_sheet/print_installation_sheet?id=<?php echo $installation_sheet->id; ?>" target="_blank"><?php echo $installation_sheet->installation_sheet_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $status; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            } else if($status_type == 2){
                                                $tech_response_installation_sheets = App\Model\TechResponseInstallationSheet::where('tech_response_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($tech_response_installation_sheets as $tech_response_installation_sheet){
                                                    $status = '';
                                                    $status = $tech_response_installation_sheet->is_posted == 0 && $tech_response_installation_sheet->is_approved == 0 ? 'Pending' : $status;
                                                    $status = $tech_response_installation_sheet->is_posted == 1 ? 'Completed' : $status;
                                                    $status = $tech_response_installation_sheet->is_approved == 1 ? 'Authorized' : $status;
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Installation Sheet</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/tech_response_installation_sheet/print_tech_response_installation_sheet?id=<?php echo $tech_response_installation_sheet->id; ?>" target="_blank"><?php echo $tech_response_installation_sheet->tech_response_installation_sheet_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $status; ?></td>
                                            </tr>
                                        <?php
                                                }
                                            }
                                            
                                            $item_issues = \App\Model\ItemIssue::where('item_issue_type_id', $status_type)
                                                    ->where(function($q) use($status_type, $result) {
                                                        $status_type == 1 ? $q->whereHas('Job', function ($query) use($result){$query->where('inquiry_id', $result->id);}) : $q->whereHas('TechResponse', function ($query) use($result){$query->where('id', $result->id);});
                                                    })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach ($item_issues as $item_issue){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Item Issue</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/item_issue/print_item_issue?id=<?php echo $item_issue->id; ?>" target="_blank"><?php echo $item_issue->item_issue_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $item_issue->is_posted == 1 ? 'Issued' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                            }
                                            
                                            $item_receives = \App\Model\ItemReceive::whereHas('ItemIssue', function ($query) use($status_type, $result){
                                                        $query->where('item_issue_type_id', $status_type)
                                                                ->where(function($q) use($status_type, $result) {
                                                                    $status_type == 1 ? $q->whereHas('Job', function ($query) use($result){$query->where('inquiry_id', $result->id);}) : $q->whereHas('TechResponse', function ($query) use($result){$query->where('id', $result->id);});
                                                                })
                                                                ->where('is_delete', 0);
                                                    })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach ($item_receives as $item_receive){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Item Return</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/item_receive/print_item_receive?id=<?php echo $item_receive->id; ?>" target="_blank"><?php echo $item_receive->item_receive_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $item_receive->is_posted == 1 ? 'Received' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                            }
                                        ?>
                                            <tr>
                                                <td colspan="3" style="vertical-align: middle; text-align: center;"><a href="<?php echo URL::to('/'); ?>/customer_status/print_item_issue_balance?id=<?php echo $result->id; ?>&status_type=<?php echo $status_type; ?>" target="_blank">Item Issue Balance</a></td>
                                            </tr>
                                        <?php
                                            
                                            $petty_cash_issues = \App\Model\PettyCashIssue::where('petty_cash_issue_type_id', $status_type)
                                                    ->where(function($q) use($status_type, $result) {
                                                        $status_type == 1 ? $q->whereHas('Job', function ($query) use($result){$query->where('inquiry_id', $result->id);}) : $q->whereHas('TechResponse', function ($query) use($result){$query->where('id', $result->id);});
                                                    })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach ($petty_cash_issues as $petty_cash_issue){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Petty Cash Issue</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/petty_cash_issue/print_petty_cash_issue?id=<?php echo $petty_cash_issue->id; ?>" target="_blank"><?php echo $petty_cash_issue->petty_cash_issue_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $petty_cash_issue->is_posted == 1 ? 'Issued' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                            }
                                            
                                            $petty_cash_returns = \App\Model\PettyCashReturn::whereHas('PettyCashIssue', function ($query) use($status_type, $result){
                                                        $query->where('petty_cash_issue_type_id', $status_type)
                                                                ->where(function($q) use($status_type, $result) {
                                                                    $status_type == 1 ? $q->whereHas('Job', function ($query) use($result){$query->where('inquiry_id', $result->id);}) : $q->whereHas('TechResponse', function ($query) use($result){$query->where('id', $result->id);});
                                                                })
                                                                ->where('is_delete', 0);
                                                    })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach ($petty_cash_returns as $petty_cash_return){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Petty Cash Return</td>
                                                <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/petty_cash_return/print_petty_cash_return?id=<?php echo $petty_cash_return->id; ?>" target="_blank"><?php echo $petty_cash_return->petty_cash_return_no; ?></a></td>
                                                <td style="vertical-align: middle;"><?php echo $petty_cash_return->is_posted == 1 ? 'Received' : 'Pending'; ?></td>
                                            </tr>
                                        <?php
                                            }
                                            
                                            if($status_type == 1){
                                                $upload_documents = \App\Model\DocumentUpload::where('document_type_id', 2)
                                                        ->where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($upload_documents as $upload_document){
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;">Handover Document</td>
                                                <td colspan="2" style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/assets/uploads/documents/<?php echo $upload_document->upload_document; ?>" target="_blank"><?php echo $upload_document->document_name; ?></a></td>
                                            </tr>
                                        <?php
                                                }
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /Body -->

                            </article>
                            <!-- /Panel -->

                            <?php 
                                if($status_type == 1){
                                    $job_status = \App\Model\JobDetails::selectRaw('job_id, MAX(job_status_id) AS job_status_id')
                                            ->whereHas('Job', function($query) use($result){
                                                $query->where('inquiry_id', $result->id)->where('is_delete', 0);
                                            })
                                            ->where('is_delete', 0)
                                            ->first();
                                    if($job_status && $job_status->job_status_id){
                            ?>
                            <!-- Panel -->
                            <article class="panel panel-default">

                                <!-- Heading -->
                                <div class="panel-heading">
                                    <h2 class="panel-title">Job Profit/Loss</h2>
                                </div>
                                <!-- /Heading -->
                                
                                <!-- Body -->
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover table-condensed">
                                        <tr>
                                            <th colspan="2" style="font-size: 14px; text-align: center;">Quoted Price</th>
                                            <?php $quoted_price = 0; ?>
                                        </tr> 
                                        <?php
                                            $quotations = \App\Model\Quotation::where('inquiry_id', $result->id)
                                                    ->where('is_confirmed', 1)
                                                    ->where('is_revised', 0)
                                                    ->where('is_delete', 0)
                                                    ->get();
                                            foreach($quotations as $quotation){
                                                $job_card_ids = array(); 
                                                foreach ($quotation->QuotationJobCard as $detail){
                                                    array_push($job_card_ids, $detail['id']);
                                                }
                                                $cost_sheet_ids = array();
                                                foreach ($quotation->QuotationCostSheet as $detail){
                                                    array_push($cost_sheet_ids, $detail['id']);
                                                }

                                                $usd = false;
                                                $usd_rate = 0;
                                               if($quotation->is_currency == 0){
                                                    $usd = true;
                                                    $usd_rate = $quotation->usd_rate;
                                                }

                                                $main_value = $quotation_value = 0;
                                                $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($job_card_details as $job_card_detail){
                                                    $margin = ($job_card_detail->margin + 100)/100;
                                                    $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity)*$usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                                                    if($job_card_detail->is_main == 1){
                                                        $main_value += $value;
                                                    } else{
                                                        $quotation_value += $value;
                                                    }
                                                }  
                                                
                                                foreach ($quotation->QuotationDiscount as $detail){
                                                    if($detail['discount_type_id'] == 1){
                                                        $main_value = $main_value * (100 - $detail['percentage']) / 100;
                                                    }
                                                } 
                                                $quotation_value += $main_value;

                                                $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                $rate_ids = array();
                                                foreach ($cost_sheet_details as $main_cost_sheet_detail){
                                                    if($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)){
                                                        $meters = 0;
                                                        foreach ($cost_sheet_details as $sub_cost_sheet_detail){
                                                            if($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id){
                                                                $meters += $sub_cost_sheet_detail->meters;
                                                            }
                                                        }

                                                        $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate * $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                                                        $quotation_value += $installation_rate * $meters;

                                                        array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                                                    }
                                                }

                                                $manday_rate = \App\Model\Rate::find(1);
                                                foreach ($cost_sheet_details as $cost_sheet_detail){
                                                    $quotation_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                                                    $quotation_value += $usd ? ($cost_sheet_detail->transport+($cost_sheet_detail->traveling_mandays*$manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport+($cost_sheet_detail->traveling_mandays*$manday_rate->value);
                                                    $quotation_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                                                    $quotation_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                                                    $quotation_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                                                    $quotation_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;
                                                }
                                                
                                                foreach ($quotation->QuotationDiscount as $detail){
                                                    if($detail['discount_type_id'] == 2){
                                                        $quotation_value = $quotation_value * (100 - $detail['percentage']) / 100;
                                                    }
                                                }
                                                
                                                $quoted_price += $quotation_value;                                                
                                        ?>
                                        <tr>
                                            <td style="width: 80%; text-align: right;">Quotation No : <?php echo $quotation->quotation_no; ?></td>
                                            <td style="width: 20%; text-align: right;"><?php echo number_format($quotation_value, 2); ?></td>
                                        </tr>
                                        <?php
                                            }
                                        ?> 
                                        <tr>
                                            <th style="width: 80%; text-align: right;">Total</th>
                                            <th style="width: 20%; text-align: right; border-top: 1px double black; border-bottom: 1px double black;"><?php echo number_format($quoted_price, 2); ?></th>
                                        </tr> 
                                    </table>
                                    <table class="table table-striped table-bordered table-hover table-condensed" style="margin-top: 10px;">
                                        <tr>
                                            <th colspan="2" style="font-size: 14px; text-align: center;">Actual Cost</th>
                                            <?php $actual_cost = 0; ?>
                                        </tr> 
                                        <tr>
                                            <td style="width: 80%; text-align: right;">Equipments & Installation Items</td>
                                            <td style="width: 20%; text-align: right;">
                                            <?php
                                                $equipment_installation_items = 0;
                                                
                                                $item_issues = \App\Model\ItemIssue::where('item_issue_type_id', 1)
                                                        ->whereHas('Job', function ($query) use($result){
                                                            $query->where('inquiry_id', $result->id);
                                                        })
                                                        ->where('is_posted', 1)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($item_issues as $item_issue){
                                                    $equipment_installation_items += $item_issue->item_issue_value;
                                                }
                                            
                                                $item_receives = \App\Model\ItemReceive::whereHas('ItemIssue', function ($query) use($result){
                                                            $query->where('item_issue_type_id', 1)
                                                                    ->whereHas('Job', function ($query) use($result){
                                                                        $query->where('inquiry_id', $result->id);
                                                                    })
                                                                    ->where('is_delete', 0);
                                                        })
                                                        ->where('is_posted', 1)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($item_receives as $item_receive){
                                                    $equipment_installation_items -= $item_receive->item_receive_value;
                                                }
                                                $actual_cost += $equipment_installation_items;
                                                echo number_format($equipment_installation_items, 2);
                                            ?>
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td style="width: 80%; text-align: right;">Payments</td>
                                            <td style="width: 20%; text-align: right;">
                                            <?php
                                                $petty_cash_charges = 0;
                                                
                                                $petty_cash_issues = \App\Model\PettyCashIssue::where('petty_cash_issue_type_id', 1)
                                                        ->whereHas('Job', function ($query) use($result){
                                                            $query->where('inquiry_id', $result->id);
                                                        })
                                                        ->where('is_posted', 1)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($petty_cash_issues as $petty_cash_issue){
                                                    $petty_cash_charges += $petty_cash_issue->petty_cash_issue_value;
                                                }
                                            
                                                $petty_cash_returns = \App\Model\PettyCashReturn::whereHas('PettyCashIssue', function ($query) use($result){
                                                            $query->where('petty_cash_issue_type_id', 1)
                                                                    ->whereHas('Job', function ($query) use($result){
                                                                        $query->where('inquiry_id', $result->id);
                                                                    })
                                                                    ->where('is_delete', 0);
                                                        })
                                                        ->where('is_posted', 1)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($petty_cash_returns as $petty_cash_return){
                                                    $petty_cash_charges -= $petty_cash_return->petty_cash_return_value;
                                                }
                                                $actual_cost += $petty_cash_charges;
                                                echo number_format($petty_cash_charges, 2);
                                            ?>
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td style="width: 80%; text-align: right;">Attended Mandays</td>
                                            <td style="width: 20%; text-align: right;">
                                            <?php
                                                $mandays_charges = 0;
                                                
                                                $job_attendances = \App\Model\JobAttendance::where('job_type_id', 1)
                                                        ->whereHas('Job', function ($query) use($result){
                                                            $query->where('inquiry_id', $result->id);
                                                        })
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                foreach ($job_attendances as $job_attendance){
                                                    $mandays_charges += $job_attendance->mandays * 1153.85;
                                                }
                                                $actual_cost += $mandays_charges;
                                                echo number_format($mandays_charges, 2);
                                            ?>
                                            </td>
                                        </tr> 
                                        <tr>
                                            <th style="width: 80%; text-align: right;">Total</th>
                                            <th style="width: 20%; text-align: right; border-top: 1px double black; border-bottom: 1px double black;"><?php echo number_format($actual_cost, 2); ?></th>
                                        </tr> 
                                    </table>
                                    <table class="table table-striped table-bordered table-hover table-condensed" style="margin-top: 10px;">
                                        <tr>
                                            <th style="font-size: 20px; width: 80%; text-align: right;">Gross Profit/Loss</th>
                                            <th style="font-size: 20px; width: 20%; text-align: right; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($quoted_price-$actual_cost, 2); ?></th>
                                        </tr> 
                                    </table>
                                </div>
                                <!-- /Body -->

                            </article>
                            <!-- /Panel -->
                            <?php 
                                    }
                                } 
                            ?>

                        </div>
                        <!-- /Timeline -->
                    <?php } ?> 
                </div>
            </div>
        </form>

    </div>
</div> 

<script type="text/javascript">
    var myApp = angular.module('myModule', [
        'ui.bootstrap',
        'ngAnimate', 
        'ngTouch'
    ]).config(function ($interpolateProvider) {
        // To prevent the conflict of `{{` and `}}` symbols
        // between Blade template engine and AngularJS templating we need
        // to use different symbols for AngularJS.

        $interpolateProvider.startSymbol('<%=');
        $interpolateProvider.endSymbol('%>');
    });
    
    myApp.config(['$httpProvider', function($httpProvider) {
        //initialize get if not there
        if (!$httpProvider.defaults.headers.get) {
            $httpProvider.defaults.headers.get = {};    
        }    

        // Answer edited to include suggestions from comments
        // because previous version of code introduced browser-related errors

        //disable IE ajax request caching
        $httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
        // extra
        $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
        $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
    }]);

    myApp.controller('menuController', function ($scope) {
        angular.element(document.querySelector('#main_menu_completed_jobs')).addClass('active');
    }); 
    
    myApp.controller('mainController', ['$scope', '$http', '$rootScope', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        
    }]);
</script>
@endsection