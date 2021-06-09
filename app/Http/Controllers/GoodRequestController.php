<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class GoodRequestController extends Controller
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
        return view('stock.good_request', $data);
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
        
        $data['good_request_id'] = $request->id;
        
        return view('stock.good_request_detail', $data);
    }

    public function good_request_list(Request $request)
    {
        $good_requests = \App\Model\GoodRequest::select('id', 'good_request_no', 'good_request_date_time', 'remarks', 'good_request_value', 'is_posted')
                ->whereBetween('good_request_date_time', array($request->from.' 00:01', $request->to.' 23:59'))
                ->where('is_delete', 0)
                ->get();
        $not_posted = \App\Model\GoodRequest::where('is_posted', 0)->where('is_delete', 0)->first();
                
        $data = array(
            'good_requests' => $good_requests,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group')),
            'add_disable' => $not_posted ? true : false
        );
        
        return response($data);
    }

    public function find_good_request(Request $request)
    {
        $good_request = \App\Model\GoodRequest::select('id', 'good_request_no', 'good_request_date_time', 'remarks', 'good_request_value', 'is_posted')
                ->with(array('GoodRequestDetails' => function($query) {
                    $query->select('id', 'good_request_id', 'type', 'detail_id', 'document_no', 'item_id', 'rate', 'quantity')
                            ->with(array('Item' => function($query) {
                                $query->select('id', 'code', 'name', 'unit_type_id', 'rate')
                                        ->with(array('UnitType' => function($query) {
                                            $query->select('id', 'code', 'name');
                                        }));
                            }));
                }))
                ->with(array('GoodRequestDocument' => function($query) {
                    $query->select('id', 'good_request_id', 'type', 'document_id');
                }))
                ->find($request->id);
                    
        $data = array(
            'good_request' => $good_request,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function get_details()
    {
        $item_details = array();
        $items = \App\Model\Item::where('is_active', 1)->where('is_delete', 0)->get();
        foreach ($items as $item){
            $row = array(
                'id' => $item->id,
                'stock' => $item->stock
            );
            array_push($item_details, $row);
        }
        
        $documents = $details = array();
        $jobs = \App\Model\Job::where('is_completed', 0)->where('is_delete', 0)->get();
        foreach ($jobs as $job){
            $job_card_details = \App\Model\JobCardDetails::whereHas('JobCard', function($q) use($job){
                                $q->where('inquiry_id', $job->inquiry_id)->where('is_used', 1)->where('is_ordered', 0)->where('is_delete', 0);
                            })
                            ->where('is_delete', 0)
                            ->get();
            $document_ids = array();
            foreach ($job_card_details as $job_card_detail){
                if(!in_array($job_card_detail->JobCard->id, $document_ids)){
                    $row = array(
                        'type' => 1,
                        'document_id' => $job_card_detail->JobCard->id
                    );
                    array_push($documents, $row);
                    array_push($document_ids, $job_card_detail->JobCard->id);
                }
                
                $request = true;
                for($i=0; $i<count($item_details); $i++){
                    if($item_details[$i]['id'] == $job_card_detail->Item->id && $item_details[$i]['stock'] >= $job_card_detail->quantity){
                        $request = false;
                        $item_details[$i]['stock'] -= $job_card_detail->quantity;
                    }
                }
                
                if($request){
                    $row = array(
                        'id' => 0,
                        'type' => 1,
                        'detail_id' => $job_card_detail->id,
                        'document_no' => $job_card_detail->JobCard->job_card_no,
                        'item_id' => $job_card_detail->Item->id,
                        'item_code' => $job_card_detail->Item->code,
                        'item_name' => $job_card_detail->Item->name,
                        'unit_type' => $job_card_detail->Item->UnitType->code,
                        'rate' => number_format($job_card_detail->Item->rate, 2, '.', ''),
                        'quantity' => $job_card_detail->quantity,
                        'value' => number_format($job_card_detail->Item->rate*$job_card_detail->quantity, 2, '.', '')
                    );
                    array_push($details, $row);
                }
            }
            
            $installation_sheet_details = \App\Model\InstallationSheetDetails::whereHas('InstallationSheet', function($q) use($job){
                                $q->where('inquiry_id', $job->inquiry_id)->where('is_ordered', 0)->where('is_delete', 0);
                            })
                            ->where('is_delete', 0)
                            ->get();
            $document_ids = array();
            foreach ($installation_sheet_details as $installation_sheet_detail){
                if(!in_array($installation_sheet_detail->InstallationSheet->id, $document_ids)){
                    $row = array(
                        'type' => 2,
                        'document_id' => $installation_sheet_detail->InstallationSheet->id
                    );
                    array_push($documents, $row);
                    array_push($document_ids, $installation_sheet_detail->InstallationSheet->id);
                }
                
                $request = true;
                for($i=0; $i<count($item_details); $i++){
                    if($item_details[$i]['id'] == $installation_sheet_detail->Item->id && $item_details[$i]['stock'] >= $installation_sheet_detail->quantity){
                        $request = false;
                        $item_details[$i]['stock'] -= $installation_sheet_detail->quantity;
                    }
                }
                
                if($request){
                    $row = array(
                        'id' => 0,
                        'type' => 2,
                        'detail_id' => $installation_sheet_detail->id,
                        'document_no' => $installation_sheet_detail->InstallationSheet->installation_sheet_no,
                        'item_id' => $installation_sheet_detail->Item->id,
                        'item_code' => $installation_sheet_detail->Item->code,
                        'item_name' => $installation_sheet_detail->Item->name,
                        'unit_type' => $installation_sheet_detail->Item->UnitType->code,
                        'rate' => number_format($installation_sheet_detail->Item->rate, 2, '.', ''),
                        'quantity' => $installation_sheet_detail->quantity,
                        'value' => number_format($installation_sheet_detail->Item->rate*$installation_sheet_detail->quantity, 2, '.', '')
                    );
                    array_push($details, $row);
                }
            }
        }
        
        $tech_responses = \App\Model\TechResponse::where('is_completed', 0)->where('is_delete', 0)->get();
        foreach ($tech_responses as $tech_response){
            $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::whereHas('TechResponseJobCard', function($q) use($tech_response){
                                $q->where('tech_response_id', $tech_response->id)->where('is_used', 1)->where('is_ordered', 0)->where('is_delete', 0);
                            })
                            ->where('is_delete', 0)
                            ->get();
            $document_ids = array();
            foreach ($tech_response_job_card_details as $tech_response_job_card_detail){
                if(!in_array($tech_response_job_card_detail->TechResponseJobCard->id, $document_ids)){
                    $row = array(
                        'type' => 3,
                        'document_id' => $tech_response_job_card_detail->TechResponseJobCard->id
                    );
                    array_push($documents, $row);
                    array_push($document_ids, $tech_response_job_card_detail->TechResponseJobCard->id);
                }
                
                $request = true;
                for($i=0; $i<count($item_details); $i++){
                    if($item_details[$i]['id'] == $tech_response_job_card_detail->Item->id && $item_details[$i]['stock'] >= $tech_response_job_card_detail->quantity){
                        $request = false;
                        $item_details[$i]['stock'] -= $tech_response_job_card_detail->quantity;
                    }
                }
                
                if($request){
                    $row = array(
                        'id' => 0,
                        'type' => 3,
                        'detail_id' => $tech_response_job_card_detail->id,
                        'document_no' => $tech_response_job_card_detail->TechResponseJobCard->tech_response_job_card_no,
                        'item_id' => $tech_response_job_card_detail->Item->id,
                        'item_code' => $tech_response_job_card_detail->Item->code,
                        'item_name' => $tech_response_job_card_detail->Item->name,
                        'unit_type' => $tech_response_job_card_detail->Item->UnitType->code,
                        'rate' => number_format($tech_response_job_card_detail->Item->rate, 2, '.', ''),
                        'quantity' => $tech_response_job_card_detail->quantity,
                        'value' => number_format($tech_response_job_card_detail->Item->rate*$tech_response_job_card_detail->quantity, 2, '.', '')
                    );
                    array_push($details, $row);
                }
            }
            
            $tech_response_installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::whereHas('TechResponseInstallationSheet', function($q) use($tech_response){
                                $q->where('tech_response_id', $tech_response->id)->where('is_used', 1)->where('is_ordered', 0)->where('is_delete', 0);
                            })
                            ->where('is_delete', 0)
                            ->get();
            $document_ids = array();
            foreach ($tech_response_installation_sheet_details as $tech_response_installation_sheet_detail){
                if(!in_array($tech_response_installation_sheet_detail->TechResponseInstallationSheet->id, $document_ids)){
                    $row = array(
                        'type' => 4,
                        'document_id' => $tech_response_installation_sheet_detail->TechResponseInstallationSheet->id
                    );
                    array_push($documents, $row);
                    array_push($document_ids, $tech_response_installation_sheet_detail->TechResponseInstallationSheet->id);
                }
                
                $request = true;
                for($i=0; $i<count($item_details); $i++){
                    if($item_details[$i]['id'] == $tech_response_installation_sheet_detail->Item->id && $item_details[$i]['stock'] >= $tech_response_installation_sheet_detail->quantity){
                        $request = false;
                        $item_details[$i]['stock'] -= $tech_response_installation_sheet_detail->quantity;
                    }
                }
                
                if($request){
                    $row = array(
                        'id' => 0,
                        'type' => 4,
                        'detail_id' => $tech_response_installation_sheet_detail->id,
                        'document_no' => $tech_response_installation_sheet_detail->TechResponseInstallationSheet->tech_response_installation_sheet_no,
                        'item_id' => $tech_response_installation_sheet_detail->Item->id,
                        'item_code' => $tech_response_installation_sheet_detail->Item->code,
                        'item_name' => $tech_response_installation_sheet_detail->Item->name,
                        'unit_type' => $tech_response_installation_sheet_detail->Item->UnitType->code,
                        'rate' => number_format($tech_response_installation_sheet_detail->Item->rate, 2, '.', ''),
                        'quantity' => $tech_response_installation_sheet_detail->quantity,
                        'value' => number_format($tech_response_installation_sheet_detail->Item->rate*$tech_response_installation_sheet_detail->quantity, 2, '.', '')
                    );
                    array_push($details, $row);
                }
            }
        }
        
        $data = array(
            'documents' => $documents,
            'details' => $details
        );
        
        return response($data);
    }

    public function post_good_request(Request $request)
    {
        $good_request = \App\Model\GoodRequest::find($request->id);
        $good_request->is_posted = 1;
        
        if($good_request->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_request_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,' . $good_request->id. ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Good Request posted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Good Request post failed'
            );
        }

        echo json_encode($result);
    }
    
    public function print_good_request(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $good_request = \App\Model\GoodRequest::find($request->id);
        $data['good_request'] = $good_request;
        $title = $good_request ? 'Good Request Details '.$good_request->good_request_no : 'Good Request Details';
        
        $html = view('stock.good_request_pdf', $data);
        
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
        $good_request = new \App\Model\GoodRequest();
        $last_id = 0;
        $last_good_request = \App\Model\GoodRequest::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
        $last_id = $last_good_request ? $last_good_request->id : $last_id;
        $good_request->good_request_no = 'G-REQ/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id+1);
        $good_request->good_request_date_time = date('Y-m-d', strtotime($request->good_request_date)).' '.$request->good_request_time;
        $good_request->remarks = $request->remarks;
        
        if($good_request->save()) {
            $good_request_details = \App\Model\GoodRequestDetails::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            $good_request_documents = \App\Model\GoodRequestDocument::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            
            $good_request_detail_ids = array();
            foreach ($request->details as $detail){
                $good_request_detail = \App\Model\GoodRequestDetails::find($detail['id']);
                $good_request_detail = $good_request_detail ? $good_request_detail : new \App\Model\GoodRequestDetails();
                $good_request_detail->good_request_id = $good_request->id;
                $good_request_detail->type = $detail['type'];
                $good_request_detail->detail_id = $detail['detail_id'];
                $good_request_detail->document_no = $detail['document_no'];
                $good_request_detail->item_id = $detail['item_id'];
                $good_request_detail->rate = $detail['rate'];
                $good_request_detail->quantity = $detail['quantity'];
                $good_request_detail->is_delete = 0;
                $good_request_detail->save();
                
                array_push($good_request_detail_ids, $good_request_detail->id);
            }
            foreach ($request->documents as $document){
                $good_request_document = \App\Model\GoodRequestDocument::where('good_request_id', $good_request->id)
                        ->where('type', $document['type'])
                        ->where('document_id', $document['document_id'])
                        ->first();
                $good_request_document = $good_request_document ? $good_request_document : new \App\Model\GoodRequestDocument();
                $good_request_document->good_request_id = $good_request->id;
                $good_request_document->type = $document['type'];
                $good_request_document->document_id = $document['document_id'];
                $good_request_document->is_delete = 0;
                $good_request_document->save();
            }
            
            $good_request_details = \App\Model\GoodRequestDetails::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->get();
            $good_request_value = 0;
            foreach ($good_request_details as $good_request_detail){
                $good_request_value += $good_request_detail->rate*$good_request_detail->quantity; 
            }
            $good_request->good_request_value = $good_request_value;
            $good_request->save();   
            
            $good_request_details = \App\Model\GoodRequestDetails::whereIn('id', $good_request_detail_ids)
                                ->get();
            if(count($good_request_details) > 0){
                foreach($good_request_details as $good_request_detail){
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_request_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Created,' . $good_request->id. ',' . $good_request->good_request_no. ',' . $good_request->good_request_date_time. ',' . $good_request->good_request_value. ',' . str_replace(',',' ',$good_request->remarks). ',' . $good_request_detail->id. ',' . $good_request_detail->type. ',' . $good_request_detail->detail_id. ',' . str_replace(',',' ',$good_request_detail->document_no). ',' . $good_request_detail->item_id. ',' . $good_request_detail->rate. ',' . $good_request_detail->quantity. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                    fclose($myfile);
                }
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_request_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $good_request->id. ',' . $good_request->good_request_no. ',' . $good_request->good_request_date_time. ',' . $good_request->good_request_value. ',' . str_replace(',',' ',$good_request->remarks). ',,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Good Request created successfully',
                'data' => $good_request
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Good Request creation failed'
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
        $good_request = \App\Model\GoodRequest::find($id);
        $good_request->good_request_no = $request->good_request_no;
        $good_request->good_request_date_time = date('Y-m-d', strtotime($request->good_request_date)).' '.$request->good_request_time;
        $good_request->remarks = $request->remarks;
        
        if($good_request->save()) {
            $good_request_details = \App\Model\GoodRequestDetails::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            $good_request_documents = \App\Model\GoodRequestDocument::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            
            $good_request_detail_ids = array();
            foreach ($request->details as $detail){
                $good_request_detail = \App\Model\GoodRequestDetails::find($detail['id']);
                $good_request_detail = $good_request_detail ? $good_request_detail : new \App\Model\GoodRequestDetails();
                $good_request_detail->good_request_id = $good_request->id;
                $good_request_detail->type = $detail['type'];
                $good_request_detail->detail_id = $detail['detail_id'];
                $good_request_detail->document_no = $detail['document_no'];
                $good_request_detail->item_id = $detail['item_id'];
                $good_request_detail->rate = $detail['rate'];
                $good_request_detail->quantity = $detail['quantity'];
                $good_request_detail->is_delete = 0;
                $good_request_detail->save();
                
                array_push($good_request_detail_ids, $good_request_detail->id);
            }
            foreach ($request->documents as $document){
                $good_request_document = \App\Model\GoodRequestDocument::where('good_request_id', $good_request->id)
                        ->where('type', $document['type'])
                        ->where('document_id', $document['document_id'])
                        ->first();
                $good_request_document = $good_request_document ? $good_request_document : new \App\Model\GoodRequestDocument();
                $good_request_document->good_request_id = $good_request->id;
                $good_request_document->type = $document['type'];
                $good_request_document->document_id = $document['document_id'];
                $good_request_document->is_delete = 0;
                $good_request_document->save();
            }
            
            $good_request_details = \App\Model\GoodRequestDetails::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->get();
            $good_request_value = 0;
            foreach ($good_request_details as $good_request_detail){
                $good_request_value += $good_request_detail->rate*$good_request_detail->quantity; 
            }
            $good_request->good_request_value = $good_request_value;
            $good_request->save();
            
            $good_request_details = \App\Model\GoodRequestDetails::whereIn('id', $good_request_detail_ids)
                                ->get();
            if(count($good_request_details) > 0){
                foreach($good_request_details as $good_request_detail){
                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_request_controller.csv', 'a+') or die('Unable to open/create file!');
                    fwrite($myfile, 'Updated,' . $good_request->id. ',' . $good_request->good_request_no. ',' . $good_request->good_request_date_time. ',' . $good_request->good_request_value. ',' . str_replace(',',' ',$good_request->remarks). ',' . $good_request_detail->id. ',' . $good_request_detail->type. ',' . $good_request_detail->detail_id. ',' . str_replace(',',' ',$good_request_detail->document_no). ',' . $good_request_detail->item_id. ',' . $good_request_detail->rate. ',' . $good_request_detail->quantity. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                    fclose($myfile);
                }
            } else{
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_request_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $good_request->id. ',' . $good_request->good_request_no. ',' . $good_request->good_request_date_time. ',' . $good_request->good_request_value. ',' . str_replace(',',' ',$good_request->remarks). ',,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
                fclose($myfile);
            }
            
            $result = array(
                'response' => true,
                'message' => 'Good Request updated successfully',
                'data' => $good_request
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Good Request updation failed'
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
        $good_request = \App\Model\GoodRequest::find($id);
        $good_request->is_delete = 1;
        
        if($good_request->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_request_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $good_request->id. ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
                
            $good_request_details = \App\Model\GoodRequestDetails::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            $good_request_documents = \App\Model\GoodRequestDocument::where('good_request_id', $good_request->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);
            $result = array(
                'response' => true,
                'message' => 'Good Request deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Good Request deletion failed'
            );
        }

        echo json_encode($result);
    }
}
