<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class CTaxTypeController extends Controller
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
        return view('master.c_tax_type', $data);
    }

    public function c_tax_type_list()
    {
        $c_tax_types = \App\Model\CTaxType::select('id', 'code', 'name', 'percentage')->where('is_delete', 0)->get();
                
        $data = array(
            'c_tax_types' => $c_tax_types,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_c_tax_type(Request $request)
    {
        if($request->value != $request->code){
            $c_tax_type = \App\Model\CTaxType::where('code', $request->code)
                    ->where('is_delete', 0)
                    ->first();
            if($c_tax_type){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_c_tax_type(Request $request)
    {
        $c_tax_type = \App\Model\CTaxType::select('id', 'code', 'name', 'percentage')->find($request->id);
        return response($c_tax_type);
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
        $c_tax_type = new \App\Model\CTaxType();
        $c_tax_type->code = $request->code;
        $c_tax_type->name = $request->name;
        $c_tax_type->percentage = $request->percentage;
        
        if($c_tax_type->save()) {   
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_tax_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $c_tax_type->id. ',' . str_replace(',',' ',$c_tax_type->code). ',' . str_replace(',',' ',$c_tax_type->name). ',' . $c_tax_type->percentage. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'C-Tax Type created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'C-Tax Type creation failed'
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
        $c_tax_type = \App\Model\CTaxType::find($id);
        $c_tax_type->code = $request->code;
        $c_tax_type->name = $request->name;
        $c_tax_type->percentage = $request->percentage;
        
        if($c_tax_type->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_tax_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $c_tax_type->id. ',' . str_replace(',',' ',$c_tax_type->code). ',' . str_replace(',',' ',$c_tax_type->name). ',' . $c_tax_type->percentage. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'C-Tax Type updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'C-Tax Type updation failed'
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
        $c_tax_type = \App\Model\CTaxType::find($id);
        $c_tax_type->is_delete = 1;
        
        if($c_tax_type->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_tax_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $c_tax_type->id. ',,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'C-Tax Type deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'C-Tax Type deletion failed'
            );
        }

        echo json_encode($result);
    }
}
