<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class JobController extends Controller
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

    public function new_job()
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

        return view('job.new_job', $data);
    }

    public function update_job(Request $request)
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

        $data['job_id'] = $request->id;

        return view('job.update_job', $data);
    }

    public function new_job_list(Request $request)
    {
        $data = array();
        $jobs = \App\Model\Job::whereHas('Inquiry', function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $request->inquiry_type_id != -1 ? $q->where('inquiry_type_id', $request->inquiry_type_id) : '';
            })
                ->where(function ($q) use ($request) {
                    $request->sales_team_id != -1 ? $q->where('sales_team_id', $request->sales_team_id) : '';
                });
        })
            ->where('is_job_scheduled', 0)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($jobs as $job) {
            $job_value = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $job_value += $quotation->quotation_value;
            }
            $job->job_value = $job_value;
            $job->save();

            $advance_value = 0;
            $inquiry_details = \App\Model\InquiryDetials::where('inquiry_id', $job->inquiry_id)
                ->where('inquiry_status_id', 16)
                ->where('is_delete', 0)
                ->get();
            foreach ($inquiry_details as $inquiry_detail) {
                $advance_value += $inquiry_detail->advance_payment;
            }

            $row = array(
                'id' => $job->id,
                'inquiry_id' => $job->inquiry_id,
                'job_no' => $job->job_no,
                'job_date_time' => $job->job_date_time,
                'job_type' => $job->Inquiry && $job->Inquiry->IInquiryType ? $job->Inquiry->IInquiryType->name : '',
                'customer_name' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->name : '',
                'customer_address' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->address : '',
                'customer_contact_no' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->contact_no : '',
                'client_type' => $job->Inquiry && $job->Inquiry->Contact && $job->Inquiry->Contact->IClientType ? $job->Inquiry->Contact->IClientType->name : '',
                'business_type' => $job->Inquiry && $job->Inquiry->Contact && $job->Inquiry->Contact->IBusinessType ? $job->Inquiry->Contact->IBusinessType->name : '',
                'sales_person' => $job->Inquiry && $job->Inquiry->SalesTeam ? $job->Inquiry->SalesTeam->name : '',
                'job_value' => $job->job_value,
                'advance_value' => $advance_value,
                'mandays' => $job->mandays,
                'log_user' => $job->Inquiry && $job->Inquiry->User ? $job->Inquiry->User->first_name : ''
            );
            array_push($data, $row);
        }

        return response($data);
    }

    public function validate_job_status(Request $request)
    {
        $avoid = array(4, 6);
        if ($request->value != $request->update_status && !in_array($request->update_status, $avoid)) {
            $job_details = \App\Model\JobDetails::where('job_id', $request->job_id)
                ->where('job_status_id', $request->update_status)
                ->where('is_delete', 0)
                ->first();
            if ($job_details) {
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
        $job_status = \App\Model\JobStatus::select('id', 'name')->where('show_update', 1)->get();
        return response($job_status);
    }

    public function find_job(Request $request)
    {
        $job = \App\Model\Job::select('id', 'inquiry_id', 'job_value')
            ->with(array('Inquiry' => function ($query) {
                $query->select('id', 'contact_id')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name', 'address', 'contact_no');
                    }));
            }))
            ->find($request->id);
        return response($job);
    }

    public function find_job_status(Request $request)
    {
        $job_status = \App\Model\JobDetails::select('id', 'job_id', 'update_date_time', 'job_status_id', 'job_scheduled_date_time', 'start_date', 'end_date', 'remarks')
            ->with(array('JobStatus' => function ($query) {
                $query->select('id', 'name');
            }))
            ->find($request->id);
        return response($job_status);
    }

    public function job_status_list(Request $request)
    {
        $job_status = \App\Model\JobDetails::select('id', 'job_id', 'update_date_time', 'job_status_id', 'job_scheduled_date_time', 'start_date', 'end_date', 'remarks', 'user_id')
            ->with(array('JobStatus' => function ($query) {
                $query->select('id', 'name', 'show_update');
            }))
            ->with(array('User' => function ($query) {
                $query->select('id', 'first_name');
            }))
            ->where('job_id', $request->job_id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'job_status' => $job_status,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function ongoing_job()
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

        return view('job.ongoing_job', $data);
    }

    public function ongoing_job_list(Request $request)
    {
        $data = array();
        $jobs = \App\Model\Job::whereHas('Inquiry', function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $request->inquiry_type_id != -1 ? $q->where('inquiry_type_id', $request->inquiry_type_id) : '';
            })
                ->where(function ($q) use ($request) {
                    $request->sales_team_id != -1 ? $q->where('sales_team_id', $request->sales_team_id) : '';
                });
        })
            ->where('is_job_scheduled', 1)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($jobs as $job) {
            $job_value = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $job_value += $quotation->quotation_value;
            }
            $job->job_value = $job_value;
            $job->save();

            $advance_value = 0;
            $inquiry_details = \App\Model\InquiryDetials::where('inquiry_id', $job->inquiry_id)
                ->where('inquiry_status_id', 16)
                ->where('is_delete', 0)
                ->get();
            foreach ($inquiry_details as $inquiry_detail) {
                $advance_value += $inquiry_detail->advance_payment;
            }

            $job_status = \App\Model\JobDetails::selectRaw('MAX(job_status_id) AS job_status_id')
                ->where('job_id', $job->id)
                ->where('is_delete', 0)
                ->first();
            if ($job_status && ($request->update_status_id == -1 || $request->update_status_id == $job_status->job_status_id)) {
                $used_mandays = 0;
                $job_attendances = \App\Model\JobAttendance::where('job_id', $job->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($job_attendances as $job_attendance) {
                    $used_mandays += $job_attendance->mandays;
                }

                $job_detail = \App\Model\JobDetails::where('job_id', $job->id)
                    ->where('job_status_id', $job_status->job_status_id)
                    ->where('is_delete', 0)
                    ->orderBy('update_date_time', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();
                $row = array(
                    'id' => $job->id,
                    'inquiry_id' => $job->inquiry_id,
                    'status_id' => $job_detail && $job_detail->JobStatus ? $job_detail->JobStatus->id : 0,
                    'job_no' => $job->job_no,
                    'job_date_time' => $job->job_date_time,
                    'job_type' => $job->Inquiry && $job->Inquiry->IInquiryType ? $job->Inquiry->IInquiryType->name : '',
                    'customer_name' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->name : '',
                    'customer_address' => $job->Inquiry && $job->Inquiry->Contact ? $job->Inquiry->Contact->address : '',
                    'client_type' => $job->Inquiry && $job->Inquiry->Contact && $job->Inquiry->Contact->IClientType ? $job->Inquiry->Contact->IClientType->name : '',
                    'business_type' => $job->Inquiry && $job->Inquiry->Contact && $job->Inquiry->Contact->IBusinessType ? $job->Inquiry->Contact->IBusinessType->name : '',
                    'update_date_time' => $job_detail ? $job_detail->update_date_time : '',
                    'update_status' => $job_detail && $job_detail->JobStatus ? $job_detail->JobStatus->name : '',
                    'remarks' => $job_detail ? $job_detail->remarks : '',
                    'sales_person' => $job->Inquiry && $job->Inquiry->SalesTeam ? $job->Inquiry->SalesTeam->name : '',
                    'job_value' => $job->job_value,
                    'advance_value' => $advance_value,
                    'mandays' => $job->mandays,
                    'used_mandays' => $used_mandays,
                    'log_user' => $job->Inquiry && $job->Inquiry->User ? $job->Inquiry->User->first_name : ''
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

        $data['job_id'] = $request->id;
        $job = \App\Model\Job::find($request->id);
        $data['inquiry_id'] = $job->Inquiry ? $job->Inquiry->id : 0;

        return view('job.upload_documents', $data);
    }

    public function advance_receipt()
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

        return view('job.advance_receipt', $data);
    }

    public function advance_receipt_list()
    {
        $inquiry_ids = array();
        $jobs = \App\Model\Job::where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($jobs as $job) {
            array_push($inquiry_ids, $job->inquiry_id);
        }

        $inquiry_details = \App\Model\InquiryDetials::select('id', 'inquiry_id', 'update_date_time', 'receipt_no', 'advance_payment')
            ->with(array('Inquiry' => function ($query) {
                $query->select('id', 'contact_id')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name', 'address');
                    }));
            }))
            ->whereIn('inquiry_id', $inquiry_ids)
            ->where('inquiry_status_id', 16)
            ->where('is_delete', 0)
            ->orderBy('update_date_time', 'DESC')
            ->get();

        return response($inquiry_details);
    }

    public function print_advance_receipt(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $inquiry_detail = \App\Model\InquiryDetials::find($request->id);
        $data['inquiry_detail'] = $inquiry_detail;
        $title = $inquiry_detail ? 'Advance Payment Details ' . $inquiry_detail->receipt_no : 'Advance Payment Details';

        $html = view('job.advance_receipt_pdf', $data);

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

    public function handover_documents(Request $request)
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

        return view('job.handover_documents', $data);
    }

    public function print_handover_documents(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $inquiry = \App\Model\Inquiry::find($request->id);
        $data['inquiry'] = $inquiry;

        if ($request->type == 1) {
            $title = 'Company Contact Details Document';
            $html = view('job.company_contact_details_document_pdf', $data);
        } else if ($request->type == 2) {
            $title = 'Installation Completion Acknowledgement Document';
            $html = view('job.customer_acknowledgement_document_pdf', $data);
        } else if ($request->type == 3) {
            header("Location: " . \Illuminate\Support\Facades\URL::to('/') . "/assets/uploads/Customer Feedback Form - Installations.docx");
            die();
        } else if ($request->type == 4) {
            header("Location: " . \Illuminate\Support\Facades\URL::to('/') . "/assets/uploads/Customer Detail Schedule - Monitoring.docx");
            die();
        } else {
            header("Location: " . \Illuminate\Support\Facades\URL::to('/') . "/assets/uploads/FO  SM  08 Monitoring & Response Agreement.doc");
            die();
        }

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
            $job = \App\Model\Job::find($request->job_id);
            $completed = isset($request->update_status['id']) && $request->update_status['id'] == 10 ? false : true;
            $remote_monitoring = $hand_over = $item_issue = $item_mismatch = true;
            if (!$completed) {
                if (in_array($job->Inquiry->inquiry_type_id, array(2, 4))) {
                    $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                        ->where('job_status_id', 8)
                        ->where('is_delete', 0)
                        ->first();
                    $remote_monitoring = $job_status ? true : false;
                }

                if (!in_array($job->Inquiry->inquiry_type_id, array(3, 6))) {
                    $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                        ->where('job_status_id', 9)
                        ->where('is_delete', 0)
                        ->first();
                    $hand_over = $job_status ? true : false;
                }

                $job_card_ids = array();
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
                        $item_issue = false;
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
                    $item_mismatch = false;
                }

                //////////////////////////////////

                $completed = $remote_monitoring && $hand_over && $item_issue && $item_mismatch;
            }

            if ($completed) {
                $job_status = new \App\Model\JobDetails();
                $job_status->job_id = $job->id;
                $job_status->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
                $job_status->job_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
                $job_status->job_scheduled_date_time = isset($request->update_status['id']) && $request->update_status['id'] == 4 ? date('Y-m-d', strtotime($request->job_scheduled_date)) . ' ' . $request->job_scheduled_time : '';
                $job_status->start_date = isset($request->update_status['id']) && $request->update_status['id'] == 10 ? date('Y-m-d', strtotime($request->start_date)) : '';
                $job_status->end_date = isset($request->update_status['id']) && $request->update_status['id'] == 10 ? date('Y-m-d', strtotime($request->end_date)) : '';
                $job_status->remarks = $request->remarks;
                $job_status->user_id = $request->session()->get('users_id');

                if ($job_status->save()) {
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/job_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $job_status->id . ',' . $job_status->job_id . ',' . $job_status->update_date_time . ',' . $job_status->job_status_id . ',' . $job_status->job_scheduled_date_time . ',' . $job_status->start_date . ',' . $job_status->end_date . ',' . str_replace(',', ' ', $job_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);

                    if ($job->is_job_scheduled == 0) {
                        $job->is_job_scheduled = 1;
                        $job->save();
                    }

                    if ($job_status->job_status_id == 10) {
                        $job->is_completed = 1;
                        $job->save();

                        $contact = \App\Model\Contact::find($job->Inquiry->contact_id);
                        if ($contact) {
                            $contact->start_date = $job_status->start_date;
                            $contact->end_date = $job_status->end_date;
                            $contact->save();
                            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
                            fwrite($myfile, 'Updated,' . $contact->id . ',' . $contact->contact_type_id . ',' . $contact->business_type_id . ',' . $contact->contact_id . ',' . $contact->code . ',' . str_replace(',', ' ', $contact->name) . ',' . str_replace(',', ' ', $contact->nic) . ',' . str_replace(',', ' ', $contact->address) . ',' . str_replace(',', ' ', $contact->contact_no) . ',' . str_replace(',', ' ', $contact->email) . ',' . $contact->region_id . ',' . $contact->collection_manager_id . ',' . str_replace(',', ' ', $contact->contact_person_1) . ',' . str_replace(',', ' ', $contact->contact_person_no_1) . ',' . str_replace(',', ' ', $contact->contact_person_2) . ',' . str_replace(',', ' ', $contact->contact_person_no_2) . ',' . str_replace(',', ' ', $contact->contact_person_3) . ',' . str_replace(',', ' ', $contact->contact_person_no_3) . ',' . $contact->start_date . ',' . $contact->end_date . ',' . str_replace(',', ' ', $contact->invoice_name) . ',' . str_replace(',', ' ', $contact->invoice_delivering_address) . ',' . str_replace(',', ' ', $contact->collection_address) . ',' . str_replace(',', ' ', $contact->invoice_email) . ',' . str_replace(',', ' ', $contact->vat_no) . ',' . str_replace(',', ' ', $contact->svat_no) . ',' . str_replace(',', ' ', $contact->monitoring_fee) . ',' . $contact->service_mode_id . ',' . $contact->client_type_id . ',' . $contact->group_id . ',' . $contact->is_group . ',' . $contact->is_active . ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                            fclose($myfile);
                        }

                        $quotation_ids = array();
                        $total_value = 0;
                        $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                            ->where('is_confirmed', 1)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($quotations as $quotation) {
                            array_push($quotation_ids, $quotation->id);
                            $total_value += $quotation->quotation_value;
                        }

                        $complete_job = new \App\Model\CompletedJobs();
                        $complete_job->inquiry_id = $job->inquiry_id;
                        $complete_job->completed_date = date('Y-m-d');
                        $complete_job->invoice_value = $total_value;
                        $complete_job->save();

                        $job_done_customer = \App\Model\JobDoneCustomer::where('contact_id', $job->Inquiry->contact_id)
                            ->where('is_delete', 0)
                            ->first();
                        if (!$job_done_customer) {
                            $job_done_customer = new \App\Model\JobDoneCustomer();
                            $job_done_customer->contact_id = $job->Inquiry->contact_id;
                            $job_done_customer->pending_amount = 0;
                        }
                        $job_done_customer->update_date = date('Y-m-d');
                        $job_done_customer->save();

                        foreach ($quotation_ids as $quotation_id) {
                            $job_done_customer_invoice = new \App\Model\JobDoneCustomerInvoice();
                            $job_done_customer_invoice->job_done_customer_id = $job_done_customer->id;
                            $job_done_customer_invoice->quotation_id = $quotation_id;

                            $last_id = 0;
                            $last_job_done_customer_invoice = \App\Model\JobDoneCustomerInvoice::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
                            $last_id = $last_job_done_customer_invoice ? $last_job_done_customer_invoice->id : $last_id;

                            $job_done_customer_invoice->invoice_date =  date('Y-m-d');
                            $job_done_customer_invoice->invoice_no = 'INV/JB/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
                            $job_done_customer_invoice->save();
                        }

                        $inquiry_status = \App\Model\InquiryDetials::where('inquiry_id', $job->inquiry_id)
                            ->where('inquiry_status_id', 16)
                            ->where('is_delete', 0)
                            ->get();
                        $total_advance = 0;
                        foreach ($inquiry_status as $inquiry_state) {
                            $job_done_customer_payment = new \App\Model\JobDoneCustomerPayment();
                            $job_done_customer_payment->job_done_customer_id = $job_done_customer->id;
                            $job_done_customer_payment->payment_mode_id = $inquiry_state->payment_mode_id;
                            $job_done_customer_payment->receipt_no = $inquiry_state->receipt_no;
                            $job_done_customer_payment->receipt_date_time = $inquiry_state->update_date_time;
                            $job_done_customer_payment->amount = $inquiry_state->advance_payment;
                            $job_done_customer_payment->cheque_no = $inquiry_state->cheque_no;
                            $job_done_customer_payment->bank = $inquiry_state->bank;
                            $job_done_customer_payment->realize_date = $inquiry_state->realize_date;
                            $job_done_customer_payment->remarks = $inquiry_state->remarks;
                            $job_done_customer_payment->save();
                            $total_advance += $inquiry_state->advance_payment;
                        }

                        $pending_amount = $job_done_customer->pending_amount;
                        $job_done_customer->pending_amount = $pending_amount + ($total_value - $total_advance);
                        $job_done_customer->save();

                        $total_payments = 0;
                        $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $job_done_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($job_done_customer_payments as $job_done_customer_payment) {
                            $total_payments += $job_done_customer_payment->amount;
                        }
                        $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $job_done_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($job_done_customer_invoices as $job_done_customer_invoice) {
                            if ($job_done_customer_invoice->Quotation->quotation_value <= $total_payments) {
                                $job_done_customer_invoice->payment_received = $job_done_customer_invoice->Quotation->quotation_value;
                                $job_done_customer_invoice->is_settled = 1;
                                $total_payments -= $job_done_customer_invoice->Quotation->quotation_value;
                            } else {
                                $job_done_customer_invoice->payment_received = $total_payments;
                                $job_done_customer_invoice->is_settled = 0;
                                $total_payments = 0;
                            }
                            $job_done_customer_invoice->save();
                        }
                    }

                    $result = array(
                        'response' => true,
                        'message' => 'Job Status created successfully'
                    );
                } else {
                    $result = array(
                        'response' => false,
                        'message' => 'Job Status creation failed'
                    );
                }
            } else if (!$remote_monitoring) {
                $result = array(
                    'response' => false,
                    'message' => 'Remote Monitoring not connected'
                );
            } else if (!$hand_over) {
                $result = array(
                    'response' => false,
                    'message' => 'Handover Document required'
                );
            } else if (!$item_issue) {
                $result = array(
                    'response' => false,
                    'message' => 'Job card items not issued'
                );
            } else if (!$item_mismatch) {
                $result = array(
                    'response' => false,
                    'message' => 'Job items mismatched'
                );
            }
        } else if ($request->type == 1) {
            $document_upload = new \App\Model\DocumentUpload();
            $document_upload->inquiry_id = $request->inquiry_id;
            $document_upload->document_type_id = isset($request->document_type['id']) ? $request->document_type['id'] : 0;
            $document_upload->document_name = $request->document_name;
            $document_upload->upload_document = $request->upload_document;

            if ($document_upload->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/document_upload_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Uploaded,' . $document_upload->id . ',' . $document_upload->inquiry_id . ',' . $document_upload->document_type_id . ',' . str_replace(',', ' ', $document_upload->document_name) . ',' . str_replace(',', ' ', $document_upload->upload_document) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                if ($document_upload->document_type_id == 2) {
                    $job_status = new \App\Model\JobDetails();
                    $job_status->job_id = $request->job_id;
                    $job_status->update_date_time = date('Y-m-d H:i');
                    $job_status->job_status_id = 9;
                    $job_status->job_scheduled_date_time = '';
                    $job_status->remarks = isset($request->document_type['name']) ? $request->document_type['name'] . ' - ' . $document_upload->document_name . ' ( ' . $request->doc_name . ' )' : $document_upload->document_name . ' ( ' . $request->doc_name . ' )';
                    $job_status->user_id = $request->session()->get('users_id');
                    $job_status->save();

                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/job_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $job_status->id . ',' . $job_status->job_id . ',' . $job_status->update_date_time . ',' . $job_status->job_status_id . ',' . $job_status->job_scheduled_date_time . ',' . str_replace(',', ' ', $job_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
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
        if ($request->type == 0) {
            $job = \App\Model\Job::find($request->job_id);
            $completed = isset($request->update_status['id']) && $request->update_status['id'] == 10 ? false : true;
            $remote_monitoring = $hand_over = $item_issue = true;
            if (!$completed) {
                if (in_array($job->Inquiry->inquiry_type_id, array(2, 4))) {
                    $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                        ->where('job_status_id', 8)
                        ->where('is_delete', 0)
                        ->first();
                    $remote_monitoring = $job_status ? true : false;
                }

                if (!in_array($job->Inquiry->inquiry_type_id, array(3, 6))) {
                    $job_status = \App\Model\JobDetails::where('job_id', $job->id)
                        ->where('job_status_id', 9)
                        ->where('is_delete', 0)
                        ->first();
                    $hand_over = $job_status ? true : false;
                }

                $job_card_ids = array();
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

                $item_issue = true;
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
                        $item_issue = false;
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
                    $item_mismatch = false;
                }

                //////////////////////////////////

                $completed = $remote_monitoring && $hand_over && $item_issue && $item_mismatch;
            }

            if ($completed) {
                $job_status = \App\Model\JobDetails::find($id);
                $job_status->job_id = $request->job_id;
                $job_status->update_date_time = date('Y-m-d', strtotime($request->update_date)) . ' ' . $request->update_time;
                $job_status->job_status_id = isset($request->update_status['id']) ? $request->update_status['id'] : 0;
                $job_status->job_scheduled_date_time = isset($request->update_status['id']) && $request->update_status['id'] == 4 ? date('Y-m-d', strtotime($request->job_scheduled_date)) . ' ' . $request->job_scheduled_time : '';
                $job_status->start_date = isset($request->update_status['id']) && $request->update_status['id'] == 10 ? date('Y-m-d', strtotime($request->start_date)) : '';
                $job_status->end_date = isset($request->update_status['id']) && $request->update_status['id'] == 10 ? date('Y-m-d', strtotime($request->end_date)) : '';
                $job_status->remarks = $request->remarks;
                $job_status->user_id = $request->session()->get('users_id');

                if ($job_status->save()) {
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/job_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Updated,' . $job_status->id . ',' . $job_status->job_id . ',' . $job_status->update_date_time . ',' . $job_status->job_status_id . ',' . $job_status->job_scheduled_date_time . ',' . $job_status->start_date . ',' . $job_status->end_date . ',' . str_replace(',', ' ', $job_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                    fclose($myfile);

                    if ($job->is_job_scheduled == 0) {
                        $job->is_job_scheduled = 1;
                        $job->save();
                    }

                    if ($job_status->job_status_id == 10) {
                        $job->is_completed = 1;
                        $job->save();

                        $contact = \App\Model\Contact::find($job->Inquiry->contact_id);
                        if ($contact) {
                            $contact->start_date = $job_status->start_date;
                            $contact->end_date = $job_status->end_date;
                            $contact->save();
                            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
                            fwrite($myfile, 'Updated,' . $contact->id . ',' . $contact->contact_type_id . ',' . $contact->business_type_id . ',' . $contact->contact_id . ',' . $contact->code . ',' . str_replace(',', ' ', $contact->name) . ',' . str_replace(',', ' ', $contact->nic) . ',' . str_replace(',', ' ', $contact->address) . ',' . str_replace(',', ' ', $contact->contact_no) . ',' . str_replace(',', ' ', $contact->email) . ',' . $contact->region_id . ',' . $contact->collection_manager_id . ',' . str_replace(',', ' ', $contact->contact_person_1) . ',' . str_replace(',', ' ', $contact->contact_person_no_1) . ',' . str_replace(',', ' ', $contact->contact_person_2) . ',' . str_replace(',', ' ', $contact->contact_person_no_2) . ',' . str_replace(',', ' ', $contact->contact_person_3) . ',' . str_replace(',', ' ', $contact->contact_person_no_3) . ',' . $contact->start_date . ',' . $contact->end_date . ',' . str_replace(',', ' ', $contact->invoice_name) . ',' . str_replace(',', ' ', $contact->invoice_delivering_address) . ',' . str_replace(',', ' ', $contact->collection_address) . ',' . str_replace(',', ' ', $contact->invoice_email) . ',' . str_replace(',', ' ', $contact->vat_no) . ',' . str_replace(',', ' ', $contact->svat_no) . ',' . str_replace(',', ' ', $contact->monitoring_fee) . ',' . $contact->service_mode_id . ',' . $contact->client_type_id . ',' . $contact->group_id . ',' . $contact->is_group . ',' . $contact->is_active . ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                            fclose($myfile);
                        }

                        $quotation_ids = array();
                        $total_value = 0;
                        $quotations = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                            ->where('is_confirmed', 1)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($quotations as $quotation) {
                            array_push($quotation_ids, $quotation->id);
                            $total_value += $quotation->quotation_value;
                        }

                        $complete_job = new \App\Model\CompletedJobs();
                        $complete_job->inquiry_id = $job->inquiry_id;
                        $complete_job->completed_date = date('Y-m-d');
                        $complete_job->invoice_value = $total_value;
                        $complete_job->save();

                        $job_done_customer = \App\Model\JobDoneCustomer::where('contact_id', $job->Inquiry->contact_id)
                            ->where('is_delete', 0)
                            ->first();
                        if (!$job_done_customer) {
                            $job_done_customer = new \App\Model\JobDoneCustomer();
                            $job_done_customer->contact_id = $job->Inquiry->contact_id;
                            $job_done_customer->pending_amount = 0;
                        }
                        $job_done_customer->update_date = date('Y-m-d');
                        $job_done_customer->save();

                        foreach ($quotation_ids as $quotation_id) {
                            $job_done_customer_invoice = new \App\Model\JobDoneCustomerInvoice();
                            $job_done_customer_invoice->job_done_customer_id = $job_done_customer->id;
                            $job_done_customer_invoice->quotation_id = $quotation_id;

                            $last_id = 0;
                            $last_job_done_customer_invoice = \App\Model\JobDoneCustomerInvoice::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
                            $last_id = $last_job_done_customer_invoice ? $last_job_done_customer_invoice->id : $last_id;

                            $job_done_customer_invoice->invoice_date =  date('Y-m-d');
                            $job_done_customer_invoice->invoice_no = 'INV/JB/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
                            $job_done_customer_invoice->save();
                        }

                        $inquiry_status = \App\Model\InquiryDetials::where('inquiry_id', $job->inquiry_id)
                            ->where('inquiry_status_id', 16)
                            ->where('is_delete', 0)
                            ->get();
                        $total_advance = 0;
                        foreach ($inquiry_status as $inquiry_state) {
                            $job_done_customer_payment = new \App\Model\JobDoneCustomerPayment();
                            $job_done_customer_payment->job_done_customer_id = $job_done_customer->id;
                            $job_done_customer_payment->payment_mode_id = $inquiry_state->payment_mode_id;
                            $job_done_customer_payment->receipt_no = $inquiry_state->receipt_no;
                            $job_done_customer_payment->receipt_date_time = $inquiry_state->update_date_time;
                            $job_done_customer_payment->amount = $inquiry_state->advance_payment;
                            $job_done_customer_payment->cheque_no = $inquiry_state->cheque_no;
                            $job_done_customer_payment->bank = $inquiry_state->bank;
                            $job_done_customer_payment->realize_date = $inquiry_state->realize_date;
                            $job_done_customer_payment->remarks = $inquiry_state->remarks;
                            $job_done_customer_payment->save();
                            $total_advance += $inquiry_state->advance_payment;
                        }

                        $pending_amount = $job_done_customer->pending_amount;
                        $job_done_customer->pending_amount = $pending_amount + ($total_value - $total_advance);
                        $job_done_customer->save();

                        $total_payments = 0;
                        $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $job_done_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($job_done_customer_payments as $job_done_customer_payment) {
                            $total_payments += $job_done_customer_payment->amount;
                        }
                        $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $job_done_customer->id)
                            ->where('is_delete', 0)
                            ->get();
                        foreach ($job_done_customer_invoices as $job_done_customer_invoice) {
                            if ($job_done_customer_invoice->Quotation->quotation_value <= $total_payments) {
                                $job_done_customer_invoice->payment_received = $job_done_customer_invoice->Quotation->quotation_value;
                                $job_done_customer_invoice->is_settled = 1;
                                $total_payments -= $job_done_customer_invoice->Quotation->quotation_value;
                            } else {
                                $job_done_customer_invoice->payment_received = $total_payments;
                                $job_done_customer_invoice->is_settled = 0;
                                $total_payments = 0;
                            }
                            $job_done_customer_invoice->save();
                        }
                    }

                    $result = array(
                        'response' => true,
                        'message' => 'Job Status updated successfully'
                    );
                } else {
                    $result = array(
                        'response' => false,
                        'message' => 'Job Status updation failed'
                    );
                }
            } else if (!$remote_monitoring) {
                $result = array(
                    'response' => false,
                    'message' => 'Remote Monitoring not connected'
                );
            } else if (!$hand_over) {
                $result = array(
                    'response' => false,
                    'message' => 'Handover Document required'
                );
            } else if (!$item_issue) {
                $result = array(
                    'response' => false,
                    'message' => 'Job card items not issued'
                );
            } else if (!$item_mismatch) {
                $result = array(
                    'response' => false,
                    'message' => 'Job items mismatched'
                );
            }
        } else if ($request->type == 1) {
            $document_upload = \App\Model\DocumentUpload::find($id);
            $document_upload->inquiry_id = $request->inquiry_id;
            $document_upload->document_type_id = isset($request->document_type['id']) ? $request->document_type['id'] : 0;
            $document_upload->document_name = $request->document_name;
            $document_upload->upload_document = $request->upload_document;

            if ($document_upload->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/document_upload_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Uploaded,' . $document_upload->id . ',' . $document_upload->inquiry_id . ',' . $document_upload->document_type_id . ',' . str_replace(',', ' ', $document_upload->document_name) . ',' . str_replace(',', ' ', $document_upload->upload_document) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                if ($document_upload->document_type_id == 2) {
                    $job_status = new \App\Model\JobDetails();
                    $job_status->job_id = $request->job_id;
                    $job_status->update_date_time = date('Y-m-d H:i');
                    $job_status->job_status_id = 9;
                    $job_status->job_scheduled_date_time = '';
                    $job_status->remarks = isset($request->document_type['name']) ? $request->document_type['name'] . ' - ' . $document_upload->document_name . ' ( ' . $request->doc_name . ' )' : $document_upload->document_name . ' ( ' . $request->doc_name . ' )';
                    $job_status->user_id = $request->session()->get('users_id');
                    $job_status->save();

                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/job_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $job_status->id . ',' . $job_status->job_id . ',' . $job_status->update_date_time . ',' . $job_status->job_status_id . ',' . $job_status->job_scheduled_date_time . ',' . str_replace(',', ' ', $job_status->remarks) . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
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
        if ($request->type == 0) {
            $job_status = \App\Model\JobDetails::find($id);
            $job_status->is_delete = 1;

            if ($job_status->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/job_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $job_status->id . ',,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $result = array(
                    'response' => true,
                    'message' => 'Job Status deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Job Status deletion failed'
                );
            }
        } else if ($request->type == 1) {
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
