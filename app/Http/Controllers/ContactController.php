<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class ContactController extends Controller
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
        return view('master.contact', $data);
    }

    public function contact_list(Request $request)
    {
        $contacts = \App\Model\Contact::select('id', 'contact_type_id', 'contact_id', 'code', 'name', 'address', 'contact_no', 'email', 'start_date', 'end_date', 'is_active')
                ->with(array('CContactType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->where(function($q) use($request){
                    $request->contact_type != -1 ? $q->where('contact_type_id', $request->contact_type) : '';
                })
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'contacts' => $contacts,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function find_contact(Request $request)
    {
        $contact = \App\Model\Contact::select('id', 'contact_type_id', 'business_type_id', 'contact_id', 'code', 'name', 'nic', 'address', 'contact_no', 'email', 'start_date', 'end_date', 'region_id', 'collection_manager_id', 'contact_person_1', 'contact_person_no_1', 'contact_person_2', 'contact_person_no_2', 'contact_person_3', 'contact_person_no_3', 'invoice_name', 'invoice_delivering_address', 'collection_address', 'invoice_email', 'vat_no', 'svat_no', 'monitoring_fee', 'service_mode_id', 'client_type_id', 'group_id', 'is_group', 'is_active')
                ->with(array('CContactType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('IBusinessType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('Region' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('CollectionManager' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('ServiceMode' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('IClientType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('CGroup' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('ContactTax' => function($query) {
                    $query->select('id', 'contact_id', 'tax_id')
                            ->with(array('CTaxType' => function($query) {
                                $query->select('id', 'code', 'name', 'percentage');
                            }));
                }))
                ->with(array('ContactInvoiceMonth' => function($query) {
                    $query->select('id', 'contact_id', 'month');
                }))
                ->find($request->id);
        return response($contact);
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
        
        $data['contact_id'] = $request->id;
        
        return view('master.contact_detail', $data);
    }

    public function validate_contact(Request $request)
    {
        if($request->value != $request->contact_id){
            $contact = \App\Model\Contact::where('contact_id', $request->contact_id)
                    ->where('is_delete', 0)
                    ->first();
            if($contact){
                $response = 'false';
            } else{
                $response = 'true';
            }
        } else{
            $response = 'true';
        }
        
        echo $response;
    }

    public function get_data()
    {
        $contact_types = \App\Model\CContactType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $business_types = \App\Model\IBusinessType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $regions = \App\Model\Region::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $taxes = \App\Model\CTaxType::select('id', 'code', 'name', 'percentage')->where('is_delete', 0)->orderBy('name')->get();
        $groups = \App\Model\CGroup::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $collection_managers = \App\Model\CollectionManager::select('id', 'name')->where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        $service_modes = \App\Model\ServiceMode::select('id', 'name')->orderBy('name')->get();
        $client_types = \App\Model\IClientType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        
        $data = array(
            'contact_types' => $contact_types,
            'business_types' => $business_types,
            'regions' => $regions,
            'taxes' => $taxes,
            'groups' => $groups,
            'collection_managers' => $collection_managers,
            'service_modes' => $service_modes,
            'client_types' => $client_types
        );
        
        return response($data);
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
        $contact = new \App\Model\Contact();
        
        $contact->contact_type_id = isset($request->contact_type['id']) ? $request->contact_type['id'] : 0;
        $contact->business_type_id = isset($request->business_type['id']) && isset($request->contact_type['id']) && $request->contact_type['id'] == 2 ? $request->business_type['id'] : 0;
        $prefix = '';
        $prefix = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 ? 'C-MC' : $prefix;
        $prefix = isset($request->contact_type['id']) && $request->contact_type['id'] == 2 ? 'C-NMC' : $prefix;
        $prefix = isset($request->contact_type['id']) && $request->contact_type['id'] == 3 ? 'C-S' : $prefix;
        $last_id = 0;
        if(isset($request->contact_type['id'])){
            $last_contact = \App\Model\Contact::selectRaw('COUNT(id) AS id')->where('contact_type_id', $request->contact_type['id'])->orderBy('id', 'desc')->first();
            $last_id = $last_contact ? $last_contact->id : $last_id;
        }
        $contact->code = $prefix.sprintf('%05d', $last_id+1);
        $contact->name = $request->name;
        $contact->nic = $request->nic;
        $contact->address = $request->address;
        $contact->contact_no = $request->contact_no;
        $contact->email = $request->email;
        $contact->region_id = !$request->is_group ? isset($request->region['id']) ? $request->region['id'] : 0 : 0;
        $contact->collection_manager_id = !$request->is_group ? isset($request->collection_manager['id']) ? $request->collection_manager['id'] : 0 : 0;
        $contact->contact_person_1 = !$request->is_group ? $request->contact_person_1 : '';
        $contact->contact_person_no_1 = !$request->is_group ? $request->contact_person_no_1 : '';
        $contact->contact_person_2 = !$request->is_group ? $request->contact_person_2 : '';
        $contact->contact_person_no_2 = !$request->is_group ? $request->contact_person_no_2 : '';
        $contact->contact_person_3 = !$request->is_group ? $request->contact_person_3 : '';
        $contact->contact_person_no_3 = !$request->is_group ? $request->contact_person_no_3 : '';
        $contact->start_date = isset($request->contact_type['id']) && ($request->contact_type['id'] == 1 || $request->contact_type['id'] == 2) ? date('Y-m-d', strtotime($request->start_date)) : '';
        $contact->end_date = isset($request->contact_type['id']) && ($request->contact_type['id'] == 1 || $request->contact_type['id'] == 2) ? date('Y-m-d', strtotime($request->end_date)) : '';
        $contact->invoice_name = !$request->is_group ? $request->invoice_name : '';
        $contact->invoice_delivering_address = !$request->is_group ? $request->invoice_delivering_address : '';
        $contact->collection_address = !$request->is_group ? $request->collection_address : '';
        $contact->invoice_email = !$request->is_group ? $request->invoice_email : '';
        $contact->vat_no = !$request->is_group ? $request->vat_no : '';
        $contact->svat_no = !$request->is_group ? $request->svat_no : '';
        $contact->monitoring_fee = !$request->is_group ? $request->monitoring_fee : 0;
        $contact->service_mode_id = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 && isset($request->service_mode['id']) ? $request->service_mode['id'] : 0;
        $contact->client_type_id = isset($request->contact_type['id']) && ($request->contact_type['id'] == 1 || $request->contact_type['id'] == 2) && isset($request->client_type['id']) ? $request->client_type['id'] : 0;
        $contact->group_id = $request->is_group ? isset($request->group['id']) ? $request->group['id'] : 0 : 0;
        $contact->is_group = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 ? $request->is_group ? 1 : 0 : 0;
        $contact->is_active = $request->is_active ? 1 : 0;
        
        if($contact->save()) {
            $contact->contact_id = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 ? $request->contact_id : $contact->id;
            $contact->save();
        
            $cus_inv_months = $cus_taxes = '';
            if(!$request->is_group){
                foreach ($request->inv_months_1 as $detail){
                    if($detail['selected']){
                        $contact_inv_month = new \App\Model\ContactInvoiceMonth();
                        $contact_inv_month->contact_id = $contact->id;
                        $contact_inv_month->month = $detail['id'];
                        $contact_inv_month->save();
                        
                        $cus_inv_months .= $cus_inv_months != '' ? '|'.$contact_inv_month->month : $contact_inv_month->month;
                    }
                }
                foreach ($request->inv_months_2 as $detail){
                    if($detail['selected']){
                        $contact_inv_month = new \App\Model\ContactInvoiceMonth();
                        $contact_inv_month->contact_id = $contact->id;
                        $contact_inv_month->month = $detail['id'];
                        $contact_inv_month->save();
                        
                        $cus_inv_months .= $cus_inv_months != '' ? '|'.$contact_inv_month->month : $contact_inv_month->month;
                    }
                }
                foreach ($request->inv_months_3 as $detail){
                    if($detail['selected']){
                        $contact_inv_month = new \App\Model\ContactInvoiceMonth();
                        $contact_inv_month->contact_id = $contact->id;
                        $contact_inv_month->month = $detail['id'];
                        $contact_inv_month->save();
                        
                        $cus_inv_months .= $cus_inv_months != '' ? '|'.$contact_inv_month->month : $contact_inv_month->month;
                    }
                }
                foreach ($request->taxes as $detail){
                    if($detail['selected']){
                        $contact_tax = new \App\Model\ContactTax();
                        $contact_tax->contact_id = $contact->id;
                        $contact_tax->tax_id = $detail['id'];
                        $contact_tax->save();
                        
                        $cus_taxes .= $cus_taxes != '' ? '|'.$contact_tax->tax_id : $contact_tax->tax_id;
                    }
                }
            }  
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $contact->id. ',' . $contact->contact_type_id. ',' . $contact->business_type_id. ',' . $contact->contact_id. ',' . $contact->code. ',' . str_replace(',',' ',$contact->name). ',' . str_replace(',',' ',$contact->nic). ',' . str_replace(',',' ',$contact->address). ',' . str_replace(',',' ',$contact->contact_no). ',' . str_replace(',',' ',$contact->email). ',' . $contact->region_id. ',' . $contact->collection_manager_id. ',' . str_replace(',',' ',$contact->contact_person_1). ',' . str_replace(',',' ',$contact->contact_person_no_1). ',' . str_replace(',',' ',$contact->contact_person_2). ',' . str_replace(',',' ',$contact->contact_person_no_2). ',' . str_replace(',',' ',$contact->contact_person_3). ',' . str_replace(',',' ',$contact->contact_person_no_3) . ',' . $contact->start_date . ',' . $contact->end_date . ',' . str_replace(',',' ',$contact->invoice_name). ',' . str_replace(',',' ',$contact->invoice_delivering_address). ',' . str_replace(',',' ',$contact->collection_address). ',' . str_replace(',',' ',$contact->invoice_email). ',' . str_replace(',',' ',$contact->vat_no). ',' . str_replace(',',' ',$contact->svat_no). ',' . str_replace(',',' ',$contact->monitoring_fee). ',' . $contact->service_mode_id. ',' . $contact->client_type_id. ',' . $contact->group_id. ',' . $contact->is_group. ',' . $contact->is_active. ',' . $cus_inv_months. ',' . $cus_taxes. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Contact created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Contact creation failed'
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
        $contact = \App\Model\Contact::find($id);
        
        $contact_changed = false;
        if(isset($request->contact_type['id']) && $request->contact_type['id'] != $contact->contact_type_id){
            $contact_changed = true;
        }

        $contact->contact_type_id = isset($request->contact_type['id']) ? $request->contact_type['id'] : 0;
        $contact->business_type_id = isset($request->business_type['id']) && isset($request->contact_type['id']) && $request->contact_type['id'] == 2 ? $request->business_type['id'] : 0;
        $contact->contact_id = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 ? $request->contact_id : $contact->id;
        $contact->name = $request->name;
        $contact->nic = $request->nic;
        $contact->address = $request->address;
        $contact->contact_no = $request->contact_no;
        $contact->email = $request->email;
        $contact->region_id = !$request->is_group ? isset($request->region['id']) ? $request->region['id'] : 0 : 0;
        $contact->collection_manager_id = !$request->is_group ? isset($request->collection_manager['id']) ? $request->collection_manager['id'] : 0 : 0;
        $contact->contact_person_1 = !$request->is_group ? $request->contact_person_1 : '';
        $contact->contact_person_no_1 = !$request->is_group ? $request->contact_person_no_1 : '';
        $contact->contact_person_2 = !$request->is_group ? $request->contact_person_2 : '';
        $contact->contact_person_no_2 = !$request->is_group ? $request->contact_person_no_2 : '';
        $contact->contact_person_3 = !$request->is_group ? $request->contact_person_3 : '';
        $contact->contact_person_no_3 = !$request->is_group ? $request->contact_person_no_3 : '';
        $contact->start_date = isset($request->contact_type['id']) && ($request->contact_type['id'] == 1 || $request->contact_type['id'] == 2) ? date('Y-m-d', strtotime($request->start_date)) : '';
        $contact->end_date = isset($request->contact_type['id']) && ($request->contact_type['id'] == 1 || $request->contact_type['id'] == 2) ? date('Y-m-d', strtotime($request->end_date)) : '';
        $contact->invoice_name = !$request->is_group ? $request->invoice_name : '';
        $contact->invoice_delivering_address = !$request->is_group ? $request->invoice_delivering_address : '';
        $contact->collection_address = !$request->is_group ? $request->collection_address : '';
        $contact->invoice_email = !$request->is_group ? $request->invoice_email : '';
        $contact->vat_no = !$request->is_group ? $request->vat_no : '';
        $contact->svat_no = !$request->is_group ? $request->svat_no : '';
        $contact->monitoring_fee = !$request->is_group ? $request->monitoring_fee : 0;
        $contact->service_mode_id = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 && isset($request->service_mode['id']) ? $request->service_mode['id'] : 0;
        $contact->client_type_id = isset($request->contact_type['id']) && ($request->contact_type['id'] == 1 || $request->contact_type['id'] == 2) && isset($request->client_type['id']) ? $request->client_type['id'] : 0;
        $contact->group_id = $request->is_group ? isset($request->group['id']) ? $request->group['id'] : 0 : 0;
        $contact->is_group = isset($request->contact_type['id']) && $request->contact_type['id'] == 1 ? $request->is_group ? 1 : 0 : 0;
        $contact->is_active = $request->is_active ? 1 : 0;
        
        if($contact->save()) {
            $contact_inv_months = \App\Model\ContactInvoiceMonth::where('contact_id', $contact->id)->where('is_delete', 0)->get();
            foreach ($contact_inv_months as $contact_inv_month){
                $contact_inv_month->is_delete = 1;
                $contact_inv_month->save();
            }
            $contact_taxes = \App\Model\ContactTax::where('contact_id', $contact->id)->where('is_delete', 0)->get();
            foreach ($contact_taxes as $contact_tax){
                $contact_tax->is_delete = 1;
                $contact_tax->save();
            }
            
            $cus_inv_months = $cus_taxes = '';
            if(!$request->is_group){
                foreach ($request->inv_months_1 as $detail){
                    if($detail['selected']){
                        $contact_inv_month = \App\Model\ContactInvoiceMonth::where('contact_id', $contact->id)->where('month', $detail['id'])->first();
                        $contact_inv_month = $contact_inv_month ? $contact_inv_month : new \App\Model\ContactInvoiceMonth();
                        $contact_inv_month->contact_id = $contact->id;
                        $contact_inv_month->month = $detail['id'];
                        $contact_inv_month->is_delete = 0;
                        $contact_inv_month->save();
                        
                        $cus_inv_months .= $cus_inv_months != '' ? '|'.$contact_inv_month->month : $contact_inv_month->month;
                    }
                }
                foreach ($request->inv_months_2 as $detail){
                    if($detail['selected']){
                        $contact_inv_month = \App\Model\ContactInvoiceMonth::where('contact_id', $contact->id)->where('month', $detail['id'])->first();
                        $contact_inv_month = $contact_inv_month ? $contact_inv_month : new \App\Model\ContactInvoiceMonth();
                        $contact_inv_month->contact_id = $contact->id;
                        $contact_inv_month->month = $detail['id'];
                        $contact_inv_month->is_delete = 0;
                        $contact_inv_month->save();
                        
                        $cus_inv_months .= $cus_inv_months != '' ? '|'.$contact_inv_month->month : $contact_inv_month->month;
                    }
                }
                foreach ($request->inv_months_3 as $detail){
                    if($detail['selected']){
                        $contact_inv_month = \App\Model\ContactInvoiceMonth::where('contact_id', $contact->id)->where('month', $detail['id'])->first();
                        $contact_inv_month = $contact_inv_month ? $contact_inv_month : new \App\Model\ContactInvoiceMonth();
                        $contact_inv_month->contact_id = $contact->id;
                        $contact_inv_month->month = $detail['id'];
                        $contact_inv_month->is_delete = 0;
                        $contact_inv_month->save();
                        
                        $cus_inv_months .= $cus_inv_months != '' ? '|'.$contact_inv_month->month : $contact_inv_month->month;
                    }
                }
                foreach ($request->taxes as $detail){
                    if($detail['selected']){
                        $contact_tax = \App\Model\ContactTax::where('contact_id', $contact->id)->where('tax_id', $detail['id'])->first();
                        $contact_tax = $contact_tax ? $contact_tax : new \App\Model\ContactTax();
                        $contact_tax->contact_id = $contact->id;
                        $contact_tax->tax_id = $detail['id'];
                        $contact_tax->is_delete = 0;
                        $contact_tax->save();
                        
                        $cus_taxes .= $cus_taxes != '' ? '|'.$contact_tax->tax_id : $contact_tax->tax_id;
                    }
                }
            }
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $contact->id. ',' . $contact->contact_type_id. ',' . $contact->business_type_id. ',' . $contact->contact_id. ',' . $contact->code. ',' . str_replace(',',' ',$contact->name). ',' . str_replace(',',' ',$contact->nic). ',' . str_replace(',',' ',$contact->address). ',' . str_replace(',',' ',$contact->contact_no). ',' . str_replace(',',' ',$contact->email). ',' . $contact->region_id. ',' . $contact->collection_manager_id. ',' . str_replace(',',' ',$contact->contact_person_1). ',' . str_replace(',',' ',$contact->contact_person_no_1). ',' . str_replace(',',' ',$contact->contact_person_2). ',' . str_replace(',',' ',$contact->contact_person_no_2). ',' . str_replace(',',' ',$contact->contact_person_3). ',' . str_replace(',',' ',$contact->contact_person_no_3) . ',' . $contact->start_date . ',' . $contact->end_date . ',' . str_replace(',',' ',$contact->invoice_name). ',' . str_replace(',',' ',$contact->invoice_delivering_address). ',' . str_replace(',',' ',$contact->collection_address). ',' . str_replace(',',' ',$contact->invoice_email). ',' . str_replace(',',' ',$contact->vat_no). ',' . str_replace(',',' ',$contact->svat_no). ',' . str_replace(',',' ',$contact->monitoring_fee). ',' . $contact->service_mode_id. ',' . $contact->client_type_id. ',' . $contact->group_id. ',' . $contact->is_group. ',' . $contact->is_active. ',' . $cus_inv_months. ',' . $cus_taxes. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);

            if($contact_changed){
                $contact_types = \App\Model\CContactType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
                foreach($contact_types as $contact_type){
                    $prefix = '';
                    $prefix = $contact_type->id == 1 ? 'C-MC' : $prefix;
                    $prefix = $contact_type->id == 2 ? 'C-NMC' : $prefix;
                    $prefix = $contact_type->id == 3 ? 'C-S' : $prefix;
                    $contacts = \App\Model\Contact::where('contact_type_id', $contact_type->id)->get();
                    $count = 1;
                    foreach($contacts as $contact){
                        $contact->code = $prefix.sprintf('%05d', $count);
                        $contact->save();
                        $count++;
                    }
                }
            }
            
            $result = array(
                'response' => true,
                'message' => 'Contact updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Contact updation failed'
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
        $contact = \App\Model\Contact::find($id);
        $contact->is_delete = 1;
        
        if($contact->save()) {
            $contact_inv_months = \App\Model\ContactInvoiceMonth::where('contact_id', $contact->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            $contact_taxes = \App\Model\ContactTax::where('contact_id', $contact->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/contact_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $contact->id. ',,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Contact deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Contact deletion failed'
            );
        }

        echo json_encode($result);
    }
}