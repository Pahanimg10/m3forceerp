<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CustomerComplainController extends Controller
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
    public function new_customer_complain(Request $request)
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

        return view('customer_complain.new_customer_complain', $data);
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

        return view('customer_complain.add_new_contact', $data);
    }

    public function validate_customer_name(Request $request)
    {
        $contact = \App\Model\Contact::where('name', $request->name)
                ->where('is_delete', 0)
                ->first();
        if (! $contact) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function add_new_customer_complain(Request $request)
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
        $data['customer_complain_id'] = $request->customer_complain_id;

        return view('customer_complain.add_new_customer_complain', $data);
    }

    public function validate_complain_type(Request $request)
    {
        if ($request->value != $request->complain_type_id) {
            $customer_complain = \App\Model\CustomerComplain::where('contact_id', $request->contact_id)
                    ->where('complain_type_id', $request->complain_type_id)
                    ->where('is_completed', 0)
                    ->where('is_delete', 0)
                    ->first();
            if (! $customer_complain) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function get_complain_data()
    {
        $complain_types = \App\Model\ComplainType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $person_responsibles = \App\Model\PersonResponsible::select('id', 'name')->where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        $data = [
            'complain_types' => $complain_types,
            'person_responsibles' => $person_responsibles,
        ];

        return response($data);
    }

    public function find_customer_complain(Request $request)
    {
        $customer_complain = \App\Model\CustomerComplain::select('id', 'contact_id', 'complain_type_id', 'person_responsible_id', 'complain_no', 'record_date_time', 'remarks', 'reported_person', 'reported_contact_no', 'reported_email', 'is_completed', 'user_id')
                ->with(['Contact' => function ($query) {
                    $query->select('id', 'name', 'address', 'contact_no', 'email');
                }])
                ->with(['ComplainType' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['PersonResponsible' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['User' => function ($query) {
                    $query->select('id', 'first_name');
                }])
                ->find($request->id);

        return response($customer_complain);
    }

    public function ongoing_customer_complain()
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

        return view('customer_complain.ongoing_customer_complain', $data);
    }

    public function ongoing_customer_complain_list()
    {
        $data = [];
        $customer_complains = \App\Model\CustomerComplain::where('is_completed', 0)
                ->where('is_delete', 0)
                ->get();
        foreach ($customer_complains as $customer_complain) {
            $customer_complain_status = \App\Model\CustomerComplainDetails::selectRaw('MAX(customer_complain_status_id) AS customer_complain_status_id')
                    ->where('customer_complain_id', $customer_complain->id)
                    ->where('is_delete', 0)
                    ->first();
            if ($customer_complain_status) {
                $customer_complain_detail = \App\Model\CustomerComplainDetails::where('customer_complain_status_id', $customer_complain_status->customer_complain_status_id)
                        ->where('is_delete', 0)
                        ->orderBy('update_date_time', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->first();
                $row = [
                    'id' => $customer_complain->id,
                    'contact_id' => $customer_complain->contact_id,
                    'complain_no' => $customer_complain->complain_no,
                    'record_date_time' => $customer_complain->record_date_time,
                    'complain_type' => $customer_complain->ComplainType ? $customer_complain->ComplainType->name : '',
                    'person_responsible' => $customer_complain->PersonResponsible ? $customer_complain->PersonResponsible->name : '',
                    'customer_name' => $customer_complain->Contact ? $customer_complain->Contact->name : '',
                    'customer_address' => $customer_complain->Contact ? $customer_complain->Contact->address : '',
                    'update_date_time' => $customer_complain_detail ? $customer_complain_detail->update_date_time : '',
                    'update_status' => $customer_complain_detail && $customer_complain_detail->CustomerComplainStatus ? $customer_complain_detail->CustomerComplainStatus->name : '',
                    'remarks' => $customer_complain_detail ? $customer_complain_detail->remarks : '',
                    'log_user' => $customer_complain->User ? $customer_complain->User->first_name : '',
                ];
                array_push($data, $row);
            }
        }

        return response($data);
    }

    public function update_customer_complain(Request $request)
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

        $data['customer_complain_id'] = $request->id;

        return view('customer_complain.update_customer_complain', $data);
    }

    public function validate_customer_complain_status(Request $request)
    {
        $avoid = [3];
        if ($request->value != $request->update_status && ! in_array($request->update_status, $avoid)) {
            $customer_complain_detail = \App\Model\CustomerComplainDetails::where('customer_complain_id', $request->customer_complain_id)
                    ->where('customer_complain_status_id', $request->update_status)
                    ->where('is_delete', 0)
                    ->first();
            if ($customer_complain_detail) {
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
        $customer_complain_status = \App\Model\CustomerComplainStatus::select('id', 'name')->where('show_update', 1)->get();

        return response($customer_complain_status);
    }

    public function find_customer_complain_status(Request $request)
    {
        $customer_complain_status = \App\Model\CustomerComplainDetails::select('id', 'customer_complain_id', 'update_date_time', 'customer_complain_status_id', 'remarks')
                ->with(['CustomerComplainStatus' => function ($query) {
                    $query->select('id', 'name', 'show_update');
                }])
                ->find($request->id);

        return response($customer_complain_status);
    }

    public function customer_complain_status_list(Request $request)
    {
        $customer_complain_status = \App\Model\CustomerComplainDetails::select('id', 'customer_complain_id', 'update_date_time', 'customer_complain_status_id', 'remarks', 'user_id')
                ->with(['CustomerComplainStatus' => function ($query) {
                    $query->select('id', 'name', 'show_update');
                }])
                ->with(['User' => function ($query) {
                    $query->select('id', 'first_name');
                }])
                ->where('customer_complain_id', $request->customer_complain_id)
                ->where('is_delete', 0)
                ->get();

        $data = [
            'customer_complain_status' => $customer_complain_status,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
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
            $customer_complain = new \App\Model\CustomerComplain();
            $customer_complain->contact_id = $request->contact_id;
            $customer_complain->complain_type_id = isset($request->complain_types['id']) ? $request->complain_types['id'] : 0;
            $customer_complain->person_responsible_id = isset($request->person_responsibles['id']) ? $request->person_responsibles['id'] : 0;
            $customer_complain->record_date_time = date('Y-m-d', strtotime($request->record_date)).' '.$request->record_time;
            $customer_complain->remarks = $request->remarks;
            $customer_complain->reported_person = $request->reported_person;
            $customer_complain->reported_contact_no = $request->reported_contact_no;
            $customer_complain->reported_email = $request->reported_email;
            $customer_complain->user_id = $request->session()->get('users_id');

            if ($customer_complain->save()) {
                $customer_complain->complain_no = 'CC/'.date('m').'/'.date('y').'/'.sprintf('%05d', $customer_complain->id);
                $customer_complain->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/customer_complain_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$customer_complain->id.','.$customer_complain->complain_no.','.$customer_complain->contact_id.','.$customer_complain->complain_type_id.','.$customer_complain->person_responsible_id.','.$customer_complain->record_date_time.','.str_replace(',', ' ', $customer_complain->remarks).','.str_replace(',', ' ', $customer_complain->reported_person).','.str_replace(',', ' ', $customer_complain->reported_contact_no).','.str_replace(',', ' ', $customer_complain->reported_email).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $customer_complain_detail = new \App\Model\CustomerComplainDetails();
                $customer_complain_detail->customer_complain_id = $customer_complain->id;
                $customer_complain_detail->update_date_time = date('Y-m-d H:i');
                $customer_complain_detail->customer_complain_status_id = 1;
                $customer_complain_detail->remarks = '';
                $customer_complain_detail->user_id = $request->session()->get('users_id');
                $customer_complain_detail->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/customer_complain_detail_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$customer_complain_detail->id.','.$customer_complain_detail->customer_complain_id.','.$customer_complain_detail->update_date_time.','.$customer_complain_detail->customer_complain_status_id.','.str_replace(',', ' ', $customer_complain_detail->remarks).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $data = [
                    'person_responsible_name' => $customer_complain->PersonResponsible->name,
                    'complain_no' => $customer_complain->complain_no,
                    'customer_name' => $customer_complain->Contact->name,
                    'customer_address' => $customer_complain->Contact->address,
                    'customer_contact_no' => $customer_complain->Contact->contact_no,
                    'customer_email' => $customer_complain->Contact->email,
                    'complain_type' => $customer_complain->ComplainType->name,
                    'remarks' => $customer_complain->remarks,
                    'reported_name' => $customer_complain->reported_person,
                    'reported_contact_no' => $customer_complain->reported_contact_no,
                    'reported_email' => $customer_complain->reported_email,
                    'logged_user' => $customer_complain->User->first_name.' '.$customer_complain->User->last_name,
                ];

                Mail::send('emails.complain_details', $data, function ($message) use ($customer_complain) {
                    $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                    $message->to($customer_complain->PersonResponsible->email, $customer_complain->PersonResponsible->name.' | '.$customer_complain->PersonResponsible->title);
                    $message->cc($customer_complain->PersonResponsible->head_email, $customer_complain->PersonResponsible->head_name);
                    $message->subject('M3Force Customer Complain');
                });

                $sms = '--- M3Force Customer Complain ---'.PHP_EOL;
                $sms .= 'Complain No : '.$customer_complain->complain_no.PHP_EOL;
                $sms .= 'Customer Name : '.$customer_complain->Contact->name.PHP_EOL;
                $sms .= 'Customer Address : '.$customer_complain->Contact->address.PHP_EOL;
                $sms .= 'Customer Contact No : '.$customer_complain->Contact->contact_no.PHP_EOL;
                $sms .= 'Complain Type : '.$customer_complain->ComplainType->name.PHP_EOL;
                $sms .= 'Remarks : '.$customer_complain->remarks.PHP_EOL;
                $sms .= 'Logged User : '.$customer_complain->User->first_name.' '.$customer_complain->User->last_name;

                $session = createSession('', 'esmsusr_1na2', '3p4lfqe', '');
                sendMessages($session, 'M3FORCE', $sms, [$customer_complain->PersonResponsible->contact_no, $customer_complain->PersonResponsible->head_contact_no]);

                $result = [
                    'response' => true,
                    'message' => 'Customer Complain created successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Customer Complain creation failed',
                ];
            }
        } elseif ($request->type == 1) {
            $customer_complain_status = new \App\Model\CustomerComplainDetails();
            $customer_complain_status->customer_complain_id = $request->customer_complain_id;
            $customer_complain_status->update_date_time = date('Y-m-d', strtotime($request->update_date)).' '.$request->update_time;
            $customer_complain_status->customer_complain_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
            $customer_complain_status->remarks = $request->remarks;
            $customer_complain_status->user_id = $request->session()->get('users_id');

            if ($customer_complain_status->save()) {
                $customer_complain = \App\Model\CustomerComplain::find($customer_complain_status->customer_complain_id);
                if ($customer_complain_status->customer_complain_status_id == 4) {
                    $customer_complain->is_completed = 1;
                    $customer_complain->save();
                }

                $result = [
                    'response' => true,
                    'message' => 'Customer Complain Status created successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Customer Complain Status creation failed',
                ];
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
            $customer_complain = \App\Model\CustomerComplain::find($id);
            $customer_complain->contact_id = $request->contact_id;
            $customer_complain->complain_type_id = isset($request->complain_types['id']) ? $request->complain_types['id'] : 0;
            $customer_complain->person_responsible_id = isset($request->person_responsibles['id']) ? $request->person_responsibles['id'] : 0;
            $customer_complain->complain_no = $request->complain_no;
            $customer_complain->record_date_time = date('Y-m-d', strtotime($request->record_date)).' '.$request->record_time;
            $customer_complain->remarks = $request->remarks;
            $customer_complain->reported_person = $request->reported_person;
            $customer_complain->reported_contact_no = $request->reported_contact_no;
            $customer_complain->reported_email = $request->reported_email;
            $customer_complain->user_id = $request->session()->get('users_id');

            if ($customer_complain->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/customer_complain_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$customer_complain->id.','.$customer_complain->complain_no.','.$customer_complain->contact_id.','.$customer_complain->complain_type_id.','.$customer_complain->person_responsible_id.','.$customer_complain->record_date_time.','.str_replace(',', ' ', $customer_complain->remarks).','.str_replace(',', ' ', $customer_complain->reported_person).','.str_replace(',', ' ', $customer_complain->reported_contact_no).','.str_replace(',', ' ', $customer_complain->reported_email).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Customer Complain updated successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Customer Complain updation failed',
                ];
            }
        } elseif ($request->type == 1) {
            $customer_complain_status = \App\Model\CustomerComplainDetails::find($id);
            $customer_complain_status->customer_complain_id = $request->customer_complain_id;
            $customer_complain_status->update_date_time = date('Y-m-d', strtotime($request->update_date)).' '.$request->update_time;
            $customer_complain_status->customer_complain_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
            $customer_complain_status->remarks = $request->remarks;
            $customer_complain_status->user_id = $request->session()->get('users_id');

            if ($customer_complain_status->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/customer_complain_detail_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$customer_complain_status->id.','.$customer_complain_status->customer_complain_id.','.$customer_complain_status->update_date_time.','.$customer_complain_status->customer_complain_status_id.','.str_replace(',', ' ', $customer_complain_status->remarks).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $customer_complain = \App\Model\CustomerComplain::find($customer_complain_status->customer_complain_id);
                if ($customer_complain_status->customer_complain_status_id == 4) {
                    $customer_complain->is_completed = 1;
                    $customer_complain->save();
                }

                $result = [
                    'response' => true,
                    'message' => 'Customer Complain Status updated successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Customer Complain Status updation failed',
                ];
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
            $customer_complain = \App\Model\CustomerComplain::find($id);
            $customer_complain->is_delete = 1;

            if ($customer_complain->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/customer_complain_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$customer_complain->id.',,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $customer_complain_details = \App\Model\CustomerComplainDetails::where('customer_complain_id', $customer_complain->id)
                        ->where('is_delete', 0)
                        ->update(['is_delete' => 1]);
                $result = [
                    'response' => true,
                    'message' => 'Customer Complain deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Customer Complain deletion failed',
                ];
            }
        } elseif ($request->type == 1) {
            $customer_complain_detail = \App\Model\CustomerComplainDetails::find($id);
            $customer_complain_detail->is_delete = 1;

            if ($customer_complain_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/customer_complain_detail_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$customer_complain_detail->id.',,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Customer Complain Status deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Customer Complain Status deletion failed',
                ];
            }
        }

        echo json_encode($result);
    }
}
