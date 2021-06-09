<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class DocumentTypeController extends Controller
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
        return view('master.document_type', $data);
    }

    public function document_type_list()
    {
        $document_types = \App\Model\DocumentType::select('id', 'code', 'name')->where('is_delete', 0)->get();
                
        $data = array(
            'document_types' => $document_types,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_document_type(Request $request)
    {
        if($request->value != $request->name){
            $document_type = \App\Model\DocumentType::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if($document_type){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_document_type(Request $request)
    {
        $document_type = \App\Model\DocumentType::select('id', 'name')->find($request->id);
        return response($document_type);
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
        $document_type = new \App\Model\DocumentType();
        $document_type->name = $request->name;
        
        if($document_type->save()) {
            $code = 'DT'.sprintf('%03d', $document_type->id);
            $document_type->code = $code;
            $document_type->save();
                  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/document_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $document_type->id. ',' . str_replace(',',' ',$document_type->code). ',' . str_replace(',',' ',$document_type->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Document Type created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Document Type creation failed'
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
        $document_type = \App\Model\DocumentType::find($id);
        $document_type->name = $request->name;
        
        if($document_type->save()) {      
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/document_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $document_type->id. ',' . str_replace(',',' ',$document_type->code). ',' . str_replace(',',' ',$document_type->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Document Type updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Document Type updation failed'
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
        $document_type = \App\Model\DocumentType::find($id);
        $document_type->is_delete = 1;
        
        if($document_type->save()) {      
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/document_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $document_type->id. ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Document Type deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Document Type deletion failed'
            );
        }

        echo json_encode($result);
    }
}
