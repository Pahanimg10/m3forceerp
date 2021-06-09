<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    function __construct()
    {
        $this->middleware('user_access');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_new(Request $request)
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

        $quotation = \App\Model\Quotation::find($request->id);

        $data['quotation_id'] = $request->id;
        $data['inquiry_id'] = $request->inquiry_id;
        $data['view'] = $request->view;
        $data['type'] = $request->type;
        $data['users_id'] = $request->session()->get('users_id');
        $data['sales_team_id'] = $quotation ? $quotation->Inquiry->SalesTeam->user_id : 0;

        return view('inquiry.quotation_detail', $data);
    }

    public function get_data(Request $request)
    {
        $job_cards = \App\Model\JobCard::select('id', 'job_card_no', 'job_card_value')
            ->where('inquiry_id', $request->id)
            ->where('is_used', 0)
            ->where('is_delete', 0)
            ->orderBy('job_card_no')
            ->get();
        $cost_sheets = \App\Model\CostSheet::select('id', 'cost_sheet_no', 'cost_sheet_value')
            ->where('inquiry_id', $request->id)
            ->where('is_used', 0)
            ->where('is_delete', 0)
            ->orderBy('cost_sheet_no')
            ->get();
        $discount_types = \App\Model\DiscountType::all();
        $terms_conditions = \App\Model\TermsCondition::all();

        $data = array(
            'job_cards' => $job_cards,
            'cost_sheets' => $cost_sheets,
            'discount_types' => $discount_types,
            'terms_conditions' => $terms_conditions,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function quotation_list(Request $request)
    {
        $quotations = \App\Model\Quotation::select('id', 'inquiry_id', 'quotation_no', 'quotation_date_time', 'remarks', 'quotation_value', 'is_confirmed', 'is_revised', 'user_id')
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->where('inquiry_id', $request->id)
            ->where('is_delete', 0)
            ->get();
        $inquiry = \App\Model\Inquiry::select('id', 'inquiry_no')->find($request->id);

        $data = array(
            'quotations' => $quotations,
            'inquiry' => $inquiry,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_quotation(Request $request)
    {
        $quotation = \App\Model\Quotation::select('id', 'inquiry_id', 'quotation_no', 'quotation_date_time', 'remarks', 'special_notes', 'show_brand', 'show_origin', 'show_installation_meters', 'is_currency', 'usd_rate', 'show_excavation_work', 'show_transport', 'show_food', 'show_accommodation', 'show_bata', 'show_other_expenses', 'other_expenses_text', 'quotation_value', 'is_confirmed', 'is_revised')
            ->with(array('QuotationDiscount' => function ($query) {
                $query->select('id', 'quotation_id', 'discount_type_id', 'description', 'percentage')
                    ->with(array('DiscountType' => function ($query) {
                        $query->select('id', 'name');
                    }));
            }))
            ->with(array('QuotationJobCard' => function ($query) {
                $query->select('id', 'quotation_id', 'job_card_id')
                    ->with(array('JobCard' => function ($query) {
                        $query->select('id', 'job_card_no', 'job_card_value');
                    }));
            }))
            ->with(array('QuotationCostSheet' => function ($query) {
                $query->select('id', 'quotation_id', 'cost_sheet_id')
                    ->with(array('CostSheet' => function ($query) {
                        $query->select('id', 'cost_sheet_no', 'cost_sheet_value');
                    }));
            }))
            ->with(array('QuotationTermsCondition' => function ($query) {
                $query->select('id', 'quotation_id', 'terms_condition_id')
                    ->with(array('TermsCondition' => function ($query) {
                        $query->select('id', 'name');
                    }));
            }))
            ->find($request->id);
        return response($quotation);
    }

    public function discount_detail(Request $request)
    {
        $discounts = \App\Model\QuotationDiscount::select('id', 'quotation_id', 'discount_type_id', 'description', 'percentage')
            ->with(array('DiscountType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->where('quotation_id', $request->id)
            ->where('is_delete', 0)
            ->get();
        $quotation = \App\Model\Quotation::select('id', 'is_confirmed', 'is_revised')->find($request->id);

        $data = array(
            'discounts' => $discounts,
            'quotation' => $quotation,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function confirm_quotation(Request $request)
    {
        $quotation = \App\Model\Quotation::find($request->id);
        $quotation->is_confirmed = 1;

        if ($quotation->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/quotation_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Confirmed,' . $quotation->id . ',,,,,,,,,,,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $issued_item_ids = $issued_items = $returned_item_ids = $returned_items = array();
            $job = \App\Model\Job::where('inquiry_id', $quotation->inquiry_id)->where('is_delete', 0)->first();
            if ($job) {
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
                        $issued_issued_to = '';
                        $item_issue_ids = array();
                        foreach ($item_issue_details as $sub_item_issue_detail) {
                            if ($main_item_issue_detail->item_id == $sub_item_issue_detail->item_id) {
                                $issued_quantity += $sub_item_issue_detail->quantity;
                                $issued_issued_to .= $issued_issued_to != '' ? ' / ' . $sub_item_issue_detail->ItemIssue->issued_to : $sub_item_issue_detail->ItemIssue->issued_to;
                                if (!in_array($sub_item_issue_detail->item_issue_id, $item_issue_ids)) {
                                    array_push($item_issue_ids, $sub_item_issue_detail->item_issue_id);
                                }
                            }
                        }
                        $row = array(
                            'id' => $main_item_issue_detail->Item->id,
                            'code' => $main_item_issue_detail->Item->code,
                            'name' => $main_item_issue_detail->Item->name,
                            'quantity' => $issued_quantity,
                            'issued_to' => $issued_issued_to
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
                                $returned_issued_to = '';
                                foreach ($item_receive_details as $sub_item_receive_detail) {
                                    if ($main_item_receive_detail->item_id == $sub_item_receive_detail->item_id) {
                                        $returned_quantity += $sub_item_receive_detail->quantity;
                                        $returned_issued_to .= $returned_issued_to != '' ? ' / ' . $sub_item_receive_detail->ItemReceive->ItemIssue->issued_to : $sub_item_issue_detail->ItemReceive->ItemIssue->issued_to;
                                    }
                                }
                                $row = array(
                                    'id' => $main_item_receive_detail->Item->id,
                                    'code' => $main_item_receive_detail->Item->code,
                                    'name' => $main_item_receive_detail->Item->name,
                                    'quantity' => $returned_quantity,
                                    'issued_to' => $returned_issued_to
                                );
                                array_push($returned_items, $row);
                                array_push($returned_item_ids, $main_item_receive_detail->item_id);
                            }
                        }

                        array_push($issued_item_ids, $main_item_issue_detail->item_id);
                    }
                }
            }

            $balance_items = array();
            foreach ($issued_items as $issued_item) {
                $balance_quantity = $issued_item['quantity'];
                $returned_quantity = 0;
                $returned_issued_to = '';
                foreach ($returned_items as $returned_item) {
                    if ($issued_item['id'] == $returned_item['id']) {
                        $balance_quantity -= $returned_item['quantity'];
                        $returned_quantity += $returned_item['quantity'];
                        $returned_issued_to .= $returned_issued_to != '' ? ' / ' . $returned_item['issued_to'] : $returned_item['issued_to'];
                    }
                }
                $row = array(
                    'id' => $issued_item['id'],
                    'code' => $issued_item['code'],
                    'name' => $issued_item['name'],
                    'quantity' => $balance_quantity,
                    'issued_quantity' => $issued_item['quantity'],
                    'issued_issued_to' => $issued_item['issued_to'],
                    'returned_quantity' => $returned_quantity,
                    'returned_issued_to' => $returned_issued_to
                );
                array_push($balance_items, $row);
            }

            $job_card_ids = $job_card_items = $installation_items = array();
            foreach ($quotation->QuotationJobCard as $detail) {
                array_push($job_card_ids, $detail['id']);
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
                        'code' => $balance_item['code'],
                        'name' => $balance_item['name'],
                        'requested_quantity' => $requested_quantity,
                        'issued_quantity' => $balance_item['issued_quantity'],
                        'issued_issued_to' => $balance_item['issued_issued_to'],
                        'returned_quantity' => $balance_item['returned_quantity'],
                        'returned_issued_to' => $balance_item['returned_issued_to'],
                        'pending_quantity' => $pending_quantity
                    );
                    array_push($pending_items, $row);
                }
            }

            if (count($pending_items) > 0) {
                $data = array(
                    'inquiry_id' => $quotation->inquiry_id,
                    'quotation_no' => $quotation->quotation_no,
                    'quotation_value' => $quotation->quotation_value,
                    'customer_name' => $quotation->Inquiry->Contact->name,
                    'customer_address' => $quotation->Inquiry->Contact->address,
                    'sales_person' => $quotation->Inquiry->SalesTeam->name,
                    'pending_items' => $pending_items
                );

                Mail::send('emails.job_item_mismatch_notification', $data, function ($message) use ($quotation) {
                    $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                    $message->to('accounts@m3force.com', 'Dilini Harshani');
                    $message->to('accountant@m3force.com', 'Madushika');
                    $message->to('nilmini@m3force.com', 'Nilmini');
                    $message->cc($quotation->Inquiry->SalesTeam->email, $quotation->Inquiry->SalesTeam->name);
                    $message->subject('M3Force Job Item Mismatch Details');
                });
            }

            $inquiry_status = new \App\Model\InquiryDetials();
            $inquiry_status->inquiry_id = $quotation->inquiry_id;
            $inquiry_status->update_date_time = date('Y-m-d H:i');
            $inquiry_status->inquiry_status_id = 14;
            $inquiry_status->sales_team_id = 0;
            $inquiry_status->site_inspection_date_time = '';
            $inquiry_status->advance_payment = 0;
            $inquiry_status->remarks = $quotation->quotation_no;
            $inquiry_status->user_id = $request->session()->get('users_id');
            $inquiry_status->save();

            $quotation_job_cards = \App\Model\QuotationJobCard::where('quotation_id', $quotation->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotation_job_cards as $quotation_job_card) {
                $job_card = \App\Model\JobCard::find($quotation_job_card->job_card_id);
                $job_card->is_used = 1;
                $job_card->save();
            }
            $quotation_cost_sheets = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotation_cost_sheets as $quotation_cost_sheet) {
                $cost_sheet = \App\Model\CostSheet::find($quotation_cost_sheet->cost_sheet_id);
                $cost_sheet->is_used = 1;
                $cost_sheet->save();
            }

            $job_value = $mandays = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $quotation->inquiry_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $job_value += $quotation->quotation_value;

                $quotation_cost_sheets = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_cost_sheets as $quotation_cost_sheet) {
                    $mandays += $quotation_cost_sheet->CostSheet->mandays;
                }
            }

            $job = \App\Model\Job::where('inquiry_id', $quotation->inquiry_id)->where('is_delete', 0)->first();
            if ($job) {
                $job->job_value = $job_value;
                $job->mandays = $mandays;
                $job->save();
            }

            $data = array(
                'inquiry_id' => $quotation->inquiry_id,
                'quotation_no' => $quotation->quotation_no,
                'quotation_value' => $quotation->quotation_value,
                'customer_name' => $quotation->Inquiry->Contact->name,
                'customer_address' => $quotation->Inquiry->Contact->address,
                'sales_person' => $quotation->Inquiry->SalesTeam->name
            );

            Mail::send('emails.advance_payment_notification', $data, function ($message) use ($quotation) {
                $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                $message->to('accounts@m3force.com', 'Dilini Harshani');
                $message->to('accountant@m3force.com', 'Madushika');
                $message->to('stores@m3force.com', 'Nalin Silva');
                $message->to('procurement@m3force.com', 'Deepal Gunasekera');
                $message->cc($quotation->Inquiry->SalesTeam->email, $quotation->Inquiry->SalesTeam->name);
                $message->cc('nilmini@m3force.com', 'Nilmini');
                $message->cc('palitha@m3force.com', 'Palitha Wickramathunga');
                $message->subject('M3Force Customer Quotation Confirmation Details');
            });

            $inquiry = \App\Model\Inquiry::find($quotation->inquiry_id);
            $data = array(
                'id' => $inquiry->id,
                'type' => 1,
                'customer_name' => $inquiry->Contact->name,
                'customer_address' => $inquiry->Contact->address
            );

            Mail::send('emails.installation_update_notification', $data, function ($message) {
                $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                $message->to('stores@m3force.com', 'Nalin Silva');
                $message->to('procurement@m3force.com', 'Deepal Gunasekera');
                $message->subject('M3Force Customer Installation Update Details');
            });

            $result = array(
                'response' => true,
                'message' => 'Quotation confirmed successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Quotation confirm failed'
            );
        }

        echo json_encode($result);
    }

    public function revise_quotation(Request $request)
    {
        $quotation = \App\Model\Quotation::find($request->id);
        $quotation->is_confirmed = 0;
        $quotation->is_revised = 1;

        if ($quotation->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/quotation_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Revised,' . $quotation->id . ',,,,,,,,,,,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $inquiry_status = new \App\Model\InquiryDetials();
            $inquiry_status->inquiry_id = $quotation->inquiry_id;
            $inquiry_status->update_date_time = date('Y-m-d H:i');
            $inquiry_status->inquiry_status_id = 13;
            $inquiry_status->sales_team_id = 0;
            $inquiry_status->site_inspection_date_time = '';
            $inquiry_status->advance_payment = 0;
            $inquiry_status->remarks = $quotation->quotation_no . ' : ' . $request->reason;
            $inquiry_status->user_id = $request->session()->get('users_id');
            $inquiry_status->save();

            $quotation_job_cards = \App\Model\QuotationJobCard::where('quotation_id', $quotation->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotation_job_cards as $quotation_job_card) {
                $job_card = \App\Model\JobCard::find($quotation_job_card->job_card_id);
                $job_card->is_used = 0;
                $job_card->save();
            }
            $quotation_cost_sheets = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotation_cost_sheets as $quotation_cost_sheet) {
                $cost_sheet = \App\Model\CostSheet::find($quotation_cost_sheet->cost_sheet_id);
                $cost_sheet->is_used = 0;
                $cost_sheet->save();
            }

            $job_value = $mandays = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $quotation->inquiry_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $job_value += $quotation->quotation_value;

                $quotation_cost_sheets = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_cost_sheets as $quotation_cost_sheet) {
                    $mandays += $quotation_cost_sheet->CostSheet->mandays;
                }
            }

            $job = \App\Model\Job::where('inquiry_id', $quotation->inquiry_id)->where('is_delete', 0)->first();
            if ($job) {
                $job->job_value = $job_value;
                $job->mandays = $mandays;
                $job->save();
            }

            $result = array(
                'response' => true,
                'message' => 'Quotation revised successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Quotation revise failed'
            );
        }

        echo json_encode($result);
    }

    public function preview_quotation(Request $request)
    {
        $job_card_ids = array();
        foreach ($request->data['job_cards'] as $detail) {
            if ($detail['selected']) {
                array_push($job_card_ids, $detail['id']);
            }
        }
        $cost_sheet_ids = array();
        foreach ($request->data['cost_sheets'] as $detail) {
            if ($detail['selected']) {
                array_push($cost_sheet_ids, $detail['id']);
            }
        }

        $usd = false;
        $usd_rate = 0;
        $currency = 'LKR';
        if (!$request->data['is_currency']) {
            $usd = true;
            $usd_rate = $request->data['usd_rate'];
            $currency = 'USD';
        }

        $main_items = $sub_items = array();
        $job_card_details = \App\Model\JobCardDetails::whereIn('job_card_id', $job_card_ids)
            ->where('is_delete', 0)
            ->get();
        foreach ($job_card_details as $job_card_detail) {
            $margin = ($job_card_detail->margin + 100) / 100;
            $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) / $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
            $row = array(
                'description' => $job_card_detail->Item->name,
                'model_no' => $job_card_detail->Item->model_no,
                'brand' => $job_card_detail->Item->brand,
                'origin' => $job_card_detail->Item->origin,
                'unit_type' => $job_card_detail->Item->UnitType->code,
                'rate' => $value / $job_card_detail->quantity,
                'quantity' => $job_card_detail->quantity,
                'value' => $value
            );
            if ($job_card_detail->is_main == 1) {
                array_push($main_items, $row);
            } else {
                array_push($sub_items, $row);
            }
        }

        $cost_sheet_details = \App\Model\CostSheet::whereIn('id', $cost_sheet_ids)
            ->where('is_delete', 0)
            ->get();
        $rate_ids = $rate_meters = array();
        foreach ($cost_sheet_details as $main_cost_sheet_detail) {
            if ($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                $meters = 0;
                foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                    if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                        $meters += $sub_cost_sheet_detail->meters;
                    }
                }
                $row = array(
                    'installation_name' => $main_cost_sheet_detail->InstallationRate->name,
                    'installation_rate' => $usd ? $main_cost_sheet_detail->InstallationRate->rate / $usd_rate : $main_cost_sheet_detail->InstallationRate->rate,
                    'meters' => $meters
                );
                array_push($rate_meters, $row);
                array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
            }
        }

        $manday_rate = \App\Model\Rate::find(1);
        $excavation_work = $transport = $food = $accommodation = $bata = $other_expenses = 0;
        foreach ($cost_sheet_details as $cost_sheet_detail) {
            $excavation_work += $usd ? $cost_sheet_detail->excavation_work / $usd_rate : $cost_sheet_detail->excavation_work;
            $transport += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) / $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $food += $usd ? $cost_sheet_detail->food / $usd_rate : $cost_sheet_detail->food;
            $accommodation += $usd ? $cost_sheet_detail->accommodation / $usd_rate : $cost_sheet_detail->accommodation;
            $bata += $usd ? $cost_sheet_detail->bata / $usd_rate : $cost_sheet_detail->bata;
            $other_expenses += $usd ? $cost_sheet_detail->other_expenses / $usd_rate : $cost_sheet_detail->other_expenses;
        }

        $package_exist = $special_exist = false;
        $package_description = $special_description = '';
        $package_percentage = $special_percentage = 0;
        foreach ($request->data['discount_data'] as $detail) {
            if ($detail['discount_type_id'] == 1) {
                $package_exist = true;
                $package_description = $detail['description'];
                $package_percentage = $detail['percentage'];
            } else if ($detail['discount_type_id'] == 2) {
                $special_exist = true;
                $special_description = $detail['description'];
                $special_percentage = $detail['percentage'];
            }
        }

        $nbt_exist = $svat_exist = $vat_exist = false;
        $nbt_description = $svat_description = $vat_description = '';
        $nbt_percentage = $svat_percentage = $vat_percentage = 0;
        foreach ($request->data['customer_tax'] as $detail) {
            if ($detail['c_tax_type']) {
                if ($detail['c_tax_type']['id'] == 1) {
                    $nbt_exist = true;
                    $nbt_description = $detail['c_tax_type']['code'];
                    $nbt_percentage = $detail['c_tax_type']['percentage'];
                } else if ($detail['c_tax_type']['id'] == 2) {
                    $svat_exist = true;
                    $svat_description = $detail['c_tax_type']['code'];
                    $svat_percentage = $detail['c_tax_type']['percentage'];
                } else if ($detail['c_tax_type']['id'] == 3) {
                    $vat_exist = true;
                    $vat_description = $detail['c_tax_type']['code'];
                    $vat_percentage = $detail['c_tax_type']['percentage'];
                }
            }
        }

        $colspan = 6;
        if ($request->data['show_brand']) {
            $colspan++;
        }
        if ($request->data['show_origin']) {
            $colspan++;
        }

        $view = '
                <table id="data_table" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">No#</th>
                            <th style="text-align: center; vertical-align: middle;">Description</th>
                            <th style="text-align: center; vertical-align: middle;">Model No</th>
            ';
        $view .= $request->data['show_brand'] ? '<th style="text-align: center; vertical-align: middle;">Brand</th>' : '';
        $view .= $request->data['show_origin'] ? '<th style="text-align: center; vertical-align: middle;">Origin</th>' : '';
        $view .= '
                            <th style="text-align: center; vertical-align: middle;">Unit Type</th>
                            <th style="text-align: center; vertical-align: middle;">Rate (' . $currency . ')</th>
                            <th style="text-align: center; vertical-align: middle;">Quantity</th>
                            <th style="text-align: center; vertical-align: middle;">Value (' . $currency . ')</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

        $count = 1;
        $equipment_installation_total = 0;
        foreach ($main_items as $main_item) {
            $equipment_installation_total += $main_item['value'];
            $view .= '
                <tr>
                    <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                    <td style="vertical-align: middle;">' . $main_item['description'] . '</td>
                    <td style="vertical-align: middle;">' . $main_item['model_no'] . '</td>
                ';
            $view .= $request->data['show_brand'] ? '<td style="vertical-align: middle;">' . $main_item['brand'] . '</td>' : '';
            $view .= $request->data['show_origin'] ? '<td style="vertical-align: middle;">' . $main_item['origin'] . '</td>' : '';
            $view .= '
                    <td style="text-align: center; vertical-align: middle;">' . $main_item['unit_type'] . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($main_item['rate'], 2) . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . $main_item['quantity'] . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($main_item['value'], 2) . '</td>
                </tr>
                ';
            $count++;
        }

        if ($package_exist) {
            $view .= '
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">Package Price</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
                ';
            $discount_value = $equipment_installation_total * $package_percentage / 100;
            $equipment_installation_total -= $discount_value;
            $view .= '
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle; background-color: #fdff32;">' . $package_description . '</th>
                    <th style="text-align: right; vertical-align: middle; background-color: #fdff32;">' . number_format($discount_value, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">Sign up fee -Package Cost</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
                <tr>
                    <td colspan="' . ($colspan + 1) . '" style="vertical-align: middle;"><u>Extra Equipment</u></td>
                </tr>
                ';
        }

        foreach ($sub_items as $sub_item) {
            $equipment_installation_total += $sub_item['value'];
            $view .= '
                <tr>
                    <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                    <td style="vertical-align: middle;">' . $sub_item['description'] . '</td>
                    <td style="vertical-align: middle;">' . $sub_item['model_no'] . '</td>
                ';
            $view .= $request->data['show_brand'] ? '<td style="vertical-align: middle;">' . $sub_item['brand'] . '</td>' : '';
            $view .= $request->data['show_origin'] ? '<td style="vertical-align: middle;">' . $sub_item['origin'] . '</td>' : '';
            $view .= '
                    <td style="text-align: center; vertical-align: middle;">' . $sub_item['unit_type'] . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($sub_item['rate'], 2) . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . $sub_item['quantity'] . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($sub_item['value'], 2) . '</td>
                </tr>
                ';
            $count++;
        }

        $view .= '
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">Total - Equipment</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
            ';

        foreach ($rate_meters as $rate_meter) {
            $installation_value = $rate_meter['installation_rate'] * $rate_meter['meters'];
            $equipment_installation_total += $installation_value;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                ';
            $view .= $request->data['show_installation_meters'] ? '<td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Installation ' . $rate_meter['installation_name'] . ' X ' . number_format($rate_meter['meters'], 2) . ' Meters</td>' : '<td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Installation ' . $rate_meter['installation_name'] . '</td>';
            $view .= '
                        <td style="text-align: right; vertical-align: middle;">' . number_format($installation_value, 2) . '</td>
                    </tr>
                ';
            $count++;
        }

        $engineering_commissioning = 0;
        if ($request->data['show_other_expenses']) {
            $equipment_installation_total += $other_expenses;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                        <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">' . $request->data['other_expenses_text'] . '</td>
                        <td style="text-align: right; vertical-align: middle;">' . number_format($other_expenses, 2) . '</td>
                    </tr>
                ';
            $count++;
        } else {
            $engineering_commissioning += $other_expenses;
        }
        if ($request->data['show_excavation_work']) {
            $equipment_installation_total += $excavation_work;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                        <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Excavation Work</td>
                        <td style="text-align: right; vertical-align: middle;">' . number_format($excavation_work, 2) . '</td>
                    </tr>
                ';
            $count++;
        } else {
            $engineering_commissioning += $excavation_work;
        }
        if ($request->data['show_transport']) {
            $equipment_installation_total += $transport;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                        <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Transport</td>
                        <td style="text-align: right; vertical-align: middle;">' . number_format($transport, 2) . '</td>
                    </tr>
                ';
            $count++;
        } else {
            $engineering_commissioning += $transport;
        }
        if ($request->data['show_food']) {
            $equipment_installation_total += $food;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                        <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Food</td>
                        <td style="text-align: right; vertical-align: middle;">' . number_format($food, 2) . '</td>
                    </tr>
                ';
            $count++;
        } else {
            $engineering_commissioning += $food;
        }
        if ($request->data['show_accommodation']) {
            $equipment_installation_total += $accommodation;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                        <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Accommodation</td>
                        <td style="text-align: right; vertical-align: middle;">' . number_format($accommodation, 2) . '</td>
                    </tr>
                ';
            $count++;
        } else {
            $engineering_commissioning += $accommodation;
        }
        if ($request->data['show_bata']) {
            $equipment_installation_total += $bata;
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                        <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Bata</td>
                        <td style="text-align: right; vertical-align: middle;">' . number_format($bata, 2) . '</td>
                    </tr>
                ';
            $count++;
        } else {
            $engineering_commissioning += $bata;
        }
        $view .= '
                <tr>
                    <td style="text-align: center; vertical-align: middle;">' . $count . '</td>
                    <td colspan="' . ($colspan - 1) . '" style="vertical-align: middle;">Engineering & Commissioning</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($engineering_commissioning, 2) . '</td>
                </tr>
            ';
        $count++;

        $equipment_installation_total += $engineering_commissioning;
        $view .= '
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">Total - Equipment & Installation</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
            ';

        if ($special_exist) {
            $discount_value = $equipment_installation_total * $special_percentage / 100;
            $equipment_installation_total -= $discount_value;
            $view .= '
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle; background-color: #fdff32;">' . $special_description . '</th>
                    <th style="text-align: right; vertical-align: middle; background-color: #fdff32;">' . number_format($discount_value, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;"></th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
                ';
        }

        if ($nbt_exist) {
            $nbt_value = $equipment_installation_total * $nbt_percentage / 100;
            $equipment_installation_total += $nbt_value;
            $view .= '
                <tr>
                    <td colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">' . $nbt_percentage . '% ' . $nbt_description . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($nbt_value, 2) . '</td>
                </tr>
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;"></th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
                ';
        }
        if ($svat_exist) {
            $svat_value = $equipment_installation_total * $svat_percentage / 100;
            $view .= '
                <tr>
                    <td colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">' . $svat_percentage . '% ' . $svat_description . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($svat_value, 2) . '</td>
                </tr>
                ';
        }
        if ($vat_exist) {
            $vat_value = $equipment_installation_total * $vat_percentage / 100;
            $equipment_installation_total += $vat_value;
            $view .= '
                <tr>
                    <td colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">' . $vat_percentage . '% ' . $vat_description . '</td>
                    <td style="text-align: right; vertical-align: middle;">' . number_format($vat_value, 2) . '</td>
                </tr>
                ';
        }
        if (!$nbt_exist && !$svat_exist && !$vat_exist) {
            $view .= '
                <tr>
                    <td colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">VAT EXEMPTED</td>
                    <td style="text-align: right; vertical-align: middle;"></td>
                </tr>
                ';
        }

        $view .= '
                <tr>
                    <th colspan="' . $colspan . '" style="text-align: right; vertical-align: middle;">Grand Total – Equipment & Installation</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black; border-bottom: 3px double black;">' . number_format($equipment_installation_total, 2) . '</th>
                </tr>
            ';

        $view .= '
                    </tbody>
                </table>
            ';

        $result = array(
            'view' => $view,
            'quotation_value' => $equipment_installation_total
        );

        echo json_encode($result);
    }

    public function print_quotation(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $quotation = \App\Model\Quotation::find($request->id);
        $data['quotation'] = $quotation;
        $title = $quotation ? 'Quotation Details ' . $quotation->quotation_no : 'Quotation Details';

        $html = view('inquiry.quotation_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'] . '/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="' . $title . '.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Portrait',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }

    public function print_file_quotation(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $quotation = \App\Model\Quotation::find($request->id);
        $data['quotation'] = $quotation;
        $title = $quotation ? 'Quotation Details ' . $quotation->quotation_no : 'Quotation Details';

        $html = view('inquiry.quotation_file_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'] . '/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="' . $title . '.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Portrait',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item_exist = true;
        $installation_exist = true;
        $valid = true;

        $job_card_ids = array();
        foreach ($request->job_cards as $detail) {
            if ($detail['selected']) {
                array_push($job_card_ids, $detail['id']);
                $item_exist = false;
            }
        }
        $cost_sheet_ids = array();
        foreach ($request->cost_sheets as $detail) {
            if ($detail['selected']) {
                array_push($cost_sheet_ids, $detail['id']);
            }
        }

        $job_value = $quotation_value = 0;
        $main_items_total = $equipment_installation_total = 0;
        $job_card_details = \App\Model\JobCardDetails::whereIn('job_card_id', $job_card_ids)
            ->where('is_delete', 0)
            ->get();
        foreach ($job_card_details as $job_card_detail) {
            $job_value += $job_card_detail->rate * $job_card_detail->quantity;
            $margin = ($job_card_detail->margin + 100) / 100;
            $quotation_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
            if ($job_card_detail->is_main == 1) {
                $main_items_total += $job_card_detail->rate * $margin * $job_card_detail->quantity;
            }
            $equipment_installation_total += $job_card_detail->rate * $margin * $job_card_detail->quantity;
        }

        $cost_sheet_details = \App\Model\CostSheet::whereIn('id', $cost_sheet_ids)
            ->where('is_delete', 0)
            ->get();
        $rate_ids = array();
        $installation_cost = $labour_cost = 0;
        foreach ($cost_sheet_details as $main_cost_sheet_detail) {
            if ($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                $meters = 0;
                foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                    if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                        $meters += $sub_cost_sheet_detail->meters;
                    }
                }

                $installation_cost += $main_cost_sheet_detail->InstallationRate->installation_cost * $meters;
                $labour_cost += $main_cost_sheet_detail->InstallationRate->labour * $meters;

                $quotation_value += $main_cost_sheet_detail->InstallationRate->rate * $meters;
                $equipment_installation_total += $main_cost_sheet_detail->InstallationRate->rate * $meters;

                $installation_exist = false;
                array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
            }
        }

        $manday_rate = \App\Model\Rate::find(1);
        foreach ($cost_sheet_details as $cost_sheet_detail) {
            $job_value += $cost_sheet_detail->excavation_work;
            $job_value += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $job_value += $cost_sheet_detail->food;
            $job_value += $cost_sheet_detail->accommodation;
            $job_value += $cost_sheet_detail->bata;

            $installation_cost += $cost_sheet_detail->other_expenses / 2;
            $labour_cost += $cost_sheet_detail->other_expenses / 2;

            $quotation_value += $cost_sheet_detail->excavation_work;
            $quotation_value += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $quotation_value += $cost_sheet_detail->food;
            $quotation_value += $cost_sheet_detail->accommodation;
            $quotation_value += $cost_sheet_detail->bata;
            $quotation_value += $cost_sheet_detail->other_expenses;

            $equipment_installation_total += $cost_sheet_detail->excavation_work;
            $equipment_installation_total += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $equipment_installation_total += $cost_sheet_detail->food;
            $equipment_installation_total += $cost_sheet_detail->accommodation;
            $equipment_installation_total += $cost_sheet_detail->bata;
            $equipment_installation_total += $cost_sheet_detail->other_expenses;
        }

        $job_value += $installation_cost + $labour_cost;

        $package_percentage = $special_percentage = 0;
        foreach ($request->discount_data as $detail) {
            if ($detail['discount_type_id'] == 1) {
                $package_percentage = $detail['percentage'] / 100;
            } else if ($detail['discount_type_id'] == 2) {
                $special_percentage = $detail['percentage'] / 100;
            }
        }

        if ($package_percentage != 0) {
            $quotation_value -= $main_items_total * $package_percentage;
            $equipment_installation_total -= $main_items_total * $package_percentage;
        }
        if ($special_percentage != 0) {
            $quotation_value -= $equipment_installation_total * $special_percentage;
        }

        $profit_percentage = ($quotation_value - $job_value) * 100 / $job_value;

        if (!$item_exist && !$installation_exist && $profit_percentage < 30) {
            $valid = false;
        }
        // var_dump($package_percentage);
        // var_dump($special_percentage);
        // var_dump($main_items_total);
        // var_dump($equipment_installation_total);
        // var_dump($job_value);
        // var_dump($quotation_value);
        // var_dump($profit_percentage);
        // var_dump($item_exist);
        // var_dump($installation_exist);
        // var_dump($valid);
        // die;
        // $valid = true;

        if ($valid) {
            $quotation = new \App\Model\Quotation();
            $quotation->inquiry_id = $request->inquiry_id;

            $last_id = 0;
            $last_quotation = \App\Model\Quotation::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_quotation ? $last_quotation->id : $last_id;
            $quotation->quotation_no = 'QT/' . date('m') . '/' . date('y') . '/' . $request->inquiry_id . '/' . sprintf('%05d', $last_id + 1);

            $quotation->quotation_date_time = date('Y-m-d', strtotime($request->quotation_date)) . ' ' . $request->quotation_time;
            $quotation->remarks = $request->remarks;
            $quotation->special_notes = $request->special_notes;
            $quotation->show_brand = $request->show_brand ? 1 : 0;
            $quotation->show_origin = $request->show_origin ? 1 : 0;
            $quotation->show_installation_meters = $request->show_installation_meters ? 1 : 0;
            $quotation->is_currency = $request->is_currency ? 1 : 0;
            $quotation->usd_rate = $request->usd_rate;
            $quotation->show_excavation_work = $request->show_excavation_work ? 1 : 0;
            $quotation->show_transport = $request->show_transport ? 1 : 0;
            $quotation->show_food = $request->show_food ? 1 : 0;
            $quotation->show_accommodation = $request->show_accommodation ? 1 : 0;
            $quotation->show_bata = $request->show_bata ? 1 : 0;
            $quotation->show_other_expenses = $request->show_other_expenses ? 1 : 0;
            $quotation->other_expenses_text = $request->other_expenses_text;
            $quotation->quotation_value = $request->quotation_value;
            $quotation->user_id = $request->session()->get('users_id');

            if ($quotation->save()) {
                $job_card_id_list = '';
                foreach ($request->job_cards as $job_card) {
                    if ($job_card['selected']) {
                        $quotation_job_card = new \App\Model\QuotationJobCard();
                        $quotation_job_card->quotation_id = $quotation->id;
                        $quotation_job_card->job_card_id = $job_card['id'];
                        $quotation_job_card->save();

                        $job_card_id_list .= $job_card_id_list != '' ? '|' . $quotation_job_card->job_card_id : $quotation_job_card->job_card_id;

                        $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $quotation_job_card->job_card_id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($job_card_details as $job_card_detail) {
                            $quotation_job_card_detail = new \App\Model\QuotationJobCardDetails();
                            $quotation_job_card_detail->quotation_job_card_id = $quotation_job_card->id;
                            $quotation_job_card_detail->item_id = $job_card_detail->item_id;
                            $quotation_job_card_detail->rate = $job_card_detail->rate;
                            $quotation_job_card_detail->quantity = $job_card_detail->quantity;
                            $quotation_job_card_detail->margin = $job_card_detail->margin;
                            $quotation_job_card_detail->is_main = $job_card_detail->is_main;
                            $quotation_job_card_detail->save();
                        }
                    }
                }
                $cost_sheet_id_list = '';
                foreach ($request->cost_sheets as $cost_sheet) {
                    if ($cost_sheet['selected']) {
                        $quotation_cost_sheet = new \App\Model\QuotationCostSheet();
                        $quotation_cost_sheet->quotation_id = $quotation->id;
                        $quotation_cost_sheet->cost_sheet_id = $cost_sheet['id'];

                        $cost_sheet_id_list .= $cost_sheet_id_list != '' ? '|' . $quotation_cost_sheet->cost_sheet_id : $quotation_cost_sheet->cost_sheet_id;

                        $cost_sheet = \App\Model\CostSheet::find($cost_sheet['id']);
                        if ($cost_sheet) {
                            $quotation_cost_sheet->inquiry_id = $cost_sheet->inquiry_id;
                            $quotation_cost_sheet->cost_sheet_no = $cost_sheet->cost_sheet_no;
                            $quotation_cost_sheet->cost_sheet_date_time = $cost_sheet->cost_sheet_date_time;
                            $quotation_cost_sheet->installation_rate_id = $cost_sheet->installation_rate_id;
                            $quotation_cost_sheet->meters = $cost_sheet->meters;
                            $quotation_cost_sheet->excavation_work = $cost_sheet->excavation_work;
                            $quotation_cost_sheet->transport = $cost_sheet->transport;
                            $quotation_cost_sheet->traveling_mandays = $cost_sheet->traveling_mandays;
                            $quotation_cost_sheet->food = $cost_sheet->food;
                            $quotation_cost_sheet->accommodation = $cost_sheet->accommodation;
                            $quotation_cost_sheet->bata = $cost_sheet->bata;
                            $quotation_cost_sheet->other_expenses = $cost_sheet->other_expenses;
                            $quotation_cost_sheet->remarks = $cost_sheet->remarks;
                            $quotation_cost_sheet->cost_sheet_value = $cost_sheet->cost_sheet_value;
                            $quotation_cost_sheet->installation_value = $cost_sheet->installation_value;
                            $quotation_cost_sheet->mandays = $cost_sheet->mandays;
                            $quotation_cost_sheet->user_id = $cost_sheet->user_id;
                        }

                        $quotation_cost_sheet->save();
                    }
                }
                $terms_condition_id_list = '';
                foreach ($request->terms_conditions as $terms_condition) {
                    if ($terms_condition['selected']) {
                        $quotation_terms_condition = new \App\Model\QuotationTermsCondition();
                        $quotation_terms_condition->quotation_id = $quotation->id;
                        $quotation_terms_condition->terms_condition_id = $terms_condition['id'];
                        $quotation_terms_condition->save();

                        $terms_condition_id_list .= $terms_condition_id_list != '' ? '|' . $quotation_terms_condition->terms_condition_id : $quotation_terms_condition->terms_condition_id;
                    }
                }
                $discount_list = '';
                foreach ($request->discount_data as $details) {
                    $quotation_discount = new \App\Model\QuotationDiscount();
                    $quotation_discount->quotation_id = $quotation->id;
                    $quotation_discount->discount_type_id = $details['discount_type_id'];
                    $quotation_discount->description = $details['description'];
                    $quotation_discount->percentage = $details['percentage'];
                    $quotation_discount->save();

                    $discount_list .= $discount_list != '' ? '|' . $quotation_discount->discount_type_id . '-' . $quotation_discount->percentage : $quotation_discount->discount_type_id . '-' . $quotation_discount->percentage;
                }

                $inquiry_status = new \App\Model\InquiryDetials();
                $inquiry_status->inquiry_id = $quotation->inquiry_id;
                $inquiry_status->update_date_time = date('Y-m-d H:i');
                $inquiry_status->inquiry_status_id = 8;
                $inquiry_status->sales_team_id = 0;
                $inquiry_status->site_inspection_date_time = '';
                $inquiry_status->advance_payment = 0;
                $inquiry_status->remarks = $quotation->quotation_no;
                $inquiry_status->user_id = $request->session()->get('users_id');
                $inquiry_status->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/quotation_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $quotation->id . ',' . $quotation->inquiry_id . ',' . str_replace(',', ' ', $quotation->quotation_no) . ',' . str_replace(',', ' ', $quotation->quotation_date_time) . ',' . str_replace(',', ' ', $quotation->remarks) . ',' . str_replace(',', ' ', $quotation->special_notes) . ',' . $quotation->show_brand . ',' . $quotation->show_origin . ',' . $quotation->show_installation_meters . ',' . $quotation->is_currency . ',' . $quotation->usd_rate . ',' . $quotation->show_excavation_work . ',' . $quotation->show_transport . ',' . $quotation->show_food . ',' . $quotation->show_accommodation . ',' . $quotation->show_bata . ',' . $quotation->show_other_expenses . ',' . str_replace(',', ' ', $quotation->other_expenses_text) . ',' . $quotation->quotation_value . ',' . $job_card_id_list . ',' . $cost_sheet_id_list . ',' . $terms_condition_id_list . ',' . $discount_list . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Quotation created successfully',
                    'data' => $quotation->id
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Quotation creation failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Quotation does not meet the profit margin'
            );
        }

        echo json_encode($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item_exist = true;
        $installation_exist = true;
        $valid = true;

        $job_card_ids = array();
        foreach ($request->job_cards as $detail) {
            if ($detail['selected']) {
                array_push($job_card_ids, $detail['id']);
                $item_exist = false;
            }
        }
        $cost_sheet_ids = array();
        foreach ($request->cost_sheets as $detail) {
            if ($detail['selected']) {
                array_push($cost_sheet_ids, $detail['id']);
            }
        }

        $job_value = $quotation_value = 0;
        $main_items_total = $equipment_installation_total = 0;
        $job_card_details = \App\Model\JobCardDetails::whereIn('job_card_id', $job_card_ids)
            ->where('is_delete', 0)
            ->get();
        foreach ($job_card_details as $job_card_detail) {
            $job_value += $job_card_detail->rate * $job_card_detail->quantity;
            $margin = ($job_card_detail->margin + 100) / 100;
            $quotation_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
            if ($job_card_detail->is_main == 1) {
                $main_items_total += $job_card_detail->rate * $margin * $job_card_detail->quantity;
            }
            $equipment_installation_total += $job_card_detail->rate * $margin * $job_card_detail->quantity;
        }

        $cost_sheet_details = \App\Model\CostSheet::whereIn('id', $cost_sheet_ids)
            ->where('is_delete', 0)
            ->get();
        $rate_ids = array();
        $installation_cost = $labour_cost = 0;
        foreach ($cost_sheet_details as $main_cost_sheet_detail) {
            if ($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                $meters = 0;
                foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                    if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                        $meters += $sub_cost_sheet_detail->meters;
                    }
                }

                $installation_cost += $main_cost_sheet_detail->InstallationRate->installation_cost * $meters;
                $labour_cost += $main_cost_sheet_detail->InstallationRate->labour * $meters;

                $quotation_value += $main_cost_sheet_detail->InstallationRate->rate * $meters;
                $equipment_installation_total += $main_cost_sheet_detail->InstallationRate->rate * $meters;

                $installation_exist = false;
                array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
            }
        }

        $manday_rate = \App\Model\Rate::find(1);
        foreach ($cost_sheet_details as $cost_sheet_detail) {
            $job_value += $cost_sheet_detail->excavation_work;
            $job_value += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $job_value += $cost_sheet_detail->food;
            $job_value += $cost_sheet_detail->accommodation;
            $job_value += $cost_sheet_detail->bata;

            $installation_cost += $cost_sheet_detail->other_expenses / 2;
            $labour_cost += $cost_sheet_detail->other_expenses / 2;

            $quotation_value += $cost_sheet_detail->excavation_work;
            $quotation_value += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $quotation_value += $cost_sheet_detail->food;
            $quotation_value += $cost_sheet_detail->accommodation;
            $quotation_value += $cost_sheet_detail->bata;
            $quotation_value += $cost_sheet_detail->other_expenses;

            $equipment_installation_total += $cost_sheet_detail->excavation_work;
            $equipment_installation_total += $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
            $equipment_installation_total += $cost_sheet_detail->food;
            $equipment_installation_total += $cost_sheet_detail->accommodation;
            $equipment_installation_total += $cost_sheet_detail->bata;
            $equipment_installation_total += $cost_sheet_detail->other_expenses;
        }

        $job_value += $installation_cost + $labour_cost;

        $package_percentage = $special_percentage = 0;
        foreach ($request->discount_data as $detail) {
            if ($detail['discount_type_id'] == 1) {
                $package_percentage = $detail['percentage'] / 100;
            } else if ($detail['discount_type_id'] == 2) {
                $special_percentage = $detail['percentage'] / 100;
            }
        }

        if ($package_percentage != 0) {
            $quotation_value -= $main_items_total * $package_percentage;
            $equipment_installation_total -= $main_items_total * $package_percentage;
        }
        if ($special_percentage != 0) {
            $quotation_value -= $equipment_installation_total * $special_percentage;
        }

        $profit_percentage = ($quotation_value - $job_value) * 100 / $job_value;

        if (!$item_exist && !$installation_exist && $profit_percentage < 30) {
            $valid = false;
        }
        // var_dump($package_percentage);
        // var_dump($special_percentage);
        // var_dump($main_items_total);
        // var_dump($equipment_installation_total);
        // var_dump($job_value);
        // var_dump($quotation_value);
        // var_dump($profit_percentage);
        // var_dump($item_exist);
        // var_dump($installation_exist);
        // var_dump($valid);
        // die;
        // $valid = true;

        if ($valid) {
            $quotation = \App\Model\Quotation::find($request->quotation_id);
            $quotation->inquiry_id = $request->inquiry_id;
            $quotation->quotation_no = $request->quotation_no;
            $quotation->quotation_date_time = date('Y-m-d', strtotime($request->quotation_date)) . ' ' . $request->quotation_time;
            $quotation->remarks = $request->remarks;
            $quotation->special_notes = $request->special_notes;
            $quotation->show_brand = $request->show_brand ? 1 : 0;
            $quotation->show_origin = $request->show_origin ? 1 : 0;
            $quotation->show_installation_meters = $request->show_installation_meters ? 1 : 0;
            $quotation->is_currency = $request->is_currency ? 1 : 0;
            $quotation->usd_rate = $request->usd_rate;
            $quotation->show_excavation_work = $request->show_excavation_work ? 1 : 0;
            $quotation->show_transport = $request->show_transport ? 1 : 0;
            $quotation->show_food = $request->show_food ? 1 : 0;
            $quotation->show_accommodation = $request->show_accommodation ? 1 : 0;
            $quotation->show_bata = $request->show_bata ? 1 : 0;
            $quotation->show_other_expenses = $request->show_other_expenses ? 1 : 0;
            $quotation->other_expenses_text = $request->other_expenses_text;
            $quotation->quotation_value = $request->quotation_value;
            $quotation->user_id = $request->session()->get('users_id');

            if ($quotation->save()) {
                $quotation_job_cards = \App\Model\QuotationJobCard::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_job_cards as $quotation_job_card) {
                    $quotation_job_card->is_delete = 1;
                    $quotation_job_card->save();

                    $quotation_job_card_details = \App\Model\QuotationJobCardDetails::where('quotation_job_card_id', $quotation_job_card->id)
                        ->where('is_delete', 0)
                        ->get();
                    foreach ($quotation_job_card_details as $quotation_job_card_detail) {
                        $quotation_job_card_detail->is_delete = 1;
                        $quotation_job_card_detail->save();
                    }
                }
                $quotation_cost_sheets = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_cost_sheets as $quotation_cost_sheet) {
                    $quotation_cost_sheet->is_delete = 1;
                    $quotation_cost_sheet->save();
                }
                $quotation_terms_conditions = \App\Model\QuotationTermsCondition::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_terms_conditions as $quotation_terms_condition) {
                    $quotation_terms_condition->is_delete = 1;
                    $quotation_terms_condition->save();
                }
                $quotation_discounts = \App\Model\QuotationDiscount::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_discounts as $quotation_discount) {
                    $quotation_discount->is_delete = 1;
                    $quotation_discount->save();
                }

                $job_card_id_list = '';
                foreach ($request->job_cards as $job_card) {
                    if ($job_card['selected']) {
                        $quotation_job_card = \App\Model\QuotationJobCard::where('quotation_id', $quotation->id)
                            ->where('job_card_id', $job_card['id'])
                            ->first();
                        $quotation_job_card = $quotation_job_card ? $quotation_job_card : new \App\Model\QuotationJobCard();
                        $quotation_job_card->quotation_id = $quotation->id;
                        $quotation_job_card->job_card_id = $job_card['id'];
                        $quotation_job_card->is_delete = 0;
                        $quotation_job_card->save();

                        $job_card_id_list .= $job_card_id_list != '' ? '|' . $quotation_job_card->job_card_id : $quotation_job_card->job_card_id;

                        $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $quotation_job_card->job_card_id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($job_card_details as $job_card_detail) {
                            $quotation_job_card_detail = \App\Model\QuotationJobCardDetails::where('quotation_job_card_id', $quotation_job_card->id)
                                ->where('item_id', $job_card_detail->item_id)
                                ->where('is_main', $job_card_detail->is_main)
                                ->first();
                            $quotation_job_card_detail = $quotation_job_card_detail ? $quotation_job_card_detail : new \App\Model\QuotationJobCardDetails();
                            $quotation_job_card_detail->quotation_job_card_id = $quotation_job_card->id;
                            $quotation_job_card_detail->item_id = $job_card_detail->item_id;
                            $quotation_job_card_detail->rate = $job_card_detail->rate;
                            $quotation_job_card_detail->quantity = $job_card_detail->quantity;
                            $quotation_job_card_detail->margin = $job_card_detail->margin;
                            $quotation_job_card_detail->is_main = $job_card_detail->is_main;
                            $quotation_job_card_detail->is_delete = 0;
                            $quotation_job_card_detail->save();
                        }
                    }
                }
                $cost_sheet_id_list = '';
                foreach ($request->cost_sheets as $cost_sheet) {
                    if ($cost_sheet['selected']) {
                        $quotation_cost_sheet = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                            ->where('cost_sheet_id', $cost_sheet['id'])
                            ->first();
                        $quotation_cost_sheet = $quotation_cost_sheet ? $quotation_cost_sheet : new \App\Model\QuotationCostSheet();
                        $quotation_cost_sheet->quotation_id = $quotation->id;
                        $quotation_cost_sheet->cost_sheet_id = $cost_sheet['id'];

                        $cost_sheet = \App\Model\CostSheet::find($cost_sheet['id']);
                        if ($cost_sheet) {
                            $quotation_cost_sheet->inquiry_id = $cost_sheet->inquiry_id;
                            $quotation_cost_sheet->cost_sheet_no = $cost_sheet->cost_sheet_no;
                            $quotation_cost_sheet->cost_sheet_date_time = $cost_sheet->cost_sheet_date_time;
                            $quotation_cost_sheet->installation_rate_id = $cost_sheet->installation_rate_id;
                            $quotation_cost_sheet->meters = $cost_sheet->meters;
                            $quotation_cost_sheet->excavation_work = $cost_sheet->excavation_work;
                            $quotation_cost_sheet->transport = $cost_sheet->transport;
                            $quotation_cost_sheet->traveling_mandays = $cost_sheet->traveling_mandays;
                            $quotation_cost_sheet->food = $cost_sheet->food;
                            $quotation_cost_sheet->accommodation = $cost_sheet->accommodation;
                            $quotation_cost_sheet->bata = $cost_sheet->bata;
                            $quotation_cost_sheet->other_expenses = $cost_sheet->other_expenses;
                            $quotation_cost_sheet->remarks = $cost_sheet->remarks;
                            $quotation_cost_sheet->cost_sheet_value = $cost_sheet->cost_sheet_value;
                            $quotation_cost_sheet->installation_value = $cost_sheet->installation_value;
                            $quotation_cost_sheet->mandays = $cost_sheet->mandays;
                            $quotation_cost_sheet->user_id = $cost_sheet->user_id;
                        }

                        $quotation_cost_sheet->is_delete = 0;
                        $quotation_cost_sheet->save();

                        $cost_sheet_id_list .= $cost_sheet_id_list != '' ? '|' . $quotation_cost_sheet->cost_sheet_id : $quotation_cost_sheet->cost_sheet_id;
                    }
                }
                $terms_condition_id_list = '';
                foreach ($request->terms_conditions as $terms_condition) {
                    if ($terms_condition['selected']) {
                        $quotation_terms_condition = \App\Model\QuotationTermsCondition::where('quotation_id', $quotation->id)
                            ->where('terms_condition_id', $terms_condition['id'])
                            ->first();
                        $quotation_terms_condition = $quotation_terms_condition ? $quotation_terms_condition : new \App\Model\QuotationTermsCondition();
                        $quotation_terms_condition->quotation_id = $quotation->id;
                        $quotation_terms_condition->terms_condition_id = $terms_condition['id'];
                        $quotation_terms_condition->is_delete = 0;
                        $quotation_terms_condition->save();

                        $terms_condition_id_list .= $terms_condition_id_list != '' ? '|' . $quotation_terms_condition->terms_condition_id : $quotation_terms_condition->terms_condition_id;
                    }
                }
                $discount_list = '';
                foreach ($request->discount_data as $details) {
                    $quotation_discount = \App\Model\QuotationDiscount::where('quotation_id', $quotation->id)
                        ->where('discount_type_id', $details['discount_type_id'])
                        ->first();
                    $quotation_discount = $quotation_discount ? $quotation_discount : new \App\Model\QuotationDiscount();
                    $quotation_discount->quotation_id = $quotation->id;
                    $quotation_discount->discount_type_id = $details['discount_type_id'];
                    $quotation_discount->description = $details['description'];
                    $quotation_discount->percentage = $details['percentage'];
                    $quotation_discount->is_delete = 0;
                    $quotation_discount->save();

                    $discount_list .= $discount_list != '' ? '|' . $quotation_discount->discount_type_id . '-' . $quotation_discount->percentage : $quotation_discount->discount_type_id . '-' . $quotation_discount->percentage;
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/quotation_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $quotation->id . ',' . $quotation->inquiry_id . ',' . str_replace(',', ' ', $quotation->quotation_no) . ',' . str_replace(',', ' ', $quotation->quotation_date_time) . ',' . str_replace(',', ' ', $quotation->remarks) . ',' . str_replace(',', ' ', $quotation->special_notes) . ',' . $quotation->show_brand . ',' . $quotation->show_origin . ',' . $quotation->show_installation_meters . ',' . $quotation->is_currency . ',' . $quotation->usd_rate . ',' . $quotation->show_excavation_work . ',' . $quotation->show_transport . ',' . $quotation->show_food . ',' . $quotation->show_accommodation . ',' . $quotation->show_bata . ',' . $quotation->show_other_expenses . ',' . str_replace(',', ' ', $quotation->other_expenses_text) . ',' . $quotation->quotation_value . ',' . $job_card_id_list . ',' . $cost_sheet_id_list . ',' . $terms_condition_id_list . ',' . $discount_list . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Quotation updated successfully',
                    'data' => $quotation->id
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Quotation updation failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Quotation does not meet the profit margin',
                'data' => $request->quotation_id
            );
        }

        echo json_encode($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $quotation = \App\Model\Quotation::find($id);
        $quotation->is_delete = 1;

        if ($quotation->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/quotation_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $quotation->id . ',,,,,,,,,,,,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Quotation deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Quotation deletion failed'
            );
        }

        echo json_encode($result);
    }
}
