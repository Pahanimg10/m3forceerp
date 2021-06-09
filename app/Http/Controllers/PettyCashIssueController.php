<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class PettyCashIssueController extends Controller
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
        
        return view('petty_cash.petty_cash_issue', $data);
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
        
        $data['petty_cash_issue_id'] = $request->id;
        
        return view('petty_cash.petty_cash_issue_detail', $data);
    }

    public function validate_document_no(Request $request)
    {
        $exist = false;
        if($request->petty_cash_issue_type == 1){            
            $job = \App\Model\Job::where('job_no', $request->document)
                    ->where('is_completed', 0)
                    ->where('is_delete', 0)
                    ->first();
            $exist = $job ? true : false;
        } else if($request->petty_cash_issue_type == 2){            
            $tech_response = \App\Model\TechResponse::where('tech_response_no', $request->document)
                    ->where('is_completed', 0)
                    ->where('is_delete', 0)
                    ->first();
            $exist = $tech_response ? true : false;
        }
        
        if($exist){
            $response = 'true';
        } else{
            $response = 'false';
        }
            
        echo $response;
    }

    public function get_data()
    {
        $petty_cash_issue_types = \App\Model\ItemIssueType::select('id', 'name')->orderBy('name')->get(); 
        $issue_modes = \App\Model\PaymentMode::select('id', 'name')->orderBy('name')->get();
        $data = array(
            'petty_cash_issue_types' => $petty_cash_issue_types,
            'issue_modes' => $issue_modes
        );
        return response($data);
    }

    public function petty_cash_issue_list(Request $request)
    {
        $petty_cash_issues = \App\Model\PettyCashIssue::select('id', 'petty_cash_issue_type_id', 'document_id', 'petty_cash_request_date_time', 'petty_cash_issue_no', 'petty_cash_issue_date_time', 'issued_to', 'remarks', 'issue_mode_id', 'petty_cash_issue_value', 'cheque_no', 'bank', 'is_posted', 'logged_user', 'posted_user')
                ->with(array('ItemIssueType' => function($query) {
                    $query->select('id', 'name');
                }))
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
                }))
                ->with(array('IssueMode' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('LoggedUser' => function($query) {
                    $query->select('id', 'first_name');
                }))
                ->with(array('PostedUser' => function($query) {
                    $query->select('id', 'first_name');
                }))
                ->where(function($query) use($request){
                    $request->type != -1 ? $query->where('is_posted', $request->type) : '';
                    $request->issue_mode_id != -1 ? $query->where('issue_mode_id', $request->issue_mode_id) : '';
                })
                ->whereBetween('petty_cash_request_date_time', array($request->from.' 00:01', $request->to.' 23:59'))
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'petty_cash_issues' => $petty_cash_issues,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function find_petty_cash_issue(Request $request)
    {
        $petty_cash_issue = \App\Model\PettyCashIssue::select('id', 'petty_cash_issue_type_id', 'document_id', 'petty_cash_issue_no', 'issued_to', 'remarks', 'issue_mode_id', 'petty_cash_issue_value', 'cheque_no', 'bank', 'is_posted')
                ->with(array('ItemIssueType' => function($query) {
                    $query->select('id', 'name');
                }))
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
                }))
                ->with(array('IssueMode' => function($query) {
                    $query->select('id', 'name');
                }))
                ->find($request->id);
                
        $data = array(
            'petty_cash_issue' => $petty_cash_issue,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
                
        return response($data);
    }

    public function post_petty_cash_issue(Request $request)
    {
        $petty_cash_issue = \App\Model\PettyCashIssue::find($request->id);
        $petty_cash_issue->is_posted = 1;  
        $petty_cash_issue->petty_cash_issue_date_time = date('Y-m-d H:i');
        $petty_cash_issue->posted_user = $request->session()->get('users_id');
        if($petty_cash_issue->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_issue_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,' . $petty_cash_issue->id. ',,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Issued successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Issue failed'
            );
        }

        echo json_encode($result);
    }
    
    public function print_petty_cash_issue(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $petty_cash_issue = \App\Model\PettyCashIssue::find($request->id);
        $data['petty_cash_issue'] = $petty_cash_issue;
        $title = $petty_cash_issue ? 'Petty Cash Issue Details '.$petty_cash_issue->petty_cash_issue_no : 'Petty Cash Issue Details';
        
        $html = view('petty_cash.petty_cash_issue_pdf', $data);
        
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
        $petty_cash_issue = \App\Model\PettyCashIssue::find($request->petty_cash_issue_id);             
        $petty_cash_issue_type_id = isset($request->petty_cash_issue_type['id']) ? $request->petty_cash_issue_type['id'] : 0;     
        if(!$petty_cash_issue){
            $exist = true;
            $petty_cash_issue = new \App\Model\PettyCashIssue();
            $last_id = 0;
            $last_petty_cash_issue = \App\Model\PettyCashIssue::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_petty_cash_issue ? $last_petty_cash_issue->id : $last_id;
            $petty_cash_issue_type = '';
            $petty_cash_issue_type = $petty_cash_issue_type_id == 1 ? 'JB' : $petty_cash_issue_type;
            $petty_cash_issue_type = $petty_cash_issue_type_id == 2 ? 'FC' : $petty_cash_issue_type;
            $petty_cash_issue_type = $petty_cash_issue_type_id == 3 ? 'OT' : $petty_cash_issue_type;
            $petty_cash_issue->petty_cash_issue_no = 'PI/'.$petty_cash_issue_type.'/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id+1);
        }
          
        $petty_cash_issue->document_id = isset($request->document['id']) ? $request->document['id'] : 0;
        $petty_cash_issue->petty_cash_issue_type_id = $petty_cash_issue_type_id;     
        $petty_cash_issue->petty_cash_request_date_time = date('Y-m-d H:i');
        $petty_cash_issue->issued_to = $request->issued_to;
        $petty_cash_issue->issue_mode_id = isset($request->issue_mode['id']) ? $request->issue_mode['id'] : 0;
        $petty_cash_issue->petty_cash_issue_value = $request->petty_cash_issue_value;
        $petty_cash_issue->cheque_no = $request->cheque_no;
        $petty_cash_issue->bank = $request->bank;
        $petty_cash_issue->remarks = $request->remarks;
        $petty_cash_issue->logged_user = $request->session()->get('users_id');
        
        if($petty_cash_issue->save()) {    
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_issue_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $petty_cash_issue->id. ',' . $petty_cash_issue->petty_cash_issue_no. ',' . $petty_cash_issue->document_id. ',' . $petty_cash_issue->petty_cash_issue_type_id. ',' . $petty_cash_issue->petty_cash_request_date_time. ',' . str_replace(',',' ',$petty_cash_issue->issued_to). ',' . $petty_cash_issue->issue_mode_id. ',' . $petty_cash_issue->petty_cash_issue_value. ',' . str_replace(',',' ',$petty_cash_issue->cheque_no). ',' . str_replace(',',' ',$petty_cash_issue->bank). ',' . str_replace(',',' ',$petty_cash_issue->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Issue Detail created successfully',
                'data' => $petty_cash_issue
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Issue Detail creation failed'
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
        $petty_cash_issue = \App\Model\PettyCashIssue::find($request->petty_cash_issue_id);
        $petty_cash_issue_type_id = isset($request->petty_cash_issue_type['id']) ? $request->petty_cash_issue_type['id'] : 0;
        if($petty_cash_issue->petty_cash_issue_type_id != $petty_cash_issue_type_id){
            $last_id = 0;
            $last_petty_cash_issue = \App\Model\PettyCashIssue::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_petty_cash_issue ? $last_petty_cash_issue->id : $last_id;
            $petty_cash_issue_type = '';
            $petty_cash_issue_type = $petty_cash_issue_type_id == 1 ? 'JB' : $petty_cash_issue_type;
            $petty_cash_issue_type = $petty_cash_issue_type_id == 2 ? 'FC' : $petty_cash_issue_type;
            $petty_cash_issue_type = $petty_cash_issue_type_id == 3 ? 'OT' : $petty_cash_issue_type;
            $petty_cash_issue->petty_cash_issue_no = 'IS/'.$petty_cash_issue_type.'/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id+1);
        } else{
            $petty_cash_issue->petty_cash_issue_no = $request->petty_cash_issue_no;
        }
        $petty_cash_issue->petty_cash_issue_type_id = $petty_cash_issue_type_id; 
        $petty_cash_issue->document_id = isset($request->document['id']) ? $request->document['id'] : 0;
        $petty_cash_issue->issued_to = $request->issued_to;
        $petty_cash_issue->issue_mode_id = isset($request->issue_mode['id']) ? $request->issue_mode['id'] : 0;
        $petty_cash_issue->petty_cash_issue_value = $request->petty_cash_issue_value;
        $petty_cash_issue->cheque_no = $request->cheque_no;
        $petty_cash_issue->bank = $request->bank;
        $petty_cash_issue->remarks = $request->remarks;
        $petty_cash_issue->logged_user = $request->session()->get('users_id');
        
        if($petty_cash_issue->save()) {  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_issue_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $petty_cash_issue->id. ',' . $petty_cash_issue->petty_cash_issue_no. ',' . $petty_cash_issue->document_id. ',' . $petty_cash_issue->petty_cash_issue_type_id. ',' . $petty_cash_issue->petty_cash_request_date_time. ',' . str_replace(',',' ',$petty_cash_issue->issued_to). ',' . $petty_cash_issue->issue_mode_id. ',' . $petty_cash_issue->petty_cash_issue_value. ',' . str_replace(',',' ',$petty_cash_issue->cheque_no). ',' . str_replace(',',' ',$petty_cash_issue->bank). ',' . str_replace(',',' ',$petty_cash_issue->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Issue Detail updated successfully',
                'data' => $petty_cash_issue
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Issue Detail updation failed'
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
        $petty_cash_issue = \App\Model\PettyCashIssue::find($id);
        $petty_cash_issue->is_delete = 1;

        if($petty_cash_issue->save()) {  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/petty_cash_issue_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $petty_cash_issue->id. ',,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Petty Cash Issue deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Petty Cash Issue deletion failed'
            );
        }

        echo json_encode($result);
    }
}
