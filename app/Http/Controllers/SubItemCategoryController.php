<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class SubItemCategoryController extends Controller
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
        return view('master.sub_item_category', $data);
    }

    public function sub_item_category_list()
    {
        $sub_item_categorys = \App\Model\SubItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->get();
                
        $data = array(
            'sub_item_categorys' => $sub_item_categorys,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_sub_item_category(Request $request)
    {
        if($request->value != $request->code){
            $sub_item_category = \App\Model\SubItemCategory::where('code', $request->code)
                    ->where('is_delete', 0)
                    ->first();
            if($sub_item_category){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_sub_item_category(Request $request)
    {
        $sub_item_category = \App\Model\SubItemCategory::select('id', 'code', 'name')->find($request->id);
        return response($sub_item_category);
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
        $sub_item_category = new \App\Model\SubItemCategory();
        $sub_item_category->code = $request->code;
        $sub_item_category->name = $request->name;
        
        if($sub_item_category->save()) {     
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/sub_item_category_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'. $sub_item_category->id. ',' . str_replace(',',' ',$sub_item_category->code). ',' . str_replace(',',' ',$sub_item_category->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Sub Item Category created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Sub Item Category creation failed'
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
        $sub_item_category = \App\Model\SubItemCategory::find($id);
        $sub_item_category->code = $request->code;
        $sub_item_category->name = $request->name;
        
        if($sub_item_category->save()) {  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/sub_item_category_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'. $sub_item_category->id. ',' . str_replace(',',' ',$sub_item_category->code). ',' . str_replace(',',' ',$sub_item_category->name). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Sub Item Category updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Sub Item Category updation failed'
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
        $sub_item_category = \App\Model\SubItemCategory::find($id);
        $sub_item_category->is_delete = 1;
        
        if($sub_item_category->save()) {  
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/sub_item_category_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'. $sub_item_category->id. ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            $result = array(
                'response' => true,
                'message' => 'Sub Item Category deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Sub Item Category deletion failed'
            );
        }

        echo json_encode($result);
    }
}
