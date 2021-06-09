<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class TechResponseFaultController extends Controller
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
        return view('master.tech_response_fault', $data);
    }

    public function tech_response_fault_list()
    {
        $tech_response_faults = \App\Model\TechResponseFault::select('id', 'code', 'name')->where('is_delete', 0)->get();
                
        $data = array(
            'tech_response_faults' => $tech_response_faults,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_tech_response_fault(Request $request)
    {
        if($request->value != $request->name){
            $tech_response_fault = \App\Model\TechResponseFault::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if($tech_response_fault){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_tech_response_fault(Request $request)
    {
        $tech_response_fault = \App\Model\TechResponseFault::select('id', 'name')->find($request->id);
        return response($tech_response_fault);
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
        $tech_response_fault = new \App\Model\TechResponseFault();
        $tech_response_fault->name = $request->name;
        
        if($tech_response_fault->save()) {
            $code = 'TRF'.sprintf('%03d', $tech_response_fault->id);
            $tech_response_fault->code = $code;
            $tech_response_fault->save();    
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_fault_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'. $tech_response_fault->id. ',' . $tech_response_fault->code. ',' . str_replace(',',' ',$tech_response_fault->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Tech Response Fault created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Tech Response Fault creation failed'
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
        $tech_response_fault = \App\Model\TechResponseFault::find($id);
        $tech_response_fault->name = $request->name;
        
        if($tech_response_fault->save()) {             
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_fault_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'. $tech_response_fault->id. ',' . $tech_response_fault->code. ',' . str_replace(',',' ',$tech_response_fault->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Tech Response Fault updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Tech Response Fault updation failed'
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
        $tech_response_fault = \App\Model\TechResponseFault::find($id);
        $tech_response_fault->is_delete = 1;
        
        if($tech_response_fault->save()) {           
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_fault_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'. $tech_response_fault->id. ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Tech Response Fault deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Tech Response Fault deletion failed'
            );
        }

        echo json_encode($result);
    }
}
