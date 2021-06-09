<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class CGroupController extends Controller
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
        return view('master.c_group', $data);
    }

    public function get_data()
    {
        $regions = \App\Model\Region::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $taxes = \App\Model\CTaxType::select('id', 'code', 'name', 'percentage')->where('is_delete', 0)->get();
        $collection_managers = \App\Model\CollectionManager::select('id', 'name')->where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        
        $data = array(
            'regions' => $regions,
            'taxes' => $taxes,
            'collection_managers' => $collection_managers
        );
        
        return response($data);
    }

    public function c_group_list()
    {
        $c_groups = \App\Model\CGroup::select('id', 'code', 'name', 'address', 'contact_no', 'email', 'region_id', 'collection_manager_id', 'contact_person_1', 'contact_person_no_1', 'contact_person_2', 'contact_person_no_2', 'contact_person_3', 'contact_person_no_3', 'invoice_name', 'invoice_delivering_address', 'collection_address', 'invoice_email', 'vat_no', 'svat_no', 'monitoring_fee')
                ->with(array('Region' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('CollectionManager' => function($query) {
                    $query->select('id', 'name');
                }))
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'c_groups' => $c_groups,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function validate_c_group(Request $request)
    {
        if($request->value != $request->name){
            $c_group = \App\Model\CGroup::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if($c_group){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function find_c_group(Request $request)
    {
        $c_group = \App\Model\CGroup::select('id', 'code', 'name', 'address', 'contact_no', 'email', 'region_id', 'collection_manager_id', 'contact_person_1', 'contact_person_no_1', 'contact_person_2', 'contact_person_no_2', 'contact_person_3', 'contact_person_no_3', 'invoice_name', 'invoice_delivering_address', 'collection_address', 'invoice_email', 'vat_no', 'svat_no', 'monitoring_fee')
                ->with(array('Region' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('CollectionManager' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('CGroupTax' => function($query) {
                    $query->select('id', 'group_id', 'tax_id')
                            ->with(array('CTaxType' => function($query) {
                                $query->select('id', 'code', 'name', 'percentage');
                            }));
                }))
                ->with(array('CGroupInvoiceMonth' => function($query) {
                    $query->select('id', 'group_id', 'month');
                }))
                ->find($request->id);
        return response($c_group);
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
        $c_group = new \App\Model\CGroup();
        $c_group->name = $request->name;
        $c_group->address = $request->address;
        $c_group->contact_no = $request->contact_no;
        $c_group->email = $request->email;
        $c_group->region_id = isset($request->region['id']) ? $request->region['id'] : 0;
        $c_group->collection_manager_id = isset($request->collection_manager['id']) ? $request->collection_manager['id'] : 0;
        $c_group->contact_person_1 = $request->contact_person_1;
        $c_group->contact_person_no_1 = $request->contact_person_no_1;
        $c_group->contact_person_2 = $request->contact_person_2;
        $c_group->contact_person_no_2 = $request->contact_person_no_2;
        $c_group->contact_person_3 = $request->contact_person_3;
        $c_group->contact_person_no_3 = $request->contact_person_no_3;
        $c_group->invoice_name = $request->invoice_name;
        $c_group->invoice_delivering_address = $request->invoice_delivering_address;
        $c_group->collection_address = $request->collection_address;
        $c_group->invoice_email = $request->invoice_email;
        $c_group->vat_no = $request->vat_no;
        $c_group->svat_no = $request->svat_no;
        $c_group->monitoring_fee = $request->monitoring_fee;
        
        if($c_group->save()) {
            $code = 'C-G'.sprintf('%03d', $c_group->id);
            $c_group->code = $code;
            $c_group->save();
        
            $c_group_inv_months = $c_group_taxes = '';
            foreach ($request->inv_months_1 as $detail){
                if($detail['selected']){
                    $c_group_inv_month = new \App\Model\CGroupInvoiceMonth();
                    $c_group_inv_month->group_id = $c_group->id;
                    $c_group_inv_month->month = $detail['id'];
                    $c_group_inv_month->save();
                    
                    $c_group_inv_months .= $c_group_inv_months != '' ? '|'.$c_group_inv_month->month : $c_group_inv_month->month;
                }
            }
            foreach ($request->inv_months_2 as $detail){
                if($detail['selected']){
                    $c_group_inv_month = new \App\Model\CGroupInvoiceMonth();
                    $c_group_inv_month->group_id = $c_group->id;
                    $c_group_inv_month->month = $detail['id'];
                    $c_group_inv_month->save();
                    
                    $c_group_inv_months .= $c_group_inv_months != '' ? '|'.$c_group_inv_month->month : $c_group_inv_month->month;
                }
            }
            foreach ($request->inv_months_3 as $detail){
                if($detail['selected']){
                    $c_group_inv_month = new \App\Model\CGroupInvoiceMonth();
                    $c_group_inv_month->group_id = $c_group->id;
                    $c_group_inv_month->month = $detail['id'];
                    $c_group_inv_month->save();
                    
                    $c_group_inv_months .= $c_group_inv_months != '' ? '|'.$c_group_inv_month->month : $c_group_inv_month->month;
                }
            }
            foreach ($request->taxes as $detail){
                if($detail['selected']){
                    $c_group_tax = new \App\Model\CGroupTax();
                    $c_group_tax->group_id = $c_group->id;
                    $c_group_tax->tax_id = $detail['id'];
                    $c_group_tax->save();
                    
                    $c_group_taxes .= $c_group_taxes != '' ? '|'.$c_group_tax->tax_id : $c_group_tax->tax_id;
                }
            }
               
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_group_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $c_group->id. ',' . str_replace(',',' ',$c_group->code). ',' . str_replace(',',' ',$c_group->name). ',' . str_replace(',',' ',$c_group->address). ',' . str_replace(',',' ',$c_group->contact_no). ',' . str_replace(',',' ',$c_group->email). ',' . $c_group->region_id. ',' . $c_group->collection_manager_id. ',' . str_replace(',',' ',$c_group->contact_person_1). ',' . str_replace(',',' ',$c_group->contact_person_no_1). ',' . str_replace(',',' ',$c_group->contact_person_2). ',' . str_replace(',',' ',$c_group->contact_person_no_2). ',' . str_replace(',',' ',$c_group->contact_person_3). ',' . str_replace(',',' ',$c_group->contact_person_no_3). ',' . str_replace(',',' ',$c_group->invoice_name). ',' . str_replace(',',' ',$c_group->invoice_delivering_address). ',' . str_replace(',',' ',$c_group->collection_address). ',' . str_replace(',',' ',$c_group->invoice_email). ',' . str_replace(',',' ',$c_group->vat_no). ',' . str_replace(',',' ',$c_group->svat_no). ',' . $c_group->monitoring_fee. ',' . $c_group_inv_months. ',' . $c_group_taxes. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'C-Group created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'C-Group creation failed'
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
        $c_group = \App\Model\CGroup::find($id);
        $c_group->name = $request->name;
        $c_group->address = $request->address;
        $c_group->contact_no = $request->contact_no;
        $c_group->email = $request->email;
        $c_group->region_id = isset($request->region['id']) ? $request->region['id'] : 0;
        $c_group->collection_manager_id = isset($request->collection_manager['id']) ? $request->collection_manager['id'] : 0;
        $c_group->contact_person_1 = $request->contact_person_1;
        $c_group->contact_person_no_1 = $request->contact_person_no_1;
        $c_group->contact_person_2 = $request->contact_person_2;
        $c_group->contact_person_no_2 = $request->contact_person_no_2;
        $c_group->contact_person_3 = $request->contact_person_3;
        $c_group->contact_person_no_3 = $request->contact_person_no_3;
        $c_group->invoice_name = $request->invoice_name;
        $c_group->invoice_delivering_address = $request->invoice_delivering_address;
        $c_group->collection_address = $request->collection_address;
        $c_group->invoice_email = $request->invoice_email;
        $c_group->vat_no = $request->vat_no;
        $c_group->svat_no = $request->svat_no;
        $c_group->monitoring_fee = $request->monitoring_fee;
        
        if($c_group->save()) {
            $c_group_inv_months = \App\Model\CGroupInvoiceMonth::where('group_id', $c_group->id)->where('is_delete', 0)->get();
            foreach ($c_group_inv_months as $c_group_inv_month){
                $c_group_inv_month->is_delete = 1;
                $c_group_inv_month->save();
            }
            $c_group_taxes = \App\Model\CGroupTax::where('group_id', $c_group->id)->where('is_delete', 0)->get();
            foreach ($c_group_taxes as $c_group_tax){
                $c_group_tax->is_delete = 1;
                $c_group_tax->save();
            }
		
            $c_group_inv_months = $c_group_taxes = '';
            foreach ($request->inv_months_1 as $detail){
                if($detail['selected']){
                    $c_group_inv_month = \App\Model\CGroupInvoiceMonth::where('group_id', $c_group->id)->where('month', $detail['id'])->first();
                    $c_group_inv_month = $c_group_inv_month ? $c_group_inv_month : new \App\Model\CGroupInvoiceMonth();
                    $c_group_inv_month->group_id = $c_group->id;
                    $c_group_inv_month->month = $detail['id'];
                    $c_group_inv_month->is_delete = 0;
                    $c_group_inv_month->save();
                    
                    $c_group_inv_months .= $c_group_inv_months != '' ? '|'.$c_group_inv_month->month : $c_group_inv_month->month;
                }
            }
            foreach ($request->inv_months_2 as $detail){
                if($detail['selected']){
                    $c_group_inv_month = \App\Model\CGroupInvoiceMonth::where('group_id', $c_group->id)->where('month', $detail['id'])->first();
                    $c_group_inv_month = $c_group_inv_month ? $c_group_inv_month : new \App\Model\CGroupInvoiceMonth();
                    $c_group_inv_month->group_id = $c_group->id;
                    $c_group_inv_month->month = $detail['id'];
                    $c_group_inv_month->is_delete = 0;
                    $c_group_inv_month->save();
                    
                    $c_group_inv_months .= $c_group_inv_months != '' ? '|'.$c_group_inv_month->month : $c_group_inv_month->month;
                }
            }
            foreach ($request->inv_months_3 as $detail){
                if($detail['selected']){
                    $c_group_inv_month = \App\Model\CGroupInvoiceMonth::where('group_id', $c_group->id)->where('month', $detail['id'])->first();
                    $c_group_inv_month = $c_group_inv_month ? $c_group_inv_month : new \App\Model\CGroupInvoiceMonth();
                    $c_group_inv_month->group_id = $c_group->id;
                    $c_group_inv_month->month = $detail['id'];
                    $c_group_inv_month->is_delete = 0;
                    $c_group_inv_month->save();
                    
                    $c_group_inv_months .= $c_group_inv_months != '' ? '|'.$c_group_inv_month->month : $c_group_inv_month->month;
                }
            }
            foreach ($request->taxes as $detail){
                if($detail['selected']){
                    $c_group_tax = \App\Model\CGroupTax::where('group_id', $c_group->id)->where('tax_id', $detail['id'])->first();
                    $c_group_tax = $c_group_tax ? $c_group_tax : new \App\Model\CGroupTax();
                    $c_group_tax->group_id = $c_group->id;
                    $c_group_tax->tax_id = $detail['id'];
                    $c_group_tax->is_delete = 0;
                    $c_group_tax->save();
                    
                    $c_group_taxes .= $c_group_taxes != '' ? '|'.$c_group_tax->tax_id : $c_group_tax->tax_id;
                }
            }
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_group_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $c_group->id. ',' . str_replace(',',' ',$c_group->code). ',' . str_replace(',',' ',$c_group->name). ',' . str_replace(',',' ',$c_group->address). ',' . str_replace(',',' ',$c_group->contact_no). ',' . str_replace(',',' ',$c_group->email). ',' . $c_group->region_id. ',' . $c_group->collection_manager_id. ',' . str_replace(',',' ',$c_group->contact_person_1). ',' . str_replace(',',' ',$c_group->contact_person_no_1). ',' . str_replace(',',' ',$c_group->contact_person_2). ',' . str_replace(',',' ',$c_group->contact_person_no_2). ',' . str_replace(',',' ',$c_group->contact_person_3). ',' . str_replace(',',' ',$c_group->contact_person_no_3). ',' . str_replace(',',' ',$c_group->invoice_name). ',' . str_replace(',',' ',$c_group->invoice_delivering_address). ',' . str_replace(',',' ',$c_group->collection_address). ',' . str_replace(',',' ',$c_group->invoice_email). ',' . str_replace(',',' ',$c_group->vat_no). ',' . str_replace(',',' ',$c_group->svat_no). ',' . $c_group->monitoring_fee. ',' . $c_group_inv_months. ',' . $c_group_taxes. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'C-Group updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'C-Group updation failed'
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
        $c_group = \App\Model\CGroup::find($id);
        $c_group->is_delete = 1;
        
        if($c_group->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_group_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $c_group->id. ',,,,,,,,,,,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'C-Group deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'C-Group deletion failed'
            );
        }

        echo json_encode($result);
    }
}
