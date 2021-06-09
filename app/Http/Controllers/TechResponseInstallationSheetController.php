<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class TechResponseInstallationSheetController extends Controller
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
        
        $data['tech_response_installation_sheet_id'] = $request->id;
        $data['tech_response_id'] = $request->tech_response_id;
        $data['view'] = $request->view;
        $data['type'] = $request->type;
        
        return view('tech_response.tech_response_installation_sheet_detail', $data);
    }

    public function tech_response_installation_sheet_list(Request $request)
    {
        $tech_response_installation_sheets = \App\Model\TechResponseInstallationSheet::select('id', 'tech_response_id', 'tech_response_installation_sheet_no', 'tech_response_installation_sheet_date_time', 'remarks', 'tech_response_installation_sheet_value', 'user_id', 'is_posted', 'is_approved')
                ->with(array('User' => function($query) {
                    $query->select('id', 'first_name');
                }))
                ->where('tech_response_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $tech_response = \App\Model\TechResponse::select('id', 'tech_response_no')->find($request->id);
                
        $data = array(
            'tech_response_installation_sheets' => $tech_response_installation_sheets,
            'tech_response' => $tech_response,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function find_tech_response_installation_sheet(Request $request)
    {
        $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::select('id', 'tech_response_id', 'tech_response_installation_sheet_no', 'tech_response_installation_sheet_date_time', 'remarks', 'tech_response_installation_sheet_value', 'user_id', 'is_posted', 'is_approved')
                ->find($request->id);
                
        $data = array(
            'tech_response_installation_sheet' => $tech_response_installation_sheet,
            'permission' => !in_array(session()->get('users_id'), array(1, 12, 59, 71))
        );
                
        return response($data);
    }

    public function find_tech_response_installation_sheet_detail(Request $request)
    {
        $tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::select('id', 'tech_response_installation_sheet_id', 'item_id', 'rate', 'quantity')
                ->with(array('TechResponseInstallationSheet' => function($query) {
                    $query->select('id', 'tech_response_id', 'tech_response_installation_sheet_no', 'tech_response_installation_sheet_date_time', 'remarks', 'is_posted', 'is_approved');
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
        return response($tech_response_installation_sheet_detail);
    }

    public function tech_response_installation_sheet_detail_list(Request $request)
    {
        $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::select('id', 'item_id', 'rate', 'quantity')
                ->with(array('TechResponseInstallationSheet' => function($query) {
                    $query->select('is_posted');
                }))
                ->with(array('Item' => function($query) {
                    $query->select('id', 'code', 'name', 'unit_type_id', 'stock')
                            ->with(array('UnitType' => function($query) {
                                $query->select('id', 'code', 'name');
                            }));
                }))
                ->where('tech_response_installation_sheet_id', $request->id)
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'tech_response_installation_sheet_details' => $tech_response_installation_sheet_details,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }
    
    public function post_tech_response_installation_sheet(Request $request)
    {
        $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::find($request->id);
        
        $is_posted = $tech_response_installation_sheet->is_posted == 0 ? true : false;
        
        $tech_response_installation_sheet->is_posted = 1;
        
        if($tech_response_installation_sheet->save()) {    
            if($is_posted){                
                $sms = '--- Tech Response Installation Sheet Created ---'.PHP_EOL;
                $sms .= 'Tech Response Installation Sheet No : '.$tech_response_installation_sheet->tech_response_installation_sheet_no.PHP_EOL;
                $sms .= 'Customer Name : '.$tech_response_installation_sheet->TechResponse->Contact->name.PHP_EOL;
                $sms .= 'Customer Address : '.$tech_response_installation_sheet->TechResponse->Contact->address.PHP_EOL;
                $sms .= 'Logged User : '.$tech_response_installation_sheet->User->first_name.' '.$tech_response_installation_sheet->User->last_name.PHP_EOL;
                $sms .= 'URL : http://erp.m3force.com/m3force/public/tech_response_installation_sheet/add_new?view=0&id='.$tech_response_installation_sheet->id.'&tech_response_id='.$tech_response_installation_sheet->tech_response_id;

                $session = createSession('','esmsusr_1na2','3p4lfqe','');
                sendMessages($session, 'M3FORCE', $sms, array('0704599310', '0704599321', '0772030007'));

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Posted,'. $tech_response_installation_sheet->id. ',,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);

                $data = array(
                    'tech_response_installation_sheet_no' => $tech_response_installation_sheet->tech_response_installation_sheet_no,
                    'customer_name' => $tech_response_installation_sheet->TechResponse->Contact->name,
                    'customer_address' => $tech_response_installation_sheet->TechResponse->Contact->address,
                    'logged_user' => $tech_response_installation_sheet->User->first_name.' '.$tech_response_installation_sheet->User->last_name,
                    'id' => $tech_response_installation_sheet->id,
                    'tech_response_id' => $tech_response_installation_sheet->tech_response_id
                );

                Mail::send('emails.tech_response_installation_sheet_authorization', $data, function($message) use($tech_response_installation_sheet){
                    $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                    $message->to('sanjaya@m3force.com', 'Sanjaya Perera');
                    $message->to('palitha@m3force.com', 'Palitha Wickramatunga');
                    $message->to('bandara@m3force.com', 'Lakshitha Bandara');
                    $message->subject('Tech Response Item Issue Authorization - '.$tech_response_installation_sheet->TechResponse->Contact->name);
                });
            }
   
            $result = array(
                'response' => true,
                'message' => 'Tech Response Installation Sheet posted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Tech Response Installation Sheet post failed'
            );
        }

        echo json_encode($result);
    }
    
    public function print_tech_response_installation_sheet(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::find($request->id);
        $data['tech_response_installation_sheet'] = $tech_response_installation_sheet;
        $title = $tech_response_installation_sheet ? 'Tech Response Installation Sheet Details '.$tech_response_installation_sheet->tech_response_installation_sheet_no : 'Tech Response Installation Sheet Details';
        
        $html = view('tech_response.tech_response_installation_sheet_pdf', $data);
        
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
        $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::find($request->tech_response_installation_sheet_id);
        
        if(!$tech_response_installation_sheet){
            $exist = true;
            $tech_response_installation_sheet = new \App\Model\TechResponseInstallationSheet();
            $last_id = 0;
            $last_tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_tech_response_installation_sheet ? $last_tech_response_installation_sheet->id : $last_id;
            $tech_response_installation_sheet->tech_response_installation_sheet_no = 'TR/INS/'.date('m').'/'.date('y').'/'.$request->tech_response_id.'/'.sprintf('%05d', $last_id+1);
        }
        
        $tech_response_installation_sheet->tech_response_id = $request->tech_response_id;        
        $tech_response_installation_sheet->tech_response_installation_sheet_date_time = date('Y-m-d', strtotime($request->tech_response_installation_sheet_date)).' '.$request->tech_response_installation_sheet_time;
        $tech_response_installation_sheet->remarks = $request->remarks;
        $tech_response_installation_sheet->user_id = $request->session()->get('users_id');
        
        if($tech_response_installation_sheet->save()) {
            $tech_response_installation_sheet_detail_id = '';
            if(isset($request->item['id'])){
                $old_tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet->id)
                        ->where('item_id', $request->item['id'])
                        ->first();

                $tech_response_installation_sheet_detail = $old_tech_response_installation_sheet_detail ? $old_tech_response_installation_sheet_detail : new \App\Model\TechResponseInstallationSheetDetails();
                $tech_response_installation_sheet_detail->tech_response_installation_sheet_id = $tech_response_installation_sheet->id;
                $tech_response_installation_sheet_detail->item_id = $request->item['id'];
                $tech_response_installation_sheet_detail->rate = $request->rate;
                $tech_response_installation_sheet_detail->quantity = $old_tech_response_installation_sheet_detail ? $old_tech_response_installation_sheet_detail->quantity + $request->quantity : $request->quantity;
                $tech_response_installation_sheet_detail->is_delete = 0;
                $tech_response_installation_sheet_detail->save();
                
                $tech_response_installation_sheet_detail_id = $tech_response_installation_sheet_detail->id;
            }
            
            $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($tech_response_installation_sheet_details as $tech_response_installation_sheet_detail){
                $total_value += $tech_response_installation_sheet_detail->rate * $tech_response_installation_sheet_detail->quantity;
            }
            $tech_response_installation_sheet->tech_response_installation_sheet_value = $total_value;
            $tech_response_installation_sheet->save();  
            
            if($tech_response_installation_sheet_detail_id != ''){
                $tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::find($tech_response_installation_sheet_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'. $tech_response_installation_sheet->id. ','. $tech_response_installation_sheet->tech_response_id. ','. $tech_response_installation_sheet->tech_response_installation_sheet_no. ','. $tech_response_installation_sheet->tech_response_installation_sheet_date_time. ',' . str_replace(',',' ',$tech_response_installation_sheet->remarks). ','. $tech_response_installation_sheet_detail->id. ','. $tech_response_installation_sheet_detail->item_id. ','. $tech_response_installation_sheet_detail->rate. ','. $tech_response_installation_sheet_detail->quantity. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'. $tech_response_installation_sheet->id. ','. $tech_response_installation_sheet->tech_response_id. ','. $tech_response_installation_sheet->tech_response_installation_sheet_no. ','. $tech_response_installation_sheet->tech_response_installation_sheet_date_time. ',' . str_replace(',',' ',$tech_response_installation_sheet->remarks). ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Tech Response Installation Sheet Detail created successfully',
                'data' => $tech_response_installation_sheet->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Tech Response Installation Sheet Detail creation failed'
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
        $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::find($request->tech_response_installation_sheet_id);
        $tech_response_installation_sheet->tech_response_id = $request->tech_response_id;
        $tech_response_installation_sheet->tech_response_installation_sheet_no = $request->tech_response_installation_sheet_no;
        $tech_response_installation_sheet->tech_response_installation_sheet_date_time = date('Y-m-d', strtotime($request->tech_response_installation_sheet_date)).' '.$request->tech_response_installation_sheet_time;
        $tech_response_installation_sheet->remarks = $request->remarks;
        $tech_response_installation_sheet->user_id = $request->session()->get('users_id');
        
        if($tech_response_installation_sheet->save()) {
            $tech_response_installation_sheet_detail_id = '';
            if(isset($request->item['id'])){
                $tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::find($id);
                $tech_response_installation_sheet_detail->quantity = 0;
                $tech_response_installation_sheet_detail->is_delete = 1;
                $tech_response_installation_sheet_detail->save();
            
                $old_tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet->id)
                        ->where('item_id', $request->item['id'])
                        ->first();

                $tech_response_installation_sheet_detail = $old_tech_response_installation_sheet_detail ? $old_tech_response_installation_sheet_detail : new \App\Model\TechResponseInstallationSheetDetails();
                $tech_response_installation_sheet_detail->tech_response_installation_sheet_id = $tech_response_installation_sheet->id;
                $tech_response_installation_sheet_detail->item_id = $request->item['id'];
                $tech_response_installation_sheet_detail->rate = $request->rate;
                $tech_response_installation_sheet_detail->quantity = $old_tech_response_installation_sheet_detail ? $old_tech_response_installation_sheet_detail->quantity + $request->quantity : $request->quantity;
                $tech_response_installation_sheet_detail->is_delete = 0;
                $tech_response_installation_sheet_detail->save();
                
                $tech_response_installation_sheet_detail_id = $tech_response_installation_sheet_detail->id;
            }
            
            $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($tech_response_installation_sheet_details as $tech_response_installation_sheet_detail){
                $total_value += $tech_response_installation_sheet_detail->rate * $tech_response_installation_sheet_detail->quantity;
            }
            $tech_response_installation_sheet->tech_response_installation_sheet_value = $total_value;
            $tech_response_installation_sheet->save();
            
            if($tech_response_installation_sheet_detail_id != ''){
                $tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::find($tech_response_installation_sheet_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'. $tech_response_installation_sheet->id. ','. $tech_response_installation_sheet->tech_response_id. ','. $tech_response_installation_sheet->tech_response_installation_sheet_no. ','. $tech_response_installation_sheet->tech_response_installation_sheet_date_time. ',' . str_replace(',',' ',$tech_response_installation_sheet->remarks). ','. $tech_response_installation_sheet_detail->id. ','. $tech_response_installation_sheet_detail->item_id. ','. $tech_response_installation_sheet_detail->rate. ','. $tech_response_installation_sheet_detail->quantity. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'. $tech_response_installation_sheet->id. ','. $tech_response_installation_sheet->tech_response_id. ','. $tech_response_installation_sheet->tech_response_installation_sheet_no. ','. $tech_response_installation_sheet->tech_response_installation_sheet_date_time. ',' . str_replace(',',' ',$tech_response_installation_sheet->remarks). ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Tech Response Installation Sheet Detail updated successfully',
                'data' => $tech_response_installation_sheet->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Tech Response Installation Sheet Detail updation failed'
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
            $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::find($id);
            $tech_response_installation_sheet->is_delete = 1;

            if($tech_response_installation_sheet->save()) {
                $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet->id)->where('is_delete', 0)->get();
                foreach ($tech_response_installation_sheet_details as $tech_response_installation_sheet_detail){
                    $tech_response_installation_sheet_detail->quantity = 0;
                    $tech_response_installation_sheet_detail->is_delete = 1;
                    $tech_response_installation_sheet_detail->save();
                }
            
                $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet->id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($tech_response_installation_sheet_details as $tech_response_installation_sheet_detail){
                    $total_value += $tech_response_installation_sheet_detail->rate * $tech_response_installation_sheet_detail->quantity;
                }
                $tech_response_installation_sheet->tech_response_installation_sheet_value = $total_value;
                $tech_response_installation_sheet->save();
            
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'. $tech_response_installation_sheet->id. ',,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
                
                $result = array(
                    'response' => true,
                    'message' => 'Tech Response Installation Sheet deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response Installation Sheet deletion failed'
                );
            }
        } else if($request->type == 1){
            $tech_response_installation_sheet_detail = \App\Model\TechResponseInstallationSheetDetails::find($id);
            $tech_response_installation_sheet_detail->quantity = 0;
            $tech_response_installation_sheet_detail->is_delete = 1;

            if($tech_response_installation_sheet_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_installation_sheet_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'. $tech_response_installation_sheet_detail->tech_response_installation_sheet_id. ',,,,,'. $tech_response_installation_sheet_detail->id. ',,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
                
                $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $tech_response_installation_sheet_detail->tech_response_installation_sheet_id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($tech_response_installation_sheet_details as $tech_response_installation_sheet_detail){
                    $total_value += $tech_response_installation_sheet_detail->rate * $tech_response_installation_sheet_detail->quantity;
                }
                $tech_response_installation_sheet = \App\Model\TechResponseInstallationSheet::find($tech_response_installation_sheet_detail->tech_response_installation_sheet_id);
                $tech_response_installation_sheet->tech_response_installation_sheet_value = $total_value;
                $tech_response_installation_sheet->save();
            
                $result = array(
                    'response' => true,
                    'message' => 'Tech Response Installation Sheet Detail deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Tech Response Installation Sheet Detail deletion failed'
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
