<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
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

    public function new_inquiry(Request $request)
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

        $data['inquiry_id'] = $request->id;

        return view('inquiry.new_inquiry', $data);
    }

    public function get_data()
    {
        $mode_of_inquries = \App\Model\IModeOfInquiry::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $inquiry_types = \App\Model\IInquiryType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $sales_team = \App\Model\SalesTeam::select('id', 'name')->where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        $inquiry_status = \App\Model\InquiryStatus::select('id', 'name')->where('show_update', 1)->get();
        $payment_modes = \App\Model\PaymentMode::select('id', 'name')->orderBy('name')->get();

        $data = array(
            'mode_of_inquries' => $mode_of_inquries,
            'inquiry_types' => $inquiry_types,
            'sales_team' => $sales_team,
            'inquiry_status' => $inquiry_status,
            'payment_modes' => $payment_modes
        );

        return response($data);
    }

    public function find_inquiry(Request $request)
    {
        $inquiry = \App\Model\Inquiry::select('id', 'contact_id', 'inquiry_no', 'inquiry_date_time', 'mode_of_inquiry_id', 'contact_of', 'inquiry_type_id', 'sales_team_id', 'remarks')
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
            ->with(array('IModeOfInquiry' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('IInquiryType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('SalesTeam' => function ($query) {
                $query->select('id', 'name');
            }))
            ->find($request->id);
        return response($inquiry);
    }

    public function new_inquiry_list(Request $request)
    {
        $inquiries = \App\Model\Inquiry::select('id', 'contact_id', 'inquiry_no', 'inquiry_date_time', 'inquiry_type_id', 'sales_team_id', 'user_id')
            ->with(array('Contact' => function ($query) {
                $query->select('id', 'name', 'address', 'contact_no', 'business_type_id', 'client_type_id')
                    ->with(array('IBusinessType' => function ($query) {
                        $query->select('id', 'name');
                    }))
                    ->with(array('IClientType' => function ($query) {
                        $query->select('id', 'name');
                    }));
            }))
            ->with(array('IInquiryType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('SalesTeam' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->where(function ($q) use ($request) {
                $request->inquiry_type_id != -1 ? $q->where('inquiry_type_id', $request->inquiry_type_id) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sales_team_id != -1 ? $q->where('sales_team_id', $request->sales_team_id) : '';
            })
            ->where('is_first_call_done', 0)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'inquiries' => $inquiries,
            'permission' => !in_array(1, session()->get('user_group'))
        );

        return response($data);
    }

    public function update_inquiry(Request $request)
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

        $data['inquiry_id'] = $request->id;

        return view('inquiry.update_inquiry', $data);
    }

    public function validate_inquiry_status(Request $request)
    {
        $avoid = array(2, 3, 12, 15);
        if ($request->value != $request->update_status && !in_array($request->update_status, $avoid)) {
            $inquiry_details = \App\Model\InquiryDetials::where('inquiry_id', $request->inquiry_id)
                ->where('inquiry_status_id', $request->update_status)
                ->where('is_delete', 0)
                ->first();
            if ($inquiry_details) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_inquiry_status(Request $request)
    {
        $inquiry_status = \App\Model\InquiryDetials::select('id', 'inquiry_id', 'update_date_time', 'inquiry_status_id', 'sales_team_id', 'site_inspection_date_time', 'advance_payment', 'payment_mode_id', 'receipt_no', 'cheque_no', 'bank', 'realize_date', 'remarks')
            ->with(array('InquiryStatus' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('SalesTeam' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('PaymentMode' => function ($query) {
                $query->select('id', 'name');
            }))
            ->find($request->id);
        return response($inquiry_status);
    }

    public function inquiry_status_list(Request $request)
    {
        $inquiry_status = \App\Model\InquiryDetials::select('id', 'inquiry_id', 'update_date_time', 'inquiry_status_id', 'sales_team_id', 'site_inspection_date_time', 'advance_payment', 'remarks', 'user_id')
            ->with(array('InquiryStatus' => function ($query) {
                $query->select('id', 'name', 'show_update');
            }))
            ->with(array('SalesTeam' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->where('inquiry_id', $request->inquiry_id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'inquiry_status' => $inquiry_status,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function ongoing_inquiry()
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

        return view('inquiry.ongoing_inquiry', $data);
    }

    public function ongoing_inquiry_list(Request $request)
    {
        $data = array();
        $inquiries = \App\Model\Inquiry::select('id', 'contact_id', 'inquiry_no', 'inquiry_date_time', 'inquiry_type_id', 'sales_team_id', 'user_id')
            ->where(function ($q) use ($request) {
                $request->inquiry_type_id != -1 ? $q->where('inquiry_type_id', $request->inquiry_type_id) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sales_team_id != -1 ? $q->where('sales_team_id', $request->sales_team_id) : '';
            })
            ->where('is_first_call_done', 1)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($inquiries as $inquiry) {
            $inquiry_status = \App\Model\InquiryDetials::selectRaw('MAX(inquiry_status_id) AS inquiry_status_id')
                ->where('inquiry_id', $inquiry->id)
                ->where('is_delete', 0)
                ->first();
            if ($inquiry_status && ($request->update_status_id == -1 || $request->update_status_id == $inquiry_status->inquiry_status_id)) {
                $inquiry_detail = \App\Model\InquiryDetials::where('inquiry_id', $inquiry->id)
                    ->where('inquiry_status_id', $inquiry_status->inquiry_status_id)
                    ->where('is_delete', 0)
                    ->orderBy('update_date_time', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();
                $quotations = \App\Model\Quotation::where('inquiry_id', $inquiry->id)
                    ->where('is_confirmed', 1)
                    ->where('is_revised', 0)
                    ->where('is_delete', 0)
                    ->get();
                $quotation_value = 0;
                foreach ($quotations as $quotation) {
                    $quotation_value += $quotation->quotation_value;
                }
                $row = array(
                    'id' => $inquiry->id,
                    'status_id' => $inquiry_detail && $inquiry_detail->InquiryStatus ? $inquiry_detail->InquiryStatus->id : 0,
                    'inquiry_type_id' => $inquiry->inquiry_type_id,
                    'inquiry_no' => $inquiry->inquiry_no,
                    'inquiry_date_time' => $inquiry->inquiry_date_time,
                    'inquiry_type' => $inquiry->IInquiryType ? $inquiry->IInquiryType->name : '',
                    'customer_name' => $inquiry->Contact ? $inquiry->Contact->name : '',
                    'customer_address' => $inquiry->Contact ? $inquiry->Contact->address : '',
                    'customer_contact_no' => $inquiry->Contact ? $inquiry->Contact->contact_no : '',
                    'client_type' => $inquiry->Contact && $inquiry->Contact->IClientType ? $inquiry->Contact->IClientType->name : '',
                    'business_type' => $inquiry->Contact && $inquiry->Contact->IBusinessType ? $inquiry->Contact->IBusinessType->name : '',
                    'update_date_time' => $inquiry_detail ? $inquiry_detail->update_date_time : '',
                    'update_status' => $inquiry_detail && $inquiry_detail->InquiryStatus ? $inquiry_detail->InquiryStatus->name : '',
                    'remarks' => $inquiry_detail ? $inquiry_detail->remarks : '',
                    'quotation_value' => $quotation_value,
                    'sales_person' => $inquiry->SalesTeam ? $inquiry->SalesTeam->name : '',
                    'log_user' => $inquiry_detail && $inquiry_detail->User ? $inquiry_detail->User->first_name : ''
                );
                array_push($data, $row);
            }
        }

        return response($data);
    }

    public function upload_documents(Request $request)
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

        $data['inquiry_id'] = $request->id;

        return view('inquiry.upload_documents', $data);
    }

    public function validate_document_upload(Request $request)
    {
        if ($request->value != $request->document_name) {
            $document_upload = \App\Model\DocumentUpload::where('inquiry_id', $request->inquiry_id)
                ->where('document_name', $request->document_name)
                ->where('is_delete', 0)
                ->first();
            if ($document_upload) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function file_upload()
    {
        if (!empty($_FILES['file'])) {
            $path_info = pathinfo($_FILES['file']['name']);
            $file = $path_info['filename'] . ' UD' . time() . '.' . $path_info['extension'];
            if (move_uploaded_file($_FILES["file"]["tmp_name"], 'assets/uploads/documents/' . $file)) {
                $result = array(
                    'response' => true,
                    'message' => 'success',
                    'file' => $file,
                    'name' => $_FILES['file']['name']
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'File upload error'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'File is empty'
            );
        }

        echo json_encode($result);
    }

    public function upload_document_list(Request $request)
    {
        $upload_documents = \App\Model\DocumentUpload::select('id', 'inquiry_id', 'document_type_id', 'document_name', 'upload_document')
            ->with(array('DocumentType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->where('inquiry_id', $request->inquiry_id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'upload_documents' => $upload_documents,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function job_card(Request $request)
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

        $data['inquiry_id'] = $request->id;
        $data['type'] = $request->type;

        return view('inquiry.job_card', $data);
    }

    public function cost_sheet(Request $request)
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

        $data['inquiry_id'] = $request->id;
        $data['type'] = $request->type;

        return view('inquiry.cost_sheet', $data);
    }

    public function installation_sheet(Request $request)
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

        $data['inquiry_id'] = $request->id;
        $data['type'] = $request->type;

        return view('inquiry.installation_sheet', $data);
    }

    public function quotation(Request $request)
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

        $data['inquiry_id'] = $request->id;
        $data['type'] = $request->type;

        return view('inquiry.quotation', $data);
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
        if ($request->type == 1) {
            $contact_id = 0;
            if (!$request->customer_id || $request->customer_id == '') {
                $contact = new \App\Model\Contact();
                $contact->contact_type_id = 2;
                $contact->business_type_id = 0;
                $last_id = 0;
                $last_contact = \App\Model\Contact::select('id')->where('contact_type_id', 2)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                $last_id = $last_contact ? $last_contact->id : $last_id;
                $contact->code = 'C-NMC' . sprintf('%05d', $last_id + 1);
                $contact->name = isset($request->name['name']) ? $request->name['name'] : $request->name;
                $contact->nic = '';
                $contact->address = $request->address;
                $contact->contact_no = $request->contact_no;
                $contact->email = $request->email;
                $contact->region_id = 0;
                $contact->contact_person_1 = '';
                $contact->contact_person_no_1 = '';
                $contact->contact_person_2 = '';
                $contact->contact_person_no_2 = '';
                $contact->vat_no = '';
                $contact->svat_no = '';
                $contact->monitoring_fee = 0;
                $contact->group_id = 0;
                $contact->is_group = 0;
                $contact->is_active = 1;
                if ($contact->save()) {
                    $contact->contact_id = $contact->id;
                    $contact->save();

                    $cus_inv_months = $cus_taxes = '';
                    $taxes = array(5);
                    for ($t = 0; $t < count($taxes); $t++) {
                        $contact_tax = new \App\Model\ContactTax();
                        $contact_tax->contact_id = $contact->id;
                        $contact_tax->tax_id = $taxes[$t];
                        $contact_tax->save();

                        $cus_taxes .= $cus_taxes != '' ? '|' . $contact_tax->tax_id : $contact_tax->tax_id;
                    }
                }
                $contact_id = $contact->id;

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $contact->id . ',' . $contact->contact_type_id . ',' . $contact->business_type_id . ',' . $contact->contact_id . ',' . $contact->code . ',' . str_replace(',', ' ', $contact->name) . ',' . str_replace(',', ' ', $contact->nic) . ',' . str_replace(',', ' ', $contact->address) . ',' . str_replace(',', ' ', $contact->contact_no) . ',' . str_replace(',', ' ', $contact->email) . ',' . $contact->region_id . ',' . $contact->collection_manager_id . ',' . str_replace(',', ' ', $contact->contact_person_1) . ',' . str_replace(',', ' ', $contact->contact_person_no_1) . ',' . str_replace(',', ' ', $contact->contact_person_2) . ',' . str_replace(',', ' ', $contact->contact_person_no_2) . ',' . str_replace(',', ' ', $contact->contact_person_3) . ',' . str_replace(',', ' ', $contact->contact_person_no_3) . ',' . str_replace(',', ' ', $contact->invoice_name) . ',' . str_replace(',', ' ', $contact->invoice_delivering_address) . ',' . str_replace(',', ' ', $contact->collection_address) . ',' . str_replace(',', ' ', $contact->invoice_email) . ',' . str_replace(',', ' ', $contact->vat_no) . ',' . str_replace(',', ' ', $contact->svat_no) . ',' . str_replace(',', ' ', $contact->monitoring_fee) . ',' . $contact->service_mode_id . ',' . $contact->client_type_id . ',' . $contact->group_id . ',' . $contact->is_group . ',' . $contact->is_active . ',' . $cus_inv_months . ',' . $cus_taxes . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            } else {
                $contact_id = $request->customer_id;
            }

            $inquiry = new \App\Model\Inquiry();
            $inquiry->contact_id = $contact_id;
            $inquiry->inquiry_date_time = date('Y-m-d', strtotime($request->inquiry_date)) . ' ' . $request->inquiry_time;
            $inquiry->mode_of_inquiry_id = isset($request->mode_of_inquiry['id']) ? $request->mode_of_inquiry['id'] : 0;
            $inquiry->contact_of = $request->contact_of;
            $inquiry->inquiry_type_id = isset($request->inquiry_type['id']) ? $request->inquiry_type['id'] : 0;
            $inquiry->sales_team_id = isset($request->sales_person['id']) ? $request->sales_person['id'] : 0;
            $inquiry->remarks = $request->remarks;
            $inquiry->user_id = $request->session()->get('users_id');

            if ($inquiry->save()) {
                $inquiry->inquiry_no = 'INQ/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $inquiry->id);
                $inquiry->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $inquiry->id . ',' . $inquiry->contact_id . ',' . $inquiry->inquiry_no . ',' . $inquiry->inquiry_date_time . ',' . $inquiry->mode_of_inquiry_id . ',' . str_replace(',', ' ', $inquiry->contact_of) . ',' . $inquiry->inquiry_type_id . ',' . $inquiry->sales_team_id . ',' . str_replace(',', ' ', $inquiry->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $contact = \App\Model\Contact::find($inquiry->contact_id);
                $sales_person = \App\Model\SalesTeam::find($inquiry->sales_team_id);

                if ($contact && $sales_person) {
                    $sms = '--- New Inquiry ---' . PHP_EOL;
                    $sms .= 'Inquiry No : ' . $inquiry->inquiry_no . PHP_EOL;
                    $sms .= 'Date & Time : ' . $inquiry->inquiry_date_time . PHP_EOL;
                    $sms .= 'Mode of Inquiry : ' . $request->mode_of_inquiry['name'] . PHP_EOL;
                    $sms .= 'Inquiry Type : ' . $request->inquiry_type['name'] . PHP_EOL;
                    $sms .= 'Customer Name : ' . $contact->name . PHP_EOL;
                    $sms .= 'Contact No : ' . $contact->contact_no . PHP_EOL;
                    $sms .= 'Address : ' . $contact->address . PHP_EOL;
                    $sms .= 'Remarks : ' . $inquiry->remarks;

                    $session = createSession('', 'esmsusr_1na2', '3p4lfqe', '');
                    sendMessages($session, 'M3FORCE', $sms, array($sales_person->contact_no));
                }

                $result = array(
                    'response' => true,
                    'message' => 'Inquiry created successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inquiry creation failed'
                );
            }
        } else if ($request->type == 0) {
            $completed = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? false : true;
            $drawing_uploaded = $quotation_confirmed = $installation_sheet_created = false;
            if (!$completed) {
                $inquiry = \App\Model\Inquiry::find($request->inquiry_id);
                $drawing_uploaded = \App\Model\InquiryDetials::where('inquiry_id', $request->inquiry_id)
                    ->where('inquiry_status_id', 5)
                    ->where('is_delete', 0)
                    ->first();
                $drawing_uploaded = $inquiry && ($inquiry->inquiry_type_id == 3 || $inquiry->inquiry_type_id == 5 || $inquiry->inquiry_type_id == 6) ? true : $drawing_uploaded;
                $quotation_confirmed = \App\Model\Quotation::where('inquiry_id', $request->inquiry_id)
                    ->where('is_confirmed', 1)
                    ->where('is_delete', 0)
                    ->first();
                //                $installation_sheet_created = \App\Model\InstallationSheet::where('inquiry_id', $request->inquiry_id)
                //                        ->where('is_delete', 0)
                //                        ->first();
                //                $installation_sheet_created = $inquiry && ($inquiry->inquiry_type_id == 5 || $inquiry->inquiry_type_id == 6) ? true : $installation_sheet_created;
                $installation_sheet_created = true;
                $completed = $drawing_uploaded && $quotation_confirmed && $installation_sheet_created ? true : false;
            }

            if ($completed) {
                $inquiry_status = new \App\Model\InquiryDetials();
                $inquiry_status->inquiry_id = $request->inquiry_id;
                $inquiry_status->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
                $inquiry_status->inquiry_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
                $inquiry_status->sales_team_id = isset($request->update_status['id']) && $request->update_status['id'] == 1 ? isset($request->sales_person['id']) ? $request->sales_person['id'] : 0 : 0;
                $inquiry_status->site_inspection_date_time = isset($request->update_status['id']) && $request->update_status['id'] == 3 ? date('Y-m-d', strtotime($request->site_inspection_date)) . ' ' . $request->site_inspection_time : '';
                $inquiry_status->advance_payment = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->advance_payment : 0;
                $inquiry_status->payment_mode_id = isset($request->update_status['id']) && $request->update_status['id'] == 16 && isset($request->payment_mode['id']) ? $request->payment_mode['id'] : 0;

                $last_id = 0;
                $count_advance_payment = \App\Model\InquiryDetials::select('id')->where('inquiry_status_id', 16)->where('is_delete', 0)->get()->count();
                $last_id = $count_advance_payment ? $count_advance_payment : $last_id;

                $inquiry_status->receipt_no = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? 'REC/A/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1) : '';
                $inquiry_status->cheque_no = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->cheque_no : '';
                $inquiry_status->bank = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->bank : '';
                $inquiry_status->realize_date = isset($request->update_status['id']) && $request->update_status['id'] == 16 && $request->realize_date != '' ? date('Y-m-d', strtotime($request->realize_date)) : '';
                $inquiry_status->remarks = $request->remarks;
                $inquiry_status->user_id = $request->session()->get('users_id');

                if ($inquiry_status->save()) {
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $inquiry_status->id . ',' . $inquiry_status->inquiry_id . ',' . $inquiry_status->update_date_time . ',' . $inquiry_status->inquiry_status_id . ',' . $inquiry_status->sales_team_id . ',' . $inquiry_status->site_inspection_date_time . ',' . $inquiry_status->advance_payment . ',' . $inquiry_status->payment_mode_id . ',' . $inquiry_status->receipt_no . ',' . str_replace(',', ' ', $inquiry_status->cheque_no) . ',' . str_replace(',', ' ', $inquiry_status->bank) . ',' . $inquiry_status->realize_date . ',' . str_replace(',', ' ', $inquiry_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);

                    $inquiry = \App\Model\Inquiry::find($inquiry_status->inquiry_id);
                    if ($inquiry->is_first_call_done == 0) {
                        $inquiry->is_first_call_done = 1;
                        $inquiry->save();
                    }

                    if ($inquiry_status->inquiry_status_id == 1) {
                        $inquiry->sales_team_id = $inquiry_status->sales_team_id;
                        $inquiry->save();
                    }

                    if ($inquiry_status->inquiry_status_id == 16) {
                        $inquiry->is_completed = 1;
                        $inquiry->save();

                        $job_value = $mandays = 0;
                        $quotations = \App\Model\Quotation::where('inquiry_id', $inquiry->id)
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

                        $job = new \App\Model\Job();
                        $job->inquiry_id = $inquiry->id;
                        $job->job_date_time = $inquiry_status->update_date_time;
                        $job->job_value = $job_value;
                        $job->mandays = $mandays;
                        $job->user_id = $request->session()->get('users_id');
                        if ($job->save()) {
                            $job->job_no = 'JB/' . date('m') . '/' . date('y') . '/' . $inquiry->id . '/' . sprintf('%05d', $job->id);
                            $job->save();

                            $job_details = new \App\Model\JobDetails();
                            $job_details->job_id = $job->id;
                            $job_details->update_date_time = $job->job_date_time;
                            $job_details->job_status_id = 1;
                            $job_details->job_scheduled_date_time = '';
                            $job_details->remarks = 'Job value : ' . number_format($job->job_value, 2);
                            $job_details->user_id = $request->session()->get('users_id');
                            $job_details->save();
                        }

                        $data = array(
                            'inquiry_id' => $inquiry->id,
                            'job_type' => $inquiry->IInquiryType->name,
                            'job_date_time' => $job->job_date_time,
                            'job_no' => $job->job_no,
                            'job_value' => $job->job_value,
                            'customer_name' => $inquiry->Contact->name,
                            'customer_address' => $inquiry->Contact->address,
                            'sales_person' => $inquiry->SalesTeam->name
                        );

                        Mail::send('emails.job_confirmation_notification', $data, function ($message) use ($quotation) {
                            $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                            $message->to('stores@m3force.com', 'Stores Assistant');
                            $message->to('procurement@m3force.com', 'Deepal Gunasekera');
                            $message->cc('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                            $message->cc('palitha@m3force.com', 'Palitha Wickramathunga');
                            $message->subject('M3Force Job Confirmation Details');
                        });
                    }

                    if ($inquiry_status->inquiry_status_id == 17 || $inquiry_status->inquiry_status_id == 18) {
                        $inquiry->is_completed = 1;
                        $inquiry->save();
                    }

                    $result = array(
                        'response' => true,
                        'message' => 'Inquiry Status created successfully'
                    );
                } else {
                    $result = array(
                        'response' => false,
                        'message' => 'Inquiry Status creation failed'
                    );
                }
            } else if (!$drawing_uploaded) {
                $result = array(
                    'response' => false,
                    'message' => 'Site Drawing required'
                );
            } else if (!$quotation_confirmed) {
                $result = array(
                    'response' => false,
                    'message' => 'Quotation not confirmed'
                );
            } else if (!$installation_sheet_created) {
                $result = array(
                    'response' => false,
                    'message' => 'Installation Sheet required'
                );
            }
        } else if ($request->type == 2) {
            $document_upload = new \App\Model\DocumentUpload();
            $document_upload->inquiry_id = $request->inquiry_id;
            $document_upload->document_type_id = isset($request->document_type['id']) ? $request->document_type['id'] : 0;
            $document_upload->document_name = $request->document_name;
            $document_upload->upload_document = $request->upload_document;

            if ($document_upload->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/document_upload_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Uploaded,' . $document_upload->id . ',' . $document_upload->inquiry_id . ',' . $document_upload->document_type_id . ',' . str_replace(',', ' ', $document_upload->document_name) . ',' . str_replace(',', ' ', $document_upload->upload_document) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                if ($document_upload->document_type_id == 1) {
                    $inquiry_status = new \App\Model\InquiryDetials();
                    $inquiry_status->inquiry_id = $document_upload->inquiry_id;
                    $inquiry_status->update_date_time = date('Y-m-d H:i');
                    $inquiry_status->inquiry_status_id = 5;
                    $inquiry_status->sales_team_id = 0;
                    $inquiry_status->site_inspection_date_time = '';
                    $inquiry_status->advance_payment = 0;
                    $inquiry_status->remarks = isset($request->document_type['name']) ? $request->document_type['name'] . ' - ' . $document_upload->document_name . ' ( ' . $request->doc_name . ' )' : $document_upload->document_name . ' ( ' . $request->doc_name . ' )';
                    $inquiry_status->user_id = $request->session()->get('users_id');
                    $inquiry_status->save();

                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $inquiry_status->id . ',' . $inquiry_status->inquiry_id . ',' . $inquiry_status->update_date_time . ',' . $inquiry_status->inquiry_status_id . ',' . $inquiry_status->sales_team_id . ',' . $inquiry_status->site_inspection_date_time . ',' . $inquiry_status->advance_payment . ',' . $inquiry_status->payment_mode_id . ',' . $inquiry_status->receipt_no . ',' . str_replace(',', ' ', $inquiry_status->cheque_no) . ',' . str_replace(',', ' ', $inquiry_status->bank) . ',' . $inquiry_status->realize_date . ',' . str_replace(',', ' ', $inquiry_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);
                }

                $result = array(
                    'response' => true,
                    'message' => 'Document uploaded successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Document upload failed'
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
        if ($request->type == 1) {
            $contact_id = 0;
            if (!$request->customer_id || $request->customer_id == '') {
                $contact = new \App\Model\Contact();
                $contact->contact_type_id = 2;
                $contact->business_type_id = 0;
                $last_id = 0;
                $last_contact = \App\Model\Contact::select('id')->where('contact_type_id', 2)->where('is_delete', 0)->orderBy('id', 'desc')->first();
                $last_id = $last_contact ? $last_contact->id : $last_id;
                $contact->code = 'C-NMC' . sprintf('%05d', $last_id + 1);
                $contact->name = isset($request->name['name']) ? $request->name['name'] : $request->name;
                $contact->nic = '';
                $contact->address = $request->address;
                $contact->contact_no = $request->contact_no;
                $contact->email = $request->email;
                $contact->region_id = 0;
                $contact->contact_person_1 = '';
                $contact->contact_person_no_1 = '';
                $contact->contact_person_2 = '';
                $contact->contact_person_no_2 = '';
                $contact->vat_no = '';
                $contact->svat_no = '';
                $contact->monitoring_fee = 0;
                $contact->group_id = 0;
                $contact->is_group = 0;
                $contact->is_active = 1;
                if ($contact->save()) {
                    $contact->contact_id = $contact->id;
                    $contact->save();

                    $cus_inv_months = $cus_taxes = '';
                    $taxes = array(1, 3);
                    for ($t = 0; $t < count($taxes); $t++) {
                        $contact_tax = new \App\Model\ContactTax();
                        $contact_tax->contact_id = $contact->id;
                        $contact_tax->tax_id = $taxes[$t];
                        $contact_tax->save();

                        $cus_taxes .= $cus_taxes != '' ? '|' . $contact_tax->tax_id : $contact_tax->tax_id;
                    }
                }
                $contact_id = $contact->id;

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $contact->id . ',' . $contact->contact_type_id . ',' . $contact->business_type_id . ',' . $contact->contact_id . ',' . $contact->code . ',' . str_replace(',', ' ', $contact->name) . ',' . str_replace(',', ' ', $contact->nic) . ',' . str_replace(',', ' ', $contact->address) . ',' . str_replace(',', ' ', $contact->contact_no) . ',' . str_replace(',', ' ', $contact->email) . ',' . $contact->region_id . ',' . $contact->collection_manager_id . ',' . str_replace(',', ' ', $contact->contact_person_1) . ',' . str_replace(',', ' ', $contact->contact_person_no_1) . ',' . str_replace(',', ' ', $contact->contact_person_2) . ',' . str_replace(',', ' ', $contact->contact_person_no_2) . ',' . str_replace(',', ' ', $contact->contact_person_3) . ',' . str_replace(',', ' ', $contact->contact_person_no_3) . ',' . str_replace(',', ' ', $contact->invoice_name) . ',' . str_replace(',', ' ', $contact->invoice_delivering_address) . ',' . str_replace(',', ' ', $contact->collection_address) . ',' . str_replace(',', ' ', $contact->invoice_email) . ',' . str_replace(',', ' ', $contact->vat_no) . ',' . str_replace(',', ' ', $contact->svat_no) . ',' . str_replace(',', ' ', $contact->monitoring_fee) . ',' . $contact->service_mode_id . ',' . $contact->client_type_id . ',' . $contact->group_id . ',' . $contact->is_group . ',' . $contact->is_active . ',' . $cus_inv_months . ',' . $cus_taxes . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            } else {
                $contact_id = $request->customer_id;
            }

            $inquiry = \App\Model\Inquiry::find($id);
            $inquiry->contact_id = $contact_id;
            $inquiry->inquiry_no = $request->inquiry_no;
            $inquiry->inquiry_date_time = date('Y-m-d', strtotime($request->inquiry_date)) . ' ' . $request->inquiry_time;
            $inquiry->mode_of_inquiry_id = isset($request->mode_of_inquiry['id']) ? $request->mode_of_inquiry['id'] : 0;
            $inquiry->contact_of = $request->contact_of;
            $inquiry->inquiry_type_id = isset($request->inquiry_type['id']) ? $request->inquiry_type['id'] : 0;
            $inquiry->sales_team_id = isset($request->sales_person['id']) ? $request->sales_person['id'] : 0;
            $inquiry->remarks = $request->remarks;
            $inquiry->user_id = $request->session()->get('users_id');

            if ($inquiry->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $inquiry->id . ',' . $inquiry->contact_id . ',' . $inquiry->inquiry_no . ',' . $inquiry->inquiry_date_time . ',' . $inquiry->mode_of_inquiry_id . ',' . str_replace(',', ' ', $inquiry->contact_of) . ',' . $inquiry->inquiry_type_id . ',' . $inquiry->sales_team_id . ',' . str_replace(',', ' ', $inquiry->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Inquiry updated successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inquiry updation failed'
                );
            }
        } else if ($request->type == 0) {
            $completed = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? false : true;
            $drawing_uploaded = $quotation_confirmed = $installation_sheet_created = false;
            if (!$completed) {
                $inquiry = \App\Model\Inquiry::find($request->inquiry_id);
                $drawing_uploaded = \App\Model\InquiryDetials::where('inquiry_id', $request->inquiry_id)
                    ->where('inquiry_status_id', 5)
                    ->where('is_delete', 0)
                    ->first();
                $drawing_uploaded = $inquiry && ($inquiry->inquiry_type_id == 5 || $inquiry->inquiry_type_id == 6) ? true : $drawing_uploaded;
                $quotation_confirmed = \App\Model\Quotation::where('inquiry_id', $request->inquiry_id)
                    ->where('is_confirmed', 1)
                    ->where('is_delete', 0)
                    ->first();
                //                $installation_sheet_created = \App\Model\InstallationSheet::where('inquiry_id', $request->inquiry_id)
                //                        ->where('is_delete', 0)
                //                        ->first();
                //                $installation_sheet_created = $inquiry && ($inquiry->inquiry_type_id == 5 || $inquiry->inquiry_type_id == 6) ? true : $installation_sheet_created;
                $installation_sheet_created = true;
                $completed = $drawing_uploaded && $quotation_confirmed && $installation_sheet_created ? true : false;
            }

            if ($completed) {
                $inquiry_status = \App\Model\InquiryDetials::find($id);
                $inquiry_status->inquiry_id = $request->inquiry_id;
                $inquiry_status->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
                $inquiry_status->inquiry_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
                $inquiry_status->sales_team_id = isset($request->update_status['id']) && $request->update_status['id'] == 1 ? isset($request->sales_person['id']) ? $request->sales_person['id'] : 0 : 0;
                $inquiry_status->site_inspection_date_time = isset($request->update_status['id']) && $request->update_status['id'] == 3 ? date('Y-m-d', strtotime($request->site_inspection_date)) . ' ' . $request->site_inspection_time : '';
                $inquiry_status->advance_payment = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->advance_payment : 0;
                $inquiry_status->payment_mode_id = isset($request->update_status['id']) && $request->update_status['id'] == 16 && isset($request->payment_mode['id']) ? $request->payment_mode['id'] : 0;
                $inquiry_status->receipt_no = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->receipt_no : '';
                $inquiry_status->cheque_no = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->cheque_no : '';
                $inquiry_status->bank = isset($request->update_status['id']) && $request->update_status['id'] == 16 ? $request->bank : '';
                $inquiry_status->realize_date = isset($request->update_status['id']) && $request->update_status['id'] == 16 && $request->realize_date != '' ? date('Y-m-d', strtotime($request->realize_date)) : '';
                $inquiry_status->remarks = $request->remarks;
                $inquiry_status->user_id = $request->session()->get('users_id');

                if ($inquiry_status->save()) {
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Updated,' . $inquiry_status->id . ',' . $inquiry_status->inquiry_id . ',' . $inquiry_status->update_date_time . ',' . $inquiry_status->inquiry_status_id . ',' . $inquiry_status->sales_team_id . ',' . $inquiry_status->site_inspection_date_time . ',' . $inquiry_status->advance_payment . ',' . $inquiry_status->payment_mode_id . ',' . $inquiry_status->receipt_no . ',' . str_replace(',', ' ', $inquiry_status->cheque_no) . ',' . str_replace(',', ' ', $inquiry_status->bank) . ',' . $inquiry_status->realize_date . ',' . str_replace(',', ' ', $inquiry_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);

                    $inquiry = \App\Model\Inquiry::find($inquiry_status->inquiry_id);
                    if ($inquiry->is_first_call_done == 0) {
                        $inquiry->is_first_call_done = 1;
                        $inquiry->save();
                    }

                    if ($inquiry_status->inquiry_status_id == 16) {
                        $inquiry->is_completed = 1;
                        $inquiry->save();

                        $job = new \App\Model\Job();
                        $job->inquiry_id = $inquiry->id;
                        $job->job_date_time = $inquiry_status->update_date_time;
                        $job->job_value = 0;
                        $job->user_id = $request->session()->get('users_id');
                        if ($job->save()) {
                            $job->job_no = 'J' . sprintf('%05d', $job->id);
                            $job->save();

                            $job_details = new \App\Model\JobDetails();
                            $job_details->job_id = $job->id;
                            $job_details->update_date_time = $job->job_date_time;
                            $job_details->job_status_id = 1;
                            $job_details->job_scheduled_date_time = '';
                            $job_details->remarks = 'Job value : Rs. 0.00';
                            $job_details->user_id = $request->session()->get('users_id');
                            $job_details->save();
                        }

                        $data = array(
                            'inquiry_id' => $inquiry->id,
                            'job_type' => $inquiry->IInquiryType->name,
                            'job_date_time' => $job->job_date_time,
                            'job_no' => $job->job_no,
                            'job_value' => $job->job_value,
                            'customer_name' => $inquiry->Contact->name,
                            'customer_address' => $inquiry->Contact->address,
                            'sales_person' => $inquiry->SalesTeam->name
                        );

                        Mail::send('emails.job_confirmation_notification', $data, function ($message) use ($quotation) {
                            $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                            $message->to('stores@m3force.com', 'Stores Assistant');
                            $message->to('procurement@m3force.com', 'Deepal Gunasekera');
                            $message->cc('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                            $message->cc('palitha@m3force.com', 'Palitha Wickramathunga');
                            $message->subject('M3Force Job Confirmation Details');
                        });
                    } else if ($inquiry_status->inquiry_status_id == 17 || $inquiry_status->inquiry_status_id == 18) {
                        $inquiry->is_completed = 1;
                        $inquiry->save();
                    }

                    $result = array(
                        'response' => true,
                        'message' => 'Inquiry Status updated successfully'
                    );
                } else {
                    $result = array(
                        'response' => false,
                        'message' => 'Inquiry Status updation failed'
                    );
                }
            } else if (!$drawing_uploaded) {
                $result = array(
                    'response' => false,
                    'message' => 'Site Drawing required'
                );
            } else if (!$quotation_confirmed) {
                $result = array(
                    'response' => false,
                    'message' => 'Quotation not confirmed'
                );
            } else if (!$installation_sheet_created) {
                $result = array(
                    'response' => false,
                    'message' => 'Installation Sheet required'
                );
            }
        } else if ($request->type == 2) {
            $document_upload = \App\Model\DocumentUpload::find($id);
            $document_upload->inquiry_id = $request->inquiry_id;
            $document_upload->document_type_id = isset($request->document_type['id']) ? $request->document_type['id'] : 0;
            $document_upload->document_name = $request->document_name;
            $document_upload->upload_document = $request->upload_document;

            if ($document_upload->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/document_upload_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Uploaded,' . $document_upload->id . ',' . $document_upload->inquiry_id . ',' . $document_upload->document_type_id . ',' . str_replace(',', ' ', $document_upload->document_name) . ',' . str_replace(',', ' ', $document_upload->upload_document) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                if ($document_upload->document_type_id == 1) {
                    $inquiry_status = new \App\Model\InquiryDetials();
                    $inquiry_status->inquiry_id = $document_upload->inquiry_id;
                    $inquiry_status->update_date_time = date('Y-m-d H:i');
                    $inquiry_status->inquiry_status_id = 5;
                    $inquiry_status->sales_team_id = 0;
                    $inquiry_status->site_inspection_date_time = '';
                    $inquiry_status->advance_payment = 0;
                    $inquiry_status->remarks = isset($request->document_type['name']) ? $request->document_type['name'] . ' - ' . $document_upload->document_name . ' ( ' . $request->doc_name . ' )' : $document_upload->document_name . ' ( ' . $request->doc_name . ' )';
                    $inquiry_status->user_id = $request->session()->get('users_id');
                    $inquiry_status->save();

                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $inquiry_status->id . ',' . $inquiry_status->inquiry_id . ',' . $inquiry_status->update_date_time . ',' . $inquiry_status->inquiry_status_id . ',' . $inquiry_status->sales_team_id . ',' . $inquiry_status->site_inspection_date_time . ',' . $inquiry_status->advance_payment . ',' . $inquiry_status->payment_mode_id . ',' . $inquiry_status->receipt_no . ',' . str_replace(',', ' ', $inquiry_status->cheque_no) . ',' . str_replace(',', ' ', $inquiry_status->bank) . ',' . $inquiry_status->realize_date . ',' . str_replace(',', ' ', $inquiry_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);
                }

                $result = array(
                    'response' => true,
                    'message' => 'Document updated successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Document updation failed'
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
        if ($request->type == 1) {
            $inquiry = \App\Model\Inquiry::find($id);
            $inquiry->is_delete = 1;

            if ($inquiry->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $inquiry->id . ',,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Inquiry deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inquiry deletion failed'
                );
            }
        } else if ($request->type == 0) {
            $inquiry_status = \App\Model\InquiryDetials::find($id);
            $inquiry_status->is_delete = 1;

            if ($inquiry_status->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $inquiry_status->id . ',,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Inquiry Status deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inquiry Status deletion failed'
                );
            }
        } else if ($request->type == 2) {
            $document_upload = \App\Model\DocumentUpload::find($id);
            $document_upload->is_delete = 1;

            if ($document_upload->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/document_upload_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $document_upload->id . ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Document Upload deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Document Upload deletion failed'
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
