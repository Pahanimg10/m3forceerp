<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class JobCardController extends Controller
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
        
        $data['job_card_id'] = $request->id;
        $data['inquiry_id'] = $request->inquiry_id;
        $data['view'] = $request->view;
        $data['type'] = $request->type;
        
        return view('inquiry.job_card_detail', $data);
    }

    public function job_card_list(Request $request)
    {
        $job_cards = \App\Model\JobCard::select('id', 'inquiry_id', 'job_card_no', 'job_card_date_time', 'remarks', 'job_card_value', 'is_used', 'user_id')
                ->with(array('User' => function($query) {
                    $query->select('id', 'first_name');
                }))
                ->where('inquiry_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $inquiry = \App\Model\Inquiry::select('id', 'inquiry_no')->find($request->id);
                
        $data = array(
            'job_cards' => $job_cards,
            'inquiry' => $inquiry,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function find_job_card(Request $request)
    {
        $job_card = \App\Model\JobCard::select('id', 'inquiry_id', 'job_card_no', 'job_card_date_time', 'remarks', 'job_card_value', 'is_used', 'user_id')
                ->find($request->id);
        return response($job_card);
    }

    public function find_job_card_detail(Request $request)
    {
        $job_card_detail = \App\Model\JobCardDetails::select('id', 'job_card_id', 'item_id', 'rate', 'quantity', 'margin', 'is_main')
                ->with(array('JobCard' => function($query) {
                    $query->select('id', 'inquiry_id', 'job_card_no', 'job_card_date_time', 'remarks');
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
        return response($job_card_detail);
    }

    public function job_card_detail_list(Request $request)
    {
        $job_card_details = \App\Model\JobCardDetails::select('id', 'item_id', 'rate', 'quantity', 'margin', 'is_main')
                ->with(array('Item' => function($query) {
                    $query->select('id', 'code', 'name', 'unit_type_id', 'stock')
                            ->with(array('UnitType' => function($query) {
                                $query->select('id', 'code', 'name');
                            }));
                }))
                ->where('job_card_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $job_card = \App\Model\JobCard::select('is_used')->find($request->id);
                
        $data = array(
            'job_card_details' => $job_card_details,
            'job_card' => $job_card,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }
    
    public function print_job_card(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $job_card = \App\Model\JobCard::find($request->id);
        $data['job_card'] = $job_card;
        $title = $job_card ? 'Job Card Details '.$job_card->job_card_no : 'Job Card Details';
        
        $html = view('inquiry.job_card_pdf', $data);
        
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
    
    public function print_job_card_no_price(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $job_card = \App\Model\JobCard::find($request->id);
        $data['job_card'] = $job_card;
        $title = $job_card ? 'Job Card Details '.$job_card->job_card_no : 'Job Card Details';
        
        $html = view('inquiry.job_card_pdf_no_price', $data);
        
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
        $job_card = \App\Model\JobCard::find($request->job_card_id);
        
        if(!$job_card){
            $exist = true;
            $job_card = new \App\Model\JobCard();
            $last_id = 0;
            $last_job_card = \App\Model\JobCard::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_job_card ? $last_job_card->id : $last_id;
            $job_card->job_card_no = 'JC/'.date('m').'/'.date('y').'/'.$request->inquiry_id.'/'.sprintf('%05d', $last_id+1);
        }
        
        $job_card->inquiry_id = $request->inquiry_id;        
        $job_card->job_card_date_time = date('Y-m-d', strtotime($request->job_card_date)).' '.$request->job_card_time;
        $job_card->remarks = $request->remarks;
        $job_card->user_id = $request->session()->get('users_id');
        
        if($job_card->save()) {
            if($exist){
                $inquiry_status = new \App\Model\InquiryDetials();
                $inquiry_status->inquiry_id = $job_card->inquiry_id;
                $inquiry_status->update_date_time = date('Y-m-d H:i');
                $inquiry_status->inquiry_status_id = 6;
                $inquiry_status->sales_team_id = 0;
                $inquiry_status->site_inspection_date_time = '';
                $inquiry_status->advance_payment = 0;
                $inquiry_status->remarks = $job_card->job_card_no;
                $inquiry_status->user_id = $request->session()->get('users_id');
                $inquiry_status->save();
                    
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/inquiry_status_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $inquiry_status->id. ',' . $inquiry_status->inquiry_id. ',' . $inquiry_status->update_date_time. ',' . $inquiry_status->inquiry_status_id. ',' . $inquiry_status->sales_team_id. ',' . $inquiry_status->site_inspection_date_time. ',' . $inquiry_status->advance_payment. ',' . $inquiry_status->payment_mode_id. ',' . $inquiry_status->receipt_no. ',' . str_replace(',',' ',$inquiry_status->cheque_no). ',' . str_replace(',',' ',$inquiry_status->bank). ',' . $inquiry_status->realize_date. ',' . str_replace(',',' ',$inquiry_status->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $job_card_detail_id = '';
            if(isset($request->item['id'])){
                $old_job_card_detail = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)
                        ->where('item_id', $request->item['id'])
                        ->where('is_main', $request->is_main)
                        ->first();

                $job_card_detail = $old_job_card_detail ? $old_job_card_detail : new \App\Model\JobCardDetails();
                $job_card_detail->job_card_id = $job_card->id;
                $job_card_detail->item_id = $request->item['id'];
                $job_card_detail->rate = $request->rate;
                $job_card_detail->quantity = $old_job_card_detail ? $old_job_card_detail->quantity + $request->quantity : $request->quantity;
                $job_card_detail->margin = $old_job_card_detail ? ($old_job_card_detail->margin + $request->margin)/2 : $request->margin;
                $job_card_detail->is_main = $request->is_main ? 1 : 0;
                $job_card_detail->is_delete = 0;
                $job_card_detail->save();
                
                $job_card_detail_id = $job_card_detail->id;
            }
            
            $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($job_card_details as $job_card_detail){
                $margin = ($job_card_detail->margin + 100)/100;
                $total_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
            }
            $job_card->job_card_value = $total_value;
            $job_card->save();
            
            if($job_card_detail_id != ''){
                $job_card_detail = \App\Model\JobCardDetails::find($job_card_detail_id);    
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_card_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $job_card->id. ',' . $job_card->inquiry_id. ',' . $job_card->job_card_date_time. ',' . str_replace(',',' ',$job_card->remarks). ',' . $job_card->job_card_value. ',' . $job_card_detail->id. ',' . $job_card_detail->item_id. ',' . $job_card_detail->rate. ',' . $job_card_detail->quantity. ',' . $job_card_detail->margin. ',' . $job_card_detail->is_main. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_card_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $job_card->id. ',' . $job_card->inquiry_id. ',' . $job_card->job_card_date_time. ',' . str_replace(',',' ',$job_card->remarks). ',' . $job_card->job_card_value. ',,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Job Card Detail created successfully',
                'data' => $job_card->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Job Card Detail creation failed'
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
        $job_card = \App\Model\JobCard::find($request->job_card_id);
        $job_card->inquiry_id = $request->inquiry_id;
        $job_card->job_card_no = $request->job_card_no;
        $job_card->job_card_date_time = date('Y-m-d', strtotime($request->job_card_date)).' '.$request->job_card_time;
        $job_card->remarks = $request->remarks;
        $job_card->user_id = $request->session()->get('users_id');
        
        if($job_card->save()) {
            $job_card_detail_id = '';
            if(isset($request->item['id'])){
                $job_card_detail = \App\Model\JobCardDetails::find($id);
                $job_card_detail->quantity = 0;
                $job_card_detail->margin = $request->margin;
                $job_card_detail->is_delete = 1;
                $job_card_detail->save();
            
                $old_job_card_detail = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)
                        ->where('item_id', $request->item['id'])
                        ->where('is_main', $request->is_main)
                        ->first();

                $job_card_detail = $old_job_card_detail ? $old_job_card_detail : new \App\Model\JobCardDetails();
                $job_card_detail->job_card_id = $job_card->id;
                $job_card_detail->item_id = $request->item['id'];
                $job_card_detail->rate = $request->rate;
                $job_card_detail->quantity = $old_job_card_detail ? $old_job_card_detail->quantity + $request->quantity : $request->quantity;
                $job_card_detail->margin = $old_job_card_detail ? ($old_job_card_detail->margin + $request->margin)/2 : $request->margin;
                $job_card_detail->is_main = $request->is_main ? 1 : 0;
                $job_card_detail->is_delete = 0;
                $job_card_detail->save();
                
                $job_card_detail_id = $job_card_detail->id;
            }
            
            $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($job_card_details as $job_card_detail){
                $margin = ($job_card_detail->margin + 100)/100;
                $total_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
            }
            $job_card->job_card_value = $total_value;
            $job_card->save();
            
            if($job_card_detail_id != ''){
                $job_card_detail = \App\Model\JobCardDetails::find($job_card_detail_id);    
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_card_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $job_card->id. ',' . $job_card->inquiry_id. ',' . $job_card->job_card_date_time. ',' . str_replace(',',' ',$job_card->remarks). ',' . $job_card->job_card_value. ',' . $job_card_detail->id. ',' . $job_card_detail->item_id. ',' . $job_card_detail->rate. ',' . $job_card_detail->quantity. ',' . $job_card_detail->margin. ',' . $job_card_detail->is_main. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_card_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $job_card->id. ',' . $job_card->inquiry_id. ',' . $job_card->job_card_date_time. ',' . str_replace(',',' ',$job_card->remarks). ',' . $job_card->job_card_value. ',,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Job Card Detail updated successfully',
                'data' => $job_card->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Job Card Detail updation failed'
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
            $job_card = \App\Model\JobCard::find($id);
            $job_card->is_delete = 1;

            if($job_card->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_card_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $job_card->id. ',,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
                
                $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)->where('is_delete', 0)->get();
                foreach ($job_card_details as $job_card_detail){
                    $job_card_detail->quantity = 0;
                    $job_card_detail->is_delete = 1;
                    $job_card_detail->save();
                }
            
                $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $job_card->id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($job_card_details as $job_card_detail){
                    $margin = ($job_card_detail->margin + 100)/100;
                    $total_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
                }
                $job_card->job_card_value = $total_value;
                $job_card->save();
                
                $result = array(
                    'response' => true,
                    'message' => 'Job Card deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Job Card deletion failed'
                );
            }
        } else if($request->type == 1){
            $job_card_detail = \App\Model\JobCardDetails::find($id);
            $job_card_detail->quantity = 0;
            $job_card_detail->is_delete = 1;

            if($job_card_detail->save()) {    
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_card_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $job_card_detail->job_card_id. ',,,,,' . $job_card_detail->id. ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
                
                $job_card = \App\Model\JobCard::find($job_card_detail->job_card_id);
                $job_card_details = \App\Model\JobCardDetails::where('job_card_id', $job_card_detail->job_card_id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($job_card_details as $job_card_detail){
                    $margin = ($job_card_detail->margin + 100)/100;
                    $total_value += $job_card_detail->rate * $margin * $job_card_detail->quantity;
                }
                $job_card->job_card_value = $total_value;
                $job_card->save();
                
                $result = array(
                    'response' => true,
                    'message' => 'Job Card Detail deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Job Card Detail deletion failed'
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
