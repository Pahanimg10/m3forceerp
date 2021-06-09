<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class CollectionManagerController extends Controller
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

        return view('master.collection_manager', $data);
    }

    public function collection_manager_list()
    {
        $collection_managers = \App\Model\CollectionManager::select('id', 'code', 'name', 'contact_no', 'is_active')->where('is_delete', 0)->get();

        $data = [
            'collection_managers' => $collection_managers,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_collection_manager(Request $request)
    {
        if ($request->value != $request->name) {
            $collection_manager = \App\Model\CollectionManager::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if ($collection_manager) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_collection_manager(Request $request)
    {
        $collection_manager = \App\Model\CollectionManager::select('id', 'name', 'contact_no', 'is_active')->find($request->id);

        return response($collection_manager);
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
        $collection_manager = new \App\Model\CollectionManager();
        $collection_manager->name = $request->name;
        $collection_manager->contact_no = $request->contact_no;
        $collection_manager->is_active = $request->is_active;

        if ($collection_manager->save()) {
            $code = 'CM'.sprintf('%03d', $collection_manager->id);
            $collection_manager->code = $code;
            $collection_manager->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/collection_manager_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$collection_manager->id.','.$collection_manager->code.','.str_replace(',', ' ', $collection_manager->name).','.str_replace(',', ' ', $collection_manager->contact_no).','.$collection_manager->is_active.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Collection Manager created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Collection Manager creation failed',
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
        $collection_manager = \App\Model\CollectionManager::find($id);
        $collection_manager->name = $request->name;
        $collection_manager->contact_no = $request->contact_no;
        $collection_manager->is_active = $request->is_active;

        if ($collection_manager->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/collection_manager_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$collection_manager->id.','.$collection_manager->code.','.str_replace(',', ' ', $collection_manager->name).','.str_replace(',', ' ', $collection_manager->contact_no).','.$collection_manager->is_active.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Collection Manager updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Collection Manager updation failed',
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
    public function destroy($id)
    {
        $collection_manager = \App\Model\CollectionManager::find($id);
        $collection_manager->is_delete = 1;

        if ($collection_manager->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/collection_manager_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$collection_manager->id.','.$collection_manager->code.','.str_replace(',', ' ', $collection_manager->name).','.str_replace(',', ' ', $collection_manager->contact_no).','.$collection_manager->is_active.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Collection Manager deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Collection Manager deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
