<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class RepairController extends Controller
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

        return view('repair.repair_list', $data);
    }

    public function get_data()
    {
        $repair_types = \App\Model\ItemIssueType::select('id', 'name')->orderBy('name')->get();
        $repair_status = \App\Model\RepairStatus::select('id', 'name')->get();

        $data = array(
            'repair_types' => $repair_types,
            'repair_status' => $repair_status
        );

        return response($data);
    }

    public function get_job_nos(Request $request)
    {
        $jobs = \App\Model\Job::select('id', 'inquiry_id', 'job_no')
            ->with(array('Inquiry' => function ($query) {
                $query->select('id', 'contact_id')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name', 'address', 'contact_no');
                    }));
            }))
            ->where('job_no', 'like', '%' . $request->job_no . '%')
            ->where('is_delete', 0)
            ->orderBy('job_no')
            ->get();
        return response($jobs);
    }

    public function find_job_no(Request $request)
    {
        $job = \App\Model\Job::select('id', 'inquiry_id', 'job_no')
            ->with(array('Inquiry' => function ($query) {
                $query->select('id', 'contact_id')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name', 'address', 'contact_no');
                    }));
            }))
            ->where('job_no', $request->job_no)
            ->where('is_delete', 0)
            ->first();

        return response($job);
    }

    public function get_tech_response_nos(Request $request)
    {
        $tech_responses = \App\Model\TechResponse::select('id', 'contact_id', 'tech_response_no')
            ->with(array('Contact' => function ($query) {
                $query->select('id', 'name', 'address', 'contact_no');
            }))
            ->where('tech_response_no', 'like', '%' . $request->tech_response_no . '%')
            ->where('is_delete', 0)
            ->orderBy('tech_response_no')
            ->get();
        return response($tech_responses);
    }

    public function find_tech_response_no(Request $request)
    {
        $tech_response = \App\Model\TechResponse::select('id', 'contact_id', 'tech_response_no')
            ->with(array('Contact' => function ($query) {
                $query->select('id', 'name', 'address', 'contact_no');
            }))
            ->where('tech_response_no', $request->tech_response_no)
            ->where('is_delete', 0)
            ->first();
        return response($tech_response);
    }

    public function get_item_codes(Request $request)
    {
        $items = \App\Model\Item::select('id', 'code', 'name', 'model_no', 'brand')
            ->where('code', 'like', '%' . $request->code . '%')
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        return response($items);
    }

    public function find_item_code(Request $request)
    {
        $item = \App\Model\Item::select('id', 'code', 'name', 'model_no', 'brand')
            ->where('code', $request->code)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();

        return response($item);
    }

    public function get_item_names(Request $request)
    {
        $items = \App\Model\Item::select('id', 'code', 'name', 'model_no', 'brand')
            ->where('name', 'like', '%' . $request->name . '%')
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        return response($items);
    }

    public function find_item_name(Request $request)
    {
        $item = \App\Model\Item::select('id', 'code', 'name', 'model_no', 'brand')
            ->where('name', $request->name)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();

        return response($item);
    }

    public function repair_list(Request $request)
    {
        $data = array();
        $repairs = \App\Model\Repair::whereBetween('repair_date_time', array($request->from . ' 00:01', $request->to . ' 23:59'))
            ->where('is_delete', 0)
            ->get();
        foreach ($repairs as $repair) {
            $repair_detail = \App\Model\RepairDetails::selectRaw('MAX(repair_status_id) AS repair_status_id')
                ->where('repair_id', $repair->id)
                ->where('is_delete', 0)
                ->first();
            if ($repair_detail && ($request->repair_status_id == -1 || $request->repair_status_id == $repair_detail->repair_status_id)) {
                $repair_detail = \App\Model\RepairDetails::where('repair_status_id', $repair_detail->repair_status_id)
                    ->where('repair_id', $repair->id)
                    ->where('is_delete', 0)
                    ->first();
                $document_no = '';
                $customer_name = '';
                $customer_address = '';
                $customer_contact_no = '';
                if ($repair->repair_type_id == 1 && $repair->Job && $repair->Job->Inquiry) {
                    $document_no = $repair->Job->job_no;
                    $customer_name = $repair->Job->Inquiry->Contact->name;
                    $customer_address = $repair->Job->Inquiry->Contact->address;
                    $customer_contact_no = $repair->Job->Inquiry->Contact->contact_no;
                } else if ($repair->repair_type_id == 2 && $repair->TechResponse) {
                    $document_no = $repair->TechResponse->tech_response_no;
                    $customer_name = $repair->TechResponse->Contact->name;
                    $customer_address = $repair->TechResponse->Contact->address;
                    $customer_contact_no = $repair->TechResponse->Contact->contact_no;
                }
                $row = array(
                    'id' => $repair_detail->repair_id,
                    'permission' => !in_array(1, session()->get('user_group')) ? 1 : 0,
                    'is_completed' => $repair->is_completed,
                    'repair_type' => $repair->RepairType->name,
                    'repair_no' => $repair->repair_no,
                    'repair_date_time' => $repair->repair_date_time,
                    'document_no' => $document_no,
                    'customer_name' => $customer_name,
                    'customer_address' => $customer_address,
                    'customer_contact_no' => $customer_contact_no,
                    'item_code' => $repair->Item->code,
                    'item_name' => $repair->Item->name,
                    'item_model_no' => $repair->model_no,
                    'item_brand' => $repair->brand,
                    'item_serial_no' => $repair->serial_no,
                    'remarks' => $repair->remarks,
                    'handed_over_taken_over' => $repair_detail->handed_over_taken_over,
                    'update_date_time' => $repair_detail->update_date_time,
                    'update_status_id' => $repair_detail->RepairStatus ? $repair_detail->RepairStatus->id : 0,
                    'update_status' => $repair_detail->RepairStatus ? $repair_detail->RepairStatus->name : '',
                    'update_remarks' => $repair_detail->remarks
                );
                array_push($data, $row);
            }
        }

        return response($data);
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

        $data['repair_id'] = $request->id;

        return view('repair.add_new', $data);
    }

    public function update_status(Request $request)
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

        $data['repair_id'] = $request->id;

        return view('repair.update_status', $data);
    }

    public function validate_repair_reference(Request $request)
    {
        if ($request->data_repair_type_id != $request->repair_type || $request->data_document_id != $request->document_no || $request->data_item_id != $request->item || $request->data_serial_no != $request->serial_no) {
            $repair = \App\Model\Repair::where('repair_type_id', $request->repair_type)
                ->where('document_id', $request->document_no)
                ->where('item_id', $request->item)
                ->where('serial_no', $request->serial_no)
                ->where('is_completed', 0)
                ->where('is_delete', 0)
                ->first();
            echo $repair ? 'false' : 'true';
        } else {
            echo 'true';
        }
    }

    public function validate_repair_status(Request $request)
    {
        if ($request->value != $request->update_status) {
            $repair_detail = \App\Model\RepairDetails::where('repair_id', $request->repair_id)
                ->where('repair_status_id', $request->update_status)
                ->where('is_delete', 0)
                ->first();
            echo $repair_detail ? 'false' : 'true';
        } else {
            echo 'true';
        }
    }

    public function find_repair(Request $request)
    {
        $repair = \App\Model\Repair::select('id', 'repair_type_id', 'document_id', 'repair_no', 'repair_date_time', 'received_from', 'item_id', 'model_no', 'brand', 'serial_no', 'remarks', 'user_id', 'is_completed')
            ->with(array('RepairType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(array('Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name', 'address', 'contact_no');
                            }));
                    }));
            }))
            ->with(array('TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name', 'address', 'contact_no');
                    }));
            }))
            ->with(array('Item' => function ($query) {
                $query->select('id', 'code', 'name', 'model_no', 'brand');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->find($request->id);

        return response($repair);
    }

    public function find_repair_status(Request $request)
    {
        $repair_detail = \App\Model\RepairDetails::select('id', 'repair_id', 'update_date_time', 'repair_status_id', 'handed_over_taken_over', 'remarks', 'user_id')
            ->with(array('Repair' => function ($query) {
                $query->select('id', 'repair_type_id', 'document_id', 'repair_no', 'repair_date_time', 'received_from', 'item_id', 'model_no', 'brand', 'serial_no', 'remarks', 'user_id', 'is_completed')
                    ->with(array('RepairType' => function ($query) {
                        $query->select('id', 'name');
                    }))
                    ->with(array('Job' => function ($query) {
                        $query->select('id', 'inquiry_id', 'job_no')
                            ->with(array('Inquiry' => function ($query) {
                                $query->select('id', 'contact_id')
                                    ->with(array('Contact' => function ($query) {
                                        $query->select('id', 'name', 'address', 'contact_no');
                                    }));
                            }));
                    }))
                    ->with(array('TechResponse' => function ($query) {
                        $query->select('id', 'contact_id', 'tech_response_no')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name', 'address', 'contact_no');
                            }));
                    }))
                    ->with(array('Item' => function ($query) {
                        $query->select('id', 'code', 'name', 'model_no', 'brand');
                    }))
                    ->with(array('User' => function ($query) {
                        $query->select('id', 'first_name');
                    }));
            }))
            ->with(array('RepairStatus' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->find($request->id);

        return response($repair_detail);
    }

    public function repair_status_list(Request $request)
    {
        $repair_status = \App\Model\RepairDetails::select('id', 'repair_id', 'update_date_time', 'repair_status_id', 'handed_over_taken_over', 'remarks', 'user_id')
            ->with(array('Repair' => function ($query) {
                $query->select('id', 'repair_type_id', 'document_id', 'repair_no', 'repair_date_time', 'received_from', 'item_id', 'model_no', 'brand', 'serial_no', 'remarks', 'user_id', 'is_completed')
                    ->with(array('RepairType' => function ($query) {
                        $query->select('id', 'name');
                    }))
                    ->with(array('Job' => function ($query) {
                        $query->select('id', 'inquiry_id', 'job_no')
                            ->with(array('Inquiry' => function ($query) {
                                $query->select('id', 'contact_id')
                                    ->with(array('Contact' => function ($query) {
                                        $query->select('id', 'name', 'address', 'contact_no');
                                    }));
                            }));
                    }))
                    ->with(array('TechResponse' => function ($query) {
                        $query->select('id', 'contact_id', 'tech_response_no')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name', 'address', 'contact_no');
                            }));
                    }))
                    ->with(array('Item' => function ($query) {
                        $query->select('id', 'code', 'name', 'model_no', 'brand');
                    }))
                    ->with(array('User' => function ($query) {
                        $query->select('id', 'first_name');
                    }));
            }))
            ->with(array('RepairStatus' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->where('repair_id', $request->repair_id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'repair_status' => $repair_status,
            'permission' => !in_array(1, session()->get('user_group'))
        );

        return response($data);
    }

    public function print_repair(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $repair = \App\Model\Repair::find($request->id);
        $data['repair'] = $repair;
        $title = $repair ? 'Repair Details ' . $repair->repair_no : 'Repair Details';

        $html = view('repair.repair_pdf', $data);

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
        if ($request->type == 0) {
            $exist = false;
            $repair = \App\Model\Repair::find($request->id);
            if (!$repair) {
                $exist = true;
                $repair = new \App\Model\Repair();
                $last_id = 0;
                $last_repair = \App\Model\Repair::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
                $last_id = $last_repair ? $last_repair->id : $last_id;
                $repair_type = '';
                $repair_type = $request->repair_type['id'] == 1 ? 'JB' : $repair_type;
                $repair_type = $request->repair_type['id'] == 2 ? 'FC' : $repair_type;
                $repair_type = $request->repair_type['id'] == 3 ? 'OT' : $repair_type;
                $repair_type = $request->repair_type['id'] == 4 ? 'RE' : $repair_type;
                $repair->repair_no = 'REP/' . $repair_type . '/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
            }
            $repair->repair_type_id = isset($request->repair_type['id']) ? $request->repair_type['id'] : 0;
            $repair->document_id = isset($request->document_no['id']) ? $request->document_no['id'] : 0;
            $repair->repair_date_time = date('Y-m-d', strtotime($request->repair_date)) . ' ' . $request->repair_time;
            $repair->received_from = $request->received_from;
            $repair->item_id = $request->item['id'];
            $repair->model_no = $request->model_no;
            $repair->brand = $request->brand;
            $repair->serial_no = $request->serial_no;
            $repair->remarks = $request->remarks;
            $repair->user_id = $request->session()->get('users_id');
            $repair->is_delete = 0;
            if ($repair->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $repair->id . ',' . $repair->repair_type_id . ',' . $repair->document_id . ',' . $repair->repair_no . ',' . $repair->repair_date_time . ',' . str_replace(',', ' ', $repair->received_from) . ',' . $repair->item_id . ',' . str_replace(',', ' ', $repair->model_no) . ',' . str_replace(',', ' ', $repair->brand) . ',' . str_replace(',', ' ', $repair->serial_no) . ',' . str_replace(',', ' ', $repair->remarks) . ',' . session()->get('users_id') . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
                if ($exist) {
                    $repair_detail = new \App\Model\RepairDetails();
                    $repair_detail->repair_id = $repair->id;
                    $repair_detail->update_date_time = $repair->repair_date_time;
                    $repair_detail->repair_status_id = 1;
                    $repair_detail->handed_over_taken_over = $repair->received_from;
                    $repair_detail->remarks = 'Item Handed Over To Store';
                    $repair_detail->user_id = $repair->user_id;
                    $repair_detail->save();
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_details_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $repair_detail->id . ',' . $repair_detail->repair_id . ',' . $repair_detail->update_date_time . ',' . $repair_detail->repair_status_id . ',' . str_replace(',', ' ', $repair_detail->handed_over_taken_over) . ',' . str_replace(',', ' ', $repair_detail->remarks) . ',' . session()->get('users_id') . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);
                }
                $result = array(
                    'response' => true,
                    'message' => 'Repair created successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Repair creation failed'
                );
            }
        } else if ($request->type == 1) {
            $repair_detail = \App\Model\RepairDetails::find($request->id);
            $repair_detail = $repair_detail ? $repair_detail : new \App\Model\RepairDetails();
            $repair_detail->repair_id = $request->repair_id;
            $repair_detail->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
            $repair_detail->repair_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
            $repair_detail->handed_over_taken_over = $request->handed_over_taken_over;
            $repair_detail->remarks = $request->remarks;
            $repair_detail->user_id = $request->session()->get('users_id');
            $repair_detail->is_delete = 0;
            if ($repair_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_details_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $repair_detail->id . ',' . $repair_detail->repair_id . ',' . $repair_detail->update_date_time . ',' . $repair_detail->repair_status_id . ',' . str_replace(',', ' ', $repair_detail->handed_over_taken_over) . ',' . str_replace(',', ' ', $repair_detail->remarks) . ',' . session()->get('users_id') . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                if (in_array($repair_detail->repair_status_id, array(4, 5))) {
                    $repair = \App\Model\Repair::find($repair_detail->repair_id);
                    $repair->is_completed = 1;
                    $repair->save();

                    if ($repair->repair_type_id == 2 && $repair_detail->repair_status_id == 4) {
                        $tech_response = new \App\Model\TechResponse();
                        $tech_response->contact_id = $repair->TechResponse->contact_id;
                        $tech_response->tech_response_fault_id = 15;
                        $tech_response->record_date_time = date('Y-m-d H:i');
                        $tech_response->remarks = 'Item Can Not Be Repaired. Repair Reference : ' . $repair->repair_no;
                        $tech_response->reported_person = 'M3Force Repair Station';
                        $tech_response->reported_contact_no = '';
                        $tech_response->reported_email = '';
                        $tech_response->user_id = 1;

                        if ($tech_response->save()) {
                            $tech_response->tech_response_no = 'TR/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $tech_response->id);
                            $tech_response->save();

                            $sms = '--- M3Force Tech Response ---' . PHP_EOL;
                            $sms .= 'Tech Response No : ' . $tech_response->tech_response_no . PHP_EOL;
                            $sms .= 'Customer ID : ' . $tech_response->Contact->contact_id . PHP_EOL;
                            $sms .= 'Customer Name : ' . $tech_response->Contact->name . PHP_EOL;
                            $sms .= 'Customer Address : ' . $tech_response->Contact->address . PHP_EOL;
                            $sms .= 'Customer Contact No : ' . $tech_response->Contact->contact_no . PHP_EOL;
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
                            $tech_response_detail->user_id = 1;
                            $tech_response_detail->save();
                        }
                    }
                }

                $result = array(
                    'response' => true,
                    'message' => 'Repair Status created successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Repair Status creation failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Creation failed'
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
        if ($request->type == 0) {
            $exist = false;
            $repair = \App\Model\Repair::find($request->id);
            if (!$repair) {
                $exist = true;
                $repair = new \App\Model\Repair();
                $last_id = 0;
                $last_repair = \App\Model\Repair::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
                $last_id = $last_repair ? $last_repair->id : $last_id;
                $repair_type = '';
                $repair_type = $request->repair_type['id'] == 1 ? 'JB' : $repair_type;
                $repair_type = $request->repair_type['id'] == 2 ? 'FC' : $repair_type;
                $repair_type = $request->repair_type['id'] == 3 ? 'OT' : $repair_type;
                $repair_type = $request->repair_type['id'] == 4 ? 'RE' : $repair_type;
                $repair->repair_no = 'REP/' . $repair_type . '/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
            }
            $repair->repair_type_id = isset($request->repair_type['id']) ? $request->repair_type['id'] : 0;
            $repair->document_id = isset($request->document_no['id']) ? $request->document_no['id'] : 0;
            $repair->repair_date_time = date('Y-m-d', strtotime($request->repair_date)) . ' ' . $request->repair_time;
            $repair->received_from = $request->received_from;
            $repair->item_id = $request->item['id'];
            $repair->model_no = $request->model_no;
            $repair->brand = $request->brand;
            $repair->serial_no = $request->serial_no;
            $repair->remarks = $request->remarks;
            $repair->user_id = $request->session()->get('users_id');
            $repair->is_delete = 0;
            if ($repair->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $repair->id . ',' . $repair->repair_type_id . ',' . $repair->document_id . ',' . $repair->repair_no . ',' . $repair->repair_date_time . ',' . str_replace(',', ' ', $repair->received_from) . ',' . $repair->item_id . ',' . str_replace(',', ' ', $repair->model_no) . ',' . str_replace(',', ' ', $repair->brand) . ',' . str_replace(',', ' ', $repair->serial_no) . ',' . str_replace(',', ' ', $repair->remarks) . ',' . session()->get('users_id') . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
                if ($exist) {
                    $repair_detail = new \App\Model\RepairDetails();
                    $repair_detail->repair_id = $repair->id;
                    $repair_detail->update_date_time = $repair->repair_date_time;
                    $repair_detail->repair_status_id = 1;
                    $repair_detail->handed_over_taken_over = $repair->received_from;
                    $repair_detail->remarks = 'Item Handed Over To Store';
                    $repair_detail->user_id = $repair->user_id;
                    $repair_detail->save();
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_details_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Updated,' . $repair_detail->id . ',' . $repair_detail->repair_id . ',' . $repair_detail->update_date_time . ',' . $repair_detail->repair_status_id . ',' . str_replace(',', ' ', $repair_detail->handed_over_taken_over) . ',' . str_replace(',', ' ', $repair_detail->remarks) . ',' . session()->get('users_id') . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);
                }
                $result = array(
                    'response' => true,
                    'message' => 'Repair updated successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Repair updation failed'
                );
            }
        } else if ($request->type == 1) {
            $repair_detail = \App\Model\RepairDetails::find($request->id);
            $repair_detail = $repair_detail ? $repair_detail : new \App\Model\RepairDetails();
            $repair_detail->repair_id = $request->repair_id;
            $repair_detail->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
            $repair_detail->repair_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
            $repair_detail->handed_over_taken_over = $request->handed_over_taken_over;
            $repair_detail->remarks = $request->remarks;
            $repair_detail->user_id = $request->session()->get('users_id');
            $repair_detail->is_delete = 0;
            if ($repair_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_details_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $repair_detail->id . ',' . $repair_detail->repair_id . ',' . $repair_detail->update_date_time . ',' . $repair_detail->repair_status_id . ',' . str_replace(',', ' ', $repair_detail->handed_over_taken_over) . ',' . str_replace(',', ' ', $repair_detail->remarks) . ',' . session()->get('users_id') . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                if (in_array($repair_detail->repair_status_id, array(4, 5))) {
                    $repair = \App\Model\Repair::find($repair_detail->repair_id);
                    $repair->is_completed = 1;
                    $repair->save();

                    if ($repair->repair_type_id == 2 && $repair_detail->repair_status_id == 4) {
                        $tech_response = new \App\Model\TechResponse();
                        $tech_response->contact_id = $repair->TechResponse->contact_id;
                        $tech_response->tech_response_fault_id = 15;
                        $tech_response->record_date_time = date('Y-m-d H:i');
                        $tech_response->remarks = 'Item Can Not Be Repaired. Repair Reference ' . $repair->repair_no;
                        $tech_response->reported_person = 'M3Force Repair Station';
                        $tech_response->reported_contact_no = '';
                        $tech_response->reported_email = '';
                        $tech_response->user_id = 1;

                        if ($tech_response->save()) {
                            $tech_response->tech_response_no = 'TR/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $tech_response->id);
                            $tech_response->save();

                            $sms = '--- M3Force Tech Response ---' . PHP_EOL;
                            $sms .= 'Tech Response No : ' . $tech_response->tech_response_no . PHP_EOL;
                            $sms .= 'Customer ID : ' . $tech_response->Contact->contact_id . PHP_EOL;
                            $sms .= 'Customer Name : ' . $tech_response->Contact->name . PHP_EOL;
                            $sms .= 'Customer Address : ' . $tech_response->Contact->address . PHP_EOL;
                            $sms .= 'Customer Contact No : ' . $tech_response->Contact->contact_no . PHP_EOL;
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
                            $tech_response_detail->user_id = 1;
                            $tech_response_detail->save();
                        }
                    }
                }

                $result = array(
                    'response' => true,
                    'message' => 'Repair Status updated successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Repair Status updation failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Updation failed'
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
        if ($request->type == 0) {
            $repair = \App\Model\Repair::find($id);
            $repair->is_delete = 1;
            if ($repair->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $repair->id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $repair_details = \App\Model\RepairDetails::where('repair_id', $repair->id)->where('is_delete', 0)->get();
                foreach ($repair_details as $repair_detail) {
                    $repair_detail->is_delete = 1;
                    $repair_detail->save();

                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_details_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Deleted,' . $repair_detail->id . ',' . $repair_detail->repair_id . ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);
                }
                $result = array(
                    'response' => true,
                    'message' => 'Repair deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Repair deletion failed'
                );
            }
        } else if ($request->type == 1) {
            $repair_detail = \App\Model\RepairDetails::find($id);
            $repair_detail->is_delete = 1;
            if ($repair_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/repair_details_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $repair_detail->id . ',' . $repair_detail->repair_id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Repair status deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Repair status deletion failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Deletion failed'
            );
        }

        echo json_encode($result);
    }
}
