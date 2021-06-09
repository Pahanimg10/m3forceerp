<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class TechResponseController extends Controller
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

    public function new_fault(Request $request)
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

        $data['type'] = $request->type;

        return view('tech_response.new_fault', $data);
    }

    public function add_new_contact()
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

        return view('tech_response.add_new_contact', $data);
    }

    public function validate_customer_name(Request $request)
    {
        $contact = \App\Model\Contact::where('name', $request->name)
            ->where('is_delete', 0)
            ->first();
        if (!$contact) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function add_new_tech_response(Request $request)
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

        $data['contact_id'] = $request->contact_id;
        $data['tech_response_id'] = $request->tech_response_id;

        return view('tech_response.add_new_tech_response', $data);
    }

    public function validate_fault_type(Request $request)
    {
        if ($request->value != $request->fault_type_id) {
            $tech_response = \App\Model\TechResponse::where('contact_id', $request->contact_id)
                ->where('tech_response_fault_id', $request->fault_type_id)
                ->where('is_completed', 0)
                ->where('is_delete', 0)
                ->first();
            if (!$tech_response) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_tech_response(Request $request)
    {
        $tech_response = \App\Model\TechResponse::select('id', 'contact_id', 'tech_response_fault_id', 'tech_response_no', 'record_date_time', 'remarks', 'reported_person', 'reported_contact_no', 'reported_email', 'tech_response_value', 'is_completed', 'user_id')
            ->with(array('Contact' => function ($query) {
                $query->select('id', 'name', 'address', 'contact_no', 'email', 'group_id', 'is_group')
                    ->with(array('ContactTax' => function ($query) {
                        $query->select('id', 'contact_id', 'tax_id')
                            ->with(array('CTaxType' => function ($query) {
                                $query->select('id', 'code', 'name', 'percentage');
                            }));
                    }))
                    ->with(array('CGroup' => function ($query) {
                        $query->select('id', 'name')
                            ->with(array('CGroupTax' => function ($query) {
                                $query->select('id', 'group_id', 'tax_id')
                                    ->with(array('CTaxType' => function ($query) {
                                        $query->select('id', 'code', 'name', 'percentage');
                                    }));
                            }));
                    }));
            }))
            ->with(array('TechResponseFault' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->find($request->id);
        return response($tech_response);
    }

    public function ongoing_tech_response()
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

        return view('tech_response.ongoing_tech_response', $data);
    }

    public function ongoing_tech_response_list(Request $request)
    {
        $tech_response_list = array();
        $tech_responses = \App\Model\TechResponse::where(function ($q) use ($request) {
            $request->fault_type_id != -1 ? $q->where('tech_response_fault_id', $request->fault_type_id) : '';
        })
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($tech_responses as $tech_response) {
            $tech_response_status = \App\Model\TechResponseDetails::selectRaw('MAX(tech_response_status_id) AS tech_response_status_id')
                ->where('tech_response_id', $tech_response->id)
                ->where('is_delete', 0)
                ->first();
            if ($tech_response_status && ($request->update_status_id == -1 || $request->update_status_id == 0 || $request->update_status_id == $tech_response_status->tech_response_status_id)) {
                $tech_response_detail = \App\Model\TechResponseDetails::where('tech_response_id', $tech_response->id)
                    ->where('tech_response_status_id', $tech_response_status->tech_response_status_id)
                    ->where('is_delete', 0)
                    ->orderBy('update_date_time', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();
                $tech_response_count = \App\Model\TechResponse::where('contact_id', $tech_response->contact_id)
                    ->where('tech_response_fault_id', $tech_response->tech_response_fault_id)
                    ->where('id', '<', $tech_response->id)
                    ->where('is_completed', 0)
                    ->where('is_delete', 0)
                    ->get()
                    ->count();
                $row = array(
                    'id' => $tech_response->id,
                    'critical_status' => $tech_response_count > 1 ? 1 : 0,
                    'contact_id' => $tech_response->contact_id,
                    'tech_response_no' => $tech_response->tech_response_no,
                    'record_date_time' => $tech_response->record_date_time,
                    'fault_type' => $tech_response->TechResponseFault ? $tech_response->TechResponseFault->name : '',
                    'tech_response_value' => $tech_response->tech_response_value,
                    'customer_id' => $tech_response->Contact ? $tech_response->Contact->contact_id : '',
                    'customer_name' => $tech_response->Contact ? $tech_response->Contact->name : '',
                    'customer_address' => $tech_response->Contact ? $tech_response->Contact->address : '',
                    'customer_contact_no' => $tech_response->Contact ? $tech_response->Contact->contact_no : '',
                    'contract_end_date' => $tech_response->Contact ? $tech_response->Contact->end : '',
                    'remarks' => $tech_response->remarks,
                    'update_date_time' => $tech_response_detail ? $tech_response_detail->update_date_time : '',
                    'update_status_id' => $tech_response_detail && $tech_response_detail->TechResponseStatus ? $tech_response_detail->TechResponseStatus->id : 1,
                    'update_status' => $tech_response_detail && $tech_response_detail->TechResponseStatus ? $tech_response_detail->TechResponseStatus->name : '',
                    'update_remarks' => $tech_response_detail ? $tech_response_detail->remarks : '',
                    'record_person' => $tech_response->User ? $tech_response->User->first_name : ''
                );
                if ($request->update_status_id == 0) {
                    if ($tech_response_count > 1) {
                        array_push($tech_response_list, $row);
                    }
                } else {
                    array_push($tech_response_list, $row);
                }
            }
        }

        $data = array(
            'tech_response_list' => $tech_response_list,
            'permission' => !in_array(1, session()->get('user_group'))
        );

        return response($data);
    }

    public function update_tech_response(Request $request)
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

        $data['tech_response_id'] = $request->id;

        return view('tech_response.update_tech_response', $data);
    }

    public function validate_tech_response_status(Request $request)
    {
        $avoid = array(2, 7, 11);
        if ($request->value != $request->update_status && !in_array($request->update_status, $avoid)) {
            $tech_response_detail = \App\Model\TechResponseDetails::where('tech_response_id', $request->tech_response_id)
                ->where('tech_response_status_id', $request->update_status)
                ->where('is_delete', 0)
                ->first();
            if ($tech_response_detail) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function get_data()
    {
        $tech_response_status = \App\Model\TechResponseStatus::select('id', 'name')->where('show_update', 1)->get();

        $data = array(
            'tech_response_status' => $tech_response_status,
            'users_id' => session()->get('users_id')
        );

        return response($data);
    }

    public function find_tech_response_status(Request $request)
    {
        $tech_response_status = \App\Model\TechResponseDetails::select('id', 'tech_response_id', 'update_date_time', 'tech_response_status_id', 'job_scheduled_date_time', 'is_chargeable', 'invoice_no', 'invoice_value', 'remarks')
            ->with(array('TechResponseStatus' => function ($query) {
                $query->select('id', 'name', 'show_update');
            }))
            ->with(array('TechResponseInvoiceDetails' => function ($query) {
                $query->select('id', 'tech_response_details_id', 'item_id', 'rate', 'quantity', 'value', 'invoice_value')
                    ->with(array('Item' => function ($query) {
                        $query->select('id', 'code', 'name', 'model_no', 'unit_type_id')
                            ->with(array('UnitType' => function ($query) {
                                $query->select('id', 'code', 'name');
                            }));
                    }));
            }))
            ->find($request->id);
        return response($tech_response_status);
    }

    public function tech_response_status_list(Request $request)
    {
        $tech_response_status = \App\Model\TechResponseDetails::select('id', 'tech_response_id', 'update_date_time', 'tech_response_status_id', 'job_scheduled_date_time', 'remarks', 'user_id')
            ->with(array('TechResponseStatus' => function ($query) {
                $query->select('id', 'name', 'show_update');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->where('tech_response_id', $request->tech_response_id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'tech_response_status' => $tech_response_status,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function get_issed_items(Request $request)
    {
        $data = $issued_items = $item_issue_ids = $returned_items = array();

        $item_issues = \App\Model\ItemIssue::where('item_issue_type_id', 2)
            ->where('document_id', $request->tech_response_id)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->get();
        foreach ($item_issues as $item_issue) {
            array_push($item_issue_ids, $item_issue->id);
            $item_issue_details = \App\Model\ItemIssueDetails::where('item_issue_id', $item_issue->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $quantity = $value = 0;
                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    $quantity += $item_issue_breakdown->quantity;
                    $value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                }

                $row = array(
                    'id' => $item_issue_detail->Item->id,
                    'code' => $item_issue_detail->Item->code,
                    'name' => $item_issue_detail->Item->name,
                    'unit_type' => $item_issue_detail->Item->UnitType->code,
                    'rate' => $quantity != 0 ? $value / $quantity : 0,
                    'quantity' => $quantity,
                    'value' => $value
                );
                array_push($issued_items, $row);
            }
        }

        $item_receives = \App\Model\ItemReceive::whereIn('item_issue_id', $item_issue_ids)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->get();
        foreach ($item_receives as $item_receive) {
            $item_receive_details = \App\Model\ItemReceiveDetails::where('item_receive_id', $item_receive->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $quantity = $value = 0;
                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    $quantity += $item_receive_breakdown->quantity;
                    $value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                }

                $row = array(
                    'id' => $item_receive_detail->Item->id,
                    'code' => $item_receive_detail->Item->code,
                    'name' => $item_receive_detail->Item->name,
                    'unit_type' => $item_receive_detail->Item->UnitType->code,
                    'rate' => $quantity != 0 ? $value / $quantity : 0,
                    'quantity' => $quantity,
                    'value' => $value
                );
                array_push($returned_items, $row);
            }
        }

        $count = 0;
        $issued_item_ids = array();
        foreach ($issued_items as $main_issued_item) {
            if (!in_array($main_issued_item['id'], $issued_item_ids)) {
                $total_quantity = $total_value = 0;
                foreach ($issued_items as $sub_issued_item) {
                    if ($main_issued_item['id'] == $sub_issued_item['id']) {
                        $total_quantity += $sub_issued_item['quantity'];
                        $total_value += $sub_issued_item['value'];
                    }
                }
                foreach ($returned_items as $returned_item) {
                    if ($main_issued_item['id'] == $returned_item['id']) {
                        $total_quantity -= $returned_item['quantity'];
                        $total_value -= $returned_item['value'];
                    }
                }

                if ($total_quantity != 0) {
                    $row = array(
                        'index' => $count,
                        'id' => $main_issued_item['id'],
                        'column' => $count + 1,
                        'code' => $main_issued_item['code'],
                        'name' => $main_issued_item['name'],
                        'unit_type' => $main_issued_item['unit_type'],
                        'rate' => number_format($total_value / $total_quantity, 2, ".", ""),
                        'quantity' => number_format($total_quantity, 2, ".", ""),
                        'value' => number_format($total_value, 2, ".", ""),
                        'invoice_value' => 0
                    );
                    array_push($data, $row);
                    $count++;
                }
                array_push($issued_item_ids, $main_issued_item['id']);
            }
        }

        return response($data);
    }

    public function tech_response_job_card(Request $request)
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

        $data['tech_response_id'] = $request->id;

        return view('tech_response.tech_response_job_card', $data);
    }

    public function tech_response_installation_sheet(Request $request)
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

        $data['tech_response_id'] = $request->id;

        return view('tech_response.tech_response_installation_sheet', $data);
    }

    public function tech_response_quotation(Request $request)
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

        $data['tech_response_id'] = $request->id;

        return view('tech_response.tech_response_quotation', $data);
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
        if ($request->type == 0) {
            $tech_response = new \App\Model\TechResponse();
            $tech_response->contact_id = $request->contact_id;
            $tech_response->tech_response_fault_id = isset($request->fault_types['id']) ? $request->fault_types['id'] : 0;
            $tech_response->record_date_time = date('Y-m-d', strtotime($request->record_date)) . ' ' . $request->record_time;
            $tech_response->remarks = $request->remarks;
            $tech_response->reported_person = $request->reported_person;
            $tech_response->reported_contact_no = $request->reported_contact_no;
            $tech_response->reported_email = $request->reported_email;
            $tech_response->user_id = $request->session()->get('users_id');

            if ($tech_response->save()) {
                $tech_response->tech_response_no = 'TR/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $tech_response->id);
                $tech_response->save();

                $sms = '--- M3Force Tech Response ---' . PHP_EOL;
                $sms .= 'Tech Response No : ' . $tech_response->tech_response_no . PHP_EOL;
                $sms .= 'Customer ID : ' . $tech_response->Contact->contact_id . PHP_EOL;
                $sms .= 'Customer Name : ' . $tech_response->Contact->name . PHP_EOL;
                $sms .= 'Customer Address : ' . $tech_response->Contact->address . PHP_EOL;
                $sms .= 'Customer Contact No : ' . $tech_response->Contact->contact_no . PHP_EOL;
                $sms .= 'Contact End Date : ' . $tech_response->Contact->end_date . PHP_EOL;
                $sms .= 'Fault Type : ' . $tech_response->TechResponseFault->name . PHP_EOL;
                $sms .= 'Remarks : ' . $tech_response->remarks . PHP_EOL;
                $sms .= 'Logged User : ' . $tech_response->User->first_name . ' ' . $tech_response->User->last_name;

                $session = createSession('', 'esmsusr_1na2', '3p4lfqe', '');
                sendMessages($session, 'M3FORCE', $sms, array('0704599310', '0704599321', '0704599323'));


                $tech_response_detail = new \App\Model\TechResponseDetails();
                $tech_response_detail->tech_response_id = $tech_response->id;
                $tech_response_detail->update_date_time = date('Y-m-d H:i');
                $tech_response_detail->tech_response_status_id = 1;
                $tech_response_detail->job_scheduled_date_time = '';
                $tech_response_detail->is_chargeable = 0;
                $tech_response_detail->remarks = '';
                $tech_response_detail->user_id = $request->session()->get('users_id');
                $tech_response_detail->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $tech_response->id . ',' . $tech_response->contact_id . ',' . $tech_response->tech_response_fault_id . ',' . $tech_response->record_date_time . ',' . str_replace(',', ' ', $tech_response->remarks) . ',' . str_replace(',', ' ', $tech_response->reported_person) . ',' . str_replace(',', ' ', $tech_response->reported_contact_no) . ',' . str_replace(',', ' ', $tech_response->reported_email) . ',' . $tech_response_detail->id . ',' . $tech_response_detail->update_date_time . ',' . $tech_response_detail->tech_response_status_id . ',' . $tech_response_detail->job_scheduled_date_time . ',' . $tech_response_detail->is_chargeable . ',' . str_replace(',', ' ', $tech_response_detail->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Tech Response created successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response creation failed'
                );
            }
        } else if ($request->type == 1) {
            $tech_response_status = new \App\Model\TechResponseDetails();
            $tech_response_status->tech_response_id = $request->tech_response_id;
            $tech_response_status->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
            $tech_response_status->tech_response_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
            $tech_response_status->job_scheduled_date_time = isset($request->update_status['id']) && $request->update_status['id'] == 9 ? date('Y-m-d', strtotime($request->job_scheduled_date)) . ' ' . $request->job_scheduled_time : '';
            $tech_response_status->is_chargeable = isset($request->update_status['id']) && $request->update_status['id'] == 13 && $request->is_chargeable ? 1 : 0;
            $tech_response_status->invoice_no = isset($request->update_status['id']) && $request->update_status['id'] == 13 ? $request->invoice_no : '';
            $tech_response_status->invoice_value = isset($request->update_status['id']) && $request->update_status['id'] == 13 && is_numeric($request->invoice_value) ? $request->invoice_value : 0;
            $tech_response_status->remarks = $request->remarks;
            $tech_response_status->user_id = $request->session()->get('users_id');

            if ($tech_response_status->save()) {
                $tech_response = \App\Model\TechResponse::find($tech_response_status->tech_response_id);
                if ($tech_response_status->tech_response_status_id == 12) {
                    $other_tech_responses = \App\Model\TechResponse::where('id', '!=', $tech_response->id)
                        ->where('contact_id', $tech_response->contact_id)
                        ->where('is_completed', 0)
                        ->where('is_delete', 0)
                        ->get();
                    foreach ($other_tech_responses as $other_tech_response) {
                        $other_tech_response_status = new \App\Model\TechResponseDetails();
                        $other_tech_response_status->tech_response_id = $other_tech_response->id;
                        $other_tech_response_status->update_date_time = $tech_response_status->update_date_time;
                        $other_tech_response_status->tech_response_status_id = $tech_response_status->tech_response_status_id;
                        $other_tech_response_status->job_scheduled_date_time = $tech_response_status->job_scheduled_date_time;
                        $other_tech_response_status->is_chargeable = $tech_response_status->is_chargeable;
                        $other_tech_response_status->invoice_no = $tech_response_status->invoice_no;
                        $other_tech_response_status->invoice_value = $tech_response_status->invoice_value;
                        $other_tech_response_status->remarks = $tech_response_status->remarks;
                        $other_tech_response_status->user_id = $tech_response_status->user_id;
                        $other_tech_response_status->save();

                        $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                        fwrite($myfile, 'Created,' . $other_tech_response->id . ',,,,,,,,,' . $other_tech_response_status->id . ',' . $other_tech_response_status->update_date_time . ',' . $other_tech_response_status->tech_response_status_id . ',' . $other_tech_response_status->job_scheduled_date_time . ',' . $other_tech_response_status->is_chargeable . ',' . str_replace(',', ' ', $other_tech_response_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                        fclose($myfile);
                    }

                    $data = array(
                        'customer_contact_id' => $tech_response->Contact->contact_id,
                        'customer_name' => $tech_response->Contact->name,
                        'customer_address' => $tech_response->Contact->address,
                        'customer_contact_no' => $tech_response->Contact->contact_no,
                        'tech_response_update_date_time' => $tech_response_status->update_date_time,
                        'tech_response_remarks' => $tech_response_status->remarks
                    );

                    Mail::send('emails.tech_response_complete_notification', $data, function ($message) {
                        $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                        $message->to('faultcall@m3force.com', 'Monitoring Station');
                        $message->to('om@m3force.com', 'Upul Jayasinghe');
                        $message->cc('accountant@m3force.com', 'Account Assistant');
                        $message->subject('M3Force Tech Response Complete Notification');
                    });
                } else if ($tech_response_status->tech_response_status_id == 13) {
                    $tech_response->is_completed = 1;
                    $tech_response->save();

                    foreach ($request->tech_response_invoice_details as $detail) {
                        $tech_response_invoice_detail = \App\Model\TechResponseInvoiceDetails::where('tech_response_id', $tech_response->id)
                            ->where('item_id', $detail['id'])
                            ->first();
                        $tech_response_invoice_detail = $tech_response_invoice_detail ? $tech_response_invoice_detail : new \App\Model\TechResponseInvoiceDetails();
                        $tech_response_invoice_detail->tech_response_id = $tech_response->id;
                        $tech_response_invoice_detail->item_id = $detail['id'];
                        $tech_response_invoice_detail->rate = $detail['rate'];
                        $tech_response_invoice_detail->quantity = $detail['quantity'];
                        $tech_response_invoice_detail->value = $detail['value'];
                        $tech_response_invoice_detail->invoice_value = $detail['invoice_value'];
                        $tech_response_invoice_detail->is_delete = 0;
                        $tech_response_invoice_detail->save();
                    }

                    if ($request->is_chargeable) {
                        $tech_response_quotation_ids = array();
                        $total_value = 0;
                        $tech_response_quotations = \App\Model\TechResponseQuotation::where('tech_response_id', $tech_response->id)
                            ->where('is_confirmed', 1)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($tech_response_quotations as $tech_response_quotation) {
                            array_push($tech_response_quotation_ids, $tech_response_quotation->id);
                            $total_value += $tech_response_quotation->tech_response_quotation_value;
                        }

                        $tech_response_customer = \App\Model\TechResponseCustomer::where('contact_id', $tech_response->contact_id)
                            ->where('is_delete', 0)
                            ->first();
                        if (!$tech_response_customer) {
                            $tech_response_customer = new \App\Model\TechResponseCustomer();
                            $tech_response_customer->contact_id = $tech_response->contact_id;
                            $tech_response_customer->pending_amount = 0;
                        }
                        $tech_response_customer->update_date = date('Y-m-d');
                        $tech_response_customer->save();

                        foreach ($tech_response_quotation_ids as $tech_response_quotation_id) {
                            $tech_response_customer_invoice = new \App\Model\TechResponseCustomerInvoice();
                            $tech_response_customer_invoice->tech_response_customer_id = $tech_response_customer->id;
                            $tech_response_customer_invoice->tech_response_quotation_id = $tech_response_quotation_id;

                            $last_id = 0;
                            $last_tech_response_customer_invoice = \App\Model\TechResponseCustomerInvoice::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
                            $last_id = $last_tech_response_customer_invoice ? $last_tech_response_customer_invoice->id : $last_id;

                            $tech_response_customer_invoice->invoice_date =  date('Y-m-d');
                            $tech_response_customer_invoice->invoice_no = 'INV/TR/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
                            $tech_response_customer_invoice->save();
                        }

                        $pending_amount = $tech_response_customer->pending_amount;
                        $tech_response_customer->pending_amount = $pending_amount + $total_value;
                        $tech_response_customer->save();

                        $total_payments = 0;
                        $tech_response_customer_payments = \App\Model\TechResponseCustomerPayment::where('tech_response_customer_id', $tech_response_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($tech_response_customer_payments as $tech_response_customer_payment) {
                            $total_payments += $tech_response_customer_payment->amount;
                        }
                        $tech_response_customer_invoices = \App\Model\TechResponseCustomerInvoice::where('tech_response_customer_id', $tech_response_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($tech_response_customer_invoices as $tech_response_customer_invoice) {
                            if ($tech_response_customer_invoice->TechResponseQuotation->tech_response_quotation_value <= $total_payments) {
                                $tech_response_customer_invoice->payment_received = $tech_response_customer_invoice->TechResponseQuotation->tech_response_quotation_value;
                                $tech_response_customer_invoice->is_settled = 1;
                                $total_payments -= $tech_response_customer_invoice->TechResponseQuotation->tech_response_quotation_value;
                            } else {
                                $tech_response_customer_invoice->payment_received = $total_payments;
                                $tech_response_customer_invoice->is_settled = 0;
                                $total_payments = 0;
                            }
                            $tech_response_customer_invoice->save();
                        }
                    }
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $tech_response->id . ',,,,,,,,' . $tech_response_status->id . ',' . $tech_response_status->update_date_time . ',' . $tech_response_status->tech_response_status_id . ',' . $tech_response_status->job_scheduled_date_time . ',' . $tech_response_status->is_chargeable . ',' . str_replace(',', ' ', $tech_response_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Tech Response Status created successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response Status creation failed'
                );
            }
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
        if ($request->type == 0) {
            $tech_response = \App\Model\TechResponse::find($id);
            $tech_response->contact_id = $request->contact_id;
            $tech_response->tech_response_fault_id = isset($request->fault_types['id']) ? $request->fault_types['id'] : 0;
            $tech_response->tech_response_no = $request->tech_response_no;
            $tech_response->record_date_time = date('Y-m-d', strtotime($request->record_date)) . ' ' . $request->record_time;
            $tech_response->remarks = $request->remarks;
            $tech_response->reported_person = $request->reported_person;
            $tech_response->reported_contact_no = $request->reported_contact_no;
            $tech_response->reported_email = $request->reported_email;

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $tech_response->id . ',' . $tech_response->contact_id . ',' . $tech_response->tech_response_fault_id . ',' . $tech_response->record_date_time . ',' . str_replace(',', ' ', $tech_response->remarks) . ',' . str_replace(',', ' ', $tech_response->reported_person) . ',' . str_replace(',', ' ', $tech_response->reported_contact_no) . ',' . str_replace(',', ' ', $tech_response->reported_email) . ',,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            if ($tech_response->save()) {
                $result = array(
                    'response' => true,
                    'message' => 'Tech Response updated successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response updation failed'
                );
            }
        } else if ($request->type == 1) {
            $tech_response_status = \App\Model\TechResponseDetails::find($id);
            $tech_response_status->tech_response_id = $request->tech_response_id;
            $tech_response_status->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
            $tech_response_status->tech_response_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
            $tech_response_status->job_scheduled_date_time = isset($request->update_status['id']) && $request->update_status['id'] == 9 ? date('Y-m-d', strtotime($request->job_scheduled_date)) . ' ' . $request->job_scheduled_time : '';
            $tech_response_status->is_chargeable = isset($request->update_status['id']) && $request->update_status['id'] == 13 && $request->is_chargeable ? 1 : 0;
            $tech_response_status->invoice_no = isset($request->update_status['id']) && $request->update_status['id'] == 13 ? $request->invoice_no : '';
            $tech_response_status->invoice_value = isset($request->update_status['id']) && $request->update_status['id'] == 13 && is_numeric($request->invoice_value) ? $request->invoice_value : '';
            $tech_response_status->remarks = $request->remarks;
            $tech_response_status->user_id = $request->session()->get('users_id');

            if ($tech_response_status->save()) {
                $tech_response = \App\Model\TechResponse::find($tech_response_status->tech_response_id);
                if ($tech_response_status->tech_response_status_id == 12) {
                    $other_tech_responses = \App\Model\TechResponse::where('id', '!=', $tech_response->id)
                        ->where('contact_id', $tech_response->contact_id)
                        ->where('is_completed', 0)
                        ->where('is_delete', 0)
                        ->get();
                    foreach ($other_tech_responses as $other_tech_response) {
                        $other_tech_response_status = new \App\Model\TechResponseDetails();
                        $other_tech_response_status->tech_response_id = $other_tech_response->id;
                        $other_tech_response_status->update_date_time = $tech_response_status->update_date_time;
                        $other_tech_response_status->tech_response_status_id = $tech_response_status->tech_response_status_id;
                        $other_tech_response_status->job_scheduled_date_time = $tech_response_status->job_scheduled_date_time;
                        $other_tech_response_status->is_chargeable = $tech_response_status->is_chargeable;
                        $other_tech_response_status->invoice_no = $tech_response_status->invoice_no;
                        $other_tech_response_status->invoice_value = $tech_response_status->invoice_value;
                        $other_tech_response_status->remarks = $tech_response_status->remarks;
                        $other_tech_response_status->user_id = $tech_response_status->user_id;
                        $other_tech_response_status->save();

                        $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                        fwrite($myfile, 'Created,' . $other_tech_response->id . ',,,,,,,,,' . $other_tech_response_status->id . ',' . $other_tech_response_status->update_date_time . ',' . $other_tech_response_status->tech_response_status_id . ',' . $other_tech_response_status->job_scheduled_date_time . ',' . $other_tech_response_status->is_chargeable . ',' . str_replace(',', ' ', $other_tech_response_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                        fclose($myfile);
                    }

                    $data = array(
                        'customer_contact_id' => $tech_response->Contact->contact_id,
                        'customer_name' => $tech_response->Contact->name,
                        'customer_address' => $tech_response->Contact->address,
                        'customer_contact_no' => $tech_response->Contact->contact_no,
                        'tech_response_update_date_time' => $tech_response_status->update_date_time,
                        'tech_response_remarks' => $tech_response_status->remarks
                    );

                    Mail::send('emails.tech_response_complete_notification', $data, function ($message) {
                        $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                        $message->to('faultcall@m3force.com', 'Monitoring Station');
                        $message->to('om@m3force.com', 'Upul Jayasinghe');
                        $message->cc('accountant@m3force.com', 'Account Assistant');
                        $message->subject('M3Force Tech Response Complete Notification');
                    });
                } else if ($tech_response_status->tech_response_status_id == 13) {
                    $tech_response->is_completed = 1;
                    $tech_response->save();

                    foreach ($request->tech_response_invoice_details as $detail) {
                        $tech_response_invoice_detail = \App\Model\TechResponseInvoiceDetails::where('tech_response_id', $tech_response->id)
                            ->where('item_id', $detail['id'])
                            ->first();
                        $tech_response_invoice_detail = $tech_response_invoice_detail ? $tech_response_invoice_detail : new \App\Model\TechResponseInvoiceDetails();
                        $tech_response_invoice_detail->tech_response_id = $tech_response->id;
                        $tech_response_invoice_detail->item_id = $detail['id'];
                        $tech_response_invoice_detail->rate = $detail['rate'];
                        $tech_response_invoice_detail->quantity = $detail['quantity'];
                        $tech_response_invoice_detail->value = $detail['value'];
                        $tech_response_invoice_detail->invoice_value = $detail['invoice_value'];
                        $tech_response_invoice_detail->is_delete = 0;
                        $tech_response_invoice_detail->save();
                    }

                    if ($request->is_chargeable) {
                        $tech_response_quotation_ids = array();
                        $total_value = 0;
                        $tech_response_quotations = \App\Model\TechResponseQuotation::where('tech_response_id', $tech_response->id)
                            ->where('is_confirmed', 1)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($tech_response_quotations as $tech_response_quotation) {
                            array_push($tech_response_quotation_ids, $tech_response_quotation->id);
                            $total_value += $tech_response_quotation->tech_response_quotation_value;
                        }

                        $tech_response_customer = \App\Model\TechResponseCustomer::where('contact_id', $tech_response->contact_id)
                            ->where('is_delete', 0)
                            ->first();
                        if (!$tech_response_customer) {
                            $tech_response_customer = new \App\Model\TechResponseCustomer();
                            $tech_response_customer->contact_id = $tech_response->contact_id;
                            $tech_response_customer->pending_amount = 0;
                        }
                        $tech_response_customer->update_date = date('Y-m-d');
                        $tech_response_customer->save();

                        foreach ($tech_response_quotation_ids as $tech_response_quotation_id) {
                            $tech_response_customer_invoice = new \App\Model\TechResponseCustomerInvoice();
                            $tech_response_customer_invoice->tech_response_customer_id = $tech_response_customer->id;
                            $tech_response_customer_invoice->tech_response_quotation_id = $tech_response_quotation_id;

                            $last_id = 0;
                            $last_tech_response_customer_invoice = \App\Model\TechResponseCustomerInvoice::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
                            $last_id = $last_tech_response_customer_invoice ? $last_tech_response_customer_invoice->id : $last_id;

                            $tech_response_customer_invoice->invoice_date =  date('Y-m-d');
                            $tech_response_customer_invoice->invoice_no = 'INV/TR/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
                            $tech_response_customer_invoice->save();
                        }

                        $pending_amount = $tech_response_customer->pending_amount;
                        $tech_response_customer->pending_amount = $pending_amount + $total_value;
                        $tech_response_customer->save();

                        $total_payments = 0;
                        $tech_response_customer_payments = \App\Model\TechResponseCustomerPayment::where('tech_response_customer_id', $tech_response_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($tech_response_customer_payments as $tech_response_customer_payment) {
                            $total_payments += $tech_response_customer_payment->amount;
                        }
                        $tech_response_customer_invoices = \App\Model\TechResponseCustomerInvoice::where('tech_response_customer_id', $tech_response_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($tech_response_customer_invoices as $tech_response_customer_invoice) {
                            if ($tech_response_customer_invoice->TechResponseQuotation->tech_response_quotation_value <= $total_payments) {
                                $tech_response_customer_invoice->payment_received = $tech_response_customer_invoice->TechResponseQuotation->tech_response_quotation_value;
                                $tech_response_customer_invoice->is_settled = 1;
                                $total_payments -= $tech_response_customer_invoice->TechResponseQuotation->tech_response_quotation_value;
                            } else {
                                $tech_response_customer_invoice->payment_received = $total_payments;
                                $tech_response_customer_invoice->is_settled = 0;
                                $total_payments = 0;
                            }
                            $tech_response_customer_invoice->save();
                        }
                    }
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $tech_response->id . ',,,,,,,,' . $tech_response_status->id . ',' . $tech_response_status->update_date_time . ',' . $tech_response_status->tech_response_status_id . ',' . $tech_response_status->job_scheduled_date_time . ',' . $tech_response_status->is_chargeable . ',' . str_replace(',', ' ', $tech_response_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Tech Response Status updated successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response Status updation failed'
                );
            }
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
        if ($request->type == 0) {
            $tech_response = \App\Model\TechResponse::find($id);
            $tech_response->is_delete = 1;

            if ($tech_response->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $tech_response->id . ',,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $tech_response_details = \App\Model\TechResponseDetails::where('tech_response_id', $tech_response->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
                $result = array(
                    'response' => true,
                    'message' => 'Tech Response deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response deletion failed'
                );
            }
        } else if ($request->type == 1) {
            $tech_response_detail = \App\Model\TechResponseDetails::find($id);
            $tech_response_detail->is_delete = 1;

            if ($tech_response_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $tech_response->id . ',,,,,,,,' . $tech_response_detail->id . ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Tech Response Status deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response Status deletion failed'
                );
            }
        }

        echo json_encode($result);
    }
}
