<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class CustomerStatusController extends Controller
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
        return view('report.customer_status', $data);
    }

    public function get_data()
    {
        $expenses_types = \App\Model\ExpensesType::select('id', 'name')->orderBy('name')->get();

        $data = array(
            'expenses_types' => $expenses_types
        );

        return response($data);
    }

    public function find_expenses(Request $request)
    {
        $actual_expense = \App\Model\ActualExpenses::select('id', 'record_id', 'expenses_date_time', 'expenses_type_id', 'expenses_value')
            ->with(array('ExpensesType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->find($request->id);
        return response($actual_expense);
    }

    public function actual_expenses_list(Request $request)
    {
        $actual_expenses = \App\Model\ActualExpenses::select('id', 'record_id', 'expenses_date_time', 'expenses_type_id', 'expenses_value')
            ->with(array('ExpensesType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->where('record_id', $request->id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'actual_expenses' => $actual_expenses
        );

        return response($data);
    }

    public function get_customer_list(Request $request)
    {
        $data = array();
        if ($request->status_type == 1) {
            $inquiries = \App\Model\Inquiry::whereBetween('inquiry_date_time', array($request->from . ' 00:00:01', $request->to . ' 23:59:59'))
                ->where('is_delete', 0)
                ->get();
            foreach ($inquiries as $inquiry) {
                $job = \App\Model\Job::where('inquiry_id', $inquiry->id)->where('is_delete', 0)->first();
                $row = array(
                    'id' => $inquiry->id,
                    'date_time' => $inquiry->inquiry_date_time,
                    'document_no' => $job ? $job->job_no : $inquiry->inquiry_no,
                    'customer_name' => $inquiry->Contact ? $inquiry->Contact->name : '',
                    'customer_address' => $inquiry->Contact ? $inquiry->Contact->address : '',
                    'customer_contact_no' => $inquiry->Contact ? $inquiry->Contact->contact_no : '',
                    'type' => $inquiry->IInquiryType ? $inquiry->IInquiryType->name : ''
                );
                array_push($data, $row);
            }
        } else if ($request->status_type == 2) {
            $tech_responses = \App\Model\TechResponse::whereBetween('record_date_time', array($request->from . ' 00:00:01', $request->to . ' 23:59:59'))
                ->where('is_delete', 0)
                ->get();
            foreach ($tech_responses as $tech_response) {
                $row = array(
                    'id' => $tech_response->id,
                    'date_time' => $tech_response->record_date_time,
                    'document_no' => $tech_response->tech_response_no,
                    'customer_name' => $tech_response->Contact ? $tech_response->Contact->name : '',
                    'customer_address' => $tech_response->Contact ? $tech_response->Contact->address : '',
                    'customer_contact_no' => $tech_response->Contact ? $tech_response->Contact->contact_no : '',
                    'type' => $tech_response->TechResponseFault ? $tech_response->TechResponseFault->name : ''
                );
                array_push($data, $row);
            }
        }

        return response($data);
    }

    public function get_customer_status_details(Request $request)
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

        $data['status_type'] = $request->status_type_id;

        $data['result'] = $request->status_type_id == 1 ? \App\Model\Inquiry::find($request->record_id) : \App\Model\TechResponse::find($request->record_id);

        return view('report.customer_status_details', $data);
    }

    public function print_item_issue_balance(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $record = $request->status_type == 1 ? \App\Model\Inquiry::find($request->id) : \App\Model\TechResponse::find($request->id);
        $data['record'] = $record;
        $data['status_type'] = (int) $request->status_type;

        $html = view('report.item_issue_balance_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'] . '/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="' . $record->Contact->name . ' Item Issue Balance Details.pdf"');
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

    public function print_lost_profit(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        $record = \App\Model\Inquiry::find($request->id);
        $data['record'] = $record;
        $data['status_type'] = 1;

        $html = view('report.print_lost_profit_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'] . '/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="' . $record->Contact->name . ' Job Lost & Profit Details.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Landscape',
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
        $actual_expense = new \App\Model\ActualExpenses();
        $actual_expense->record_id = $request->record_id;
        $actual_expense->expenses_date_time = date('Y-m-d', strtotime($request->expenses_date)) . ' ' . $request->expenses_time;
        $actual_expense->expenses_type_id = isset($request->expenses_type['id']) ? $request->expenses_type['id'] : 0;
        $actual_expense->expenses_value = $request->expenses_value;

        if ($actual_expense->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/customer_status_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $actual_expense->id . ',' . $actual_expense->record_id . ',' . $actual_expense->expenses_date_time . ',' . $actual_expense->expenses_type_id . ',' . $actual_expense->expenses_value . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $result = array(
                'response' => true,
                'message' => 'Actual Expenses created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Actual Expenses creation failed'
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
        $actual_expense = \App\Model\ActualExpenses::find($id);
        $actual_expense->record_id = $request->record_id;
        $actual_expense->expenses_date_time = date('Y-m-d', strtotime($request->expenses_date)) . ' ' . $request->expenses_time;
        $actual_expense->expenses_type_id = isset($request->expenses_type['id']) ? $request->expenses_type['id'] : 0;
        $actual_expense->expenses_value = $request->expenses_value;

        if ($actual_expense->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/customer_status_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $actual_expense->id . ',' . $actual_expense->record_id . ',' . $actual_expense->expenses_date_time . ',' . $actual_expense->expenses_type_id . ',' . $actual_expense->expenses_value . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $result = array(
                'response' => true,
                'message' => 'Actual Expenses updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Actual Expenses updation failed'
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
    public function destroy($id)
    {
        $actual_expense = \App\Model\ActualExpenses::find($id);
        $actual_expense->is_delete = 1;

        if ($actual_expense->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/customer_status_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $actual_expense->id . ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $result = array(
                'response' => true,
                'message' => 'Actual Expenses deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Actual Expenses deletion failed'
            );
        }

        echo json_encode($result);
    }
}
