<?php



namespace App\Http\Controllers;



require_once('ESMSWS.php');

session_start();

date_default_timezone_set('Asia/Colombo');

set_time_limit(0);



use Illuminate\Http\Request;



use App\Http\Requests;

use Illuminate\Support\Facades\Mail;



class TechResponseJobCardController extends Controller

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



        $data['tech_response_job_card_id'] = $request->id;

        $data['tech_response_id'] = $request->tech_response_id;

        $data['view'] = $request->view;



        return view('tech_response.tech_response_job_card_detail', $data);
    }



    public function tech_response_job_card_list(Request $request)

    {

        $tech_response_job_cards = \App\Model\TechResponseJobCard::select('id', 'tech_response_id', 'tech_response_job_card_no', 'tech_response_job_card_date_time', 'remarks', 'tech_response_job_card_value', 'is_used', 'user_id', 'is_posted', 'is_approved')

            ->with(array('User' => function ($query) {

                $query->select('id', 'first_name');
            }))

            ->where('tech_response_id', $request->id)

            ->where('is_delete', 0)

            ->get();

        $tech_response = \App\Model\TechResponse::select('id', 'tech_response_no')->find($request->id);



        $data = array(

            'tech_response_job_cards' => $tech_response_job_cards,

            'tech_response' => $tech_response,

            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))

        );



        return response($data);
    }



    public function find_tech_response_job_card(Request $request)

    {

        $tech_response_job_card = \App\Model\TechResponseJobCard::select('id', 'tech_response_id', 'tech_response_job_card_no', 'tech_response_job_card_date_time', 'remarks', 'tech_response_job_card_value', 'is_used', 'user_id', 'is_posted', 'is_approved')

            ->find($request->id);



        $data = array(

            'tech_response_job_card' => $tech_response_job_card,

            'permission' => !in_array(session()->get('users_id'), array(1, 12, 59, 71))

        );



        return response($data);
    }



    public function find_tech_response_job_card_detail(Request $request)

    {

        $tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::select('id', 'tech_response_job_card_id', 'item_id', 'rate', 'quantity', 'margin', 'is_main', 'is_chargeable')

            ->with(array('TechResponseJobCard' => function ($query) {

                $query->select('id', 'tech_response_id', 'tech_response_job_card_no', 'tech_response_job_card_date_time', 'remarks', 'is_posted', 'is_approved');
            }))

            ->with(array('Item' => function ($query) {

                $query->select('id', 'main_category_id', 'sub_category_id', 'code', 'name', 'unit_type_id')

                    ->with(array('MainItemCategory' => function ($query) {

                        $query->select('id', 'code', 'name');
                    }))

                    ->with(array('SubItemCategory' => function ($query) {

                        $query->select('id', 'code', 'name');
                    }))

                    ->with(array('UnitType' => function ($query) {

                        $query->select('id', 'code', 'name');
                    }));
            }))

            ->find($request->id);

        return response($tech_response_job_card_detail);
    }



    public function tech_response_job_card_detail_list(Request $request)

    {

        $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::select('id', 'item_id', 'rate', 'quantity', 'margin', 'is_main', 'is_chargeable')

            ->with(array('Item' => function ($query) {

                $query->select('id', 'code', 'name', 'unit_type_id', 'stock')

                    ->with(array('UnitType' => function ($query) {

                        $query->select('id', 'code', 'name');
                    }));
            }))

            ->where('tech_response_job_card_id', $request->id)

            ->where('is_delete', 0)

            ->get();

        $tech_response_job_card = \App\Model\TechResponseJobCard::select('is_used', 'is_posted')->find($request->id);



        $data = array(

            'tech_response_job_card_details' => $tech_response_job_card_details,

            'tech_response_job_card' => $tech_response_job_card,

            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))

        );



        return response($data);
    }



    public function post_tech_response_job_card(Request $request)

    {

        $tech_response_job_card = \App\Model\TechResponseJobCard::find($request->id);



        $is_posted = $tech_response_job_card->is_posted == 0 ? true : false;



        $tech_response_job_card->is_posted = 1;



        if ($tech_response_job_card->save()) {

            if ($is_posted) {

                $sms = '--- Tech Response Item Issue Authorization ---' . PHP_EOL;

                $sms .= 'Tech Response Job Card No : ' . $tech_response_job_card->tech_response_job_card_no . PHP_EOL;

                $sms .= 'Customer Name : ' . $tech_response_job_card->TechResponse->Contact->name . PHP_EOL;

                $sms .= 'Customer Address : ' . $tech_response_job_card->TechResponse->Contact->address . PHP_EOL;

                $sms .= 'Logged User : ' . $tech_response_job_card->User->first_name . ' ' . $tech_response_job_card->User->last_name . PHP_EOL;

                $sms .= 'URL : http://erp.m3force.com/m3force/public/tech_response_job_card/add_new?view=0&id=' . $tech_response_job_card->id . '&tech_response_id=' . $tech_response_job_card->tech_response_id;



                $session = createSession('', 'esmsusr_1na2', '3p4lfqe', '');

                sendMessages($session, 'M3FORCE', $sms, array('0704599310', '0704599321', '0772030007'));



                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Posted,' . $tech_response_job_card->id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);



                $data = array(

                    'tech_response_job_card_no' => $tech_response_job_card->tech_response_job_card_no,

                    'customer_name' => $tech_response_job_card->TechResponse->Contact->name,

                    'customer_address' => $tech_response_job_card->TechResponse->Contact->address,

                    'logged_user' => $tech_response_job_card->User->first_name . ' ' . $tech_response_job_card->User->last_name,

                    'id' => $tech_response_job_card->id,

                    'tech_response_id' => $tech_response_job_card->tech_response_id

                );



                Mail::send('emails.tech_response_job_card_authorization', $data, function ($message) use ($tech_response_job_card) {

                    $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');

                    $message->to('sanjaya@m3force.com', 'Sanjaya Perera');

                    $message->to('palitha@m3force.com', 'Palitha Wickramatunga');

                    $message->to('bandara@m3force.com', 'Lakshitha Bandara');

                    $message->subject('Tech Response Item Issue Authorization - ' . $tech_response_job_card->TechResponse->Contact->name);
                });
            }



            $result = array(

                'response' => true,

                'message' => 'Tech Response Job Card posted successfully'

            );
        } else {

            $result = array(

                'response' => false,

                'message' => 'Tech Response Job Card post failed'

            );
        }



        echo json_encode($result);
    }



    public function get_authorize_data(Request $request)

    {

        $job_card_items = $installation_items = array();

        $job_card_details = \App\Model\TechResponseJobCardDetails::selectRaw('id, tech_response_job_card_id, item_id, SUM(quantity) as total_quantity')

            ->whereHas('TechResponseJobCard', function ($query) use ($request) {

                $query->where('tech_response_id', $request->id)->where('is_posted', 1)->where('is_delete', 0);
            })

            ->where('is_delete', 0)

            ->groupBy('item_id')

            ->get();

        $approved_job_card_ids = array();

        foreach ($job_card_details as $job_card_detail) {

            if (!in_array($job_card_detail->tech_response_job_card_id, $approved_job_card_ids)) {

                array_push($approved_job_card_ids, $job_card_detail->tech_response_job_card_id);
            }

            $row = array(

                'id' => $job_card_detail->Item->id,

                'code' => $job_card_detail->Item->code,

                'name' => $job_card_detail->Item->name,

                'quantity' => $job_card_detail->total_quantity

            );

            array_push($job_card_items, $row);
        }

        $installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::selectRaw('id, tech_response_installation_sheet_id, item_id, SUM(quantity) as total_quantity')

            ->whereHas('TechResponseInstallationSheet', function ($query) use ($request) {

                $query->where('tech_response_id', $request->id)->where('is_posted', 1)->where('is_delete', 0);
            })

            ->where('is_delete', 0)

            ->groupBy('item_id')

            ->get();

        $approved_installation_sheet_ids = array();

        foreach ($installation_sheet_details as $installation_sheet_detail) {

            if (!in_array($installation_sheet_detail->tech_response_installation_sheet_id, $approved_installation_sheet_ids)) {

                array_push($approved_installation_sheet_ids, $installation_sheet_detail->tech_response_installation_sheet_id);
            }

            $row = array(

                'id' => $installation_sheet_detail->Item->id,

                'code' => $installation_sheet_detail->Item->code,

                'name' => $installation_sheet_detail->Item->name,

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

                    'code' => $job_card_main_item['code'],

                    'name' => $job_card_main_item['name'],

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

                    'code' => $installation_main_item['code'],

                    'name' => $installation_main_item['name'],

                    'quantity' => $total_qunatity

                );

                array_push($request_items, $row);

                array_push($request_ids, $installation_main_item['id']);
            }
        }



        $view = '';

        if (count($request_items) > 0) {

            $view .= '

                    <table id="data_table" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;">

                        <thead>

                            <tr>

                                <th rowspan="2" style="text-align: center; vertical-align: middle;">No#</th>

                                <th rowspan="2" style="text-align: center; vertical-align: middle;">Code</th>

                                <th rowspan="2" style="text-align: center; vertical-align: middle;">Description</th>

                                <th colspan="4" style="text-align: center; vertical-align: middle;">Quantity</th>

                            </tr>

                            <tr>

                                <th style="text-align: center; vertical-align: middle;">Requested</th>

                                <th style="text-align: center; vertical-align: middle;">Issued</th>

                                <th style="text-align: center; vertical-align: middle;">Received</th>

                                <th style="text-align: center; vertical-align: middle;">Balance</th>

                            </tr>

                        </thead>

                        <tbody>

                ';

            foreach ($request_items as $index => $value) {

                $view .= '

                    <tr>

                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;">' . ($index + 1) . '</td>

                        <td style="vertical-align: middle; white-space: nowrap;">' . $value['code'] . '</td>

                        <td style="vertical-align: middle;">' . $value['name'] . '</td>

                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">' . $value['quantity'] . '</td>

                    ';

                $issued_quantity = 0;

                $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {

                    $query->where('item_issue_type_id', 2)

                        ->where('document_id', $request->id)

                        ->where('is_posted', 1)

                        ->where('is_delete', 0);
                })

                    ->where('item_id', $value['id'])

                    ->where('is_delete', 0)

                    ->get();

                foreach ($item_issue_details as $item_issue_detail) {

                    $issued_quantity += $item_issue_detail->quantity;
                }

                $received_quantity = 0;

                $item_issue_ids = array();

                $item_issues = \App\Model\ItemIssue::where('item_issue_type_id', 2)

                    ->where('document_id', $request->id)

                    ->where('is_posted', 1)

                    ->where('is_delete', 0)

                    ->get();

                foreach ($item_issues as $item_issue) {

                    if (!in_array($item_issue->id, $item_issue_ids)) {

                        array_push($item_issue_ids, $item_issue->id);
                    }
                }

                $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($item_issue_ids) {

                    $query->whereIn('item_issue_id', $item_issue_ids)

                        ->where('is_posted', 1)

                        ->where('is_delete', 0);
                })

                    ->where('item_id', $value['id'])

                    ->where('is_delete', 0)

                    ->get();

                foreach ($item_receive_details as $item_receive_detail) {

                    $received_quantity += $item_receive_detail->quantity;
                }

                $view .= '

                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">' . $issued_quantity . '</td>

                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">' . $received_quantity . '</td>

                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">' . ($value['quantity'] - $issued_quantity + $received_quantity) . '</td>

                    ';
            }

            $view .= '

                        </tbody>

                    </table>

                ';
        }



        $result = array(

            'view' => $view,

            'approved_job_card_ids' => $approved_job_card_ids,

            'approved_installation_sheet_ids' => $approved_installation_sheet_ids

        );



        echo json_encode($result);
    }



    public function approve_tech_response_items(Request $request)

    {

        $tech_response_job_cards = \App\Model\TechResponseJobCard::where('tech_response_id', $request->tech_response_id)

            ->where('is_posted', 1)

            ->where('is_approved', 0)

            ->where('is_delete', 0)

            ->get();

        foreach ($tech_response_job_cards as $tech_response_job_card) {

            $tech_response_job_card->is_approved = 1;

            $tech_response_job_card->save();



            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

            fwrite($myfile, 'Approved,' . $tech_response_job_card->id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

            fclose($myfile);
        }

        $tech_response_installation_sheets = \App\Model\TechResponseInstallationSheet::where('tech_response_id', $request->tech_response_id)

            ->where('is_posted', 1)

            ->where('is_approved', 0)

            ->where('is_delete', 0)

            ->get();

        foreach ($tech_response_installation_sheets as $tech_response_installation_sheet) {

            $tech_response_installation_sheet->is_approved = 1;

            $tech_response_installation_sheet->save();



            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');

            fwrite($myfile, 'Approved,' . $tech_response_installation_sheet->id . ',,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

            fclose($myfile);
        }

        $tech_response = \App\Model\TechResponse::find($request->tech_response_id);
        $data = array(
            'id' => $tech_response->id,
            'type' => 2,
            'customer_name' => $tech_response->Contact->name,
            'customer_address' => $tech_response->Contact->address
        );

        Mail::send('emails.installation_update_notification', $data, function ($message) {
            $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
            $message->to('stores@m3force.com', 'Nalin Silva');
            $message->to('procurement@m3force.com', 'Deepal Gunasekera');
            $message->subject('M3Force Customer Installation Update Details');
        });



        $result = array(

            'response' => true,

            'message' => 'Tech Response Items approved successfully'

        );



        echo json_encode($result);
    }



    public function print_tech_response_job_card(Request $request)

    {

        $data['company'] = \App\Model\Company::find(1);



        $tech_response_job_card = \App\Model\TechResponseJobCard::find($request->id);

        $data['tech_response_job_card'] = $tech_response_job_card;

        $title = $tech_response_job_card ? 'Tech Response Job Card Details ' . $tech_response_job_card->tech_response_job_card_no : 'Tech Response Job Card Details';



        $html = view('tech_response.tech_response_job_card_pdf', $data);



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

        $exist = false;

        $tech_response_job_card = \App\Model\TechResponseJobCard::find($request->tech_response_job_card_id);



        if (!$tech_response_job_card) {

            $exist = true;

            $tech_response_job_card = new \App\Model\TechResponseJobCard();

            $last_id = 0;

            $last_tech_response_job_card = \App\Model\TechResponseJobCard::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();

            $last_id = $last_tech_response_job_card ? $last_tech_response_job_card->id : $last_id;

            $tech_response_job_card->tech_response_job_card_no = 'TR/JC/' . date('m') . '/' . date('y') . '/' . $request->tech_response_id . '/' . sprintf('%05d', $last_id + 1);
        }



        $tech_response_job_card->tech_response_id = $request->tech_response_id;

        $tech_response_job_card->tech_response_job_card_date_time = date('Y-m-d', strtotime($request->tech_response_job_card_date)) . ' ' . $request->tech_response_job_card_time;

        $tech_response_job_card->remarks = $request->remarks;

        $tech_response_job_card->user_id = $request->session()->get('users_id');



        if ($tech_response_job_card->save()) {

            if ($exist) {

                $tech_response_status = new \App\Model\TechResponseDetails();

                $tech_response_status->tech_response_id = $tech_response_job_card->tech_response_id;

                $tech_response_status->update_date_time = date('Y-m-d H:i');

                $tech_response_status->tech_response_status_id = 2;

                $tech_response_status->job_scheduled_date_time = '';

                $tech_response_status->is_chargeable = 0;

                $tech_response_status->remarks = $tech_response_job_card->tech_response_job_card_no;

                $tech_response_status->user_id = $request->session()->get('users_id');

                $tech_response_status->save();
            }



            $tech_response_job_card_detail_id = '';

            if (isset($request->item['id'])) {

                $old_tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card->id)

                    ->where('item_id', $request->item['id'])

                    ->where('is_main', $request->is_main)

                    ->where('is_chargeable', $request->is_chargeable)

                    ->first();



                $tech_response_job_card_detail = $old_tech_response_job_card_detail ? $old_tech_response_job_card_detail : new \App\Model\TechResponseJobCardDetails();

                $tech_response_job_card_detail->tech_response_job_card_id = $tech_response_job_card->id;

                $tech_response_job_card_detail->item_id = $request->item['id'];

                $tech_response_job_card_detail->rate = $request->rate;

                $tech_response_job_card_detail->quantity = $old_tech_response_job_card_detail ? $old_tech_response_job_card_detail->quantity + $request->quantity : $request->quantity;

                $tech_response_job_card_detail->margin = $old_tech_response_job_card_detail ? ($old_tech_response_job_card_detail->margin + $request->margin) / 2 : $request->margin;

                $tech_response_job_card_detail->is_main = $request->is_main ? 1 : 0;

                $tech_response_job_card_detail->is_chargeable = $request->is_chargeable ? 1 : 0;

                $tech_response_job_card_detail->is_delete = 0;

                $tech_response_job_card_detail->save();



                $tech_response_job_card_detail_id = $tech_response_job_card_detail->id;
            }



            $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card->id)

                ->where('is_delete', 0)

                ->get();

            $total_value = 0;

            foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {

                $margin = ($tech_response_job_card_detail->margin + 100) / 100;

                $total_value += $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
            }

            $tech_response_job_card->tech_response_job_card_value = $total_value;

            $tech_response_job_card->save();



            if ($tech_response_job_card_detail_id != '') {

                $tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::find($tech_response_job_card_detail_id);

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Created,' . $tech_response_job_card->id . ',' . $tech_response_job_card->tech_response_id . ',' . $tech_response_job_card->tech_response_job_card_no . ',' . $tech_response_job_card->tech_response_job_card_date_time . ',' . str_replace(',', ' ', $tech_response_job_card->remarks) . ',' . $tech_response_job_card_detail->id . ',' . $tech_response_job_card_detail->item_id . ',' . $tech_response_job_card_detail->rate . ',' . $tech_response_job_card_detail->quantity . ',' . $tech_response_job_card_detail->margin . ',' . $tech_response_job_card_detail->is_main . ',' . $tech_response_job_card_detail->is_chargeable . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);
            } else {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Created,' . $tech_response_job_card->id . ',' . $tech_response_job_card->tech_response_id . ',' . $tech_response_job_card->tech_response_job_card_no . ',' . $tech_response_job_card->tech_response_job_card_date_time . ',' . str_replace(',', ' ', $tech_response_job_card->remarks) . ',,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);
            }



            $result = array(

                'response' => true,

                'message' => 'Tech Response Job Card Detail created successfully',

                'data' => $tech_response_job_card->id

            );
        } else {

            $result = array(

                'response' => false,

                'message' => 'Tech Response Job Card Detail creation failed'

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

        $tech_response_job_card = \App\Model\TechResponseJobCard::find($request->tech_response_job_card_id);

        $tech_response_job_card->tech_response_id = $request->tech_response_id;

        $tech_response_job_card->tech_response_job_card_no = $request->tech_response_job_card_no;

        $tech_response_job_card->tech_response_job_card_date_time = date('Y-m-d', strtotime($request->tech_response_job_card_date)) . ' ' . $request->tech_response_job_card_time;

        $tech_response_job_card->remarks = $request->remarks;

        $tech_response_job_card->user_id = $request->session()->get('users_id');



        if ($tech_response_job_card->save()) {

            $tech_response_job_card_detail_id = '';

            if (isset($request->item['id'])) {

                $tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::find($id);

                $tech_response_job_card_detail->quantity = 0;

                $tech_response_job_card_detail->margin = $request->margin;

                $tech_response_job_card_detail->is_delete = 1;

                $tech_response_job_card_detail->save();



                $old_tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card->id)

                    ->where('item_id', $request->item['id'])

                    ->where('is_main', $request->is_main)

                    ->where('is_chargeable', $request->is_chargeable)

                    ->first();



                $tech_response_job_card_detail = $old_tech_response_job_card_detail ? $old_tech_response_job_card_detail : new \App\Model\TechResponseJobCardDetails();

                $tech_response_job_card_detail->tech_response_job_card_id = $tech_response_job_card->id;

                $tech_response_job_card_detail->item_id = $request->item['id'];

                $tech_response_job_card_detail->rate = $request->rate;

                $tech_response_job_card_detail->quantity = $old_tech_response_job_card_detail ? $old_tech_response_job_card_detail->quantity + $request->quantity : $request->quantity;

                $tech_response_job_card_detail->margin = $old_tech_response_job_card_detail ? ($old_tech_response_job_card_detail->margin + $request->margin) / 2 : $request->margin;

                $tech_response_job_card_detail->is_main = $request->is_main ? 1 : 0;

                $tech_response_job_card_detail->is_chargeable = $request->is_chargeable ? 1 : 0;

                $tech_response_job_card_detail->is_delete = 0;

                $tech_response_job_card_detail->save();



                $tech_response_job_card_detail_id = $tech_response_job_card_detail->id;
            }



            $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card->id)

                ->where('is_delete', 0)

                ->get();

            $total_value = 0;

            foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {

                $margin = ($tech_response_job_card_detail->margin + 100) / 100;

                $total_value += $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
            }

            $tech_response_job_card->tech_response_job_card_value = $total_value;

            $tech_response_job_card->save();



            if ($tech_response_job_card_detail_id != '') {

                $tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::find($tech_response_job_card_detail_id);

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Updated,' . $tech_response_job_card->id . ',' . $tech_response_job_card->tech_response_id . ',' . $tech_response_job_card->tech_response_job_card_no . ',' . $tech_response_job_card->tech_response_job_card_date_time . ',' . str_replace(',', ' ', $tech_response_job_card->remarks) . ',' . $tech_response_job_card_detail->id . ',' . $tech_response_job_card_detail->item_id . ',' . $tech_response_job_card_detail->rate . ',' . $tech_response_job_card_detail->quantity . ',' . $tech_response_job_card_detail->margin . ',' . $tech_response_job_card_detail->is_main . ',' . $tech_response_job_card_detail->is_chargeable . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);
            } else {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Updated,' . $tech_response_job_card->id . ',' . $tech_response_job_card->tech_response_id . ',' . $tech_response_job_card->tech_response_job_card_no . ',' . $tech_response_job_card->tech_response_job_card_date_time . ',' . str_replace(',', ' ', $tech_response_job_card->remarks) . ',,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);
            }



            $result = array(

                'response' => true,

                'message' => 'Tech Response Job Card Detail updated successfully',

                'data' => $tech_response_job_card->id

            );
        } else {

            $result = array(

                'response' => false,

                'message' => 'Tech Response Job Card Detail updation failed'

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

            $tech_response_job_card = \App\Model\TechResponseJobCard::find($id);

            $tech_response_job_card->is_delete = 1;



            if ($tech_response_job_card->save()) {

                $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card->id)->where('is_delete', 0)->get();

                foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {

                    $tech_response_job_card_detail->quantity = 0;

                    $tech_response_job_card_detail->is_delete = 1;

                    $tech_response_job_card_detail->save();
                }



                $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card->id)

                    ->where('is_delete', 0)

                    ->get();

                $total_value = 0;

                foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {

                    $margin = ($tech_response_job_card_detail->margin + 100) / 100;

                    $total_value += $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
                }

                $tech_response_job_card->tech_response_job_card_value = $total_value;

                $tech_response_job_card->save();



                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Deleted,' . $tech_response_job_card->id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);



                $result = array(

                    'response' => true,

                    'message' => 'Tech Response Job Card deleted successfully'

                );
            } else {

                $result = array(

                    'response' => false,

                    'message' => 'Tech Response Job Card deletion failed'

                );
            }
        } else if ($request->type == 1) {

            $tech_response_job_card_detail = \App\Model\TechResponseJobCardDetails::find($id);

            $tech_response_job_card_detail->quantity = 0;

            $tech_response_job_card_detail->is_delete = 1;



            if ($tech_response_job_card_detail->save()) {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/tech_response_job_card_controller.csv', 'a+') or die('Unable to open/create file!');

                fwrite($myfile, 'Deleted,' . $tech_response_job_card_detail->tech_response_job_card_id . ',,,,,' . $tech_response_job_card_detail->id . ',,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);

                fclose($myfile);



                $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::where('tech_response_job_card_id', $tech_response_job_card_detail->tech_response_job_card_id)

                    ->where('is_delete', 0)

                    ->get();

                $total_value = 0;

                foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {

                    $margin = ($tech_response_job_card_detail->margin + 100) / 100;

                    $total_value += $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
                }

                $tech_response_job_card = \App\Model\TechResponseJobCard::find($tech_response_job_card_detail->tech_response_job_card_id);

                $tech_response_job_card->tech_response_job_card_value = $total_value;

                $tech_response_job_card->save();



                $result = array(

                    'response' => true,

                    'message' => 'Tech Response Job Card Detail deleted successfully'

                );
            } else {

                $result = array(

                    'response' => false,

                    'message' => 'Tech Response Job Card Detail deletion failed'

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
