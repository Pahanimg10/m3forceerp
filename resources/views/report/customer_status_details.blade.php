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
    .grid {
        width: 100%;
        height: 405px;
    }

    .grid-align-left {
        text-align: left;
    }

    .grid-align-right {
        text-align: right;
    }

    .grid-align {
        text-align: center;
    }

    .ui-grid-header-cell {
        text-align: center;
    }

    .help-block {
        color: red;
        text-align: right;
    }

    .form-horizontal .form-group {
        margin-left: 0;
        margin-right: 0;
    }

    .control-label {
        padding: 15px 0 5px 0;
    }

    input[type="text"]:read-only {
        color: #1CB09A;
    }

    input[type="text"]:disabled {
        color: #1CB09A;
    }

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

    .timeline .line::before {
        top: -4px;
    }

    .timeline .line::after {
        bottom: -4px;
    }

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

    .timeline .panel .panel-heading.icon * {
        font-size: 20px;
        vertical-align: middle;
        line-height: 40px;
    }

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
                    <?php if ($result) { ?>
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

                                <?php if ($result->Contact) { ?>
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

                                <?php if ($status_type == 1) { ?>
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
                                            if ($job) {
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
                                                            foreach ($inquiry_details as $inquiry_detail) {
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
                                                            foreach ($job_attendances as $job_attendance) {
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
                                <?php } else if ($status_type == 2) { ?>
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
                                                if ($status_type == 1) {
                                                    $result_details = App\Model\InquiryDetials::where('inquiry_id', $result->id)->where('is_delete', 0)->get();
                                                    foreach ($result_details as $result_detail) {
                                                        $remarks = $result_detail->remarks;
                                                        $remarks .= $result_detail->SalesTeam ? $remarks != '' ? '<br/>Sales Person : ' . $result_detail->SalesTeam->name : 'Sales Person : ' . $result_detail->SalesTeam->name : '';
                                                        $remarks .= $result_detail->site_inspection_date_time && $result_detail->site_inspection_date_time != '' ? $remarks != '' ? '<br/>Site Inspection Date & Time : ' . date('Y-m-d h:i A', strtotime($result_detail->site_inspection_date_time)) : 'Site Inspection Date & Time : ' . date('Y-m-d h:i A', strtotime($result_detail->site_inspection_date_time)) : '';
                                                        $remarks .= $result_detail->PaymentMode ? $remarks != '' ? '<br/>Payment Mode : ' . $result_detail->PaymentMode->name : 'Payment Mode : ' . $result_detail->PaymentMode->name : '';
                                                        $remarks .= $result_detail->PaymentMode ? $remarks != '' ? '<br/>Receipt No : ' . $result_detail->receipt_no : 'Receipt No : ' . $result_detail->receipt_no : '';
                                                        $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id != 4 ? $remarks != '' ? '<br/>Advance Payment : ' . number_format($result_detail->advance_payment, 2) : 'Advance Payment : ' . number_format($result_detail->advance_payment, 2) : '';
                                                        $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id == 1 ? $remarks != '' ? '<br/>Cheque No : ' . $result_detail->cheque_no : 'Cheque No : ' . $result_detail->cheque_no : '';
                                                        $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id == 1 ? $remarks != '' ? '<br/>Realize Date : ' . $result_detail->realize_date : 'Realize Date : ' . $result_detail->realize_date : '';
                                                        $remarks .= $result_detail->PaymentMode && $result_detail->PaymentMode->id == 3 ? $remarks != '' ? '<br/>Bank : ' . $result_detail->bank : 'Bank : ' . $result_detail->bank : '';
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
                                                        foreach ($result_job_details as $result_job_detail) {
                                                            $remarks = $result_job_detail->remarks;
                                                            $remarks .= $result_job_detail->job_scheduled_date_time && $result_job_detail->job_scheduled_date_time != '' ? $remarks != '' ? '<br/>Job Scheduled Date & Time : ' . date('Y-m-d h:i A', strtotime($result_job_detail->job_scheduled_date_time)) : 'Job Scheduled Date & Time : ' . date('Y-m-d h:i A', strtotime($result_job_detail->job_scheduled_date_time)) : '';
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
                                                    } else if ($status_type == 2) {
                                                        $tech_response_details = \App\Model\TechResponseDetails::where('tech_response_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($tech_response_details as $tech_response_detail) {
                                                            $remarks = $tech_response_detail->remarks;
                                                            $remarks .= $tech_response_detail->job_scheduled_date_time && $tech_response_detail->job_scheduled_date_time != '' ? $remarks != '' ? '<br/>Tech Response Scheduled Date & Time : ' . date('Y-m-d h:i A', strtotime($tech_response_detail->job_scheduled_date_time)) : 'Tech Response Scheduled Date & Time : ' . date('Y-m-d h:i A', strtotime($tech_response_detail->job_scheduled_date_time)) : '';
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
                                if ($status_type == 1) {
                                    $upload_errors = array();
                                    if (!in_array($result->inquiry_type_id, array(3, 5, 6))) {
                                        $drawing_uploaded = \App\Model\InquiryDetials::where('inquiry_id', $result->id)
                                            ->where('inquiry_status_id', 5)
                                            ->where('is_delete', 0)
                                            ->first();
                                        if (!$drawing_uploaded) {
                                            array_push($upload_errors, 'Site Drawing required');
                                        }
                                    }
                                    $quotation_confirmed = \App\Model\Quotation::where('inquiry_id', $result->id)
                                        ->where('is_confirmed', 1)
                                        ->where('is_delete', 0)
                                        ->first();
                                    if (!$quotation_confirmed) {
                                        array_push($upload_errors, 'Quotation not confirmed');
                                    }

                                    $job = \App\Model\Job::where('inquiry_id', $result->id)
                                        ->where('is_delete', 0)
                                        ->first();
                                    if ($job) {
                                        $job_card_ids = array();
                                        $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                                            ->where('is_confirmed', 1)
                                            ->where('is_delete', 0)
                                            ->get();
                                        foreach ($quotations as $quotation) {
                                            foreach ($quotation->QuotationJobCard as $detail) {
                                                array_push($job_card_ids, $detail['id']);
                                            }
                                        }

                                        $items = array();
                                        $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('SUM(quantity) AS total_quantity, item_id AS item_id')
                                            ->whereIn('quotation_job_card_id', $job_card_ids)
                                            ->where('is_delete', 0)
                                            ->groupBy('item_id')
                                            ->get();
                                        foreach ($job_card_details as $job_card_detail) {
                                            $row = array(
                                                'id' => $job_card_detail->item_id,
                                                'quantity' => $job_card_detail->total_quantity
                                            );
                                            array_push($items, $row);
                                        }

                                        $item_issue = false;
                                        foreach ($items as $item) {
                                            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($job) {
                                                $query->where('item_issue_type_id', 1)->where('document_id', $job->id)->where('is_delete', 0);
                                            })
                                                ->where('item_id', $item['id'])
                                                ->where('is_delete', 0)
                                                ->get();
                                            $total_qunatity = 0;
                                            foreach ($item_issue_details as $item_issue_detail) {
                                                $total_qunatity += $item_issue_detail->quantity;
                                            }
                                            if ($total_qunatity != $item['quantity']) {
                                                $item_issue = true;
                                            }
                                        }
                                        if ($item_issue) {
                                            array_push($upload_errors, 'Job card items not issued');
                                        }

                                        if (in_array($job->Inquiry->inquiry_type_id, array(2, 4))) {
                                            $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                                                ->where('job_status_id', 8)
                                                ->where('is_delete', 0)
                                                ->first();
                                            if (!$job_status) {
                                                array_push($upload_errors, 'Remote Monitoring not connected');
                                            }
                                        }

                                        if (!in_array($job->Inquiry->inquiry_type_id, array(3, 6))) {
                                            $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                                                ->where('job_status_id', 9)
                                                ->where('is_delete', 0)
                                                ->first();
                                            if (!$job_status) {
                                                array_push($upload_errors, 'Handover Document required');
                                            }
                                        }

                                        ///////////////////////////////////

                                        $issued_item_ids = $issued_items = $returned_item_ids = $returned_items = array();
                                        $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($job) {
                                            $query->where('item_issue_type_id', 1)
                                                ->where('document_id', $job->id)
                                                ->where('is_posted', 1)
                                                ->where('is_delete', 0);
                                        })
                                            ->where('is_delete', 0)
                                            ->get();
                                        foreach ($item_issue_details as $main_item_issue_detail) {
                                            if (!in_array($main_item_issue_detail->item_id, $issued_item_ids)) {
                                                $issued_quantity = 0;
                                                $item_issue_ids = array();
                                                foreach ($item_issue_details as $sub_item_issue_detail) {
                                                    if ($main_item_issue_detail->item_id == $sub_item_issue_detail->item_id) {
                                                        $issued_quantity += $sub_item_issue_detail->quantity;
                                                        if (!in_array($sub_item_issue_detail->item_issue_id, $item_issue_ids)) {
                                                            array_push($item_issue_ids, $sub_item_issue_detail->item_issue_id);
                                                        }
                                                    }
                                                }
                                                $row = array(
                                                    'id' => $main_item_issue_detail->Item->id,
                                                    'quantity' => $issued_quantity
                                                );
                                                array_push($issued_items, $row);

                                                $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($item_issue_ids) {
                                                    $query->whereIn('item_issue_id', $item_issue_ids)
                                                        ->where('is_posted', 1)
                                                        ->where('is_delete', 0);
                                                })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                                foreach ($item_receive_details as $main_item_receive_detail) {
                                                    if (!in_array($main_item_receive_detail->item_id, $returned_item_ids)) {
                                                        $returned_quantity = 0;
                                                        foreach ($item_receive_details as $sub_item_receive_detail) {
                                                            if ($main_item_receive_detail->item_id == $sub_item_receive_detail->item_id) {
                                                                $returned_quantity += $sub_item_receive_detail->quantity;
                                                            }
                                                        }
                                                        $row = array(
                                                            'id' => $main_item_receive_detail->Item->id,
                                                            'quantity' => $returned_quantity
                                                        );
                                                        array_push($returned_items, $row);
                                                        array_push($returned_item_ids, $main_item_receive_detail->item_id);
                                                    }
                                                }

                                                array_push($issued_item_ids, $main_item_issue_detail->item_id);
                                            }
                                        }

                                        $balance_items = array();
                                        foreach ($issued_items as $issued_item) {
                                            $balance_quantity = $issued_item['quantity'];
                                            $returned_quantity = 0;
                                            foreach ($returned_items as $returned_item) {
                                                if ($issued_item['id'] == $returned_item['id']) {
                                                    $balance_quantity -= $returned_item['quantity'];
                                                    $returned_quantity += $returned_item['quantity'];
                                                }
                                            }
                                            $row = array(
                                                'id' => $issued_item['id'],
                                                'quantity' => $balance_quantity
                                            );
                                            array_push($balance_items, $row);
                                        }

                                        $job_card_ids = $job_card_items = $installation_items = array();
                                        $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                                            ->where('is_confirmed', 1)
                                            ->where('is_revised', 0)
                                            ->where('is_delete', 0)
                                            ->get();
                                        foreach ($quotations as $quotation) {
                                            foreach ($quotation->QuotationJobCard as $detail) {
                                                array_push($job_card_ids, $detail['id']);
                                            }
                                        }

                                        $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('id, item_id, SUM(quantity) as total_quantity')
                                            ->whereIn('quotation_job_card_id', $job_card_ids)
                                            ->where('is_delete', 0)
                                            ->groupBy('item_id')
                                            ->get();
                                        foreach ($job_card_details as $job_card_detail) {
                                            $row = array(
                                                'id' => $job_card_detail->Item->id,
                                                'quantity' => $job_card_detail->total_quantity
                                            );
                                            array_push($job_card_items, $row);
                                        }

                                        $installation_sheet_details = \App\Model\InstallationSheetDetails::selectRaw('id, installation_sheet_id, item_id, SUM(quantity) as total_quantity')
                                            ->whereHas('InstallationSheet', function ($query) use ($quotation) {
                                                $query->where('inquiry_id', $quotation->inquiry_id)->where('is_posted', 1)->where('is_approved', 1)->where('is_delete', 0);
                                            })
                                            ->where('is_delete', 0)
                                            ->groupBy('item_id')
                                            ->get();
                                        foreach ($installation_sheet_details as $installation_sheet_detail) {
                                            $row = array(
                                                'id' => $installation_sheet_detail->Item->id,
                                                'quantity' => $installation_sheet_detail->total_quantity
                                            );
                                            array_push($installation_items, $row);
                                        }

                                        $request_ids = $request_items = array();
                                        foreach ($job_card_items as $job_card_main_item) {
                                            if (!in_array($job_card_main_item['id'], $request_ids)) {
                                                $total_qunatity = 0;
                                                foreach ($job_card_items as $job_card_sub_item) {
                                                    if ($job_card_main_item['id'] == $job_card_sub_item['id']) {
                                                        $total_qunatity += $job_card_sub_item['quantity'];
                                                    }
                                                }
                                                foreach ($installation_items as $installation_item) {
                                                    if ($job_card_main_item['id'] == $installation_item['id']) {
                                                        $total_qunatity += $installation_item['quantity'];
                                                    }
                                                }

                                                $row = array(
                                                    'id' => $job_card_main_item['id'],
                                                    'quantity' => $total_qunatity
                                                );
                                                array_push($request_items, $row);
                                                array_push($request_ids, $job_card_main_item['id']);
                                            }
                                        }
                                        foreach ($installation_items as $installation_main_item) {
                                            if (!in_array($installation_main_item['id'], $request_ids)) {
                                                $total_qunatity = 0;
                                                foreach ($installation_items as $installation_sub_item) {
                                                    if ($installation_main_item['id'] == $installation_sub_item['id']) {
                                                        $total_qunatity += $installation_sub_item['quantity'];
                                                    }
                                                }

                                                $row = array(
                                                    'id' => $installation_main_item['id'],
                                                    'quantity' => $total_qunatity
                                                );
                                                array_push($request_items, $row);
                                                array_push($request_ids, $installation_main_item['id']);
                                            }
                                        }

                                        $pending_items = array();
                                        foreach ($balance_items as $balance_item) {
                                            $requested_quantity = 0;
                                            foreach ($request_items as $request_item) {
                                                if ($balance_item['id'] == $request_item['id']) {
                                                    $requested_quantity += $request_item['quantity'];
                                                }
                                            }
                                            $pending_quantity = $requested_quantity - $balance_item['quantity'];
                                            if ($pending_quantity < 0) {
                                                $row = array(
                                                    'id' => $balance_item['id'],
                                                    'quantity' => $pending_quantity
                                                );
                                                array_push($pending_items, $row);
                                            }
                                        }

                                        if (count($pending_items) > 0) {
                                            array_push($upload_errors, 'Job items mismatched');
                                        }

                                        //////////////////////////////////
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
                                                    for ($i = 0; $i < count($upload_errors); $i++) {
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
                                                if ($status_type == 1) {
                                                    $upload_documents = \App\Model\DocumentUpload::where('document_type_id', 1)
                                                        ->where('inquiry_id', $result->id)
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                    foreach ($upload_documents as $upload_document) {
                                                        ?>
                                                    <tr>
                                                        <td style="vertical-align: middle;">Site Drawing</td>
                                                        <td colspan="2" style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/assets/uploads/documents/<?php echo $upload_document->upload_document; ?>" target="_blank"><?php echo $upload_document->document_name; ?></a></td>
                                                    </tr>
                                                <?php
                                                        }
                                                    }

                                                    if ($status_type == 1) {
                                                        $job_cards = App\Model\JobCard::where('inquiry_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($job_cards as $job_card) {
                                                            ?>
                                                    <tr>
                                                        <td style="vertical-align: middle;">Job Card</td>
                                                        <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/job_card/print_job_card?id=<?php echo $job_card->id; ?>" target="_blank"><?php echo $job_card->job_card_no; ?></a></td>
                                                        <td style="vertical-align: middle;"><?php echo $job_card->is_used == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                                    </tr>
                                                <?php
                                                        }
                                                    } else if ($status_type == 2) {
                                                        $tech_response_job_cards = App\Model\TechResponseJobCard::where('tech_response_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($tech_response_job_cards as $tech_response_job_card) {
                                                            ?>
                                                    <tr>
                                                        <td style="vertical-align: middle;">Job Card</td>
                                                        <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/tech_response_job_card/print_tech_response_job_card?id=<?php echo $tech_response_job_card->id; ?>" target="_blank"><?php echo $tech_response_job_card->tech_response_job_card_no; ?></a></td>
                                                        <td style="vertical-align: middle;"><?php echo $tech_response_job_card->is_approved == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                                    </tr>
                                                <?php
                                                        }
                                                    }

                                                    if ($status_type == 1) {
                                                        $cost_sheets = App\Model\CostSheet::where('inquiry_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($cost_sheets as $cost_sheet) {
                                                            ?>
                                                    <tr>
                                                        <td style="vertical-align: middle;">Cost Sheet</td>
                                                        <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/cost_sheet/print_cost_sheet?id=<?php echo $cost_sheet->id; ?>" target="_blank"><?php echo $cost_sheet->cost_sheet_no; ?></a></td>
                                                        <td style="vertical-align: middle;"><?php echo $cost_sheet->is_used == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                                    </tr>
                                                <?php
                                                        }
                                                    }

                                                    if ($status_type == 1) {
                                                        $quotations = App\Model\Quotation::where('inquiry_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($quotations as $quotation) {
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
                                                    } else if ($status_type == 2) {
                                                        $tech_response_quotations = App\Model\TechResponseQuotation::where('tech_response_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($tech_response_quotations as $tech_response_quotation) {
                                                            ?>
                                                    <tr>
                                                        <td style="vertical-align: middle;">Quotation</td>
                                                        <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/tech_response_quotation/print_tech_response_quotation?id=<?php echo $tech_response_quotation->id; ?>" target="_blank"><?php echo $tech_response_quotation->tech_response_quotation_no; ?></a></td>
                                                        <td style="vertical-align: middle;"><?php echo $tech_response_quotation->is_confirmed == 1 ? 'Confirmed' : 'Pending'; ?></td>
                                                    </tr>
                                                <?php
                                                        }
                                                    }

                                                    if ($status_type == 1) {
                                                        $installation_sheets = App\Model\InstallationSheet::where('inquiry_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($installation_sheets as $installation_sheet) {
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
                                                    } else if ($status_type == 2) {
                                                        $tech_response_installation_sheets = App\Model\TechResponseInstallationSheet::where('tech_response_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($tech_response_installation_sheets as $tech_response_installation_sheet) {
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
                                                        ->where(function ($q) use ($status_type, $result) {
                                                            $status_type == 1 ? $q->whereHas('Job', function ($query) use ($result) {
                                                                $query->where('inquiry_id', $result->id);
                                                            }) : $q->whereHas('TechResponse', function ($query) use ($result) {
                                                                $query->where('id', $result->id);
                                                            });
                                                        })
                                                        ->where('is_delete', 0)
                                                        ->get();
                                                    foreach ($item_issues as $item_issue) {
                                                        ?>
                                                <tr>
                                                    <td style="vertical-align: middle;">Item Issue</td>
                                                    <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/item_issue/print_item_issue?id=<?php echo $item_issue->id; ?>" target="_blank"><?php echo $item_issue->item_issue_no; ?></a></td>
                                                    <td style="vertical-align: middle;"><?php echo $item_issue->is_posted == 1 ? 'Issued' : 'Pending'; ?></td>
                                                </tr>
                                            <?php
                                                }

                                                $item_receives = \App\Model\ItemReceive::whereHas('ItemIssue', function ($query) use ($status_type, $result) {
                                                    $query->where('item_issue_type_id', $status_type)
                                                        ->where(function ($q) use ($status_type, $result) {
                                                            $status_type == 1 ? $q->whereHas('Job', function ($query) use ($result) {
                                                                $query->where('inquiry_id', $result->id);
                                                            }) : $q->whereHas('TechResponse', function ($query) use ($result) {
                                                                $query->where('id', $result->id);
                                                            });
                                                        })
                                                        ->where('is_delete', 0);
                                                })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                                foreach ($item_receives as $item_receive) {
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
                                                    ->where(function ($q) use ($status_type, $result) {
                                                        $status_type == 1 ? $q->whereHas('Job', function ($query) use ($result) {
                                                            $query->where('inquiry_id', $result->id);
                                                        }) : $q->whereHas('TechResponse', function ($query) use ($result) {
                                                            $query->where('id', $result->id);
                                                        });
                                                    })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                                foreach ($petty_cash_issues as $petty_cash_issue) {
                                                    ?>
                                                <tr>
                                                    <td style="vertical-align: middle;">Petty Cash Issue</td>
                                                    <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/petty_cash_issue/print_petty_cash_issue?id=<?php echo $petty_cash_issue->id; ?>" target="_blank"><?php echo $petty_cash_issue->petty_cash_issue_no; ?></a></td>
                                                    <td style="vertical-align: middle;"><?php echo $petty_cash_issue->is_posted == 1 ? 'Issued' : 'Pending'; ?></td>
                                                </tr>
                                            <?php
                                                }

                                                $petty_cash_returns = \App\Model\PettyCashReturn::whereHas('PettyCashIssue', function ($query) use ($status_type, $result) {
                                                    $query->where('petty_cash_issue_type_id', $status_type)
                                                        ->where(function ($q) use ($status_type, $result) {
                                                            $status_type == 1 ? $q->whereHas('Job', function ($query) use ($result) {
                                                                $query->where('inquiry_id', $result->id);
                                                            }) : $q->whereHas('TechResponse', function ($query) use ($result) {
                                                                $query->where('id', $result->id);
                                                            });
                                                        })
                                                        ->where('is_delete', 0);
                                                })
                                                    ->where('is_delete', 0)
                                                    ->get();
                                                foreach ($petty_cash_returns as $petty_cash_return) {
                                                    ?>
                                                <tr>
                                                    <td style="vertical-align: middle;">Petty Cash Return</td>
                                                    <td style="vertical-align: middle;"><a href="<?php echo URL::to('/'); ?>/petty_cash_return/print_petty_cash_return?id=<?php echo $petty_cash_return->id; ?>" target="_blank"><?php echo $petty_cash_return->petty_cash_return_no; ?></a></td>
                                                    <td style="vertical-align: middle;"><?php echo $petty_cash_return->is_posted == 1 ? 'Received' : 'Pending'; ?></td>
                                                </tr>
                                                <?php
                                                    }

                                                    if ($status_type == 1) {
                                                        $upload_documents = \App\Model\DocumentUpload::where('document_type_id', 2)
                                                            ->where('inquiry_id', $result->id)
                                                            ->where('is_delete', 0)
                                                            ->get();
                                                        foreach ($upload_documents as $upload_document) {
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
                                if ($status_type == 1) {
                                    ?>
                                <!-- Panel -->
                                <article class="panel panel-default">

                                    <!-- Heading -->
                                    <div class="panel-heading">
                                        <h2 class="panel-title">Job Profit/Loss</h2>
                                        <a href="<?php echo URL::to('/'); ?>/customer_status/print_lost_profit?id=<?php echo $result->id; ?>" class="btn btn-default" style="float: right;" target="_blank">Print</a>
                                    </div>
                                    <!-- /Heading -->

                                    <!-- Body -->
                                    <div class="panel-body">
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><strong>Actual Expenses</strong></h3>
                                                <ul class="panel-controls">
                                                    <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                                                    <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                                                    <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                                                    <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                                                </ul>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">

                                                            <input type="hidden" id="data_record_id" name="data_record_id" ng-model="data.record_id" class="form-control" />
                                                            <input type="hidden" id="data_expenses_id" name="data_expenses_id" ng-model="data.expenses_id" class="form-control" />

                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="control-label">Expenses Date</label>
                                                                    <input type="text" id="expenses_date" name="expenses_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.expenses_date" is-open="expensesDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenExpensesDate()" class="form-control text-center" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="control-label">Expenses Time</label>
                                                                    <input type="text" id="expenses_time" name="expenses_time" ng-model="data.expenses_time" class="form-control text-center" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label">Expenses Type</label>
                                                                    <select name="expenses_type" id="expenses_type" ng-options="option.name for option in expenses_type_array track by option.id" ng-model="data.expenses_type" class="form-control"></select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label">Expenses Value</label>
                                                                    <input type="text" id="expenses_value" name="expenses_value" ng-model="data.expenses_value" class="form-control text-right" />
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" style="margin-top: 20px;">
                                                        <div ui-grid="gridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </article>
                                <!-- /Panel -->
                            <?php
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
    var submitForm;

    var myApp = angular.module('myModule', [
        'ui.bootstrap',
        'ngAnimate',
        'ngTouch',
        'ui.grid',
        'ui.grid.edit',
        'ui.grid.rowEdit',
        'ui.grid.selection',
        'ui.grid.exporter',
        'ui.grid.pagination',
        'ui.grid.moveColumns',
        'ui.grid.resizeColumns',
        'ui.grid.cellNav'
    ]).config(function($interpolateProvider) {
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

    myApp.controller('menuController', function($scope) {
        angular.element(document.querySelector('#main_menu_customer_status')).addClass('active');
    });

    myApp.directive('jValidate', function() {
        return {
            link: function(scope, element, attr) {
                element.validate({
                    rules: {
                        expenses_date: {
                            required: true,
                            date: true
                        },
                        expenses_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        expenses_type: {
                            required: true
                        },
                        expenses_value: {
                            required: true,
                            number: true,
                            min: 0
                        },
                        errorClass: 'error'
                    },
                    messages: {
                        expenses_date: {
                            required: 'Expenses Date is required',
                            date: 'Invalid date format'
                        },
                        expenses_time: {
                            required: 'Expenses Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        expenses_type: {
                            required: 'Expenses Type is required'
                        },
                        quantity: {
                            required: 'Expenses Value is required',
                            remote: 'Expenses Value exceeded',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        }
                    },
                    highlight: function(element) {
                        $(element).removeClass("valid");
                        $(element).addClass("error");
                    },
                    unhighlight: function(element) {
                        $(element).removeClass("error");
                        $(element).addClass("valid");
                    },
                    errorElement: 'label',
                    errorClass: 'message_lable',
                    submitHandler: function(form) {
                        submitForm();
                    },
                    invalidHandler: function(event, validator) {
                        //
                    }

                });

                scope.$on('$destroy', function() {
                    // Perform cleanup.
                    // (Not familiar with the plugin so don't know what should to be 
                });
            }
        }
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {
        $scope.data = [];
        $scope.expenses_type_array = [];

        $scope.data.record_id = <?php echo $result ? $result->id : 0; ?>;

        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };

        $scope.expensesDatePopup = {
            opened: false
        };
        $scope.OpenExpensesDate = function() {
            $scope.expensesDatePopup.opened = !$scope.expensesDatePopup.opened;
        };

        $('#expenses_time').mask('00:00');

        $scope.resetForm = function() {
            $http({
                method: 'GET',
                url: base_url + '/customer_status/get_data'
            }).then(function successCallback(response) {
                var expenses_type_array = [];
                expenses_type_array.push({
                    id: '',
                    name: 'Select Expenses Type'
                });
                $.each(response.data.expenses_types, function(index, value) {
                    expenses_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });

                $scope.expenses_type_array = expenses_type_array;

                var today = new Date();
                var hh = today.getHours();
                var mm = today.getMinutes();
                if (hh < 10) {
                    hh = '0' + hh;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }

                $scope.data = {
                    record_id: $scope.data.record_id,
                    expenses_id: 0,
                    expenses_date: new Date(),
                    expenses_time: hh + ':' + mm,
                    expenses_type: $scope.expenses_type_array.length > 0 ? $scope.expenses_type_array[0] : {},
                    expenses_value: ''
                };
            }, function errorCallback(response) {
                console.log(response);
            });

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.resetForm();

        $scope.gridOptions = {
            columnDefs: [{
                    field: 'id',
                    type: 'number',
                    sort: {
                        direction: 'desc',
                        priority: 0
                    },
                    visible: false
                },
                {
                    field: 'options',
                    displayName: '',
                    enableFiltering: false,
                    enableSorting: false,
                    enableCellEdit: false,
                    width: '10%',
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>',
                    visible: $scope.edit_disable ? false : true
                },
                {
                    field: 'expenses_date_time',
                    displayName: 'Expenses Date & Time',
                    cellClass: 'grid-align',
                    width: '30%',
                    enableCellEdit: false
                },
                {
                    field: 'expenses_type',
                    displayName: 'Expenses Type',
                    width: '30%',
                    enableCellEdit: false
                },
                {
                    field: 'expenses_value',
                    type: 'number',
                    displayName: 'Expenses Value',
                    cellClass: 'grid-align-right',
                    width: '30%',
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                }
            ],
            showColumnFooter: true,
            enableCellEditOnFocus: true,
            enableRowSelection: false,
            enableRowHeaderSelection: false,
            paginationPageSizes: [10, 25, 50],
            paginationPageSize: 10,
            enableFiltering: false,
            enableSorting: true,
            enableCellEdit: false,
            enableColumnResizing: true,
            exporterLinkLabel: 'get your csv here',
            exporterCsvFilename: '<?php echo $result->Contact->name; ?> Actual Expenses.csv',
            onRegisterApi: function(gridApi) {
                $scope.gridApi = gridApi;
            }
        };

        $scope.export = function() {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function() {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };

        $scope.getAggregationTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].expenses_value);
            }
            return total_value;
        };

        $scope.editRecord = function(row) {
            $http({
                method: 'GET',
                url: base_url + '/customer_status/find_expenses',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    var expenses_date_time = response.data.expenses_date_time.split(' ');
                    $scope.data = {
                        record_id: response.data.record_id,
                        expenses_id: response.data.id,
                        expenses_date: expenses_date_time[0],
                        expenses_time: expenses_date_time[1],
                        expenses_type: response.data.expenses_type ? {
                            id: response.data.expenses_type.id,
                            name: response.data.expenses_type.name
                        } : {},
                        expenses_value: response.data.expenses_value
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function(row) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + row.entity.expenses_type + "</strong> : " + row.entity.expenses_value + " actual expenses!",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function() {
                    $http.delete(base_url + '/customer_status/' + row.entity.id, row.entity).success(function(response_delete) {
                        swal({
                            title: "Deleted!",
                            text: row.entity.expenses_type + "</strong> : " + row.entity.expenses_value + " actual expenses has been deleted.",
                            html: true,
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                });
        };

        $scope.submitForm = function() {
            $('#dataForm').submit();
        };

        submitForm = function() {
            $('#save_button').prop('disabled', true);
            if ($scope.data.expenses_id == 0) {
                $http.post(base_url + '/customer_status', $scope.data).success(function(result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Actual Expenses',
                        text: result.message,
                        type: result.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    $scope.resetForm();
                    $scope.main_refresh();
                });
            } else {
                $http.put(base_url + '/customer_status/' + $scope.data.expenses_id, $scope.data).success(function(result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Actual Expenses',
                        text: result.message,
                        type: result.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    $scope.resetForm();
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function() {
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/customer_status/actual_expenses_list',
                params: {
                    id: $scope.data.record_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.actual_expenses, function(index, value) {
                    data_array.push({
                        id: value.id,
                        expenses_date_time: value.expenses_date_time,
                        expenses_type: value.expenses_type ? value.expenses_type.name : '',
                        expenses_value: parseFloat(Math.round(value.expenses_value * 100) / 100).toFixed(2)
                    });
                });
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function() {
            $scope.main_refresh();
        }, 1500, false);

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection