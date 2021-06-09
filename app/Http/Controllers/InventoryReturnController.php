<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class InventoryReturnController extends Controller
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
        
        return view('inventory.inventory_return', $data);
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
        
        $data['inventory_return_id'] = $request->id;
        
        return view('inventory.inventory_return_detail', $data);
    }

    public function validate_inventory_issue_no(Request $request)
    {
        $inventory_issue = \App\Model\InventoryIssue::where('inventory_issue_no', $request->inventory_issue_no)
                ->where('is_posted', 1)                
                ->where('is_delete', 0)
                ->first();
        
        if($inventory_issue){
            $response = 'true';
        } else{
            $response = 'false';
        }
            
        echo $response;
    }

    public function validate_inventory_code(Request $request)
    {
        if($request->inventory_code_value != $request->inventory_code){
            $inventory_issue_detail = \App\Model\InventoryIssueDetails::whereHas('InventoryIssue', function ($query) use($request){
                            $query->where('id', $request->inventory_issue_id);
                        })
                        ->whereHas('InventoryRegister', function ($query) use($request){
                            $query->where('code', $request->inventory_code);
                        })
                        ->where('is_returned', 0)
                        ->where('is_delete', 0)
                        ->first();
            if($inventory_issue_detail){
                $response = 'true';
            } else{
                $response = 'false';
            }
        } else{
            $response = 'true';
        }
            
        echo $response;
    }

    public function inventory_return_list(Request $request)
    {
        $inventory_returns = \App\Model\InventoryReturn::select('id', 'inventory_issue_id', 'inventory_return_no', 'inventory_return_date_time', 'remarks', 'inventory_return_value', 'is_posted')
                ->with(array('InventoryIssue' => function($query) {
                    $query->select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted');
                }))
                ->whereBetween('inventory_return_date_time', array($request->from.' 00:01', $request->to.' 23:59'))
                ->where('is_delete', 0)
                ->get();
        
        return response($inventory_returns);
    }

    public function find_inventory_return(Request $request)
    {
        $inventory_return = \App\Model\InventoryReturn::select('id', 'inventory_issue_id', 'inventory_return_no', 'inventory_return_date_time', 'remarks', 'inventory_return_value', 'is_posted')
                ->with(array('InventoryIssue' => function($query) {
                    $query->select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted');
                }))
                ->find($request->id);
                                
        return response($inventory_return);
    }

    public function find_inventory_return_detail(Request $request)
    {
        $inventory_return_detail = \App\Model\InventoryReturnDetails::select('id', 'inventory_return_id', 'inventory_register_id', 'remarks')
                ->with(array('InventoryReturn' => function($query) {
                    $query->select('id', 'inventory_issue_id', 'inventory_return_no', 'inventory_return_date_time', 'remarks', 'inventory_return_value', 'is_posted')
                            ->with(array('InventoryIssue' => function($query) {
                                $query->select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted');
                            }));
                }))
                ->with(array('InventoryRegister' => function($query) {
                    $query->select('id', 'code', 'inventory_type_id', 'inventory_location_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks', 'is_issued')
                            ->with(array('InventoryType' => function($query) {
                                $query->select('id', 'name');
                            }))
                            ->with(array('InventoryLocation' => function($query) {
                                $query->select('id', 'name');
                            }));
                }))
                ->find($request->id);
        return response($inventory_return_detail);
    }

    public function inventory_return_detail_list(Request $request)
    {
        $inventory_return_details = \App\Model\InventoryReturnDetails::select('id', 'inventory_return_id', 'inventory_register_id', 'remarks')
                ->with(array('InventoryReturn' => function($query) {
                    $query->select('id', 'inventory_issue_id', 'inventory_return_no', 'inventory_return_date_time', 'remarks', 'inventory_return_value', 'is_posted')
                            ->with(array('InventoryIssue' => function($query) {
                                $query->select('id', 'inventory_issue_no', 'inventory_issue_date_time', 'issued_to', 'inventory_issue_value', 'remarks', 'is_posted');
                            }));
                }))
                ->with(array('InventoryRegister' => function($query) {
                    $query->select('id', 'code', 'inventory_type_id', 'inventory_location_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks', 'is_issued')
                            ->with(array('InventoryType' => function($query) {
                                $query->select('id', 'name');
                            }))
                            ->with(array('InventoryLocation' => function($query) {
                                $query->select('id', 'name');
                            }));
                }))
                ->where('inventory_return_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        
        return response($inventory_return_details);
    }

    public function post_inventory_return(Request $request)
    {
        $inventory_exist = true;
        $error = '';
        
        $inventory_return = \App\Model\InventoryReturn::find($request->id);
        $inventory_return_details = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                ->where('is_delete', 0)
                ->get();
        foreach ($inventory_return_details as $inventory_return_detail){
            if($inventory_return_detail->InventoryRegister->is_issued == 0){
                $inventory_exist = false;
                $error .= $error == '' ? $inventory_return_detail->InventoryRegister->code.' : '.$inventory_return_detail->InventoryRegister->name : ', '.$inventory_return_detail->InventoryRegister->code.' : '.$inventory_return_detail->InventoryRegister->name;
            }
        }
        
        if($inventory_exist){
            $inventory_return->is_posted = 1;        
            if($inventory_return->save()) {
                $inventory_return_details = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                        ->where('is_delete', 0)
                        ->get();
                foreach ($inventory_return_details as $inventory_return_detail){
                    $inventory_issue_detail = \App\Model\InventoryIssueDetails::where('inventory_issue_id', $inventory_return->inventory_issue_id)
                            ->where('inventory_register_id', $inventory_return_detail->inventory_register_id)
                            ->where('is_returned', 0)
                            ->where('is_delete', 0)
                            ->first();
                    if($inventory_issue_detail){
                        $inventory_issue_detail->is_returned = 1;
                        $inventory_issue_detail->save();
                    }
                    
                    $inventory_register = \App\Model\InventoryRegister::find($inventory_return_detail->inventory_register_id);
                    $inventory_register->remarks = $inventory_return_detail->remarks;
                    $inventory_register->is_issued = 0;
                    $inventory_register->save();
                }               
        
                $result = array(
                    'response' => true,
                    'message' => 'Inventory Return posted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inventory Return post failed'
                );
            }
        } else{
            $result = array(
                'response' => false,
                'message' => $error.' already returned to the inventory.'
            );
        }

        echo json_encode($result);
    }
    
    public function print_inventory_return(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $inventory_return = \App\Model\InventoryReturn::find($request->id);
        $data['inventory_return'] = $inventory_return;
        $title = $inventory_return ? 'Inventory Return Details '.$inventory_return->inventory_return_no : 'Inventory Return Details';
        
        $html = view('inventory.inventory_return_pdf', $data);
        
        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="'.$title.'.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
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
        $inventory_return = \App\Model\InventoryReturn::find($request->inventory_return_id);
             
        $inventory_issue_id = isset($request->inventory_issue['id']) ? $request->inventory_issue['id'] : 0;     
        if(!$inventory_return){
            $inventory_return = new \App\Model\InventoryReturn();
            $last_id = 0;
            $last_inventory_return = \App\Model\InventoryReturn::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_inventory_return ? $last_inventory_return->id : $last_id;
            $inventory_return->inventory_return_no = 'INR/'.date('m').'/'.date('y').'/'.$inventory_issue_id.'/'.sprintf('%05d', $last_id+1);
        }
          
        $inventory_return->inventory_issue_id = $inventory_issue_id;     
        $inventory_return->inventory_return_date_time = date('Y-m-d', strtotime($request->inventory_return_date)).' '.$request->inventory_return_time;
        $inventory_return->remarks = $request->remarks;
        
        if($inventory_return->save()) {
            if(isset($request->inventory_code['id'])){
                $old_inventory_return_detail = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                        ->where('inventory_register_id', $request->inventory_code['id'])
                        ->first();

                $inventory_return_detail = $old_inventory_return_detail ? $old_inventory_return_detail : new \App\Model\InventoryReturnDetails();
                $inventory_return_detail->inventory_return_id = $inventory_return->id;
                $inventory_return_detail->inventory_register_id = $request->inventory_code['id'];
                $inventory_return_detail->remarks = $request->inventory_remarks;
                $inventory_return_detail->is_delete = 0;
                $inventory_return_detail->save();
            }
            
            $inventory_return_details = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                    ->where('is_delete', 0)
                    ->get();
            $inventory_return_value = 0;
            foreach ($inventory_return_details as $inventory_return_detail){
                $inventory_return_value += $inventory_return_detail->InventoryRegister->credit_limit;
            }
            $inventory_return->inventory_return_value = $inventory_return_value;
            $inventory_return->save();
            
            $result = array(
                'response' => true,
                'message' => 'Inventory Return Detail created successfully',
                'data' => $inventory_return->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Inventory Return Detail creation failed'
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
        $inventory_return = \App\Model\InventoryReturn::find($request->inventory_return_id);
        $inventory_return->inventory_issue_id = isset($request->inventory_issue['id']) ? $request->inventory_issue['id'] : 0;
        $inventory_return->inventory_return_no = $request->inventory_return_no;
        $inventory_return->inventory_return_date_time = date('Y-m-d', strtotime($request->inventory_return_date)).' '.$request->inventory_return_time;
        $inventory_return->remarks = $request->remarks;
        
        if($inventory_return->save()) {
            if(isset($request->inventory_code['id'])){
                $inventory_return_detail = \App\Model\InventoryReturnDetails::find($id);
                $inventory_return_detail->is_delete = 1;
                $inventory_return_detail->save();       
                
                $old_inventory_return_detail = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                        ->where('inventory_register_id', $request->inventory_code['id'])
                        ->first();

                $inventory_return_detail = $old_inventory_return_detail ? $old_inventory_return_detail : new \App\Model\InventoryReturnDetails();
                $inventory_return_detail->inventory_return_id = $inventory_return->id;
                $inventory_return_detail->inventory_register_id = $request->inventory_code['id'];
                $inventory_return_detail->remarks = $request->inventory_remarks;
                $inventory_return_detail->is_delete = 0;
                $inventory_return_detail->save();
            }
            
            $inventory_return_details = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                    ->where('is_delete', 0)
                    ->get();
            $inventory_return_value = 0;
            foreach ($inventory_return_details as $inventory_return_detail){
                $inventory_return_value += $inventory_return_detail->InventoryRegister->credit_limit;
            }
            $inventory_return->inventory_return_value = $inventory_return_value;
            $inventory_return->save();
            
            $result = array(
                'response' => true,
                'message' => 'Inventory Return Detail updated successfully',
                'data' => $inventory_return->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Inventory Return Detail updation failed'
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
        if($request->type == 0){
            $inventory_return = \App\Model\InventoryReturn::find($id);
            $inventory_return->is_delete = 1;

            if($inventory_return->save()) {
                $inventory_return_details = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)->where('is_delete', 0)->get();
                foreach ($inventory_return_details as $inventory_return_detail){
                    $inventory_return_detail->is_delete = 1;
                    $inventory_return_detail->save();
                }
                
                $result = array(
                    'response' => true,
                    'message' => 'Inventory Return deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inventory Return deletion failed'
                );
            }
        } else if($request->type == 1){
            $inventory_return_detail = \App\Model\InventoryReturnDetails::find($id);
            $inventory_return_detail->is_delete = 1;

            if($inventory_return_detail->save()) { 
                $inventory_return = \App\Model\InventoryReturn::find($inventory_return_detail->inventory_return_id);
                $inventory_return_details = \App\Model\InventoryReturnDetails::where('inventory_return_id', $inventory_return->id)
                        ->where('is_delete', 0)
                        ->get();
                $inventory_return_value = 0;
                foreach ($inventory_return_details as $inventory_return_detail){
                    $inventory_return_value += $inventory_return_detail->InventoryRegister->credit_limit;
                }
                $inventory_return->inventory_return_value = $inventory_return_value;
                $inventory_return->save();
                
                $result = array(
                    'response' => true,
                    'message' => 'Inventory Return Detail deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Inventory Return Detail deletion failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Deletion failed'
            );
        }

        echo json_encode($result);
    }
}
