<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class SalesTeamController extends Controller
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
        return view('master.sales_team', $data);
    }

    public function sales_team_list()
    {
        $sales_teams = \App\Model\SalesTeam::select('id', 'code', 'name', 'contact_no', 'sales_target', 'is_active')->where('is_delete', 0)->get();
                
        $data = array(
            'sales_teams' => $sales_teams,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_sales_team(Request $request)
    {
        if($request->value != $request->name){
            $sales_team = \App\Model\SalesTeam::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if($sales_team){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_sales_team(Request $request)
    {
        $sales_team = \App\Model\SalesTeam::select('id', 'name', 'contact_no', 'sales_target', 'is_active')->find($request->id);
        return response($sales_team);
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
        $sales_team = new \App\Model\SalesTeam();
        $sales_team->name = $request->name;
        $sales_team->contact_no = $request->contact_no;
        $sales_team->sales_target = $request->sales_target;
        $sales_team->is_active = $request->is_active;
        
        if($sales_team->save()) {
            $code = 'ST'.sprintf('%03d', $sales_team->id);
            $sales_team->code = $code;
            $sales_team->save();   
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/sales_team_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'. $sales_team->id. ',' . str_replace(',',' ',$sales_team->code). ',' . str_replace(',',' ',$sales_team->name). ',' . str_replace(',',' ',$sales_team->contact_no). ',' . $sales_team->sales_target. ','. $sales_team->is_active. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Sales Team created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Sales Team creation failed'
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
        $sales_team = \App\Model\SalesTeam::find($id);
        $sales_team->name = $request->name;
        $sales_team->contact_no = $request->contact_no;
        $sales_team->sales_target = $request->sales_target;
        $sales_team->is_active = $request->is_active;
        
        if($sales_team->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/sales_team_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'. $sales_team->id. ',' . str_replace(',',' ',$sales_team->code). ',' . str_replace(',',' ',$sales_team->name). ',' . str_replace(',',' ',$sales_team->contact_no). ',' . $sales_team->sales_target. ','. $sales_team->is_active. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Sales Team updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Sales Team updation failed'
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
        $sales_team = \App\Model\SalesTeam::find($id);
        $sales_team->is_delete = 1;
        
        if($sales_team->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/sales_team_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'. $sales_team->id. ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Sales Team deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Sales Team deletion failed'
            );
        }

        echo json_encode($result);
    }
}
