<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class IInquiryTypeController extends Controller
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
        return view('master.i_inquiry_type', $data);
    }

    public function i_inquiry_type_list()
    {
        $i_inquiry_types = \App\Model\IInquiryType::select('id', 'code', 'name')->where('is_delete', 0)->get();
                
        $data = array(
            'i_inquiry_types' => $i_inquiry_types,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_i_inquiry_type(Request $request)
    {
        if($request->value != $request->name){
            $i_inquiry_type = \App\Model\IInquiryType::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if($i_inquiry_type){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_i_inquiry_type(Request $request)
    {
        $i_inquiry_type = \App\Model\IInquiryType::select('id', 'name')->find($request->id);
        return response($i_inquiry_type);
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
        $i_inquiry_type = new \App\Model\IInquiryType();
        $i_inquiry_type->name = $request->name;
        
        if($i_inquiry_type->save()) {
            $code = 'I-IT'.sprintf('%03d', $i_inquiry_type->id);
            $i_inquiry_type->code = $code;
            $i_inquiry_type->save();
               
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/i_inquiry_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $i_inquiry_type->id. ',' . str_replace(',',' ',$i_inquiry_type->code). ',' . str_replace(',',' ',$i_inquiry_type->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'I-Inquiry Type created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'I-Inquiry Type creation failed'
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
        $i_inquiry_type = \App\Model\IInquiryType::find($id);
        $i_inquiry_type->name = $request->name;
        
        if($i_inquiry_type->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/i_inquiry_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $i_inquiry_type->id. ',' . str_replace(',',' ',$i_inquiry_type->code). ',' . str_replace(',',' ',$i_inquiry_type->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'I-Inquiry Type updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'I-Inquiry Type updation failed'
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
        $i_inquiry_type = \App\Model\IInquiryType::find($id);
        $i_inquiry_type->is_delete = 1;
        
        if($i_inquiry_type->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/i_inquiry_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $i_inquiry_type->id. ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'I-Inquiry Type deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'I-Inquiry Type deletion failed'
            );
        }

        echo json_encode($result);
    }
}
