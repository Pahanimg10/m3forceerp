<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('user_access');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function inquiry_status()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.inquiry_status', $data);
    }

    public function inquiry_status_details(Request $request)
    {
        $data = [];
        $inquiries = \App\Model\Inquiry::select('id', 'contact_id', 'inquiry_date_time', 'mode_of_inquiry_id', 'inquiry_type_id', 'sales_team_id', 'user_id')
            ->where(function ($q) use ($request) {
                $request->mode_of_inquiry_id != -1 ? $q->where('mode_of_inquiry_id', $request->mode_of_inquiry_id) : '';
            })
            ->where(function ($q) use ($request) {
                $request->inquiry_type_id != -1 ? $q->where('inquiry_type_id', $request->inquiry_type_id) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sales_team_id != -1 ? $q->where('sales_team_id', $request->sales_team_id) : '';
            })
            ->whereBetween('inquiry_date_time', [$request->from.' 00:00:01', $request->to.' 23:59:59'])
            ->where('is_delete', 0)
            ->get();

        foreach ($inquiries as $inquiry) {
            $quoted_price = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $inquiry->id)
                ->where('is_confirmed', 1)
                ->where('is_revised', 0)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $job_card_ids = [];
                foreach ($quotation->QuotationJobCard as $detail) {
                    array_push($job_card_ids, $detail['id']);
                }
                $cost_sheet_ids = [];
                foreach ($quotation->QuotationCostSheet as $detail) {
                    array_push($cost_sheet_ids, $detail['id']);
                }

                $usd = false;
                $usd_rate = 0;
                if ($quotation->is_currency == 0) {
                    $usd = true;
                    $usd_rate = $quotation->usd_rate;
                }

                $main_value = $quotation_value = 0;
                $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($job_card_details as $job_card_detail) {
                    $margin = ($job_card_detail->margin + 100) / 100;
                    $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) * $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                    if ($job_card_detail->is_main == 1) {
                        $main_value += $value;
                    } else {
                        $quotation_value += $value;
                    }
                }

                foreach ($quotation->QuotationDiscount as $detail) {
                    if ($detail['discount_type_id'] == 1) {
                        $main_value = $main_value * (100 - $detail['percentage']) / 100;
                    }
                }
                $quotation_value += $main_value;

                $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                    ->where('is_delete', 0)
                    ->get();
                $rate_ids = [];
                foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                    if ($main_cost_sheet_detail->InstallationRate && ! in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                        $meters = 0;
                        foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                            if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                $meters += $sub_cost_sheet_detail->meters;
                            }
                        }

                        $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate * $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                        $quotation_value += $installation_rate * $meters;

                        array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                    }
                }

                $manday_rate = \App\Model\Rate::find(1);
                foreach ($cost_sheet_details as $cost_sheet_detail) {
                    $quotation_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                    $quotation_value += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                    $quotation_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                    $quotation_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                    $quotation_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                    $quotation_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;
                }

                foreach ($quotation->QuotationDiscount as $detail) {
                    if ($detail['discount_type_id'] == 2) {
                        $quotation_value = $quotation_value * (100 - $detail['percentage']) / 100;
                    }
                }

                foreach ($quotation->Inquiry->Contact->ContactTax as $detail) {
                    if ($detail['CTaxType']) {
                        if ($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 4) {
                            $quotation_value = $quotation_value * ($detail['CTaxType']['percentage'] + 100) / 100;
                        }
                    }
                }

                $quoted_price += $quotation_value;
            }
            $job = \App\Model\Job::where('inquiry_id', $inquiry->id)
                ->where('is_delete', 0)
                ->first();

            $job_status = \App\Model\JobDetails::selectRaw('job_id, MAX(job_status_id) AS job_status_id')
                ->whereHas('Job', function ($query) use ($inquiry) {
                    $query->where('inquiry_id', $inquiry->id)->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->first();
            if ($job_status && $job_status->job_status_id) {
                $job_detail = \App\Model\JobDetails::select('job_id', 'update_date_time', 'job_status_id', 'remarks')
                    ->whereHas('Job', function ($query) use ($inquiry) {
                        $query->where('inquiry_id', $inquiry->id)->where('is_delete', 0);
                    })
                    ->where('job_status_id', $job_status->job_status_id)
                    ->where('is_delete', 0)
                    ->orderBy('update_date_time', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();
                $row = [
                    'id' => $inquiry->id,
                    'inquiry_date_time' => $inquiry->inquiry_date_time,
                    'customer_name' => $inquiry->Contact ? $inquiry->Contact->name : '',
                    'customer_address' => $inquiry->Contact ? $inquiry->Contact->address : '',
                    'customer_contact_no' => $inquiry->Contact ? $inquiry->Contact->contact_no : '',
                    'mode_of_inquiry' => $inquiry->IModeOfInquiry ? $inquiry->IModeOfInquiry->name : '',
                    'inquiry_type' => $inquiry->IInquiryType ? $inquiry->IInquiryType->name : '',
                    'sales_person' => $inquiry->SalesTeam ? $inquiry->SalesTeam->name : '',
                    'job_date_time' => $job ? $job->job_date_time : '',
                    'quoted_price' => $quoted_price,
                    'update_status_id' => 3,
                    'update_date_time' => $job_detail ? $job_detail->update_date_time : '',
                    'update_status' => $job_detail ? $job_detail->JobStatus->name : '',
                    'remarks' => $job_detail ? $job_detail->remarks : '',
                    'logged_user' => $inquiry->User ? $inquiry->User->first_name : '',
                ];
                array_push($data, $row);
            } else {
                $inquiry_status = \App\Model\InquiryDetials::selectRaw('MAX(inquiry_status_id) AS inquiry_status_id')
                    ->where('inquiry_id', $inquiry->id)
                    ->where('is_delete', 0)
                    ->first();
                if ($inquiry_status && ($inquiry_status->inquiry_status_id == 17 || $inquiry_status->inquiry_status_id == 18)) {
                    $inquiry_detail = \App\Model\InquiryDetials::select('update_date_time', 'inquiry_status_id', 'remarks')
                        ->where('inquiry_id', $inquiry->id)
                        ->where('inquiry_status_id', $inquiry_status->inquiry_status_id)
                        ->where('is_delete', 0)
                        ->orderBy('update_date_time', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->first();
                    $row = [
                        'id' => $inquiry->id,
                        'inquiry_date_time' => $inquiry->inquiry_date_time,
                        'customer_name' => $inquiry->Contact ? $inquiry->Contact->name : '',
                        'customer_address' => $inquiry->Contact ? $inquiry->Contact->address : '',
                        'customer_contact_no' => $inquiry->Contact ? $inquiry->Contact->contact_no : '',
                        'mode_of_inquiry' => $inquiry->IModeOfInquiry ? $inquiry->IModeOfInquiry->name : '',
                        'inquiry_type' => $inquiry->IInquiryType ? $inquiry->IInquiryType->name : '',
                        'sales_person' => $inquiry->SalesTeam ? $inquiry->SalesTeam->name : '',
                        'job_date_time' => $job ? $job->job_date_time : '',
                        'quoted_price' => $quoted_price,
                        'update_status_id' => 2,
                        'update_date_time' => $inquiry_detail ? $inquiry_detail->update_date_time : '',
                        'update_status' => $inquiry_detail ? $inquiry_detail->InquiryStatus->name : '',
                        'remarks' => $inquiry_detail ? $inquiry_detail->remarks : '',
                        'logged_user' => $inquiry->User ? $inquiry->User->first_name : '',
                    ];
                    array_push($data, $row);
                } elseif ($inquiry_status && $inquiry_status->inquiry_status_id) {
                    $inquiry_detail = \App\Model\InquiryDetials::select('update_date_time', 'inquiry_status_id', 'remarks')
                        ->where('inquiry_id', $inquiry->id)
                        ->where('inquiry_status_id', $inquiry_status->inquiry_status_id)
                        ->where('is_delete', 0)
                        ->orderBy('update_date_time', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->first();
                    $row = [
                        'id' => $inquiry->id,
                        'inquiry_date_time' => $inquiry->inquiry_date_time,
                        'customer_name' => $inquiry->Contact ? $inquiry->Contact->name : '',
                        'customer_address' => $inquiry->Contact ? $inquiry->Contact->address : '',
                        'customer_contact_no' => $inquiry->Contact ? $inquiry->Contact->contact_no : '',
                        'mode_of_inquiry' => $inquiry->IModeOfInquiry ? $inquiry->IModeOfInquiry->name : '',
                        'inquiry_type' => $inquiry->IInquiryType ? $inquiry->IInquiryType->name : '',
                        'sales_person' => $inquiry->SalesTeam ? $inquiry->SalesTeam->name : '',
                        'job_date_time' => $job ? $job->job_date_time : '',
                        'quoted_price' => $quoted_price,
                        'update_status_id' => 1,
                        'update_date_time' => $inquiry_detail ? $inquiry_detail->update_date_time : '',
                        'update_status' => $inquiry_detail ? $inquiry_detail->InquiryStatus->name : '',
                        'remarks' => $inquiry_detail ? $inquiry_detail->remarks : '',
                        'logged_user' => $inquiry->User ? $inquiry->User->first_name : '',
                    ];
                    array_push($data, $row);
                } else {
                    $row = [
                        'id' => $inquiry->id,
                        'inquiry_date_time' => $inquiry->inquiry_date_time,
                        'customer_name' => $inquiry->Contact ? $inquiry->Contact->name : '',
                        'customer_address' => $inquiry->Contact ? $inquiry->Contact->address : '',
                        'customer_contact_no' => $inquiry->Contact ? $inquiry->Contact->contact_no : '',
                        'mode_of_inquiry' => $inquiry->IModeOfInquiry ? $inquiry->IModeOfInquiry->name : '',
                        'inquiry_type' => $inquiry->IInquiryType ? $inquiry->IInquiryType->name : '',
                        'sales_person' => $inquiry->SalesTeam ? $inquiry->SalesTeam->name : '',
                        'job_date_time' => $job ? $job->job_date_time : '',
                        'quoted_price' => $quoted_price,
                        'update_status_id' => 0,
                        'update_date_time' => '',
                        'update_status' => '',
                        'remarks' => '',
                        'logged_user' => $inquiry->User ? $inquiry->User->first_name : '',
                    ];
                    array_push($data, $row);
                }
            }
        }

        return response($data);
    }

    public function stock_movement()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.stock_movement', $data);
    }

    public function stock_movement_details(Request $request)
    {
        $data = [];
        $items = \App\Model\Item::where(function ($q) use ($request) {
            $request->main_category != -1 ? $q->where('main_category_id', $request->main_category) : '';
        })
            ->where(function ($q) use ($request) {
                $request->sub_category != -1 ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->purchase_type != -1 ? $q->where('purchase_type_id', $request->purchase_type) : '';
            })
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->get();
        foreach ($items as $item) {
            $opening_quantity = $opening_value = $grn_quantity = $grn_value = $issue_quantity = $issue_value = $return_quantity = $return_value = 0;

            $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) use ($request) {
                $query->where('good_receive_date_time', '<', $request->from.' 00:00')->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($good_receive_details as $good_receive_detail) {
                $opening_quantity += $good_receive_detail->quantity;
                $opening_value += $good_receive_detail->quantity * $good_receive_detail->rate;
            }
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->where('item_issue_date_time', '<', $request->from.' 00:00')->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $opening_quantity -= $item_issue_detail->quantity;
                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    $opening_value -= $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                }
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($request) {
                $query->where('item_receive_date_time', '<', $request->from.' 00:00')->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $opening_quantity += $item_receive_detail->quantity;
                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    $opening_value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                }
            }

            $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) use ($request) {
                $query->whereBetween('good_receive_date_time', [$request->from.' 00:01', $request->to.' 23:59'])->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($good_receive_details as $good_receive_detail) {
                $grn_quantity += $good_receive_detail->quantity;
                $grn_value += $good_receive_detail->quantity * $good_receive_detail->rate;
            }
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->whereBetween('item_issue_date_time', [$request->from.' 00:01', $request->to.' 23:59'])->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $issue_quantity += $item_issue_detail->quantity;
                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    $issue_value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                }
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($request) {
                $query->whereBetween('item_receive_date_time', [$request->from.' 00:01', $request->to.' 23:59'])->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $return_quantity += $item_receive_detail->quantity;
                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    $return_value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                }
            }

            $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) use ($request) {
                $query->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            $location = '';
            $available_quantity = $serial_no_count = 0;
            foreach ($good_receive_details as $good_receive_detail) {
                if ($good_receive_detail->available_quantity > 0) {
                    $location .= $location != '' ? ' '.$good_receive_detail->location : $good_receive_detail->location;
                }

                $available_quantity += $good_receive_detail->available_quantity;

                $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                    ->where('is_main', 1)
                    ->where('is_issued', 0)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                    $serial_no_count++;
                }
            }

            $balance_quantity = $opening_quantity + $grn_quantity + $return_quantity - $issue_quantity;
            $balance_value = $opening_value + $grn_value + $return_value - $issue_value;
            $row = [
                'id' => $item->id,
                'main_category' => $item->MainItemCategory->name,
                'sub_category' => $item->SubItemCategory->name,
                'purchase_type' => $item->PurchaseType->name,
                'code' => $item->code,
                'name' => $item->name,
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'unit_type' => $item->UnitType->code,
                'opening_quantity' => $opening_quantity,
                'opening_value' => $opening_value,
                'grn_quantity' => $grn_quantity,
                'grn_value' => $grn_value,
                'issue_quantity' => $issue_quantity,
                'issue_value' => $issue_value,
                'return_quantity' => $return_quantity,
                'return_value' => $return_value,
                'balance_quantity' => $balance_quantity,
                'balance_value' => $balance_value,
                'available_quantity' => $available_quantity,
                'stock' => $item->stock,
                'available_quantity' => $available_quantity,
                'serial_no_count' => $serial_no_count,
                'location' => $location,
            ];
            array_push($data, $row);

            //            if($balance_quantity != 0){
            //                $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function($query) use($request) {
            //                            $query->where('is_posted', 1)->where('is_delete', 0);
            //                        })
            //                        ->where('item_id', $item->id)
            //                        ->where('is_delete', 0)
            //                        ->orderBy('id', 'DESC')
            //                        ->get();
            //                foreach ($good_receive_details as $good_receive_detail){
            //                    $grn_quantity = $good_receive_detail->quantity;
            //                    if($grn_quantity >= $balance_quantity){
            //                        $good_receive_detail->quantity = $grn_quantity - $balance_quantity;
            //                        $good_receive_detail->available_quantity = $grn_quantity - $balance_quantity;
            //                        $balance_quantity = 0;
            //                    } else{
            //                        $good_receive_detail->quantity = 0;
            //                        $good_receive_detail->available_quantity = 0;
            //                        $balance_quantity = $balance_quantity - $grn_quantity;
            //                    }
            //                    $good_receive_detail->save();
            //
            //                    if($balance_quantity == 0){
            //                        break;
            //                    }
            //                }
            //
            // $item->stock = $balance_quantity;
            // $item->save();
            //            }
        }

        return response($data);
    }

    public function job_profit_loss()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.job_profit_loss', $data);
    }

    public function job_profit_loss_details(Request $request)
    {
        $data = [];
        $jobs = \App\Model\Job::whereHas('Inquiry', function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $request->sales_team_id != -1 ? $q->where('sales_team_id', $request->sales_team_id) : '';
            });
        })
            ->whereBetween('job_date_time', [$request->from.' 00:00:01', $request->to.' 23:59:59'])
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($jobs as $job) {
            $quoted_price = $estimated_cost = $actual_cost = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                ->where('is_confirmed', 1)
                ->where('is_revised', 0)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $job_card_ids = [];
                foreach ($quotation->QuotationJobCard as $detail) {
                    array_push($job_card_ids, $detail['id']);
                }
                $cost_sheet_ids = [];
                foreach ($quotation->QuotationCostSheet as $detail) {
                    array_push($cost_sheet_ids, $detail['id']);
                }

                $usd = false;
                $usd_rate = 0;
                if ($quotation->is_currency == 0) {
                    $usd = true;
                    $usd_rate = $quotation->usd_rate;
                }

                $main_value = $quotation_value = $main_estimated_value = $quotation_estimated_value = 0;
                $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($job_card_details as $job_card_detail) {
                    $margin = ($job_card_detail->margin + 100) / 100;
                    $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) * $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                    $estimated_value = $usd ? ($job_card_detail->rate * $job_card_detail->quantity) * $usd_rate : $job_card_detail->rate * $job_card_detail->quantity;
                    if ($job_card_detail->is_main == 1) {
                        $main_value += $value;
                        $main_estimated_value += $estimated_value;
                    } else {
                        $quotation_value += $value;
                        $quotation_estimated_value += $estimated_value;
                    }
                }

                foreach ($quotation->QuotationDiscount as $detail) {
                    if ($detail['discount_type_id'] == 1) {
                        $main_value = $main_value * (100 - $detail['percentage']) / 100;
                        $main_estimated_value = $main_estimated_value * (100 - $detail['percentage']) / 100;
                    }
                }
                $quotation_value += $main_value;
                $quotation_estimated_value += $main_estimated_value;

                $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                    ->where('is_delete', 0)
                    ->get();
                $rate_ids = [];
                foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                    if ($main_cost_sheet_detail->InstallationRate && ! in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                        $meters = 0;
                        foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                            if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                $meters += $sub_cost_sheet_detail->meters;
                            }
                        }

                        $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate * $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                        $installation_estimated_rate = $usd ? (($main_cost_sheet_detail->InstallationRate->rate * 100) / 130) * $usd_rate : ($main_cost_sheet_detail->InstallationRate->rate * 100) / 130;
                        $quotation_value += $installation_rate * $meters;
                        $quotation_estimated_value += $installation_estimated_rate * $meters;

                        array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                    }
                }

                $manday_rate = \App\Model\Rate::find(1);
                foreach ($cost_sheet_details as $cost_sheet_detail) {
                    $quotation_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                    $quotation_value += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                    $quotation_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                    $quotation_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                    $quotation_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                    $quotation_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;

                    $quotation_estimated_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                    $quotation_estimated_value += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                    $quotation_estimated_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                    $quotation_estimated_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                    $quotation_estimated_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                    $quotation_estimated_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;
                }

                foreach ($quotation->QuotationDiscount as $detail) {
                    if ($detail['discount_type_id'] == 2) {
                        $quotation_value = $quotation_value * (100 - $detail['percentage']) / 100;
                        $quotation_estimated_value = $quotation_estimated_value * (100 - $detail['percentage']) / 100;
                    }
                }

                //                foreach ($quotation->Inquiry->Contact->ContactTax as $detail){
                //                    if ($detail['CTaxType']){
                //                        if($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 4){
                //                            $quotation_value = $quotation_value * ($detail['CTaxType']['percentage']+100) / 100;
                //                            $quotation_estimated_value = $quotation_estimated_value * ($detail['CTaxType']['percentage']+100) / 100;
                //                        }
                //                    }
                //                }

                $quoted_price += $quotation_value;
                $estimated_cost += $quotation_estimated_value;
            }

            $item_issues = \App\Model\ItemIssue::where('item_issue_type_id', 1)
                ->whereHas('Job', function ($query) use ($job) {
                    $query->where('inquiry_id', $job->inquiry_id);
                })
                ->where('is_posted', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issues as $item_issue) {
                $actual_cost += $item_issue->item_issue_value;
            }

            $item_receives = \App\Model\ItemReceive::whereHas('ItemIssue', function ($query) use ($job) {
                $query->where('item_issue_type_id', 1)
                    ->whereHas('Job', function ($query) use ($job) {
                        $query->where('inquiry_id', $job->inquiry_id);
                    })
                    ->where('is_delete', 0);
            })
                ->where('is_posted', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receives as $item_receive) {
                $actual_cost -= $item_receive->item_receive_value;
            }

            $petty_cash_issues = \App\Model\PettyCashIssue::where('petty_cash_issue_type_id', 1)
                ->whereHas('Job', function ($query) use ($job) {
                    $query->where('inquiry_id', $job->inquiry_id);
                })
                ->where('is_posted', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($petty_cash_issues as $petty_cash_issue) {
                $actual_cost += $petty_cash_issue->petty_cash_issue_value;
            }

            $petty_cash_returns = \App\Model\PettyCashReturn::whereHas('PettyCashIssue', function ($query) use ($job) {
                $query->where('petty_cash_issue_type_id', 1)
                    ->whereHas('Job', function ($query) use ($job) {
                        $query->where('inquiry_id', $job->inquiry_id);
                    })
                    ->where('is_delete', 0);
            })
                ->where('is_posted', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($petty_cash_returns as $petty_cash_return) {
                $actual_cost -= $petty_cash_return->petty_cash_return_value;
            }

            $job_attendances = \App\Model\JobAttendance::where('job_type_id', 1)
                ->whereHas('Job', function ($query) use ($job) {
                    $query->where('inquiry_id', $job->inquiry_id);
                })
                ->where('is_delete', 0)
                ->get();
            foreach ($job_attendances as $job_attendance) {
                $actual_cost += $job_attendance->mandays * 1153.85;
            }

            $row = [
                'id' => $job->id,
                'inquiry_id' => $job->inquiry_id,
                'job_no' => $job->job_no,
                'job_date_time' => $job->job_date_time,
                'customer_name' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->name : '',
                'customer_address' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->address : '',
                'job_type' => $job->Inquiry && $job->Inquiry->IInquiryType ? $job->Inquiry->IInquiryType->name : '',
                'quoted_price' => $quoted_price,
                'estimated_cost' => $estimated_cost,
                'estimated_gp' => $quoted_price - $estimated_cost,
                'estimated_gp_percentage' => $quoted_price != 0 ? ($quoted_price - $estimated_cost) * (100 / $quoted_price) : 0,
                'actual_cost' => $actual_cost,
                'actual_gp' => $quoted_price - $actual_cost,
                'actual_gp_percentage' => $quoted_price != 0 ? ($quoted_price - $actual_cost) * (100 / $quoted_price) : 0,
                'gp_status_id' => $quoted_price - $actual_cost > 0 ? 1 : 0,
                'sales_person' => $job->Inquiry && $job->Inquiry->SalesTeam ? $job->Inquiry->SalesTeam->name : '',
            ];
            array_push($data, $row);
        }

        return response($data);
    }

    public function technical_attendance()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.technical_attendance', $data);
    }

    public function technical_attendance_details(Request $request)
    {
        $details = [];

        $to_date = date('Y-m-d', strtotime($request->to.' +1 day'));
        $period = new \DatePeriod(
            new \DateTime($request->from),
            new \DateInterval('P1D'),
            new \DateTime($to_date)
        );
        foreach ($period as $dt) {
            $job_attendances = \App\Model\JobAttendance::where('attended_date', $dt->format('Y-m-d'))
                ->where('is_delete', 0)
                ->get();
            foreach ($job_attendances as $job_attendance) {
                $row = [
                    'attended_date_id' => $dt->format('Ymd'),
                    'technical_team_id' => $job_attendance->technical_team_id,
                    'technical_team_name' => $job_attendance->TechnicalTeam ? $job_attendance->TechnicalTeam->name : '',
                    'mandays' => (float) $job_attendance->mandays,
                ];
                array_push($details, $row);
            }
        }

        $attended_dates = $attendances = $technical_team_ids = [];
        foreach ($period as $dt) {
            $row = [
                'attended_date_id' => $dt->format('Ymd'),
                'attended_date' => $dt->format('Y-m-d'),
            ];
            array_push($attended_dates, $row);
        }

        foreach ($details as $main_detail) {
            if (! in_array($main_detail['technical_team_id'], $technical_team_ids)) {
                $row = [];
                $row['id'] = $main_detail['technical_team_id'];
                $row['technical_name'] = $main_detail['technical_team_name'];
                $total_attendance = 0;
                foreach ($period as $dt) {
                    foreach ($details as $sub_detail) {
                        if ($main_detail['technical_team_id'] == $sub_detail['technical_team_id']) {
                            if ($dt->format('Ymd') == $sub_detail['attended_date_id']) {
                                if (! array_key_exists($dt->format('Ymd'), $row)) {
                                    $row[$dt->format('Ymd')] = $sub_detail['mandays'];
                                } else {
                                    $row[$dt->format('Ymd')] += $sub_detail['mandays'];
                                }
                                $total_attendance += $sub_detail['mandays'];
                            } else {
                                if (! array_key_exists($dt->format('Ymd'), $row)) {
                                    $row[$dt->format('Ymd')] = 0;
                                } else {
                                    $row[$dt->format('Ymd')] += 0;
                                }
                            }
                        }
                    }
                }
                $row['total_attendance'] = $total_attendance;
                array_push($attendances, $row);
                array_push($technical_team_ids, $main_detail['technical_team_id']);
            }
        }

        $data = [
            'attended_date' => $attended_dates,
            'attendances' => $attendances,
        ];

        return response($data);
    }

    public function technical_job_details(Request $request)
    {
        $technical_team = \App\Model\TechnicalTeam::find($request->technical_id);
        $job_attendances = \App\Model\JobAttendance::whereBetween('attended_date', [$request->from, $request->to])
            ->where('technical_team_id', $request->technical_id)
            ->where('is_delete', 0)
            ->orderBy('attended_date')
            ->get();
        $view = '
            <table id="data_table" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">No#</th>
                        <th style="text-align: center; vertical-align: middle;">Job Type</th>
                        <th style="text-align: center; vertical-align: middle;">Customer Name</th>
                        <th style="text-align: center; vertical-align: middle;">Customer Address</th>
                        <th style="text-align: center; vertical-align: middle;">Attended Date</th>
                        <th style="text-align: center; vertical-align: middle;">Man Days</th>
                    </tr>
                </thead>
                <tbody>
            ';
        $total_mandays = 0;
        foreach ($job_attendances as $index => $value) {
            $total_mandays += $value->mandays;
            $job_type = $value->job_type_id == 1 ? 'Job' : 'Tech Response';
            if ($value->job_type_id == 1) {
                $customer_name = $value->Job && $value->Job->Inquiry && $value->Job->Inquiry->Contact ? $value->Job->Inquiry->Contact->name : '';
                $customer_address = $value->Job && $value->Job->Inquiry && $value->Job->Inquiry->Contact ? $value->Job->Inquiry->Contact->address : '';
            } else {
                $customer_name = $value->TechResponse && $value->TechResponse->Contact ? $value->TechResponse->Contact->name : '';
                $customer_address = $value->TechResponse && $value->TechResponse->Contact ? $value->TechResponse->Contact->address : '';
            }
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">'.($index + 1).'</td>
                        <td style="text-align: center; vertical-align: middle;">'.$job_type.'</td>
                        <td style="vertical-align: middle;">'.$customer_name.'</td>
                        <td style="vertical-align: middle;">'.$customer_address.'</td>
                        <td style="text-align: center; vertical-align: middle;">'.$value->attended_date.'</td>
                        <td style="text-align: center; vertical-align: middle;">'.$value->mandays.'</td>
                    </tr>
                ';
        }
        $view .= '
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right; vertical-align: middle;">Total</th>
                        <th style="text-align: center; vertical-align: middle; border-top: 1px double black; border-bottom: 3px double black;">'.$total_mandays.'</th>
                    </tr>
                </tfoot>
            </table>    
            ';

        $result = [
            'view' => $view,
            'technical_team_name' => $technical_team ? $technical_team->name : '',
        ];

        echo json_encode($result);
    }

    public function stock_check()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.stock_check', $data);
    }

    public function stock_check_details(Request $request)
    {
        $data = [];
        $items = \App\Model\Item::where(function ($q) use ($request) {
            $request->main_category != -1 ? $q->where('main_category_id', $request->main_category) : '';
        })
            ->where(function ($q) use ($request) {
                $request->sub_category != -1 ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->purchase_type != -1 ? $q->where('purchase_type_id', $request->purchase_type) : '';
            })
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->get();
        foreach ($items as $item) {
            $current_stock = 0;
            $serial_nos_array = [];

            $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) use ($request) {
                $query->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($good_receive_details as $good_receive_detail) {
                $current_stock += $good_receive_detail->quantity;
                $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                    array_push($serial_nos_array, $good_receive_breakdown->serial_no);
                }
            }
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $current_stock -= $item_issue_detail->quantity;
                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    if ($item_issue_breakdown->type == 1 && ($key = array_search($item_issue_breakdown->GoodReceiveBreakdown->serial_no, $serial_nos_array)) !== false) {
                        unset($serial_nos_array[$key]);
                    }
                }
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($request) {
                $query->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $current_stock += $item_receive_detail->quantity;
                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    if ($item_receive_breakdown->type == 1) {
                        array_push($serial_nos_array, $item_receive_breakdown->GoodReceiveBreakdown->serial_no);
                    }
                }
            }

            $serial_nos = '';
            foreach ($serial_nos_array as $index => $value) {
                $serial_nos .= $index == 0 ? $value : '|'.$value;
            }

            $row = [
                'id' => $item->id,
                'main_category' => $item->MainItemCategory->name,
                'sub_category' => $item->SubItemCategory->name,
                'purchase_type' => $item->PurchaseType->name,
                'code' => $item->code,
                'name' => $item->name,
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'unit_type' => $item->UnitType->code,
                'current_stock' => $current_stock,
                'serial_nos' => $serial_nos,
            ];
            array_push($data, $row);
        }

        return response($data);
    }

    public function item_issue()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.item_issue', $data);
    }

    public function item_issue_details(Request $request)
    {
        $data = [];

        $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
            $query->whereBetween('item_issue_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
                ->where(function ($query) use ($request) {
                    $request->item_issue_type_id != -1 ? $query->where('item_issue_type_id', $request->item_issue_type_id) : '';
                })
                ->where('is_posted', 1)
                ->where('is_delete', 0);
        })
            ->where(function ($query) use ($request) {
                $request->item_id != -1 ? $query->where('item_id', $request->item_id) : '';
            })
            ->where('is_delete', 0)
            ->get();
        foreach ($item_issue_details as $item_issue_detail) {
            $document_no = $customer_name = $customer_address = '';
            if ($item_issue_detail->ItemIssue && $item_issue_detail->ItemIssue->item_issue_type_id == 1 && $item_issue_detail->ItemIssue->Job && $item_issue_detail->ItemIssue->Job->Inquiry && $item_issue_detail->ItemIssue->Job->Inquiry->Contact) {
                $document_no = $item_issue_detail->ItemIssue->Job->job_no;
                $customer_name = $item_issue_detail->ItemIssue->Job->Inquiry->Contact->name;
                $customer_address = $item_issue_detail->ItemIssue->Job->Inquiry->Contact->address;
            } elseif ($item_issue_detail->ItemIssue && $item_issue_detail->ItemIssue->item_issue_type_id == 2 && $item_issue_detail->ItemIssue->TechResponse && $item_issue_detail->ItemIssue->TechResponse->Contact) {
                $document_no = $item_issue_detail->ItemIssue->TechResponse->tech_response_no;
                $customer_name = $item_issue_detail->ItemIssue->TechResponse->Contact->name;
                $customer_address = $item_issue_detail->ItemIssue->TechResponse->Contact->address;
            }

            $quantity = $item_issue_detail->quantity;
            $item_total = 0;
            foreach ($item_issue_detail->ItemIssueBreakdown as $item_issue_breakdown) {
                $item_total += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
            }

            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($item_issue_detail) {
                $query->where('item_issue_id', $item_issue_detail->item_issue_id)
                    ->where('is_posted', 1)
                    ->where('is_delete', 0);
            })
                ->where('item_id', $item_issue_detail->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $quantity -= $item_receive_detail->quantity;
                foreach ($item_receive_detail->ItemReceiveBreakdown as $item_receive_breakdown) {
                    $item_total -= $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                }
            }

            $row = [
                'id' => $item_issue_detail->id,
                'item_issue_type' => $item_issue_detail->ItemIssue && $item_issue_detail->ItemIssue->ItemIssueType ? $item_issue_detail->ItemIssue->ItemIssueType->name : '',
                'item_issue_no' => $item_issue_detail->ItemIssue ? $item_issue_detail->ItemIssue->item_issue_no : '',
                'item_issue_date_time' => $item_issue_detail->ItemIssue ? $item_issue_detail->ItemIssue->item_issue_date_time : '',
                'document_no' => $document_no,
                'issued_to' => $item_issue_detail->ItemIssue ? $item_issue_detail->ItemIssue->issued_to : '',
                'customer_name' => $customer_name,
                'customer_address' => $customer_address,
                'item_code' => $item_issue_detail->Item ? $item_issue_detail->Item->code : '',
                'item_name' => $item_issue_detail->Item ? $item_issue_detail->Item->name : '',
                'unit_type' => $item_issue_detail->Item && $item_issue_detail->Item->UnitType ? $item_issue_detail->Item->UnitType->code : '',
                'rate' => $quantity != 0 ? $item_total / $quantity : 0,
                'quantity' => $quantity,
                'value' => $item_total,
            ];
            array_push($data, $row);
        }

        return response($data);
    }

    public function ongoing_job_item_issue()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.ongoing_job_item_issue', $data);
    }

    public function ongoing_job_item_issue_details(Request $request)
    {
        $data = [];

        $job_ids = [];
        $job_details = \App\Model\JobDetails::selectRaw('job_id AS job_id, MAX(job_status_id) AS job_status_id')
            ->where('update_date_time', '<', date('Y-m-d', strtotime($request->to)).' 23:59')
            ->where('is_delete', 0)
            ->groupBy('job_id')
            ->get();
        foreach ($job_details as $job_detail) {
            if ($job_detail->job_status_id != 10) {
                $row = [
                    'to_date' => date('Y-m-d', strtotime($request->to)).' 23:59',
                    'job_id' => $job_detail->job_id,
                    'job_no' => $job_detail->Job->job_no,
                    'customer_name' => $job_detail->Job->Inquiry->Contact->name,
                ];
                array_push($job_ids, $row);
            }
        }

        foreach ($job_ids as $job_id) {
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($job_id) {
                $query->where('item_issue_date_time', '<', $job_id['to_date'])
                    ->where('item_issue_type_id', 1)
                    ->where('document_id', $job_id['job_id'])
                    ->where('is_posted', 1)
                    ->where('is_delete', 0);
            })
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $quantity = $item_issue_detail->quantity;
                $value = 0;
                foreach ($item_issue_detail->ItemIssueBreakdown as $item_issue_detail_breakdown) {
                    $value += $item_issue_detail_breakdown->type == 1 ? $item_issue_detail_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_detail_breakdown->quantity : $item_issue_detail_breakdown->GoodReceiveDetails->rate * $item_issue_detail_breakdown->quantity;
                }
                $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($item_issue_detail) {
                    $query->where('item_issue_id', $item_issue_detail->item_issue_id)
                        ->where('is_posted', 1)
                        ->where('is_delete', 0);
                })
                    ->where('item_id', $item_issue_detail->item_id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_receive_details as $item_receive_detail) {
                    $quantity -= $item_receive_detail->quantity;
                    foreach ($item_receive_detail->ItemReceiveBreakdown as $item_receive_breakdown) {
                        $value -= $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                    }
                }

                if ($quantity > 0) {
                    $row = [
                        'job_no' => $job_id['job_no'],
                        'customer_name' => $job_id['customer_name'],
                        'issue_date_time' => $item_issue_detail->ItemIssue->item_issue_date_time,
                        'item_code' => $item_issue_detail->Item->code,
                        'item_name' => $item_issue_detail->Item->name,
                        'unit_type' => $item_issue_detail->Item->UnitType->code,
                        'rate' => number_format($value / $quantity, 2, '.', ''),
                        'quantity' => $quantity,
                        'value' => number_format($value, 2, '.', ''),
                    ];
                    array_push($data, $row);
                }
            }
        }

        return response($data);
    }

    public function item_purchase_history()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.item_purchase_history', $data);
    }

    public function item_purchase_history_details(Request $request)
    {
        $data = [];

        $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) use ($request) {
            $query->whereBetween('good_receive_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
                ->where('is_posted', 1)
                ->where('is_delete', 0);
        })
            ->where(function ($query) use ($request) {
                $request->item_id != -1 ? $query->where('item_id', $request->item_id) : '';
            })
            ->where('is_delete', 0)
            ->get();
        foreach ($good_receive_details as $good_receive_detail) {
            $row = [
                'good_receive_no' => $good_receive_detail->GoodReceive->good_receive_no,
                'good_receive_date_time' => $good_receive_detail->GoodReceive->good_receive_date_time,
                'supplier' => $good_receive_detail->GoodReceive->PurchaseOrder ? $good_receive_detail->GoodReceive->PurchaseOrder->Contact->name : '',
                'purchase_order_no' => $good_receive_detail->GoodReceive->PurchaseOrder ? $good_receive_detail->GoodReceive->PurchaseOrder->purchase_order_no : '',
                'invoice_no' => $good_receive_detail->GoodReceive->invoice_no,
                'purchase_type' => $good_receive_detail->Item->PurchaseType->name,
                'item_code' => $good_receive_detail->Item->code,
                'item_name' => $good_receive_detail->Item->name,
                'model_no' => $good_receive_detail->model_no,
                'brand' => $good_receive_detail->brand,
                'origin' => $good_receive_detail->origin,
                'unit_type' => $good_receive_detail->Item->UnitType->code,
                'rate' => $good_receive_detail->rate,
                'quantity' => $good_receive_detail->quantity,
                'value' => $good_receive_detail->rate * $good_receive_detail->quantity,
            ];
            array_push($data, $row);
        }

        return response($data);
    }

    public function tech_response_item_issue()
    {
        $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();
        $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
            ->whereIn('user_access.user_group_id', session()->get('user_group'))
            ->where('side_menu.menu_category', '!=', 0)
            ->orderBy('side_menu.menu_order', 'asc')
            ->distinct('side_menu.id')
            ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
            ->get();

        return view('report.tech_response_item_issue', $data);
    }

    public function get_tech_response_data(Request $request)
    {
        $item_issues = \App\Model\ItemIssue::select('id', 'item_issue_type_id', 'document_id')
            ->whereHas('TechResponse', function ($query) use ($request) {
                $query->whereBetween('record_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
                    ->where('is_completed', 1)
                    ->where('is_delete', 0);
            })
            ->with(['TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no', 'record_date_time', 'is_completed')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('item_issue_type_id', 2)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->groupBy('document_id')
            ->get();
        $tech_responses = \App\Model\TechResponse::select('id', 'contact_id', 'tech_response_no', 'record_date_time', 'is_completed')
            ->with(['Contact' => function ($query) {
                $query->select('id', 'name');
            }])
            ->whereBetween('record_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
            ->where('is_completed', 1)
            ->where('is_delete', 0)
            ->get();

        $data = [
            'tech_responses' => $tech_responses,
            'item_issues' => $item_issues,
        ];

        return response($data);
    }

    public function tech_response_item_issue_details(Request $request)
    {
        $data = [];

        $tech_responses = \App\Model\TechResponse::whereBetween('record_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
            ->where(function ($query) use ($request) {
                $request->tech_response_id != -1 ? $query->where('id', $request->tech_response_id) : '';
            })
            ->where('is_completed', 1)
            ->where('is_delete', 0)
            ->get();
        foreach ($tech_responses as $tech_response) {
            $tech_response_invoice_details = \App\Model\TechResponseInvoiceDetails::where('tech_response_id', $tech_response->id)
                ->where(function ($query) use ($request) {
                    $request->item_id != -1 ? $query->where('item_id', $request->item_id) : '';
                })
                ->where('is_delete', 0)
                ->get();
            foreach ($tech_response_invoice_details as $tech_response_invoice_detail) {
                $row = [
                    'id' => $tech_response_invoice_detail->id,
                    'tech_response_no' => $tech_response_invoice_detail->TechResponse ? $tech_response_invoice_detail->TechResponse->tech_response_no : '',
                    'customer_name' => $tech_response_invoice_detail->TechResponse && $tech_response_invoice_detail->TechResponse->Contact ? $tech_response_invoice_detail->TechResponse->Contact->name : '',
                    'customer_address' => $tech_response_invoice_detail->TechResponse && $tech_response_invoice_detail->TechResponse->Contact ? $tech_response_invoice_detail->TechResponse->Contact->address : '',
                    'item_code' => $tech_response_invoice_detail->Item ? $tech_response_invoice_detail->Item->code : '',
                    'item_name' => $tech_response_invoice_detail->Item ? $tech_response_invoice_detail->Item->name : '',
                    'unit_type' => $tech_response_invoice_detail->Item && $tech_response_invoice_detail->Item->UnitType ? $tech_response_invoice_detail->Item->UnitType->code : '',
                    'rate' => $tech_response_invoice_detail->rate,
                    'quantity' => $tech_response_invoice_detail->quantity,
                    'value' => $tech_response_invoice_detail->value,
                    'invoice_value' => $tech_response_invoice_detail->invoice_value,
                ];
                array_push($data, $row);
            }
        }

        return response($data);
    }

    public function stock_update()
    {
        $items = \App\Model\Item::where('is_active', 1)
            ->where('is_delete', 0)
            ->get();
        foreach ($items as $item) {
            $balance_quantity = 0;
            $good_receive_details = \App\Model\GoodReceiveDetails::where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($good_receive_details as $good_receive_detail) {
                $balance_quantity += $good_receive_detail->quantity;
            }
            $item_issue_details = \App\Model\ItemIssueDetails::where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $balance_quantity -= $item_issue_detail->quantity;
                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    if ($item_issue_breakdown->type == 1) {
                        $good_receive_breakdown = \App\Model\GoodReceiveBreakdown::find($item_issue_breakdown->detail_id);
                        $good_receive_breakdown->is_issued = 1;
                        $good_receive_breakdown->save();
                    }
                }
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $balance_quantity += $item_receive_detail->quantity;
                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    if ($item_receive_breakdown->type == 1) {
                        $good_receive_breakdown = \App\Model\GoodReceiveBreakdown::find($item_receive_breakdown->detail_id);
                        $good_receive_breakdown->is_issued = 0;
                        $good_receive_breakdown->save();
                    }
                }
            }

            $item->stock = $balance_quantity >= 0 ? $balance_quantity : 0;
            $item->save();

            $good_receive_details = \App\Model\GoodReceiveDetails::where('item_id', $item->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($good_receive_details as $good_receive_detail) {
                $available_quantity = 0;
                $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                    ->where('is_main', 1)
                    ->where('is_issued', 0)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                    $available_quantity++;
                }
                $good_receive_detail->available_quantity = $available_quantity;
                $good_receive_detail->save();
            }

            if ($balance_quantity > 0) {
                $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) {
                    $query->where('is_posted', 1)->where('is_delete', 0);
                })
                    ->where('item_id', $item->id)
                    ->where('is_delete', 0)
                    ->orderBy('id', 'DESC')
                    ->get();
                foreach ($good_receive_details as $good_receive_detail) {
                    $grn_quantity = $good_receive_detail->quantity;
                    $available_quantity = $good_receive_detail->available_quantity;

                    if ($available_quantity == 0) {
                        if ($balance_quantity != 0) {
                            if ($grn_quantity >= $balance_quantity) {
                                $good_receive_detail->available_quantity = $balance_quantity;
                                $balance_quantity = 0;
                            } else {
                                $good_receive_detail->available_quantity = $grn_quantity;
                                $balance_quantity = $balance_quantity - $grn_quantity;
                            }
                        } else {
                            $good_receive_detail->available_quantity = 0;
                        }
                        $good_receive_detail->save();
                    }
                }
            } elseif ($balance_quantity != 0) {
                $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) {
                    $query->where('is_posted', 1)->where('is_delete', 0);
                })
                    ->where('item_id', $item->id)
                    ->where('is_delete', 0)
                    ->orderBy('id', 'ASC')
                    ->get();
                foreach ($good_receive_details as $index => $good_receive_detail) {
                    $grn_quantity = $good_receive_detail->quantity;
                    $available_quantity = $good_receive_detail->available_quantity;

                    if ($available_quantity == 0) {
                        if ($index == 0) {
                            $good_receive_detail->quantity = $grn_quantity + abs($balance_quantity);
                        }
                        $good_receive_detail->available_quantity = 0;
                        $good_receive_detail->save();
                    }
                }
            }
        }

        $result = [
            'response' => true,
            'message' => 'Stock updated successfully',
        ];

        echo json_encode($result);
    }
}
