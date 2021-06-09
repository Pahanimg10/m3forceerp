<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class CollectionPersonController extends Controller
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
        return view('master.collection_person', $data);
    }

    public function collection_person_list()
    {
        $collection_persons = \App\Model\CollectionPerson::select('id', 'code', 'name', 'contact_no', 'is_active')->where('is_delete', 0)->get();
                
        $data = array(
            'collection_persons' => $collection_persons,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_collection_person(Request $request)
    {
        if($request->value != $request->name){
            $collection_person = \App\Model\CollectionPerson::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if($collection_person){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_collection_person(Request $request)
    {
        $collection_person = \App\Model\CollectionPerson::select('id', 'name', 'contact_no', 'is_active')->find($request->id);
        return response($collection_person);
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
        $collection_person = new \App\Model\CollectionPerson();
        $collection_person->name = $request->name;
        $collection_person->contact_no = $request->contact_no;
        $collection_person->is_active = $request->is_active;
        
        if($collection_person->save()) {
            $code = 'CP'.sprintf('%03d', $collection_person->id);
            $collection_person->code = $code;
            $collection_person->save();
               
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/collection_person_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $collection_person->id. ',' . $collection_person->code. ',' . str_replace(',',' ',$collection_person->name). ',' . str_replace(',',' ',$collection_person->contact_no). ',' . $collection_person->is_active. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Collection Person created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Collection Person creation failed'
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
        $collection_person = \App\Model\CollectionPerson::find($id);
        $collection_person->name = $request->name;
        $collection_person->contact_no = $request->contact_no;
        $collection_person->is_active = $request->is_active;
        
        if($collection_person->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/collection_person_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $collection_person->id. ',' . $collection_person->code. ',' . str_replace(',',' ',$collection_person->name). ',' . str_replace(',',' ',$collection_person->contact_no). ',' . $collection_person->is_active. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Collection Person updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Collection Person updation failed'
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
        $collection_person = \App\Model\CollectionPerson::find($id);
        $collection_person->is_delete = 1;
        
        if($collection_person->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/collection_person_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $collection_person->id. ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Collection Person deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Collection Person deletion failed'
            );
        }

        echo json_encode($result);
    }
}
