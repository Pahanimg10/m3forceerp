<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class CostSheetController extends Controller
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

        $data['cost_sheet_id'] = $request->id;
        $data['inquiry_id'] = $request->inquiry_id;
        $data['view'] = $request->view;
        $data['type'] = $request->type;

        return view('inquiry.cost_sheet_detail', $data);
    }

    public function get_data()
    {
        $installation_rates = \App\Model\InstallationRate::select('id', 'code', 'name', 'installation_cost', 'labour', 'rate')->where('is_delete', 0)->orderBy('name')->get();
        $manday_rate = \App\Model\Rate::find(1);

        $data = [
            'installation_rates' => $installation_rates,
            'manday_rate' => $manday_rate,
        ];

        return response($data);
    }

    public function cost_sheet_list(Request $request)
    {
        $cost_sheets = \App\Model\CostSheet::select('id', 'inquiry_id', 'cost_sheet_no', 'cost_sheet_date_time', 'remarks', 'cost_sheet_value', 'installation_value', 'mandays', 'is_used', 'user_id')
                ->with(['User' => function ($query) {
                    $query->select('id', 'first_name');
                }])
                ->where('inquiry_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $inquiry = \App\Model\Inquiry::select('id', 'inquiry_no')->find($request->id);

        $data = [
            'cost_sheets' => $cost_sheets,
            'inquiry' => $inquiry,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function find_cost_sheet(Request $request)
    {
        $cost_sheet = \App\Model\CostSheet::select('id', 'inquiry_id', 'cost_sheet_no', 'cost_sheet_date_time', 'installation_rate_id', 'meters', 'excavation_work', 'transport', 'traveling_mandays', 'food', 'accommodation', 'bata', 'other_expenses', 'remarks', 'cost_sheet_value', 'installation_value', 'mandays', 'is_used', 'user_id')
                ->with(['InstallationRate' => function ($query) {
                    $query->select('id', 'name', 'installation_cost', 'labour', 'rate');
                }])
                ->find($request->id);

        return response($cost_sheet);
    }

    public function print_cost_sheet(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $cost_sheet = \App\Model\CostSheet::find($request->id);
        $data['cost_sheet'] = $cost_sheet;
        $title = $cost_sheet ? 'Cost Sheet Details '.$cost_sheet->cost_sheet_no : 'Cost Sheet Details';

        $html = view('inquiry.cost_sheet_pdf', $data);

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
        $cost_sheet = new \App\Model\CostSheet();
        $cost_sheet->inquiry_id = $request->inquiry_id;

        $last_id = 0;
        $last_cost_sheet = \App\Model\CostSheet::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
        $last_id = $last_cost_sheet ? $last_cost_sheet->id : $last_id;
        $cost_sheet->cost_sheet_no = 'CS/'.date('m').'/'.date('y').'/'.$request->inquiry_id.'/'.sprintf('%05d', $last_id + 1);

        $rate = \App\Model\Rate::find(1);

        $cost_sheet->cost_sheet_date_time = date('Y-m-d', strtotime($request->cost_sheet_date)).' '.$request->cost_sheet_time;
        $cost_sheet->installation_rate_id = isset($request->installation_rate['id']) ? $request->installation_rate['id'] : 0;
        $cost_sheet->meters = $request->meters;
        $cost_sheet->excavation_work = $request->excavation_work;
        $cost_sheet->transport = $request->transport;
        $cost_sheet->traveling_mandays = $request->traveling_mandays;
        $cost_sheet->food = $request->food;
        $cost_sheet->accommodation = $request->accommodation;
        $cost_sheet->bata = $request->bata;
        $cost_sheet->other_expenses = $request->other_expenses;
        $cost_sheet->remarks = $request->remarks;
        $cost_sheet->cost_sheet_value = isset($request->installation_rate['rate']) ? ($request->installation_rate['rate'] * $request->meters) + $request->excavation_work + $request->transport + ($request->traveling_mandays * $rate->value) + $request->food + $request->accommodation + $request->bata + $request->other_expenses : 0;
        $cost_sheet->installation_value = isset($request->installation_rate['installation_cost']) ? ($request->installation_rate['installation_cost'] * $request->meters) + ($request->other_expenses / 2) : ($request->other_expenses / 2);

        $cost_sheet->mandays = isset($request->installation_rate['labour']) ? ($request->installation_rate['labour'] * $request->meters / $rate->value) + $request->traveling_mandays + ($request->other_expenses / ($rate->value * 2)) : $request->other_expenses / ($rate->value * 2);

        $cost_sheet->user_id = $request->session()->get('users_id');

        if ($cost_sheet->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/cost_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$cost_sheet->id.','.$cost_sheet->inquiry_id.','.$cost_sheet->cost_sheet_no.','.$cost_sheet->cost_sheet_date_time.','.$cost_sheet->installation_rate_id.','.$cost_sheet->meters.','.$cost_sheet->excavation_work.','.$cost_sheet->transport.','.$cost_sheet->traveling_mandays.','.$cost_sheet->food.','.$cost_sheet->accommodation.','.$cost_sheet->bata.','.$cost_sheet->other_expenses.','.str_replace(',', ' ', $cost_sheet->remarks).','.$cost_sheet->cost_sheet_value.','.$cost_sheet->installation_value.','.$cost_sheet->mandays.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $inquiry_status = new \App\Model\InquiryDetials();
            $inquiry_status->inquiry_id = $cost_sheet->inquiry_id;
            $inquiry_status->update_date_time = date('Y-m-d H:i');
            $inquiry_status->inquiry_status_id = 7;
            $inquiry_status->sales_team_id = 0;
            $inquiry_status->site_inspection_date_time = '';
            $inquiry_status->advance_payment = 0;
            $inquiry_status->remarks = $cost_sheet->cost_sheet_no;
            $inquiry_status->user_id = $request->session()->get('users_id');
            $inquiry_status->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$inquiry_status->id.','.$inquiry_status->inquiry_id.','.$inquiry_status->update_date_time.','.$inquiry_status->inquiry_status_id.','.$inquiry_status->sales_team_id.','.$inquiry_status->site_inspection_date_time.','.$inquiry_status->advance_payment.','.$inquiry_status->payment_mode_id.','.$inquiry_status->receipt_no.','.str_replace(',', ' ', $inquiry_status->cheque_no).','.str_replace(',', ' ', $inquiry_status->bank).','.$inquiry_status->realize_date.','.str_replace(',', ' ', $inquiry_status->remarks).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Cost Sheet created successfully',
                'data' => $cost_sheet->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Cost Sheet creation failed',
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
        $rate = \App\Model\Rate::find(1);

        $cost_sheet = \App\Model\CostSheet::find($request->cost_sheet_id);
        $cost_sheet->inquiry_id = $request->inquiry_id;
        $cost_sheet->cost_sheet_no = $request->cost_sheet_no;
        $cost_sheet->cost_sheet_date_time = date('Y-m-d', strtotime($request->cost_sheet_date)).' '.$request->cost_sheet_time;
        $cost_sheet->installation_rate_id = isset($request->installation_rate['id']) ? $request->installation_rate['id'] : 0;
        $cost_sheet->meters = $request->meters;
        $cost_sheet->excavation_work = $request->excavation_work;
        $cost_sheet->transport = $request->transport;
        $cost_sheet->traveling_mandays = $request->traveling_mandays;
        $cost_sheet->food = $request->food;
        $cost_sheet->accommodation = $request->accommodation;
        $cost_sheet->bata = $request->bata;
        $cost_sheet->other_expenses = $request->other_expenses;
        $cost_sheet->remarks = $request->remarks;
        $cost_sheet->cost_sheet_value = isset($request->installation_rate['rate']) ? ($request->installation_rate['rate'] * $request->meters) + $request->excavation_work + $request->transport + ($request->traveling_mandays * $rate->value) + $request->food + $request->accommodation + $request->bata + $request->other_expenses : 0;
        $cost_sheet->installation_value = isset($request->installation_rate['installation_cost']) ? ($request->installation_rate['installation_cost'] * $request->meters) + ($request->other_expenses / 2) : ($request->other_expenses / 2);

        $cost_sheet->mandays = isset($request->installation_rate['labour']) ? ($request->installation_rate['labour'] * $request->meters / $rate->value) + $request->traveling_mandays + ($request->other_expenses / ($rate->value * 2)) : $request->other_expenses / ($rate->value * 2);

        $cost_sheet->user_id = $request->session()->get('users_id');

        if ($cost_sheet->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/cost_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$cost_sheet->id.','.$cost_sheet->inquiry_id.','.$cost_sheet->cost_sheet_no.','.$cost_sheet->cost_sheet_date_time.','.$cost_sheet->installation_rate_id.','.$cost_sheet->meters.','.$cost_sheet->excavation_work.','.$cost_sheet->transport.','.$cost_sheet->traveling_mandays.','.$cost_sheet->food.','.$cost_sheet->accommodation.','.$cost_sheet->bata.','.$cost_sheet->other_expenses.','.str_replace(',', ' ', $cost_sheet->remarks).','.$cost_sheet->cost_sheet_value.','.$cost_sheet->installation_value.','.$cost_sheet->mandays.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Cost Sheet updated successfully',
                'data' => $cost_sheet->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Cost Sheet updation failed',
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
        $cost_sheet = \App\Model\CostSheet::find($id);
        $cost_sheet->is_delete = 1;

        if ($cost_sheet->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/cost_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$cost_sheet->id.',,,,,,,,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Cost Sheet deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Cost Sheet deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
