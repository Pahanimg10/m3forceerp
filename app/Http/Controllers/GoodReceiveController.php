<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class GoodReceiveController extends Controller
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

        return view('stock.good_receive', $data);
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

        //        $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', 358)
        //                ->get();
        //        foreach ($good_receive_details as $good_receive_detail){
        //            $item = \App\Model\Item::find($good_receive_detail->item_id);
        //            if($item){
        //                $item->main_category_id = 6;
        //                $item->purchase_type_id = 1;
        //                $item->save();
        //            }
        //        }

        $data['good_receive_id'] = $request->id;

        return view('stock.good_receive_detail', $data);
    }

    public function validate_purchase_order_no(Request $request)
    {
        $purchase_order = \App\Model\PurchaseOrder::where('purchase_order_no', $request->purchase_order_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();
        $good_receive = \App\Model\GoodReceive::select('id', 'purchase_order_id')
            ->whereHas('PurchaseOrder', function ($query) use ($request) {
                $query->where('purchase_order_no', $request->purchase_order_no);
            })
            ->where('is_delete', 0)
            ->first();
        if ($request->value == $request->purchase_order_no || ($purchase_order && ! $good_receive)) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_item_code(Request $request)
    {
        if ($request->code != $request->item_code) {
            $item = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id', 'rate', 'quantity')
                ->whereHas('Item', function ($query) use ($request) {
                    $query->where('code', $request->code);
                })
                ->where('purchase_order_id', $request->purchase_order_id)
                ->where('is_delete', 0)
                ->first();
            $good_receive_detail = \App\Model\GoodReceiveDetails::select('id', 'good_receive_id', 'item_id')
                ->where('good_receive_id', $request->good_receive_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->first();
            if ($item && ! $good_receive_detail) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function validate_item_name(Request $request)
    {
        if ($request->name != $request->item_name) {
            $item = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id', 'rate', 'quantity')
                ->whereHas('Item', function ($query) use ($request) {
                    $query->where('name', $request->name);
                })
                ->where('purchase_order_id', $request->purchase_order_id)
                ->where('is_delete', 0)
                ->first();
            $good_receive_detail = \App\Model\GoodReceiveDetails::select('id', 'good_receive_id', 'item_id')
                ->where('good_receive_id', $request->good_receive_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->first();
            if ($item && ! $good_receive_detail) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function validate_item_quantity(Request $request)
    {
        $item = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id', 'rate', 'quantity')
            ->where('purchase_order_id', $request->purchase_order_id)
            ->where('item_id', $request->item_id)
            ->where('quantity', '>=', $request->quantity)
            ->where('is_delete', 0)
            ->first();
        if ($item) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_item_rate(Request $request)
    {
        $item = \App\Model\PurchaseOrderDetails::select('id', 'purchase_order_id', 'item_id', 'rate', 'quantity')
            ->where('purchase_order_id', $request->purchase_order_id)
            ->where('item_id', $request->item_id)
            ->where('rate', '>=', $request->rate)
            ->where('is_delete', 0)
            ->first();
        if ($item) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function good_receive_list(Request $request)
    {
        $good_receives = \App\Model\GoodReceive::select('id', 'purchase_order_id', 'invoice_no', 'good_receive_no', 'good_receive_date_time', 'remarks', 'good_receive_value', 'is_posted')
            ->with(['PurchaseOrder' => function ($query) {
                $query->select('id', 'contact_id', 'purchase_order_no')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->whereBetween('good_receive_date_time', [$request->from.' 00:01', $request->to.' 23:59'])
            ->where('is_delete', 0)
            ->get();

        $data = [
            'good_receives' => $good_receives,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function find_good_receive(Request $request)
    {
        $good_receive = \App\Model\GoodReceive::select('id', 'purchase_order_id', 'invoice_no', 'good_receive_no', 'good_receive_date_time', 'remarks', 'good_receive_value', 'is_posted')
            ->with(['PurchaseOrder' => function ($query) {
                $query->select('id', 'contact_id', 'purchase_order_no')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->find($request->id);

        $data = [
            'good_receive' => $good_receive,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function find_good_receive_detail(Request $request)
    {
        $good_receive_detail = \App\Model\GoodReceiveDetails::select('id', 'good_receive_id', 'item_id', 'model_no', 'brand', 'origin', 'rate', 'quantity', 'location', 'warranty')
            ->with(['GoodReceive' => function ($query) {
                $query->select('id', 'purchase_order_id', 'invoice_no', 'good_receive_no', 'good_receive_date_time', 'remarks', 'is_posted')
                    ->with(['PurchaseOrder' => function ($query) {
                        $query->select('id', 'contact_id', 'purchase_order_no')
                            ->with(['Contact' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                    }]);
            }])
            ->with(['Item' => function ($query) {
                $query->select('id', 'main_category_id', 'sub_category_id', 'code', 'name', 'unit_type_id', 'is_serial', 'is_warranty')
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
            ->with(['GoodReceiveBreakdown' => function ($query) {
                $query->select('id', 'good_receive_detail_id', 'serial_no', 'is_main', 'main_id');
            }])
            ->find($request->id);

        return response($good_receive_detail);
    }

    public function good_receive_detail_list(Request $request)
    {
        $good_receive_details = \App\Model\GoodReceiveDetails::select('id', 'good_receive_id', 'item_id', 'rate', 'quantity')
            ->with(['GoodReceive' => function ($query) {
                $query->select('id', 'is_posted');
            }])
            ->with(['Item' => function ($query) {
                $query->select('id', 'code', 'name', 'unit_type_id')
                    ->with(['UnitType' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }]);
            }])
            ->where('good_receive_id', $request->id)
            ->where('is_delete', 0)
            ->get();

        $data = [
            'good_receive_details' => $good_receive_details,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function post_good_receive(Request $request)
    {
        $good_receive = \App\Model\GoodReceive::find($request->id);
        $is_posted = $good_receive->is_posted == 0 ? true : false;
        $good_receive->is_posted = 1;
        $good_receive->save();

        if ($is_posted) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,'.$good_receive->id.',,,,,,,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($good_receive_details as $good_receive_detail) {
                $good_receive_detail->available_quantity = $good_receive_detail->quantity;
                $good_receive_detail->save();

                $total_value += $good_receive_detail->rate * $good_receive_detail->quantity;
                $item = \App\Model\Item::find($good_receive_detail->item_id);
                if ($item) {
                    $stock = $item->stock;
                    $item->model_no = $good_receive_detail->model_no;
                    $item->brand = $good_receive_detail->brand;
                    $item->origin = $good_receive_detail->origin;
                    $item->rate = $good_receive_detail->rate;
                    $item->stock = $stock + $good_receive_detail->quantity;
                    $item->save();
                }
            }

            foreach ($good_receive->PurchaseOrder->Contact->ContactTax as $detail) {
                if ($detail['CTaxType']) {
                    if ($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 3) {
                        $total_value += $total_value * $detail['CTaxType']['percentage'] / 100;
                    }
                }
            }

            $good_receive->good_receive_value = $total_value;
            $good_receive->save();

            $result = [
                'response' => true,
                'message' => 'Good Receive posted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Good Receive post failed',
            ];
        }

        echo json_encode($result);
    }

    public function print_good_receive(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $good_receive = \App\Model\GoodReceive::find($request->id);
        $data['good_receive'] = $good_receive;
        $title = $good_receive ? 'Good Receive Details '.$good_receive->good_receive_no : 'Good Receive Details';

        $html = view('stock.good_receive_pdf', $data);

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
        $good_receive = \App\Model\GoodReceive::find($request->good_receive_id);

        $purchase_order_id = isset($request->purchase_order['id']) ? $request->purchase_order['id'] : 0;
        if (! $good_receive) {
            $exist = true;
            $good_receive = new \App\Model\GoodReceive();
            $last_id = 0;
            $last_good_receive = \App\Model\GoodReceive::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_good_receive ? $last_good_receive->id : $last_id;
            $good_receive->good_receive_no = 'G-REC/'.date('m').'/'.date('y').'/'.$purchase_order_id.'/'.sprintf('%05d', $last_id + 1);
        }

        $good_receive->purchase_order_id = $purchase_order_id;
        $good_receive->invoice_no = $request->invoice_no;
        $good_receive->good_receive_date_time = date('Y-m-d', strtotime($request->good_receive_date)).' '.$request->good_receive_time;
        $good_receive->remarks = $request->remarks;

        if ($good_receive->save()) {
            $good_receive_detail_id = '';
            if (isset($request->item['id'])) {
                $old_good_receive_detail = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                    ->where('item_id', $request->item['id'])
                    ->first();

                $good_receive_detail = $old_good_receive_detail ? $old_good_receive_detail : new \App\Model\GoodReceiveDetails();
                $good_receive_detail->good_receive_id = $good_receive->id;
                $good_receive_detail->item_id = $request->item['id'];
                $good_receive_detail->model_no = $request->model_no;
                $good_receive_detail->brand = $request->brand;
                $good_receive_detail->origin = $request->origin;
                $good_receive_detail->rate = $request->rate;
                $good_receive_detail->quantity = $old_good_receive_detail ? $old_good_receive_detail->quantity + $request->quantity : $request->quantity;
                $good_receive_detail->location = $request->location;
                $good_receive_detail->warranty = $request->warranty;
                $good_receive_detail->is_delete = 0;
                $good_receive_detail->save();

                $good_receive_detail_id = $good_receive_detail->id;

                $main_ids = [];
                foreach ($request->serial_details as $detail) {
                    if ($detail['is_main'] == 1) {
                        $old_good_receive_breakdown = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                            ->where('serial_no', $detail['serial_no'])
                            ->where('is_main', 1)
                            ->first();
                        $good_receive_breakdown = $old_good_receive_breakdown ? $old_good_receive_breakdown : new \App\Model\GoodReceiveBreakdown();
                        $good_receive_breakdown->good_receive_detail_id = $good_receive_detail->id;
                        $good_receive_breakdown->serial_no = $detail['serial_no'];
                        $good_receive_breakdown->is_main = 1;
                        $good_receive_breakdown->is_issued = 0;
                        $good_receive_breakdown->is_delete = 0;
                        if ($good_receive_breakdown->save()) {
                            $good_receive_breakdown->main_id = $good_receive_breakdown->id;
                            $good_receive_breakdown->save();

                            $row = [
                                'id' => $detail['main_id'],
                                'main_id' => $good_receive_breakdown->id,
                            ];
                            array_push($main_ids, $row);
                        }
                    }
                }
                foreach ($main_ids as $main_id) {
                    foreach ($request->serial_details as $detail) {
                        if ($detail['is_main'] == 0 && $detail['main_id'] == $main_id['id']) {
                            $old_good_receive_breakdown = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                                ->where('serial_no', $detail['serial_no'])
                                ->where('is_main', 0)
                                ->first();
                            $good_receive_breakdown = $old_good_receive_breakdown ? $old_good_receive_breakdown : new \App\Model\GoodReceiveBreakdown();
                            $good_receive_breakdown->good_receive_detail_id = $good_receive_detail->id;
                            $good_receive_breakdown->serial_no = $detail['serial_no'];
                            $good_receive_breakdown->is_main = 0;
                            $good_receive_breakdown->main_id = $main_id['main_id'];
                            $good_receive_breakdown->is_issued = 0;
                            $good_receive_breakdown->is_delete = 0;
                            $good_receive_breakdown->save();
                        }
                    }
                }
            }

            $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($good_receive_details as $good_receive_detail) {
                $total_value += $good_receive_detail->rate * $good_receive_detail->quantity;
            }
            $good_receive->good_receive_value = $total_value;
            $good_receive->save();

            if ($good_receive_detail_id != '') {
                $good_receive_detail = \App\Model\GoodReceiveDetails::find($good_receive_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$good_receive->id.','.$good_receive->purchase_order_id.','.$good_receive->invoice_no.','.$good_receive->good_receive_date_time.','.$good_receive->good_receive_value.','.str_replace(',', ' ', $good_receive->remarks).','.$good_receive_detail->id.','.$good_receive_detail->item_id.','.$good_receive_detail->model_no.','.$good_receive_detail->brand.','.$good_receive_detail->origin.','.$good_receive_detail->rate.','.$good_receive_detail->quantity.','.$good_receive_detail->available_quantity.','.$good_receive_detail->location.','.$good_receive_detail->warranty.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$good_receive->id.','.$good_receive->purchase_order_id.','.$good_receive->invoice_no.','.$good_receive->good_receive_date_time.','.$good_receive->good_receive_value.','.str_replace(',', ' ', $good_receive->remarks).',,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            }

            $result = [
                'response' => true,
                'message' => 'Good Receive Detail created successfully',
                'data' => $good_receive->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Good Receive Detail creation failed',
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
        $good_receive = \App\Model\GoodReceive::find($request->good_receive_id);
        $good_receive->purchase_order_id = isset($request->purchase_order['id']) ? $request->purchase_order['id'] : 0;
        $good_receive->invoice_no = $request->invoice_no;
        $good_receive->good_receive_no = $request->good_receive_no;
        $good_receive->good_receive_date_time = date('Y-m-d', strtotime($request->good_receive_date)).' '.$request->good_receive_time;
        $good_receive->remarks = $request->remarks;

        if ($good_receive->save()) {
            $good_receive_detail_id = '';
            if (isset($request->item['id'])) {
                $good_receive_detail = \App\Model\GoodReceiveDetails::find($id);
                $good_receive_detail->quantity = 0;
                $good_receive_detail->available_quantity = 0;
                $good_receive_detail->is_delete = 1;
                $good_receive_detail->save();
                $good_receive_breakdown = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);

                $old_good_receive_detail = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                    ->where('item_id', $request->item['id'])
                    ->first();

                $good_receive_detail = $old_good_receive_detail ? $old_good_receive_detail : new \App\Model\GoodReceiveDetails();
                $good_receive_detail->good_receive_id = $good_receive->id;
                $good_receive_detail->item_id = $request->item['id'];
                $good_receive_detail->model_no = $request->model_no;
                $good_receive_detail->brand = $request->brand;
                $good_receive_detail->origin = $request->origin;
                $good_receive_detail->rate = $request->rate;
                $good_receive_detail->quantity = $old_good_receive_detail ? $old_good_receive_detail->quantity + $request->quantity : $request->quantity;
                $good_receive_detail->location = $request->location;
                $good_receive_detail->warranty = $request->warranty;
                $good_receive_detail->is_delete = 0;
                $good_receive_detail->save();

                $good_receive_detail_id = $good_receive_detail->id;

                $main_ids = [];
                foreach ($request->serial_details as $detail) {
                    if ($detail['is_main'] == 1) {
                        $old_good_receive_breakdown = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                            ->where('serial_no', $detail['serial_no'])
                            ->where('is_main', 1)
                            ->first();
                        $good_receive_breakdown = $old_good_receive_breakdown ? $old_good_receive_breakdown : new \App\Model\GoodReceiveBreakdown();
                        $good_receive_breakdown->good_receive_detail_id = $good_receive_detail->id;
                        $good_receive_breakdown->serial_no = $detail['serial_no'];
                        $good_receive_breakdown->is_main = 1;
                        $good_receive_breakdown->is_issued = 0;
                        $good_receive_breakdown->is_delete = 0;
                        if ($good_receive_breakdown->save()) {
                            $good_receive_breakdown->main_id = $good_receive_breakdown->id;
                            $good_receive_breakdown->save();

                            $row = [
                                'id' => $detail['main_id'],
                                'main_id' => $good_receive_breakdown->id,
                            ];
                            array_push($main_ids, $row);
                        }
                    }
                }
                foreach ($main_ids as $main_id) {
                    foreach ($request->serial_details as $detail) {
                        if ($detail['is_main'] == 0 && $detail['main_id'] == $main_id['id']) {
                            $old_good_receive_breakdown = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                                ->where('serial_no', $detail['serial_no'])
                                ->where('is_main', 0)
                                ->first();
                            $good_receive_breakdown = $old_good_receive_breakdown ? $old_good_receive_breakdown : new \App\Model\GoodReceiveBreakdown();
                            $good_receive_breakdown->good_receive_detail_id = $good_receive_detail->id;
                            $good_receive_breakdown->serial_no = $detail['serial_no'];
                            $good_receive_breakdown->is_main = 0;
                            $good_receive_breakdown->main_id = $main_id['main_id'];
                            $good_receive_breakdown->is_issued = 0;
                            $good_receive_breakdown->is_delete = 0;
                            $good_receive_breakdown->save();
                        }
                    }
                }
            }

            $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($good_receive_details as $good_receive_detail) {
                $total_value += $good_receive_detail->rate * $good_receive_detail->quantity;
            }
            $good_receive->good_receive_value = $total_value;
            $good_receive->save();

            if ($good_receive_detail_id != '') {
                $good_receive_detail = \App\Model\GoodReceiveDetails::find($good_receive_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$good_receive->id.','.$good_receive->purchase_order_id.','.$good_receive->invoice_no.','.$good_receive->good_receive_date_time.','.$good_receive->good_receive_value.','.str_replace(',', ' ', $good_receive->remarks).','.$good_receive_detail->id.','.$good_receive_detail->item_id.','.$good_receive_detail->model_no.','.$good_receive_detail->brand.','.$good_receive_detail->origin.','.$good_receive_detail->rate.','.$good_receive_detail->quantity.','.$good_receive_detail->available_quantity.','.$good_receive_detail->location.','.$good_receive_detail->warranty.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$good_receive->id.','.$good_receive->purchase_order_id.','.$good_receive->invoice_no.','.$good_receive->good_receive_date_time.','.$good_receive->good_receive_value.','.str_replace(',', ' ', $good_receive->remarks).',,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);
            }

            $result = [
                'response' => true,
                'message' => 'Good Receive Detail updated successfully',
                'data' => $good_receive->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Good Receive Detail updation failed',
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
            $good_receive = \App\Model\GoodReceive::find($id);
            $good_receive->is_delete = 1;

            if ($good_receive->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$good_receive->id.',,,,,,,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)->where('is_delete', 0)->get();
                foreach ($good_receive_details as $good_receive_detail) {
                    $good_receive_detail->quantity = 0;
                    $good_receive_detail->available_quantity = 0;
                    $good_receive_detail->is_delete = 1;
                    $good_receive_detail->save();

                    $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)->where('is_delete', 0)->get();
                    foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                        $good_receive_breakdown->is_delete = 1;
                        $good_receive_breakdown->save();
                    }
                }

                $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive->id)
                    ->where('is_delete', 0)
                    ->get();
                $total_value = 0;
                foreach ($good_receive_details as $good_receive_detail) {
                    $total_value += $good_receive_detail->rate * $good_receive_detail->quantity;
                }
                $good_receive->good_receive_value = $total_value;
                $good_receive->save();

                $result = [
                    'response' => true,
                    'message' => 'Good Receive deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Good Receive deletion failed',
                ];
            }
        } elseif ($request->type == 1) {
            $good_receive_detail = \App\Model\GoodReceiveDetails::find($id);
            $good_receive_detail->quantity = 0;
            $good_receive_detail->available_quantity = 0;
            $good_receive_detail->is_delete = 1;

            if ($good_receive_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/good_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$good_receive_detail->good_receive_id.',,,,,,'.$good_receive_detail->id.',,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)->where('is_delete', 0)->get();
                foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                    $good_receive_breakdown->is_delete = 1;
                    $good_receive_breakdown->save();
                }

                $good_receive_details = \App\Model\GoodReceiveDetails::where('good_receive_id', $good_receive_detail->good_receive_id)
                    ->where('is_delete', 0)
                    ->get();
                $total_value = 0;
                foreach ($good_receive_details as $good_receive_detail) {
                    $total_value += $good_receive_detail->rate * $good_receive_detail->quantity;
                }
                $good_receive = \App\Model\GoodReceive::find($good_receive_detail->good_receive_id);
                $good_receive->good_receive_value = $total_value;
                $good_receive->save();

                $result = [
                    'response' => true,
                    'message' => 'Good Receive Detail deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Good Receive Detail deletion failed',
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
