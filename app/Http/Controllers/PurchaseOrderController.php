<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct()
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

        return view('stock.purchase_order', $data);
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

        $data['purchase_order_id'] = $request->id;

        return view('stock.purchase_order_detail', $data);
    }

    public function validate_item_code(Request $request)
    {
        if ($request->code != $request->item_code) {
//            if($request->good_request_id){
//                $item = \App\Model\GoodRequestDetails::select('id', 'good_request_id')
//                        ->whereHas('Item', function ($query) use($request){
//                            $query->where('code', $request->code);
//                        })
//                        ->where('good_request_id', $request->good_request_id)
//                        ->where('is_delete', 0)
//                        ->first();
//                $purchase_order_detail = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id')
//                        ->where('purchase_order_id', $request->purchase_order_id)
//                        ->where('item_id', $request->item_id)
//                        ->where('is_delete', 0)
//                        ->first();
//                if($item && !$purchase_order_detail){
//                    $response = 'true';
//                } else{
//                    $response = 'false';
//                }
//            } else{
            $item = \App\Model\Item::where('code', $request->code)
                        ->where('is_active', 1)
                        ->where('is_delete', 0)
                        ->first();
            if ($item) {
                $response = 'true';
            } else {
                $response = 'false';
            }
//            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function validate_item_name(Request $request)
    {
        if ($request->name != $request->item_name) {
//            if($request->good_request_id){
//                $item = \App\Model\GoodRequestDetails::select('id', 'good_request_id')
//                        ->whereHas('Item', function ($query) use($request){
//                            $query->where('name', $request->name);
//                        })
//                        ->where('good_request_id', $request->good_request_id)
//                        ->where('is_delete', 0)
//                        ->first();
//                $purchase_order_detail = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id')
//                        ->where('purchase_order_id', $request->purchase_order_id)
//                        ->where('item_id', $request->item_id)
//                        ->where('is_delete', 0)
//                        ->first();
//                if($item && !$purchase_order_detail){
//                    $response = 'true';
//                } else{
//                    $response = 'false';
//                }
//            } else{
            $item = \App\Model\Item::where('name', $request->name)
                        ->where('is_active', 1)
                        ->where('is_delete', 0)
                        ->first();
            if ($item) {
                $response = 'true';
            } else {
                $response = 'false';
            }
//            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function purchase_order_list(Request $request)
    {
        $purchase_orders = \App\Model\PurchaseOrder::select('id', 'contact_id', 'good_request_id', 'purchase_order_no', 'purchase_order_date_time', 'remarks', 'purchase_order_value', 'is_posted')
                ->with(['Contact' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['GoodRequest' => function ($query) {
                    $query->select('id', 'good_request_no');
                }])
                ->whereBetween('purchase_order_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
                ->where('is_delete', 0)
                ->get();

        $data = [
            'purchase_orders' => $purchase_orders,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function find_purchase_order(Request $request)
    {
        $purchase_order = \App\Model\PurchaseOrder::select('id', 'contact_id', 'good_request_id', 'purchase_order_no', 'purchase_order_date_time', 'remarks', 'purchase_order_value', 'is_posted')
                ->with(['Contact' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['GoodRequest' => function ($query) {
                    $query->select('id', 'good_request_no');
                }])
                ->find($request->id);

        $data = [
            'purchase_order' => $purchase_order,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function find_purchase_order_detail(Request $request)
    {
        $purchase_order_detail = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id', 'rate', 'quantity')
                ->with(['PurchaseOrder' => function ($query) {
                    $query->select('id', 'contact_id', 'good_request_id', 'purchase_order_no', 'purchase_order_date_time', 'remarks', 'is_posted')
                            ->with(['Contact' => function ($query) {
                                $query->select('id', 'name');
                            }])
                            ->with(['GoodRequest' => function ($query) {
                                $query->select('id', 'good_request_no');
                            }]);
                }])
                ->with(['Item' => function ($query) {
                    $query->select('id', 'main_category_id', 'sub_category_id', 'code', 'name', 'unit_type_id')
                            ->with(['MainItemCategory' => function ($query) {
                                $query->select('id', 'code', 'name');
                            }])
                            ->with(['SubItemCategory' => function ($query) {
                                $query->select('id', 'code', 'name');
                            }])
                            ->with(['UnitType' => function ($query) {
                                $query->select('id', 'code', 'name');
                            }]);
                }])
                ->find($request->id);

        return response($purchase_order_detail);
    }

    public function purchase_order_detail_list(Request $request)
    {
        $purchase_order_details = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id', 'rate', 'quantity')
                ->with(['PurchaseOrder' => function ($query) {
                    $query->select('id', 'is_posted');
                }])
                ->with(['Item' => function ($query) {
                    $query->select('id', 'code', 'name', 'unit_type_id')
                            ->with(['UnitType' => function ($query) {
                                $query->select('id', 'code', 'name');
                            }]);
                }])
                ->where('purchase_order_id', $request->id)
                ->where('is_delete', 0)
                ->get();

        $data = [
            'purchase_order_details' => $purchase_order_details,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function post_purchase_order(Request $request)
    {
        $purchase_order = \App\Model\PurchaseOrder::find($request->id);
        $purchase_order->is_posted = 1;

        if ($purchase_order->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,'.$purchase_order->id.',,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $purchase_order_details = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($purchase_order_details as $purchase_order_detail) {
                $total_value += $purchase_order_detail->rate * $purchase_order_detail->quantity;
            }

            foreach ($purchase_order->Contact->ContactTax as $detail) {
                if ($detail['CTaxType']) {
                    if ($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 3) {
                        $total_value += $total_value * $detail['CTaxType']['percentage'] / 100;
                    }
                }
            }

            $purchase_order->purchase_order_value = $total_value;
            $purchase_order->save();

            $good_request_documents = \App\Model\GoodRequestDocument::where('good_request_id', $purchase_order->good_request_id)
                    ->where('is_delete', 0)
                    ->get();
            $inquiry_ids = $tech_response_ids = [];
            foreach ($good_request_documents as $good_request_document) {
                if ($good_request_document->type == 1) {
                    $job_card = \App\Model\JobCard::find($good_request_document->document_id);
                    $job_card->is_ordered = 1;
                    $job_card->save();

                    if (! in_array($job_card->inquiry_id, $inquiry_ids)) {
                        array_push($inquiry_ids, $job_card->inquiry_id);
                    }
                } elseif ($good_request_document->type == 2) {
                    $installation_sheet = \App\Model\InstallationSheet::find($good_request_document->document_id);
                    $installation_sheet->is_ordered = 1;
                    $installation_sheet->save();

                    if (! in_array($installation_sheet->inquiry_id, $inquiry_ids)) {
                        array_push($inquiry_ids, $installation_sheet->inquiry_id);
                    }
                } elseif ($good_request_document->type == 3) {
                    $tech_response_job_card = \App\Model\TechResponseJobCard::find($good_request_document->document_id);
                    $tech_response_job_card->is_ordered = 1;
                    $tech_response_job_card->save();

                    if (! in_array($tech_response_job_card->tech_response_id, $tech_response_ids)) {
                        array_push($tech_response_ids, $tech_response_job_card->tech_response_id);
                    }
                }
            }

            foreach ($inquiry_ids as $inquiry_id) {
                $job = \App\Model\Job::where('inquiry_id', $inquiry_id)->where('is_delete', 0)->first();
                if ($job) {
                    $job_status = new \App\Model\JobDetails();
                    $job_status->job_id = $job->id;
                    $job_status->update_date_time = date('Y-m-d H:i');
                    $job_status->job_status_id = 2;
                    $job_status->job_scheduled_date_time = '';
                    $job_status->remarks = $purchase_order->purchase_order_no;
                    $job_status->user_id = $request->session()->get('users_id');
                    $job_status->save();
                }
            }
            foreach ($tech_response_ids as $tech_response_id) {
                $tech_response_detail = new \App\Model\TechResponseDetails();
                $tech_response_detail->tech_response_id = $tech_response_id;
                $tech_response_detail->update_date_time = date('Y-m-d H:i');
                $tech_response_detail->tech_response_status_id = 7;
                $tech_response_detail->job_scheduled_date_time = '';
                $tech_response_detail->is_chargeable = 0;
                $tech_response_detail->remarks = $purchase_order->purchase_order_no;
                $tech_response_detail->user_id = $request->session()->get('users_id');
                $tech_response_detail->save();
            }

            $result = [
                'response' => true,
                'message' => 'Purchase Order posted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Purchase Order post failed',
            ];
        }

        echo json_encode($result);
    }

    public function print_purchase_order(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $purchase_order = \App\Model\PurchaseOrder::find($request->id);
        $data['purchase_order'] = $purchase_order;
        $title = $purchase_order ? 'Purchase Order Details '.$purchase_order->purchase_order_no : 'Purchase Order Details';

        $html = view('stock.purchase_order_pdf', $data);

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
            'footer-font-size' => 8,
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }

    public function print_purchase_order_stock(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $purchase_order = \App\Model\PurchaseOrder::find($request->id);
        $data['purchase_order'] = $purchase_order;
        $title = $purchase_order ? 'Purchase Order Details '.$purchase_order->purchase_order_no : 'Purchase Order Details';

        $html = view('stock.purchase_order_pdf_stock', $data);

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
            'footer-font-size' => 8,
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
        $exist = false;
        $purchase_order = \App\Model\PurchaseOrder::find($request->purchase_order_id);

        $contact_id = isset($request->supplier['id']) ? $request->supplier['id'] : 0;
        $good_request_id = isset($request->good_request['id']) ? $request->good_request['id'] : 0;
        if (! $purchase_order) {
            $exist = true;
            $purchase_order = new \App\Model\PurchaseOrder();
            $last_id = 0;
            $last_purchase_order = \App\Model\PurchaseOrder::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_purchase_order ? $last_purchase_order->id : $last_id;
            $purchase_order->purchase_order_no = 'PO/'.date('m').'/'.date('y').'/'.$contact_id.'/'.$good_request_id.'/'.sprintf('%05d', $last_id + 1);
        }

        $purchase_order->contact_id = $contact_id;
        $purchase_order->good_request_id = $good_request_id;
        $purchase_order->purchase_order_date_time = date('Y-m-d', strtotime($request->purchase_order_date)).' '.$request->purchase_order_time;
        $purchase_order->remarks = $request->remarks;

        if ($purchase_order->save()) {
            $purchase_order_detail_id = '';
            if (isset($request->item['id'])) {
                $old_purchase_order_detail = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)
                        ->where('item_id', $request->item['id'])
                        ->first();

                $purchase_order_detail = $old_purchase_order_detail ? $old_purchase_order_detail : new \App\Model\PurchaseOrderDetails();
                $purchase_order_detail->purchase_order_id = $purchase_order->id;
                $purchase_order_detail->item_id = $request->item['id'];
                $purchase_order_detail->rate = $request->rate;
                $purchase_order_detail->quantity = $old_purchase_order_detail ? $old_purchase_order_detail->quantity + $request->quantity : $request->quantity;
                $purchase_order_detail->is_delete = 0;
                $purchase_order_detail->save();

                $purchase_order_detail_id = $purchase_order_detail->id;

//                $item = \App\Model\Item::find($purchase_order_detail->item_id);
//                if($item){
//                    $item->rate = $purchase_order_detail->rate;
//                    $item->save();
//                }
            }

            $purchase_order_details = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($purchase_order_details as $purchase_order_detail) {
                $total_value += $purchase_order_detail->rate * $purchase_order_detail->quantity;
            }
            $purchase_order->purchase_order_value = $total_value;
            $purchase_order->save();

            if ($purchase_order_detail_id != '') {
                $purchase_order_detail = \App\Model\PurchaseOrderDetails::find($purchase_order_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$purchase_order->id.','.$purchase_order->purchase_order_no.','.$purchase_order->contact_id.','.$purchase_order->good_request_id.','.$purchase_order->purchase_order_date_time.','.$purchase_order->purchase_order_value.','.str_replace(',', ' ', $purchase_order->remarks).','.$purchase_order_detail->id.','.$purchase_order_detail->item_id.','.$purchase_order_detail->rate.','.$purchase_order_detail->quantity.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$purchase_order->id.','.$purchase_order->purchase_order_no.','.$purchase_order->contact_id.','.$purchase_order->good_request_id.','.$purchase_order->purchase_order_date_time.','.$purchase_order->purchase_order_value.','.str_replace(',', ' ', $purchase_order->remarks).',,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            }

            $result = [
                'response' => true,
                'message' => 'Purchase Order Detail created successfully',
                'data' => $purchase_order->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Purchase Order Detail creation failed',
            ];
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
        $purchase_order = \App\Model\PurchaseOrder::find($request->purchase_order_id);
        $purchase_order->contact_id = isset($request->supplier['id']) ? $request->supplier['id'] : 0;
        $purchase_order->good_request_id = isset($request->good_request['id']) ? $request->good_request['id'] : 0;
        $purchase_order->purchase_order_no = $request->purchase_order_no;
        $purchase_order->purchase_order_date_time = date('Y-m-d', strtotime($request->purchase_order_date)).' '.$request->purchase_order_time;
        $purchase_order->remarks = $request->remarks;

        if ($purchase_order->save()) {
            $purchase_order_detail_id = '';
            if (isset($request->item['id'])) {
                $purchase_order_detail = \App\Model\PurchaseOrderDetails::find($id);
                $purchase_order_detail->quantity = 0;
                $purchase_order_detail->is_delete = 1;
                $purchase_order_detail->save();

                $old_purchase_order_detail = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)
                        ->where('item_id', $request->item['id'])
                        ->first();

                $purchase_order_detail = $old_purchase_order_detail ? $old_purchase_order_detail : new \App\Model\PurchaseOrderDetails();
                $purchase_order_detail->purchase_order_id = $purchase_order->id;
                $purchase_order_detail->item_id = $request->item['id'];
                $purchase_order_detail->rate = $request->rate;
                $purchase_order_detail->quantity = $old_purchase_order_detail ? $old_purchase_order_detail->quantity + $request->quantity : $request->quantity;
                $purchase_order_detail->is_delete = 0;
                $purchase_order_detail->save();

                $purchase_order_detail_id = $purchase_order_detail->id;

                // $item = \App\Model\Item::find($purchase_order_detail->item_id);
                // if($item){
                //     $item->rate = $purchase_order_detail->rate;
                //     $item->save();
                // }
            }

            $purchase_order_details = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)
                    ->where('is_delete', 0)
                    ->get();
            $total_value = 0;
            foreach ($purchase_order_details as $purchase_order_detail) {
                $total_value += $purchase_order_detail->rate * $purchase_order_detail->quantity;
            }
            $purchase_order->purchase_order_value = $total_value;
            $purchase_order->save();

            if ($purchase_order_detail_id != '') {
                $purchase_order_detail = \App\Model\PurchaseOrderDetails::find($purchase_order_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$purchase_order->id.','.$purchase_order->purchase_order_no.','.$purchase_order->contact_id.','.$purchase_order->good_request_id.','.$purchase_order->purchase_order_date_time.','.$purchase_order->purchase_order_value.','.str_replace(',', ' ', $purchase_order->remarks).','.$purchase_order_detail->id.','.$purchase_order_detail->item_id.','.$purchase_order_detail->rate.','.$purchase_order_detail->quantity.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$purchase_order->id.','.$purchase_order->purchase_order_no.','.$purchase_order->contact_id.','.$purchase_order->good_request_id.','.$purchase_order->purchase_order_date_time.','.$purchase_order->purchase_order_value.','.str_replace(',', ' ', $purchase_order->remarks).',,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            }

            $result = [
                'response' => true,
                'message' => 'Purchase Order Detail updated successfully',
                'data' => $purchase_order->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Purchase Order Detail updation failed',
            ];
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
        if ($request->type == 0) {
            $purchase_order = \App\Model\PurchaseOrder::find($id);
            $purchase_order->is_delete = 1;

            if ($purchase_order->save()) {
                $purchase_order_details = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)->where('is_delete', 0)->get();
                foreach ($purchase_order_details as $purchase_order_detail) {
                    $purchase_order_detail->quantity = 0;
                    $purchase_order_detail->is_delete = 1;
                    $purchase_order_detail->save();
                }

                $purchase_order_details = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order->id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($purchase_order_details as $purchase_order_detail) {
                    $total_value += $purchase_order_detail->rate * $purchase_order_detail->quantity;
                }
                $purchase_order->purchase_order_value = $total_value;
                $purchase_order->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$purchase_order->id.',,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Purchase Order deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Purchase Order deletion failed',
                ];
            }
        } elseif ($request->type == 1) {
            $purchase_order_detail = \App\Model\PurchaseOrderDetails::find($id);
            $purchase_order_detail->quantity = 0;
            $purchase_order_detail->is_delete = 1;

            if ($purchase_order_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/purchase_order_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$purchase_order_detail->purchase_order_id.',,,,,,,'.$purchase_order_detail->id.',,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $purchase_order_details = \App\Model\PurchaseOrderDetails::where('purchase_order_id', $purchase_order_detail->purchase_order_id)
                        ->where('is_delete', 0)
                        ->get();
                $total_value = 0;
                foreach ($purchase_order_details as $purchase_order_detail) {
                    $total_value += $purchase_order_detail->rate * $purchase_order_detail->quantity;
                }

                $purchase_order = \App\Model\PurchaseOrder::find($purchase_order_detail->purchase_order_id);
                $purchase_order->purchase_order_value = $total_value;
                $purchase_order->save();

                $result = [
                    'response' => true,
                    'message' => 'Purchase Order Detail deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Purchase Order Detail deletion failed',
                ];
            }
        } else {
            $result = [
                'response' => false,
                'message' => 'Deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
