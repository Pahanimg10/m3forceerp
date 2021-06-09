<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class InventoryRegisterController extends Controller
{
    function __construct() 
    {
        $this->middleware('admin_access');
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
        return view('inventory.inventory_register', $data);
    }

    public function inventory_register_list(Request $request)
    {
        $inventory_registers = \App\Model\InventoryRegister::select('id', 'code', 'inventory_type_id', 'inventory_location_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks', 'is_issued')
                ->with(array('InventoryType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('InventoryLocation' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('InventoryIssueDetails' => function($query) {
                    $query->select('id', 'inventory_issue_id', 'inventory_register_id')
                            ->with(array('InventoryIssue' => function($query) {
                                $query->select('id', 'issued_to');
                            }));
                }))
                ->where(function($q) use($request){
                    $request->inventory_location_id != -1 ? $q->where('inventory_location_id', $request->inventory_location_id) : '';
                    $request->inventory_type_id != -1 ? $q->where('inventory_type_id', $request->inventory_type_id) : '';
                    $request->status_id != -1 ? $q->where('is_issued', $request->status_id) : '';
                })
                ->where('is_delete', 0)
                ->get();
        
        return response($inventory_registers);
    }

    public function validate_inventory_register(Request $request)
    {
        if($request->value != $request->serial_no){
            $inventory_register = \App\Model\InventoryRegister::where('serial_no', $request->serial_no)
                    ->where('is_delete', 0)
                    ->first();
            if($inventory_register){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_inventory_register(Request $request)
    {
        $inventory_register = \App\Model\InventoryRegister::select('id', 'code', 'inventory_type_id', 'inventory_location_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks', 'is_issued')
                ->with(array('InventoryType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('InventoryLocation' => function($query) {
                    $query->select('id', 'name');
                }))
                ->find($request->id);
        return response($inventory_register);
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
        $inventory_register = new \App\Model\InventoryRegister();
        $prefix_type = isset($request->inventory_type['code']) ? $request->inventory_type['code'] : '';
        $prefix_location = isset($request->inventory_location['code']) ? $request->inventory_location['code'] : '';
        $last_id = 0;
        if(isset($request->inventory_type['id'])){
            $last_inventory = \App\Model\InventoryRegister::selectRaw('COUNT(id) AS id')->where('inventory_type_id', $request->inventory_type['id'])->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_inventory ? $last_inventory->id : $last_id;
        }
        $inventory_register->code = 'IN-'.$prefix_location.'-'.$prefix_type.sprintf('%05d', $last_id+1);
        $inventory_register->inventory_type_id = isset($request->inventory_type['id']) ? $request->inventory_type['id'] : 0;
        $inventory_register->inventory_location_id = isset($request->inventory_location['id']) ? $request->inventory_location['id'] : 0;
        $inventory_register->name = $request->name;
        $inventory_register->model_no = $request->model_no;
        $inventory_register->imei = $request->imei;
        $inventory_register->serial_no = $request->serial_no;
        $inventory_register->credit_limit = $request->credit_limit ? $request->credit_limit : 0;
        $inventory_register->remarks = $request->remarks;
        
        if($inventory_register->save()) {
            $result = array(
                'response' => true,
                'message' => 'Inventory Register created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Inventory Register creation failed'
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
        $inventory_register = \App\Model\InventoryRegister::find($id);
        $inventory_register->code = $request->code;
        $inventory_register->inventory_type_id = isset($request->inventory_type['id']) ? $request->inventory_type['id'] : 0;
        $inventory_register->inventory_location_id = isset($request->inventory_location['id']) ? $request->inventory_location['id'] : 0;
        $inventory_register->name = $request->name;
        $inventory_register->model_no = $request->model_no;
        $inventory_register->imei = $request->imei;
        $inventory_register->serial_no = $request->serial_no;
        $inventory_register->credit_limit = $request->credit_limit ? $request->credit_limit : 0;
        $inventory_register->remarks = $request->remarks;
        
        if($inventory_register->save()) {
            $result = array(
                'response' => true,
                'message' => 'Inventory Register updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Inventory Register updation failed'
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
        $inventory_register = \App\Model\InventoryRegister::find($id);
        $inventory_register->is_delete = 1;
        
        if($inventory_register->save()) {
            $result = array(
                'response' => true,
                'message' => 'Inventory Register deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Inventory Register deletion failed'
            );
        }

        echo json_encode($result);
    }
}
