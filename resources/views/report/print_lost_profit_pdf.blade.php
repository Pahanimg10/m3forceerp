@extends('layouts.print')

@section('title')
<title>M3Force | Print Lost & Profit Details</title>
@endsection

@section('content')
<style>
    #header_table {
        width: 100%;
        font-family: Verdana, Geneva, sans-serif;
        font-size: 12px;
    }

    #header_table tr {
        page-break-inside: avoid;
    }

    #header_table tfoot {
        display: table-row-group;
    }

    #header_table td:nth-child(1),
    #header_table td:nth-child(3) {
        width: 15%;
        vertical-align: middle;
        white-space: nowrap;
    }

    #header_table td:nth-child(2),
    #header_table td:nth-child(4) {
        width: 35%;
        vertical-align: middle;
    }

    #detail_table {
        width: 100%;
        font-family: Verdana, Geneva, sans-serif;
        font-size: 12px;
        border-collapse: collapse;
    }

    #detail_table thead th {
        text-align: center;
        padding: 5px;
        background-color: #e3e0dd;
    }

    #detail_table tbody td {
        padding: 5px;
    }

    #detail_table tr {
        page-break-inside: avoid;
    }

    #detail_table tfoot {
        display: table-row-group;
    }

    #detail_table,
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th {
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
        padding: 5px;
    }

    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th,
    #detail_table tfoot td {
        vertical-align: middle;
    }
</style>

<?php
if ($record) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Lost & Profit Details</h4>
        </div>
        <div class="panel-body">
            <table id="header_table">
                <tr>
                    <td>Customer Name</td>
                    <td><?php echo $record->Contact->name; ?></td>
                    <td>Address</td>
                    <td><?php echo $record->Contact->address; ?></td>
                </tr>
                <tr>
                    <td>Contact No</td>
                    <td><?php echo $record->Contact->contact_no; ?></td>
                    <td>Email</td>
                    <td><?php echo $record->Contact->email; ?></td>
                </tr>
                <tr>
                    <td>Document No</td>
                    <td>
                        <?php
                            $job = App\Model\Job::where('inquiry_id', $record->id)->where('is_delete', 0)->first();
                            if ($job) {
                                echo $job->job_no;
                            } else {
                                echo $record->inquiry_no;
                            }
                            ?>
                    </td>
                    <td>Printed Time</td>
                    <td><?php echo date('Y-m-d H:i'); ?></td>
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
                        <th>Description</th>
                        <th>Rate</th>
                        <th>Quantity</th>
                        <th>Margin</th>
                        <th>Cost Value</th>
                        <th>Quatation Value</th>
                        <th>Budgeted Lost & Profit</th>
                        <th>Maximum Purchase Value</th>
                        <th>Actual Cost</th>
                        <th>Actual Lost & Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $quotations = \App\Model\Quotation::where(function ($q) use ($record) {
                            $record ? $q->where('inquiry_id', $record->id) : '';
                        })
                            ->where('is_confirmed', 1)
                            ->where('is_revised', 0)
                            ->where('is_delete', 0)
                            ->get();

                        $s = 1;
                        $count = 1;
                        $excavation_work = $transport = $food = $accommodation = $bata = $other_expenses = 0;
                        $quatation_installation_cost = $quatation_labour = $quatation_installation = $quatation_other = 0;
                        $job_installation_cost = 0;
                        $total_item_cost_value = $total_item_quotation_value = $total_item_lost_profit = $total_item_actual_cost = $total_item_budgeted_lost_profit = 0;
                        $total_installation_cost_value = $total_installation_quotation_value = $total_installation_lost_profit = $total_installation_actual_cost = $total_installation_budgeted_lost_profit = 0;
                        $total_cost_value = $total_quotation_value = $total_lost_profit = $total_actual_cost = $total_budgeted_lost_profit = 0;
                        $package_discount = $special_discount = 0;
                        $total_discount_quotation_value = $total_discount_budgeted_lost_profit = 0;
                        $issued_item_ids = array();
                        foreach ($quotations as $quotation) {
                            $job_card_ids = array();
                            foreach ($quotation->QuotationJobCard as $detail) {
                                array_push($job_card_ids, $detail['id']);
                            }
                            $cost_sheet_ids = array();
                            foreach ($quotation->QuotationCostSheet as $detail) {
                                array_push($cost_sheet_ids, $detail['id']);
                            }

                            $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                ->where('is_delete', 0)
                                ->get();
                            foreach ($job_card_details as $job_card_detail) {
                                $margin = ($job_card_detail->margin + 100) / 100;
                                $job_card_value = $job_card_detail->rate * $job_card_detail->quantity;
                                $total_cost_value += $job_card_value;
                                $total_item_cost_value += $job_card_value;
                                $quotation_value = $job_card_detail->rate * $margin * $job_card_detail->quantity;
                                $total_quotation_value += $quotation_value;
                                $total_item_quotation_value += $quotation_value;
                                $lost_profit = $quotation_value - $job_card_value;
                                $total_lost_profit += $lost_profit;
                                $total_item_lost_profit += $lost_profit;
                                $maximum_purchase_value = $quotation_value / ($job_card_detail->quantity * 1.3);

                                $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($record) {
                                    $query->whereHas('Job', function ($query) use ($record) {
                                        $query->where('inquiry_id', $record->id)->where('is_delete', 0);
                                    })
                                        ->where('item_issue_type_id', 1)
                                        ->where('is_delete', 0);
                                })
                                    ->where('item_id', $job_card_detail->item_id)
                                    ->where('is_delete', 0)
                                    ->get();
                                $actual_cost = 0;
                                if (count($item_issue_details) > 0) {
                                    foreach ($item_issue_details as $item_issue_detail) {
                                        $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                                            ->where('is_delete', 0)
                                            ->get();
                                        foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                                            $actual_cost += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                                        }
                                    }
                                    if (!in_array($job_card_detail->item_id, $issued_item_ids)) {
                                        array_push($issued_item_ids, $job_card_detail->item_id);
                                    }
                                }
                                $total_actual_cost += $actual_cost;
                                $total_item_actual_cost += $actual_cost;
                                $budgeted_lost_profit = $quotation_value - $actual_cost;
                                $total_budgeted_lost_profit += $budgeted_lost_profit;
                                $total_item_budgeted_lost_profit += $budgeted_lost_profit;

                                $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                                ?>
                            <tr>
                                <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                                <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $job_card_detail->Item->name; ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($job_card_detail->rate, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo $job_card_detail->quantity; ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($job_card_detail->margin, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($job_card_value, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($quotation_value, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($maximum_purchase_value, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_cost, 2); ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                            </tr>
                    <?php
                                $s++;
                                $count++;
                            }

                            $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                ->where('is_delete', 0)
                                ->get();
                            $rate_ids = array();
                            $temp_installation_cost = $temp_labour = $temp_quatation_installation = $temp_other = 0;
                            foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                                if ($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                                    $meters = 0;
                                    foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                                        if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                            $meters += $sub_cost_sheet_detail->meters;
                                        }
                                    }

                                    $quatation_installation_cost += $main_cost_sheet_detail->InstallationRate->installation_cost * $meters;
                                    $quatation_labour += $main_cost_sheet_detail->InstallationRate->labour * $meters;
                                    $quatation_installation += $main_cost_sheet_detail->InstallationRate->rate * $meters;

                                    $temp_installation_cost += $main_cost_sheet_detail->InstallationRate->installation_cost * $meters;
                                    $temp_labour += $main_cost_sheet_detail->InstallationRate->labour * $meters;
                                    $temp_quatation_installation += $main_cost_sheet_detail->InstallationRate->rate * $meters;

                                    array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                                }
                            }

                            $manday_rate = \App\Model\Rate::find(1);
                            foreach ($cost_sheet_details as $cost_sheet_detail) {
                                $excavation_work += $cost_sheet_detail->excavation_work;
                                $transport += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                                $food += $cost_sheet_detail->food;
                                $accommodation += $cost_sheet_detail->accommodation;
                                $bata += $cost_sheet_detail->bata;
                                $other_expenses += $cost_sheet_detail->other_expenses;
                                $quatation_installation_cost += $cost_sheet_detail->other_expenses / 2;
                                $quatation_labour += $cost_sheet_detail->other_expenses / 2;
                                $temp_other += $cost_sheet_detail->other_expenses;
                                $temp_installation_cost += $cost_sheet_detail->other_expenses / 2;
                                $temp_labour += $cost_sheet_detail->other_expenses / 2;
                            }

                            $quatation_other += ($temp_quatation_installation + $temp_other) - ($temp_installation_cost + $temp_labour);
                        }

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <th colspan="5" style="<?php echo $style; ?> text-align: left; vertical-align: middle;">Total - Equipment</th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_item_cost_value, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_item_quotation_value, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_item_lost_profit, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_item_actual_cost, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_item_budgeted_lost_profit, 2); ?></th>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="11" style="<?php echo $style; ?> vertical-align: middle;"></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $installation_details = \App\Model\InstallationSheetDetails::whereHas('InstallationSheet', function ($query) use ($record) {
                            $query->where('inquiry_id', $record->id)->where('is_posted', 1)->where('is_approved', 1)->where('is_delete', 0);
                        })
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($installation_details as $installation_detail) {
                            $job_installation_cost += $installation_detail->rate * $installation_detail->quantity;
                        }

                        $total_cost_value += $job_installation_cost;
                        $total_installation_cost_value += $job_installation_cost;
                        $total_quotation_value += $quatation_installation_cost;
                        $total_installation_quotation_value += $quatation_installation_cost;
                        $lost_profit = $quatation_installation_cost - $job_installation_cost;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($record) {
                            $query->whereHas('Job', function ($query) use ($record) {
                                $query->where('inquiry_id', $record->id)->where('is_delete', 0);
                            })
                                ->where('item_issue_type_id', 1)
                                ->where('is_delete', 0);
                        })
                            ->whereNotIn('item_id', $issued_item_ids)
                            ->where('is_delete', 0)
                            ->get();
                        $actual_installation_cost = 0;
                        if (count($item_issue_details) > 0) {
                            foreach ($item_issue_details as $item_issue_detail) {
                                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                                    ->where('is_delete', 0)
                                    ->get();
                                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                                    $actual_installation_cost += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                                }
                            }
                        }
                        $total_actual_cost += $actual_installation_cost;
                        $total_installation_actual_cost += $actual_installation_cost;
                        $budgeted_lost_profit = $quatation_installation_cost - $actual_installation_cost;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Installation</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($job_installation_cost, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($quatation_installation_cost, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_installation_cost, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $quatation_labour;
                        $total_installation_cost_value += $quatation_labour;
                        $total_quotation_value += $quatation_labour;
                        $total_installation_quotation_value += $quatation_labour;
                        $lost_profit = $quatation_labour - $quatation_labour;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_expenses = \App\Model\ActualExpenses::where('record_id', $record->id)->where('expenses_type_id', 1)->where('is_delete', 0)->get();
                        $actual_labour = 0;
                        foreach ($actual_expenses as $actual_expens) {
                            $actual_labour += $actual_expens->expenses_value;
                        }
                        $total_actual_cost += $actual_labour;
                        $total_installation_actual_cost += $actual_labour;
                        $budgeted_lost_profit = $quatation_labour - $actual_labour;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Labour</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($quatation_labour, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($quatation_labour, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_labour, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $excavation_work;
                        $total_installation_cost_value += $excavation_work;
                        $total_quotation_value += $excavation_work;
                        $total_installation_quotation_value += $excavation_work;
                        $lost_profit = $excavation_work - $excavation_work;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_expenses = \App\Model\ActualExpenses::where('record_id', $record->id)->where('expenses_type_id', 2)->where('is_delete', 0)->get();
                        $actual_excavation_work = 0;
                        foreach ($actual_expenses as $actual_expens) {
                            $actual_excavation_work += $actual_expens->expenses_value;
                        }
                        $total_actual_cost += $actual_excavation_work;
                        $total_installation_actual_cost += $actual_excavation_work;
                        $budgeted_lost_profit = $excavation_work - $actual_excavation_work;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Excavation Work</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($excavation_work, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($excavation_work, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_excavation_work, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $transport;
                        $total_installation_cost_value += $transport;
                        $total_quotation_value += $transport;
                        $total_installation_quotation_value += $transport;
                        $lost_profit = $transport - $transport;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_expenses = \App\Model\ActualExpenses::where('record_id', $record->id)->where('expenses_type_id', 3)->where('is_delete', 0)->get();
                        $actual_transport = 0;
                        foreach ($actual_expenses as $actual_expens) {
                            $actual_transport += $actual_expens->expenses_value;
                        }
                        $total_actual_cost += $actual_transport;
                        $total_installation_actual_cost += $actual_transport;
                        $budgeted_lost_profit = $transport - $actual_transport;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Transport</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($transport, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($transport, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_transport, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $food;
                        $total_installation_cost_value += $food;
                        $total_quotation_value += $food;
                        $total_installation_quotation_value += $food;
                        $lost_profit = $food - $food;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_expenses = \App\Model\ActualExpenses::where('record_id', $record->id)->where('expenses_type_id', 4)->where('is_delete', 0)->get();
                        $actual_food = 0;
                        foreach ($actual_expenses as $actual_expens) {
                            $actual_food += $actual_expens->expenses_value;
                        }
                        $total_actual_cost += $actual_food;
                        $total_installation_actual_cost += $actual_food;
                        $budgeted_lost_profit = $food - $actual_food;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Food</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($food, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($food, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_food, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $accommodation;
                        $total_installation_cost_value += $accommodation;
                        $total_quotation_value += $accommodation;
                        $total_installation_quotation_value += $accommodation;
                        $lost_profit = $accommodation - $accommodation;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_expenses = \App\Model\ActualExpenses::where('record_id', $record->id)->where('expenses_type_id', 5)->where('is_delete', 0)->get();
                        $actual_accommodation = 0;
                        foreach ($actual_expenses as $actual_expens) {
                            $actual_accommodation += $actual_expens->expenses_value;
                        }
                        $total_actual_cost += $actual_accommodation;
                        $total_installation_actual_cost += $actual_accommodation;
                        $budgeted_lost_profit = $accommodation - $actual_accommodation;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Accommodation</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($accommodation, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($accommodation, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_accommodation, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $bata;
                        $total_installation_cost_value += $bata;
                        $total_quotation_value += $bata;
                        $total_installation_quotation_value += $bata;
                        $lost_profit = $bata - $bata;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_expenses = \App\Model\ActualExpenses::where('record_id', $record->id)->where('expenses_type_id', 5)->where('is_delete', 0)->get();
                        $actual_bata = 0;
                        foreach ($actual_expenses as $actual_expens) {
                            $actual_accommodation += $actual_expens->expenses_value;
                        }
                        $total_actual_cost += $actual_bata;
                        $total_installation_actual_cost += $actual_bata;
                        $budgeted_lost_profit = $bata - $actual_bata;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Bata</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($bata, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($bata, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_bata, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $total_cost_value += $quatation_other;
                        $total_installation_cost_value += $quatation_other;
                        $total_quotation_value += $quatation_other;
                        $total_installation_quotation_value += $quatation_other;
                        $lost_profit = $quatation_other - $quatation_other;
                        $total_lost_profit += $lost_profit;
                        $total_installation_lost_profit += $lost_profit;

                        $actual_quatation_other = 0;
                        $total_actual_cost += $actual_quatation_other;
                        $total_installation_actual_cost += $actual_quatation_other;
                        $budgeted_lost_profit = $bata - $actual_bata;
                        $total_budgeted_lost_profit += $budgeted_lost_profit;
                        $total_installation_budgeted_lost_profit += $budgeted_lost_profit;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Balance</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($quatation_other, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($quatation_other, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($lost_profit, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_quatation_other, 2); ?></td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_lost_profit, 2); ?></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <th colspan="5" style="<?php echo $style; ?> text-align: left; vertical-align: middle;">Total - Installation, Engineering & Commissioning</th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_installation_cost_value, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_installation_quotation_value, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_installation_lost_profit, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_installation_actual_cost, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format($total_installation_budgeted_lost_profit, 2); ?></th>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td colspan="11" style="<?php echo $style; ?> vertical-align: middle;"></td>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $package_discount = $special_discount = 0;
                        foreach ($quotations as $quotation) {
                            $equipment_installation_total = 0;
                            $main_items_total = 0;

                            $job_card_ids = array();
                            foreach ($quotation->QuotationJobCard as $detail) {
                                array_push($job_card_ids, $detail['id']);
                            }
                            $cost_sheet_ids = array();
                            foreach ($quotation->QuotationCostSheet as $detail) {
                                array_push($cost_sheet_ids, $detail['id']);
                            }

                            $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                ->where('is_delete', 0)
                                ->get();
                            foreach ($job_card_details as $job_card_detail) {
                                $margin = ($job_card_detail->margin + 100) / 100;
                                $quotation_value = $job_card_detail->rate * $margin * $job_card_detail->quantity;
                                if ($job_card_detail->is_main == 1) {
                                    $main_items_total += $quotation_value;
                                }
                                $equipment_installation_total += $quotation_value;
                            }

                            $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                ->where('is_delete', 0)
                                ->get();
                            $rate_ids = array();
                            foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                                if ($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                                    $meters = 0;
                                    foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                                        if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                            $meters += $sub_cost_sheet_detail->meters;
                                        }
                                    }

                                    $equipment_installation_total += $main_cost_sheet_detail->InstallationRate->rate * $meters;

                                    array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                                }
                            }

                            $manday_rate = \App\Model\Rate::find(1);
                            foreach ($cost_sheet_details as $cost_sheet_detail) {
                                $equipment_installation_total += $cost_sheet_detail->excavation_work;
                                $equipment_installation_total += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                                $equipment_installation_total += $cost_sheet_detail->food;
                                $equipment_installation_total += $cost_sheet_detail->accommodation;
                                $equipment_installation_total += $cost_sheet_detail->bata;
                                $equipment_installation_total += $cost_sheet_detail->other_expenses;
                            }

                            $package_percentage = $special_percentage = 0;
                            foreach ($quotation->QuotationDiscount as $detail) {
                                if ($detail['discount_type_id'] == 1) {
                                    $package_percentage = $detail['percentage'] / 100;
                                } else if ($detail['discount_type_id'] == 2) {
                                    $special_percentage = $detail['percentage'] / 100;
                                }
                            }

                            if ($package_percentage != 0) {
                                $package_discount += $main_items_total * $package_percentage;
                                $total_discount_quotation_value += $main_items_total * $package_percentage;
                                $total_discount_budgeted_lost_profit += $main_items_total * $package_percentage;
                                $equipment_installation_total -= $main_items_total * $package_percentage;
                            }
                            if ($special_percentage != 0) {
                                $special_discount += $equipment_installation_total * $special_percentage;
                                $total_discount_quotation_value += $equipment_installation_total * $special_percentage;
                                $total_discount_budgeted_lost_profit += $equipment_installation_total * $special_percentage;
                            }
                        }

                        if ($package_discount != 0) {
                            $total_quotation_value -= $package_discount;
                            $total_lost_profit -= $package_discount;
                            $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                            ?>
                        <tr>
                            <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Package Discount</td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format(abs($package_discount) * -1, 2); ?></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format(abs($package_discount) * -1, 2); ?></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        </tr>
                    <?php
                            $s++;
                            $count++;
                        }

                        if ($special_discount != 0) {
                            $total_quotation_value -= $special_discount;
                            $total_lost_profit -= $special_discount;
                            $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                            ?>
                        <tr>
                            <td colspan="5" style="<?php echo $style; ?> vertical-align: middle;">Special Discount</td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format(abs($special_discount) * -1, 2); ?></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format(abs($special_discount) * -1, 2); ?></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                            <td style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></td>
                        </tr>
                    <?php
                            $s++;
                            $count++;
                        }

                        if ($package_discount != 0 || $special_discount != 0) {
                            $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                            ?>
                        <tr>
                            <th colspan="5" style="<?php echo $style; ?> text-align: left; vertical-align: middle;">Total - Discount</th>
                            <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                            <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format(abs($total_discount_quotation_value) * -1, 2); ?></th>
                            <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black;"><?php echo number_format(abs($total_discount_budgeted_lost_profit) * -1, 2); ?></th>
                            <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                            <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                            <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                        </tr>
                        <?php
                                $s++;
                                $count++;

                                $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                                ?>
                        <tr>
                            <td colspan="11" style="<?php echo $style; ?> vertical-align: middle;"></td>
                        </tr>
                    <?php
                            $s++;
                            $count++;
                        }
                        ?>
                </tbody>
                <tfoot>
                    <?php
                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <th colspan="5" style="<?php echo $style; ?> text-align: left; vertical-align: middle;">Grand Total - Equipment, Installation, Engineering & Commissioning</th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_cost_value, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_quotation_value, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_lost_profit, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_actual_cost, 2); ?></th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($total_budgeted_lost_profit, 2); ?></th>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <th colspan="11" height="100" style="<?php echo $style; ?> text-align: center; vertical-align: middle;"></th>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $budgeted_profit_percentage = $total_cost_value != 0 ? ($total_quotation_value - $total_cost_value) * 100 / $total_cost_value : 100;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <th colspan="10" style="<?php echo $style; ?> text-align: left; vertical-align: middle;">Budgeted Lost & Profit Precentage</th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($budgeted_profit_percentage, 2) . '%'; ?></th>
                    </tr>
                    <?php
                        $s++;
                        $count++;

                        $actual_profit_percentage = $total_actual_cost != 0 ? ($total_quotation_value - $total_actual_cost) * 100 / $total_actual_cost : 100;

                        $style = $s % 2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <th colspan="10" style="<?php echo $style; ?> text-align: left; vertical-align: middle;">Actual Lost & Profit Precentage</th>
                        <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; white-space: nowrap;"><?php echo number_format($actual_profit_percentage, 2) . '%'; ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php
}
?>
@endsection