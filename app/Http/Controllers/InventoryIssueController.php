<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class InventoryIssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_access');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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

        return view('inventory.inventory_issue', $data);
    }

    public function inventory_issue_list(Request $request)
    {
        $inventory_issues = \App\Model\InventoryIssue::select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted')
                ->whereBetween('inventory_issue_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
                ->where('is_delete', 0)
                ->get();

        return response($inventory_issues);
    }

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

        $data['inventory_issue_id'] = $request->id;

        return view('inventory.inventory_issue_detail', $data);
    }

    public function find_inventory_issue(Request $request)
    {
        $inventory_issue = \App\Model\InventoryIssue::select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted')
                ->find($request->id);

        return response($inventory_issue);
    }

    public function validate_inventory_code(Request $request)
    {
        if ($request->inventory_code_value != $request->inventory_code) {
            $inventory_register = \App\Model\InventoryRegister::where('code', $request->inventory_code)
                    ->where('is_issued', 0)
                    ->where('is_delete', 0)
                    ->first();
            if ($inventory_register) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_inventory_issue_detail(Request $request)
    {
        $inventory_issue_detail = \App\Model\InventoryIssueDetails::select('id', 'inventory_issue_id', 'inventory_register_id', 'remarks', 'is_returned')
                ->with(['InventoryIssue' => function ($query) {
                    $query->select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted');
                }])
                ->with(['InventoryRegister' => function ($query) {
                    $query->select('id', 'code', 'inventory_type_id', 'inventory_location_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks', 'is_issued')
                            ->with(['InventoryType' => function ($query) {
                                $query->select('id', 'name');
                            }])
                            ->with(['InventoryLocation' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                }])
                ->find($request->id);

        return response($inventory_issue_detail);
    }

    public function inventory_issue_detail_list(Request $request)
    {
        $inventory_issue_details = \App\Model\InventoryIssueDetails::select('id', 'inventory_issue_id', 'inventory_register_id', 'remarks', 'is_returned')
                ->with(['InventoryIssue' => function ($query) {
                    $query->select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted');
                }])
                ->with(['InventoryRegister' => function ($query) {
                    $query->select('id', 'code', 'inventory_type_id', 'inventory_location_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks', 'is_issued')
                            ->with(['InventoryType' => function ($query) {
                                $query->select('id', 'name');
                            }])
                            ->with(['InventoryLocation' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                }])
                ->where('inventory_issue_id', $request->id)
                ->where('is_delete', 0)
                ->get();

        return response($inventory_issue_details);
    }

    public function post_inventory_issue(Request $request)
    {
        $inventory_exist = true;
        $error = '';

        $inventory_issue = \App\Model\InventoryIssue::find($request->id);
        $inventory_issue_details = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                ->where('is_delete', 0)
                ->get();
        foreach ($inventory_issue_details as $inventory_issue_detail) {
            if ($inventory_issue_detail->InventoryRegister->is_issued != 0) {
                $inventory_exist = false;
                $error .= $error == '' ? $inventory_issue_detail->InventoryRegister->code.' : '.$inventory_issue_detail->InventoryRegister->name : ', '.$inventory_issue_detail->InventoryRegister->code.' : '.$inventory_issue_detail->InventoryRegister->name;
            }
        }

        if ($inventory_exist) {
            $inventory_issue->is_posted = 1;
            if ($inventory_issue->save()) {
                $inventory_issue_details = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                        ->where('is_delete', 0)
                        ->get();
                foreach ($inventory_issue_details as $inventory_issue_detail) {
                    $inventory_register = \App\Model\InventoryRegister::find($inventory_issue_detail->inventory_register_id);
                    $inventory_register->remarks = $inventory_issue_detail->remarks;
                    $inventory_register->is_issued = 1;
                    $inventory_register->save();
                }

                $result = [
                    'response' => true,
                    'message' => 'Inventory Issue posted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Inventory Issue post failed',
                ];
            }
        } else {
            $result = [
                'response' => false,
                'message' => $error.' does not available in the inventory.',
            ];
        }

        echo json_encode($result);
    }

    public function print_inventory_issue(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $inventory_issue = \App\Model\InventoryIssue::find($request->id);
        $data['inventory_issue'] = $inventory_issue;
        $title = $inventory_issue ? 'Inventory Issue Details '.$inventory_issue->inventory_issue_no : 'Inventory Issue Details';

        $html = view('inventory.inventory_issue_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="'.$title.'.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Portrait',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8,
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
        $inventory_issue = \App\Model\InventoryIssue::find($request->inventory_issue_id);
        if (! $inventory_issue) {
            $inventory_issue = new \App\Model\InventoryIssue();
            $last_id = 0;
            $last_inventory_issue = \App\Model\InventoryIssue::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_inventory_issue ? $last_inventory_issue->id : $last_id;
            $inventory_issue->inventory_issue_no = 'INS/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id + 1);
        }
        $inventory_issue->inventory_issue_date_time = date('Y-m-d', strtotime($request->inventory_issue_date)).' '.$request->inventory_issue_time;
        $inventory_issue->issued_to = $request->issued_to;
        $inventory_issue->remarks = $request->remarks;

        if ($inventory_issue->save()) {
            if (isset($request->inventory_code['id'])) {
                $old_inventory_issue_detail = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                        ->where('inventory_register_id', $request->inventory_code['id'])
                        ->first();

                $inventory_issue_detail = $old_inventory_issue_detail ? $old_inventory_issue_detail : new \App\Model\InventoryIssueDetails();
                $inventory_issue_detail->inventory_issue_id = $inventory_issue->id;
                $inventory_issue_detail->inventory_register_id = $request->inventory_code['id'];
                $inventory_issue_detail->remarks = $request->inventory_remarks;
                $inventory_issue_detail->is_delete = 0;
                $inventory_issue_detail->save();
            }

            $inventory_issue_details = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                    ->where('is_delete', 0)
                    ->get();
            $inventory_issue_value = 0;
            foreach ($inventory_issue_details as $inventory_issue_detail) {
                $inventory_issue_value += $inventory_issue_detail->InventoryRegister->credit_limit;
            }
            $inventory_issue->inventory_issue_value = $inventory_issue_value;
            $inventory_issue->save();

            $result = [
                'response' => true,
                'message' => 'Inventory Issue Detail created successfully',
                'data' => $inventory_issue->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Inventory Issue Detail creation failed',
            ];
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
        $inventory_issue = \App\Model\InventoryIssue::find($request->inventory_issue_id);
        $inventory_issue->inventory_issue_no = $request->inventory_issue_no;
        $inventory_issue->inventory_issue_date_time = date('Y-m-d', strtotime($request->inventory_issue_date)).' '.$request->inventory_issue_time;
        $inventory_issue->issued_to = $request->issued_to;
        $inventory_issue->remarks = $request->remarks;

        if ($inventory_issue->save()) {
            if (isset($request->inventory_code['id'])) {
                $inventory_issue_detail = \App\Model\InventoryIssueDetails::find($id);
                $inventory_issue_detail->is_delete = 1;
                $inventory_issue_detail->save();

                $old_inventory_issue_detail = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                        ->where('inventory_register_id', $request->inventory_code['id'])
                        ->first();

                $inventory_issue_detail = $old_inventory_issue_detail ? $old_inventory_issue_detail : new \App\Model\InventoryIssueDetails();
                $inventory_issue_detail->inventory_issue_id = $inventory_issue->id;
                $inventory_issue_detail->inventory_register_id = $request->inventory_code['id'];
                $inventory_issue_detail->remarks = $request->inventory_remarks;
                $inventory_issue_detail->is_delete = 0;
                $inventory_issue_detail->save();
            }

            $inventory_issue_details = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                    ->where('is_delete', 0)
                    ->get();
            $inventory_issue_value = 0;
            foreach ($inventory_issue_details as $inventory_issue_detail) {
                $inventory_issue_value += $inventory_issue_detail->InventoryRegister->credit_limit;
            }
            $inventory_issue->inventory_issue_value = $inventory_issue_value;
            $inventory_issue->save();

            $result = [
                'response' => true,
                'message' => 'Inventory Issue Detail updated successfully',
                'data' => $inventory_issue->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Inventory Issue Detail updation failed',
            ];
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
            $inventory_issue = \App\Model\InventoryIssue::find($id);
            $inventory_issue->is_delete = 1;

            if ($inventory_issue->save()) {
                $inventory_issue_details = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)->where('is_delete', 0)->get();
                foreach ($inventory_issue_details as $inventory_issue_detail) {
                    $inventory_issue_detail->is_delete = 1;
                    $inventory_issue_detail->save();
                }

                $result = [
                    'response' => true,
                    'message' => 'Inventory Issue deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Inventory Issue deletion failed',
                ];
            }
        } elseif ($request->type == 1) {
            $inventory_issue_detail = \App\Model\InventoryIssueDetails::find($id);
            $inventory_issue_detail->is_delete = 1;

            if ($inventory_issue_detail->save()) {
                $inventory_issue = \App\Model\InventoryIssue::find($inventory_issue_detail->inventory_issue_id);
                $inventory_issue_details = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_issue->id)
                        ->where('is_delete', 0)
                        ->get();
                $inventory_issue_value = 0;
                foreach ($inventory_issue_details as $inventory_issue_detail) {
                    $inventory_issue_value += $inventory_issue_detail->InventoryRegister->credit_limit;
                }
                $inventory_issue->inventory_issue_value = $inventory_issue_value;
                $inventory_issue->save();

                $result = [
                    'response' => true,
                    'message' => 'Inventory Issue Detail deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Inventory Issue Detail deletion failed',
                ];
            }
        } else {
            $result = [
                'response' => false,
                'message' => 'Deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
