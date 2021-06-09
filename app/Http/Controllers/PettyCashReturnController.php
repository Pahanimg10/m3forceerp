<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class PettyCashReturnController extends Controller
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
        
        return view('petty_cash.petty_cash_return', $data);
    }

    public function petty_cash_return_list(Request $request)
    {
        $petty_cash_returns = \App\Model\PettyCashReturn::select('id', 'petty_cash_issue_id', 'petty_cash_return_no', 'petty_cash_return_date_time', 'remarks', 'petty_cash_return_value', 'is_posted')
                ->with(array('PettyCashIssue' => function($query) {
                    $query->select('id', 'petty_cash_issue_type_id', 'petty_cash_issue_no', 'document_id', 'issued_to')
                        ->with(array('Job' => function($query) {
                            $query->select('id', 'inquiry_id', 'job_no')
                                    ->with(array('Inquiry' => function($query) {
                                        $query->select('id', 'contact_id')
                                                ->with(array('Contact' => function($query) {
                                                    $query->select('id', 'name');
                                                }));
                                    }));
                        }))
                        ->with(array('TechResponse' => function($query) {
                            $query->select('id', 'contact_id', 'tech_response_no')
                                    ->with(array('Contact' => function($query) {
                                        $query->select('id', 'name');
                                    }));
                        }));
                }))
                ->where(function($query) use($request){
                    $request->type != -1 ? $query->where('is_posted', $request->type) : '';
                })
                ->whereBetween('petty_cash_return_date_time', array($request->from.' 00:01', $request->to.' 23:59'))
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'petty_cash_returns' => $petty_cash_returns,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
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
        
        $data['petty_cash_return_id'] = $request->id;
        
        return view('petty_cash.petty_cash_return_detail', $data);
    }

    public function find_petty_cash_return(Request $request)
    {
        $petty_cash_return = \App\Model\PettyCashReturn::select('id', 'petty_cash_issue_id', 'petty_cash_return_no', 'petty_cash_return_date_time', 'remarks', 'petty_cash_return_value', 'is_posted')
                ->with(array('PettyCashIssue' => function($query) {
                    $query->select('id', 'petty_cash_issue_type_id', 'petty_cash_issue_no', 'document_id', 'issued_to')
                        ->with(array('Job' => function($query) {
                            $query->select('id', 'inquiry_id', 'job_no')
                                    ->with(array('Inquiry' => function($query) {
                                        $query->select('id', 'contact_id')
                                                ->with(array('Contact' => function($query) {
                                                    $query->select('id', 'name');
                                                }));
                                    }));
                        }))
                        ->with(array('TechResponse' => function($query) {
                            $query->select('id', 'contact_id', 'tech_response_no')
                                    ->with(array('Contact' => function($query) {
                                        $query->select('id', 'name');
                                    }));
                        }));
                }))
                ->find($request->id);
                
        $data = array(
            'petty_cash_return' => $petty_cash_return,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
                
        return response($data);
    }

    public function validate_petty_cash_return_value(Request $request)
    {            
        $petty_cash_issue = \App\Model\PettyCashIssue::where('petty_cash_issue_no', $request->petty_cash_issue_no)
                ->where('is_posted', 1)
                ->where('is_delete', 0)
                ->first();
        
        if($petty_cash_issue){
            $total_value = $petty_cash_issue->petty_cash_issue_value;
            
            $petty_cash_returns = \App\Model\PettyCashReturn::where('petty_cash_issue_id', $petty_cash_issue->id)
                    ->where('is_posted', 1)
                    ->where('is_delete', 0)
                    ->get();
            $return_value = 0;
            foreach ($petty_cash_returns as $petty_cash_return){
                $return_value += $petty_cash_return->petty_cash_return_value;
            }
            
            if($total_value - $return_value - $request->petty_cash_return_value >= 0){
                $response = 'true';
            } else{
                $response = 'false';
            }
        } else{
            $response = 'false';
        }
            
        echo $response;
    }

    public function validate_petty_cash_issue_no(Request $request)
    {            
        $petty_cash_issue = \App\Model\PettyCashIssue::where('petty_cash_issue_no', $request->petty_cash_issue_no)
                ->where('is_posted', 1)
                ->where('is_delete', 0)
                ->first();
        
        if($petty_cash_issue){
            $response = 'true';
        } else{
            $response = 'false';
        }
            
        echo $response;
    }

    public function post_petty_cash_return(Request $request)
    {
        $petty_cash_return = \App\Model\PettyCashReturn::find($request->id);
        $petty_cash_return->is_posted = 1;  
        if($petty_cash_return->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_return_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,' . $petty_cash_return->id. ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Returnd successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Return failed'
            );
        }

        echo json_encode($result);
    }
    
    public function print_petty_cash_return(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $petty_cash_return = \App\Model\PettyCashReturn::find($request->id);
        $data['petty_cash_return'] = $petty_cash_return;
        $title = $petty_cash_return ? 'Petty Cash Return Details '.$petty_cash_return->petty_cash_return_no : 'Petty Cash Return Details';
        
        $html = view('petty_cash.petty_cash_return_pdf', $data);
        
        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="'.$title.'.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 0,
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
        $petty_cash_return = \App\Model\PettyCashReturn::find($request->petty_cash_return_id);      
        $petty_cash_issue_id = isset($request->petty_cash_issue_no['id']) ? $request->petty_cash_issue_no['id'] : 0;
        if(!$petty_cash_return){
            $petty_cash_return = new \App\Model\PettyCashReturn();
            $last_id = 0;
            $last_petty_cash_return = \App\Model\PettyCashReturn::select('id')->where('petty_cash_issue_id', $petty_cash_issue_id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_petty_cash_return ? $last_petty_cash_return->id : $last_id;
            $petty_cash_return->petty_cash_return_no = 'PR/'.date('m').'/'.date('y').'/'.$petty_cash_issue_id.'/'.sprintf('%05d', $last_id+1);
        }
          
        $petty_cash_return->petty_cash_issue_id = $petty_cash_issue_id;
        $petty_cash_return->petty_cash_return_date_time = date('Y-m-d', strtotime($request->petty_cash_return_date)).' '.$request->petty_cash_return_time;
        $petty_cash_return->petty_cash_return_value = $request->petty_cash_return_value;
        $petty_cash_return->remarks = $request->remarks;
        
        if($petty_cash_return->save()) {    
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_return_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $petty_cash_return->id. ',' . $petty_cash_return->petty_cash_return_no. ',' . $petty_cash_return->petty_cash_issue_id. ',' . $petty_cash_return->petty_cash_return_date_time. ',' . $petty_cash_return->petty_cash_return_value. ',' . str_replace(',',' ',$petty_cash_return->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Return Detail created successfully',
                'data' => $petty_cash_return
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Return Detail creation failed'
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
        $petty_cash_return = \App\Model\PettyCashReturn::find($request->petty_cash_return_id);      
        $petty_cash_issue_id = isset($request->petty_cash_issue_no['id']) ? $request->petty_cash_issue_no['id'] : 0;
        if(!$petty_cash_return){
            $petty_cash_return = new \App\Model\PettyCashReturn();
            $last_id = 0;
            $last_petty_cash_return = \App\Model\PettyCashReturn::select('id')->where('petty_cash_issue_id', $petty_cash_issue_id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_petty_cash_return ? $last_petty_cash_return->id : $last_id;
            $petty_cash_return->petty_cash_return_no = 'PR/'.date('m').'/'.date('y').'/'.$petty_cash_issue_id.'/'.sprintf('%05d', $last_id+1);
        }
          
        $petty_cash_return->petty_cash_issue_id = $petty_cash_issue_id;
        $petty_cash_return->petty_cash_return_date_time = date('Y-m-d', strtotime($request->petty_cash_return_date)).' '.$request->petty_cash_return_time;
        $petty_cash_return->petty_cash_return_value = $request->petty_cash_return_value;
        $petty_cash_return->remarks = $request->remarks;
        
        if($petty_cash_return->save()) {  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_return_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $petty_cash_return->id. ',' . $petty_cash_return->petty_cash_return_no. ',' . $petty_cash_return->petty_cash_issue_id. ',' . $petty_cash_return->petty_cash_return_date_time. ',' . $petty_cash_return->petty_cash_return_value. ',' . str_replace(',',' ',$petty_cash_return->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Return Detail updated successfully',
                'data' => $petty_cash_return
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Return Detail updation failed'
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
        $petty_cash_return = \App\Model\PettyCashReturn::find($id);
        $petty_cash_return->is_delete = 1;

        if($petty_cash_return->save()) {  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_return_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $petty_cash_return->id. ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Return deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Return deletion failed'
            );
        }

        echo json_encode($result);
    }
}