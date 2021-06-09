<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class ItemController extends Controller
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

        // $main_item_categories = \App\Model\MainItemCategory::where('is_delete', 0)->get();
        // $sub_item_categories = \App\Model\SubItemCategory::where('is_delete', 0)->get();

        // foreach ($main_item_categories as $main_item_category) {
        //     foreach ($sub_item_categories as $sub_item_category) {
        //         $items = \App\Model\Item::where('main_category_id', $main_item_category->id)
        //             ->where('sub_category_id', $sub_item_category->id)
        //             ->get();
        //         if (count($items) > 0) {
        //             $count = 1;
        //             foreach ($items as $item) {
        //                 $item->code = 'IT-' . $main_item_category->code . '-' . $sub_item_category->code . sprintf('%05d', $count);
        //                 $item->save();
        //                 $count++;
        //             }
        //         }
        //     }
        // }

        return view('master.item', $data);
    }

    public function item_list(Request $request)
    {
        $item_details = array();
        $items = \App\Model\Item::where(function ($q) use ($request) {
            $request->main_category != -1 ? $q->where('main_category_id', $request->main_category) : '';
        })
            ->where(function ($q) use ($request) {
                $request->sub_category != -1 ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->purchase_type != -1 ? $q->where('purchase_type_id', $request->purchase_type) : '';
            })
            ->where('is_delete', 0)
            ->get();
        foreach ($items as $item) {
            $good_receive_details = \App\Model\GoodReceiveDetails::whereHas('GoodReceive', function ($query) {
                $query->where('is_posted', 1)->where('is_delete', 0)->orderBy('good_receive_date_time', 'asc');
            })
                ->where('item_id', $item->id)
                ->where('available_quantity', '>', 0)
                ->where('is_delete', 0)
                ->get();
            $location = $serial_nos = '';
            $available_quantity = $serial_no_count = 0;
            foreach ($good_receive_details as $good_receive_detail) {
                if($good_receive_detail->available_quantity > 0){
                    $location .= $location != '' ? ' ' . $good_receive_detail->location : $good_receive_detail->location;
                }
                
                $available_quantity += $good_receive_detail->available_quantity;

                $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                    ->where('is_main', 1)
                    ->where('is_issued', 0)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($good_receive_breakdowns as $good_receive_breakdown) {
                    $serial_nos .= $serial_nos != '' ? ' | ' . $good_receive_breakdown->serial_no : $good_receive_breakdown->serial_no;
                    $serial_no_count++;
                }
            }
            $row = array(
                'id' => $item->id,
                'main_category' => $item->MainItemCategory ? $item->MainItemCategory->name : '',
                'sub_category' => $item->SubItemCategory ? $item->SubItemCategory->name : '',
                'purchase_type' => $item->PurchaseType ? $item->PurchaseType->name : '',
                'code' => $item->code,
                'name' => $item->name,
                'supplier' => $item->Contact ? $item->Contact->name : '',
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'unit_type' => $item->UnitType ? $item->UnitType->code : '',
                'rate' => $item->rate,
                'reorder_level' => $item->reorder_level,
                'stock' => $item->stock,
                'location' => $location,
                'available_quantity' => $available_quantity,
                'serial_no_count' => $serial_no_count,
                'serial_nos' => $serial_nos,
                'is_active' => $item->is_active == 1 ? 'Yes' : 'No'
            );
            array_push($item_details, $row);
        }

        $data = array(
            'items' => $item_details,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_item(Request $request)
    {
        $item = \App\Model\Item::select('id', 'main_category_id', 'sub_category_id', 'purchase_type_id', 'code', 'name', 'model_no', 'brand', 'origin', 'unit_type_id', 'reorder_level', 'rate', 'is_serial', 'is_warranty', 'is_active')
            ->with(array('MainItemCategory' => function ($query) {
                $query->select('id', 'code', 'name');
            }))
            ->with(array('SubItemCategory' => function ($query) {
                $query->select('id', 'code', 'name');
            }))
            ->with(array('PurchaseType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('UnitType' => function ($query) {
                $query->select('id', 'code', 'name');
            }))
            ->find($request->id);
        return response($item);
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

        $data['item_id'] = $request->id;

        return view('master.item_detail', $data);
    }

    public function validate_item(Request $request)
    {
        if ($request->value != $request->name) {
            $item = \App\Model\Item::where('name', $request->name)
                ->where('is_delete', 0)
                ->first();
            if ($item) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function validate_model_no(Request $request)
    {
        if ($request->value != $request->model_no) {
            $item = \App\Model\Item::where('model_no', $request->model_no)
                ->where('is_delete', 0)
                ->first();
            if ($item) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function get_data()
    {
        $main_item_categories = \App\Model\MainItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $sub_item_categories = \App\Model\SubItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $unit_types = \App\Model\UnitType::select('id', 'code')->where('is_delete', 0)->orderBy('name')->get();
        $purchase_types = \App\Model\PurchaseType::select('id', 'name')->orderBy('name')->get();

        $data = array(
            'main_item_categories' => $main_item_categories,
            'sub_item_categories' => $sub_item_categories,
            'unit_types' => $unit_types,
            'purchase_types' => $purchase_types
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
        $item = new \App\Model\Item();

        $item->main_category_id = isset($request->main_category['id']) ? $request->main_category['id'] : 0;
        $item->sub_category_id = isset($request->sub_category['id']) ? $request->sub_category['id'] : 0;
        $item->purchase_type_id = isset($request->purchase_type['id']) ? $request->purchase_type['id'] : 0;
        $main_prefix = isset($request->main_category['code']) ? $request->main_category['code'] : '';
        $sub_prefix = isset($request->sub_category['code']) ? $request->sub_category['code'] : '';
        $last_id = 0;
        if (isset($request->main_category['id']) && isset($request->sub_category['id'])) {
            $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $request->main_category['id'])->where('sub_category_id', $request->sub_category['id'])->orderBy('id', 'desc')->first();
            $last_id = $last_item ? $last_item->id : $last_id;
        }
        $item->code = 'IT-' . $main_prefix . '-' . $sub_prefix . sprintf('%05d', $last_id + 1);
        $item->name = $request->name;
        $item->model_no = $request->model_no;
        $item->brand = $request->brand;
        $item->origin = $request->origin;
        $item->unit_type_id = isset($request->unit_type['id']) ? $request->unit_type['id'] : 0;
        $item->reorder_level = $request->reorder_level;
        $item->rate = $request->rate;
        $item->is_serial = $request->is_serial ? 1 : 0;
        $item->is_warranty = $request->is_warranty ? 1 : 0;
        $item->is_active = $request->is_active ? 1 : 0;

        if ($item->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $item->id . ',' . $item->main_category_id . ',' . $item->sub_category_id . ',' . $item->purchase_type_id . ',' . $item->code . ',' . str_replace(',', ' ', $item->name) . ',' . str_replace(',', ' ', $item->model_no) . ',' . str_replace(',', ' ', $item->brand) . ',' . str_replace(',', ' ', $item->origin) . ',' . $item->unit_type_id . ',' . $item->reorder_level . ',' . $item->rate . ',' . $item->is_serial . ',' . $item->is_warranty . ',' . $item->is_active . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $result = array(
                'response' => true,
                'message' => 'Item created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item creation failed'
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
        $item = \App\Model\Item::find($id);

        $item_changed = false;
        if ($item->main_category_id != $request->main_category['id'] || $item->sub_category_id != $request->sub_category['id']) {
            $item_changed = true;
        }

        $item->main_category_id = isset($request->main_category['id']) ? $request->main_category['id'] : 0;
        $item->sub_category_id = isset($request->sub_category['id']) ? $request->sub_category['id'] : 0;
        $item->purchase_type_id = isset($request->purchase_type['id']) ? $request->purchase_type['id'] : 0;
        if ((isset($request->main_category['id']) && $request->main_category['id'] != $item->main_category_id) || (isset($request->sub_category['id']) && $request->sub_category['id'] != $item->sub_category_id)) {
            $main_prefix = isset($request->main_category['code']) ? $request->main_category['code'] : '';
            $sub_prefix = isset($request->sub_category['code']) ? $request->sub_category['code'] : '';
            $last_id = 0;
            if (isset($request->main_category['id']) && isset($request->sub_category['id'])) {
                $last_item = \App\Model\Item::selectRaw('COUNT(id) AS id')->where('main_category_id', $request->main_category['id'])->where('sub_category_id', $request->sub_category['id'])->orderBy('id', 'desc')->first();
                $last_id = $last_item ? $last_item->id : $last_id;
            }
            $item->code = 'IT-' . $main_prefix . '-' . $sub_prefix . sprintf('%05d', $last_id + 1);
        }
        $item->name = $request->name;
        $item->model_no = $request->model_no;
        $item->brand = $request->brand;
        $item->origin = $request->origin;
        $item->unit_type_id = isset($request->unit_type['id']) ? $request->unit_type['id'] : 0;
        $item->reorder_level = $request->reorder_level;
        $item->rate = $request->rate;
        $item->is_serial = $request->is_serial ? 1 : 0;
        $item->is_warranty = $request->is_warranty ? 1 : 0;
        $item->is_active = $request->is_active ? 1 : 0;

        if ($item->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $item->id . ',' . $item->main_category_id . ',' . $item->sub_category_id . ',' . $item->purchase_type_id . ',' . $item->code . ',' . str_replace(',', ' ', $item->name) . ',' . str_replace(',', ' ', $item->model_no) . ',' . str_replace(',', ' ', $item->brand) . ',' . str_replace(',', ' ', $item->origin) . ',' . $item->unit_type_id . ',' . $item->reorder_level . ',' . $item->rate . ',' . $item->is_serial . ',' . $item->is_warranty . ',' . $item->is_active . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            if ($item_changed) {
                $main_item_categories = \App\Model\MainItemCategory::where('is_delete', 0)->get();
                $sub_item_categories = \App\Model\SubItemCategory::where('is_delete', 0)->get();

                foreach ($main_item_categories as $main_item_category) {
                    foreach ($sub_item_categories as $sub_item_category) {
                        $items = \App\Model\Item::where('main_category_id', $main_item_category->id)
                            ->where('sub_category_id', $sub_item_category->id)
                            ->get();
                        if (count($items) > 0) {
                            $count = 1;
                            foreach ($items as $item) {
                                $item->code = 'IT-' . $main_item_category->code . '-' . $sub_item_category->code . sprintf('%05d', $count);
                                $item->save();
                                $count++;
                            }
                        }
                    }
                }
            }

            $result = array(
                'response' => true,
                'message' => 'Item updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item updation failed'
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
        $item = \App\Model\Item::find($id);
        $item->is_delete = 1;

        if ($item->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $item->id . ',,,,,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $result = array(
                'response' => true,
                'message' => 'Item deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item deletion failed'
            );
        }

        echo json_encode($result);
    }
}
