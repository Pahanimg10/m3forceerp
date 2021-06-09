<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class InstallationSheetController extends Controller
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
        
        $data['installation_sheet_id'] = $request->id;
        $data['inquiry_id'] = $request->inquiry_id;
        $data['view'] = $request->view;
        $data['type'] = $request->type;
        
        return view('inquiry.installation_sheet_detail', $data);
    }

    public function installation_sheet_list(Request $request)
    {
        $installation_sheets = \App\Model\InstallationSheet::select('id', 'inquiry_id', 'installation_sheet_no', 'installation_sheet_date_time', 'remarks', 'installation_sheet_value', 'user_id', 'is_posted', 'is_approved')
                ->with(array('User' => function($query) {
                    $query->select('id', 'first_name');
                }))
                ->where('inquiry_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $inquiry = \App\Model\Inquiry::select('id', 'inquiry_no')->find($request->id);
                
        $data = array(
            'installation_sheets' => $installation_sheets,
            'inquiry' => $inquiry,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function find_installation_sheet(Request $request)
    {
        $installation_sheet = \App\Model\InstallationSheet::select('id', 'inquiry_id', 'installation_sheet_no', 'installation_sheet_date_time', 'remarks', 'installation_sheet_value', 'user_id', 'is_posted', 'is_approved')
                ->find($request->id);
                
        $data = array(
            'installation_sheet' => $installation_sheet,
            'permission' => !in_array(session()->get('users_id'), array(1, 4, 22, 63, 64))
        );
                
        return response($data);
    }

    public function find_installation_sheet_detail(Request $request)
    {
        $installation_sheet_detail = \App\Model\InstallationSheetDetails::select('id', 'installation_sheet_id', 'item_id', 'rate', 'quantity')
                ->with(array('InstallationSheet' => function($query) {
                    $query->select('id', 'inquiry_id', 'installation_sheet_no', 'installation_sheet_date_time', 'remarks', 'is_posted', 'is_approved');
                }))
                ->with(array('Item' => function($query) {
                    $query->select('id', 'main_category_id', 'sub_category_id', 'code', 'name', 'unit_type_id')
                            ->with(array('MainItemCategory' => function($query) {
                                $query->select('id', 'code', 'name');
                            }))
                            ->with(array('SubItemCategory' => function($query) {
                                $query->select('id', 'code', 'name');
                            }))
                            ->with(array('UnitType' => function($query) {
                                $query->select('id', 'code', 'name');
                            }));
                }))
                ->find($request->id);
        return response($installation_sheet_detail);
    }

    public function installation_sheet_detail_list(Request $request)
    {
        $installation_sheet_details = \App\Model\InstallationSheetDetails::select('id', 'installation_sheet_id', 'item_id', 'rate', 'quantity')
                ->with(array('InstallationSheet' => function($query) {
                    $query->select('id', 'is_posted');
                }))
                ->with(array('Item' => function($query) {
                    $query->select('id', 'code', 'name', 'unit_type_id', 'stock')
                            ->with(array('UnitType' => function($query) {
                                $query->select('id', 'code', 'name');
                            }));
                }))
                ->where('installation_sheet_id', $request->id)
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'installation_sheet_details' => $installation_sheet_details,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }
    
    public function post_installation_sheet(Request $request)
    {
        $installation_sheet = \App\Model\InstallationSheet::find($request->id);
        $installation_sheet->is_posted = 1;
        
        if($installation_sheet->save()) {                  
            $sms = '--- Job Item Issue Authorization ---'.PHP_EOL;
            $sms .= 'Installation Sheet No : '.$installation_sheet->installation_sheet_no.PHP_EOL;
            $sms .= 'Customer Name : '.$installation_sheet->Inquiry->Contact->name.PHP_EOL;
            $sms .= 'Customer Address : '.$installation_sheet->Inquiry->Contact->address.PHP_EOL;
            $sms .= 'Logged User : '.$installation_sheet->User->first_name.' '.$installation_sheet->User->last_name.PHP_EOL;
            $sms .= 'URL : http://erp.m3force.com/m3force/public/installation_sheet/add_new?type=1&view=0&id='.$installation_sheet->id.'&inquiry_id='.$installation_sheet->id;

            $session = createSession('','esmsusr_1na2','3p4lfqe','');
            sendMessages($session,'M3FORCE', $sms, array('0704599321', '0704599306', '0704599323', '0704599314'));
   
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,' . $installation_sheet->id. ',,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
        
            $data = array(
                'installation_sheet_no' => $installation_sheet->installation_sheet_no,
                'customer_name' => $installation_sheet->Inquiry->Contact->name,
                'customer_address' => $installation_sheet->Inquiry->Contact->address,
                'logged_user' => $installation_sheet->User->first_name.' '.$installation_sheet->User->last_name,
                'id' => $installation_sheet->id,
                'inquiry_id' => $installation_sheet->inquiry_id
            );

            Mail::send('emails.job_installation_sheet_authorization', $data, function($message) use($installation_sheet){
                $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                $message->to('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                $message->to('saminda@m3force.com', 'Saminda Gunawardane');
                $message->to('sachith@m3force.com', 'Sachith Rathnayake');
                $message->to('jaliya@m3force.com', 'Jaliya Kasun');
                $message->to($installation_sheet->Inquiry->SalesTeam->email, $installation_sheet->Inquiry->SalesTeam->name);
                $message->subject('Job Item Issue Authorization - '.$installation_sheet->Inquiry->Contact->name);
            });
                
            $result = array(
                'response' => true,
                'message' => 'Installation sheet posted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Installation sheet post failed'
            );
        }

        echo json_encode($result);
    }
    
    public function get_authorize_data(Request $request)
    {
        $job_card_ids = $job_card_items = $installation_items = array();
        $quotations = \App\Model\Quotation::where('inquiry_id', $request->id)
                        ->where('is_confirmed', 1)
                        ->where('is_revised', 0)
                        ->where('is_delete', 0)
                        ->get();
        foreach ($quotations as $quotation){ 
            foreach ($quotation->QuotationJobCard as $detail){
                array_push($job_card_ids, $detail['id']);
            }
        }
        $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('id, item_id, SUM(quantity) as total_quantity')
                ->whereIn('quotation_job_card_id', $job_card_ids)
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($job_card_details as $job_card_detail){
            $row = array(
                'id' => $job_card_detail->Item->id,
                'code' => $job_card_detail->Item->code,
                'name' => $job_card_detail->Item->name,
                'quantity' => $job_card_detail->total_quantity
            );
            array_push($job_card_items, $row);
        }
        $installation_sheet_details = \App\Model\InstallationSheetDetails::selectRaw('id, installation_sheet_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('InstallationSheet', function($query) use($request){
                    $query->where('inquiry_id', $request->id)->where('is_posted', 1)->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($installation_sheet_details as $installation_sheet_detail){
            $row = array(
                'id' => $installation_sheet_detail->Item->id,
                'code' => $installation_sheet_detail->Item->code,
                'name' => $installation_sheet_detail->Item->name,
                'quantity' => $installation_sheet_detail->total_quantity
            );
            array_push($installation_items, $row);
        }

        $request_ids = $request_items = array();
        foreach ($job_card_items as $job_card_main_item){
            if(!in_array($job_card_main_item['id'], $request_ids)){
                $total_qunatity = 0;
                foreach ($job_card_items as $job_card_sub_item){
                    if($job_card_main_item['id'] == $job_card_sub_item['id']){
                        $total_qunatity += $job_card_sub_item['quantity'];
                    }
                }
                foreach ($installation_items as $installation_item){
                    if($job_card_main_item['id'] == $installation_item['id']){
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
        foreach ($installation_items as $installation_main_item){
            if(!in_array($installation_main_item['id'], $request_ids)){
                $total_qunatity = 0;
                foreach ($installation_items as $installation_sub_item){
                    if($installation_main_item['id'] == $installation_sub_item['id']){
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
        if(count($request_items) > 0){
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
            foreach($request_items as $index => $value){
                $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;">'.($index+1).'</td>
                        <td style="vertical-align: middle; white-space: nowrap;">'.$value['code'].'</td>
                        <td style="vertical-align: middle;">'.$value['name'].'</td>
                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">'.$value['quantity'].'</td>
                    ';
                $issued_quantity = 0;
                $job = \App\Model\Job::where('inquiry_id', $request->id)->where('is_delete', 0)->first();
                if($job){
                    $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use($job){
                                $query->where('item_issue_type_id', 1)
                                        ->where('document_id', $job->id)
                                        ->where('is_posted', 1)
                                        ->where('is_delete', 0);
                            })
                            ->where('item_id', $value['id'])
                            ->where('is_delete', 0)
                            ->get();
                    foreach ($item_issue_details as $item_issue_detail){
                        $issued_quantity += $item_issue_detail->quantity;
                    }
                }
                $received_quantity = 0;
                $item_issue_ids = array();
                $job = \App\Model\Job::where('inquiry_id', $request->id)->where('is_delete', 0)->first();
                if($job){
                    $item_issues = \App\Model\ItemIssue::where('item_issue_type_id', 1)
                            ->where('document_id', $job->id)
                            ->where('is_posted', 1)
                            ->where('is_delete', 0)
                            ->get();
                    foreach ($item_issues as $item_issue){
                        if(!in_array($item_issue->id, $item_issue_ids)){
                            array_push($item_issue_ids, $item_issue->id);
                        }
                    }
                }
                $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use($item_issue_ids){
                                $query->whereIn('item_issue_id', $item_issue_ids)
                                        ->where('is_posted', 1)
                                        ->where('is_delete', 0);
                            })
                            ->where('item_id', $value['id'])
                            ->where('is_delete', 0)
                            ->get();
                foreach ($item_receive_details as $item_receive_detail){
                    $received_quantity += $item_receive_detail->quantity;
                }
                $view .= '
                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">'.$issued_quantity.'</td>
                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">'.$received_quantity.'</td>
                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">'.($value['quantity']-$issued_quantity+$received_quantity).'</td>
                    </tr>
                    ';
            }        
            $view .= '
                        </tbody>
                    </table>
                ';
        }
        
        $result = array(
            'view' => $view
        );

        echo json_encode($result);
    }
    
    public function approve_installation_sheet(Request $request)
    {
        $installation_sheets = \App\Model\InstallationSheet::where('inquiry_id', $request->inquiry_id)
                ->where('is_posted', 1)
                ->where('is_approved', 0)
                ->get();
        foreach($installation_sheets as $installation_sheet){
            $installation_sheet->is_approved = 1;
            $installation_sheet->save();
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Approved,' . $installation_sheet->id. ',,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
        }

        $inquiry = \App\Model\Inquiry::find($request->inquiry_id);
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
            'message' => 'Installation sheets approved successfully'
        );

        echo json_encode($result);
    }
    
    public function print_installation_sheet(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $installation_sheet = \App\Model\InstallationSheet::find($request->id);
        $data['installation_sheet'] = $installation_sheet;
        $title = $installation_sheet ? 'Installation Sheet Details '.$installation_sheet->installation_sheet_no : 'Installation Sheet Details';
        
        $html = view('inquiry.installation_sheet_pdf', $data);
        
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
            'footer-font-size' => 8
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }
    
    public function print_installation_sheet_no_price(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $installation_sheet = \App\Model\InstallationSheet::find($request->id);
        $data['installation_sheet'] = $installation_sheet;
        $title = $installation_sheet ? 'Installation Sheet Details '.$installation_sheet->installation_sheet_no : 'Installation Sheet Details';
        
        $html = view('inquiry.installation_sheet_pdf_no_price', $data);
        
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
        $installation_sheet = \App\Model\InstallationSheet::find($request->installation_sheet_id);
        
        if(!$installation_sheet){
            $exist = true;
            $installation_sheet = new \App\Model\InstallationSheet();
            $last_id = 0;
            $last_installation_sheet = \App\Model\InstallationSheet::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_installation_sheet ? $last_installation_sheet->id : $last_id;
            $installation_sheet->installation_sheet_no = 'INS/'.date('m').'/'.date('y').'/'.$request->inquiry_id.'/'.sprintf('%05d', $last_id+1);
        }
        
        $installation_sheet->inquiry_id = $request->inquiry_id;        
        $installation_sheet->installation_sheet_date_time = date('Y-m-d', strtotime($request->installation_sheet_date)).' '.$request->installation_sheet_time;
        $installation_sheet->remarks = $request->remarks;
        $installation_sheet->user_id = $request->session()->get('users_id');
        
        if($installation_sheet->save()) {
            $installation_sheet_detail_id = '';
            if(isset($request->item['id'])){
                $old_installation_sheet_detail = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)
                        ->where('item_id', $request->item['id'])
                        ->first();

                $installation_sheet_detail = $old_installation_sheet_detail ? $old_installation_sheet_detail : new \App\Model\InstallationSheetDetails();
                $installation_sheet_detail->installation_sheet_id = $installation_sheet->id;
                $installation_sheet_detail->item_id = $request->item['id'];
                $installation_sheet_detail->rate = $request->rate;
                $installation_sheet_detail->quantity = $old_installation_sheet_detail ? $old_installation_sheet_detail->quantity + $request->quantity : $request->quantity;
                $installation_sheet_detail->is_delete = 0;
                $installation_sheet_detail->save();
                
                $installation_sheet_detail_id = $installation_sheet_detail->id;
            }
            
            $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($installation_sheet_details as $installation_sheet_detail){
                $total_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
            }
            $installation_sheet->installation_sheet_value = $total_value;
            $installation_sheet->save();
            
            if($installation_sheet_detail_id != ''){
                $installation_sheet_detail = \App\Model\InstallationSheetDetails::find($installation_sheet_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $installation_sheet->id. ',' . $installation_sheet->installation_sheet_no. ',' . $installation_sheet->inquiry_id. ',' . $installation_sheet->installation_sheet_date_time. ',' . $installation_sheet->installation_sheet_value. ',' . str_replace(',',' ',$installation_sheet->remarks). ',' . $installation_sheet_detail->id. ',' . $installation_sheet_detail->item_id. ',' . $installation_sheet_detail->rate. ',' . $installation_sheet_detail->quantity. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $installation_sheet->id. ',' . $installation_sheet->installation_sheet_no. ',' . $installation_sheet->inquiry_id. ',' . $installation_sheet->installation_sheet_date_time. ',' . $installation_sheet->installation_sheet_value. ',' . str_replace(',',' ',$installation_sheet->remarks). ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Installation Sheet Detail created successfully',
                'data' => $installation_sheet->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Installation Sheet Detail creation failed'
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
        $installation_sheet = \App\Model\InstallationSheet::find($request->installation_sheet_id);
        $installation_sheet->inquiry_id = $request->inquiry_id;
        $installation_sheet->installation_sheet_no = $request->installation_sheet_no;
        $installation_sheet->installation_sheet_date_time = date('Y-m-d', strtotime($request->installation_sheet_date)).' '.$request->installation_sheet_time;
        $installation_sheet->remarks = $request->remarks;
        $installation_sheet->user_id = $request->session()->get('users_id');
        
        if($installation_sheet->save()) {
            $installation_sheet_detail_id = '';
            if(isset($request->item['id'])){
                $installation_sheet_detail = \App\Model\InstallationSheetDetails::find($id);
                $installation_sheet_detail->quantity = 0;
                $installation_sheet_detail->is_delete = 1;
                $installation_sheet_detail->save();
            
                $old_installation_sheet_detail = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)
                        ->where('item_id', $request->item['id'])
                        ->first();

                $installation_sheet_detail = $old_installation_sheet_detail ? $old_installation_sheet_detail : new \App\Model\InstallationSheetDetails();
                $installation_sheet_detail->installation_sheet_id = $installation_sheet->id;
                $installation_sheet_detail->item_id = $request->item['id'];
                $installation_sheet_detail->rate = $request->rate;
                $installation_sheet_detail->quantity = $old_installation_sheet_detail ? $old_installation_sheet_detail->quantity + $request->quantity : $request->quantity;
                $installation_sheet_detail->is_delete = 0;
                $installation_sheet_detail->save();
                
                $installation_sheet_detail_id = $installation_sheet_detail->id;
            }
            
            $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($installation_sheet_details as $installation_sheet_detail){
                $total_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
            }
            $installation_sheet->installation_sheet_value = $total_value;
            $installation_sheet->save();
            
            if($installation_sheet_detail_id != ''){
                $installation_sheet_detail = \App\Model\InstallationSheetDetails::find($installation_sheet_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $installation_sheet->id. ',' . $installation_sheet->installation_sheet_no. ',' . $installation_sheet->inquiry_id. ',' . $installation_sheet->installation_sheet_date_time. ',' . $installation_sheet->installation_sheet_value. ',' . str_replace(',',' ',$installation_sheet->remarks). ',' . $installation_sheet_detail->id. ',' . $installation_sheet_detail->item_id. ',' . $installation_sheet_detail->rate. ',' . $installation_sheet_detail->quantity. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $installation_sheet->id. ',' . $installation_sheet->installation_sheet_no. ',' . $installation_sheet->inquiry_id. ',' . $installation_sheet->installation_sheet_date_time. ',' . $installation_sheet->installation_sheet_value. ',' . str_replace(',',' ',$installation_sheet->remarks). ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Installation Sheet Detail updated successfully',
                'data' => $installation_sheet->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Installation Sheet Detail updation failed'
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
        if($request->type == 0){
            $installation_sheet = \App\Model\InstallationSheet::find($id);
            $installation_sheet->is_delete = 1;

            if($installation_sheet->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $installation_sheet->id. ',,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
                
                $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)->where('is_delete', 0)->get();
                foreach ($installation_sheet_details as $installation_sheet_detail){
                    $installation_sheet_detail->quantity = 0;
                    $installation_sheet_detail->is_delete = 1;
                    $installation_sheet_detail->save();
                }
            
                $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet->id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($installation_sheet_details as $installation_sheet_detail){
                    $total_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
                }
                $installation_sheet->installation_sheet_value = $total_value;
                $installation_sheet->save();
                
                $result = array(
                    'response' => true,
                    'message' => 'Installation Sheet deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Installation Sheet deletion failed'
                );
            }
        } else if($request->type == 1){
            $installation_sheet_detail = \App\Model\InstallationSheetDetails::find($id);
            $installation_sheet_detail->quantity = 0;
            $installation_sheet_detail->is_delete = 1;

            if($installation_sheet_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $installation_sheet_detail->installation_sheet_id. ',,,,,,' . $installation_sheet_detail->id. ',,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
                
                $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $installation_sheet_detail->installation_sheet_id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($installation_sheet_details as $installation_sheet_detail){
                    $total_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
                }
                $installation_sheet = \App\Model\InstallationSheet::find($installation_sheet_detail->installation_sheet_id);
                $installation_sheet->installation_sheet_value = $total_value;
                $installation_sheet->save();
                
                $result = array(
                    'response' => true,
                    'message' => 'Installation Sheet Detail deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Installation Sheet Detail deletion failed'
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
