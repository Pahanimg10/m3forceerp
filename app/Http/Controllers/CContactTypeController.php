<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class CContactTypeController extends Controller
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

        return view('master.c_contact_type', $data);
    }

    public function c_contact_type_list()
    {
        $c_contact_types = \App\Model\CContactType::select('id', 'code', 'name')->where('is_delete', 0)->get();

        $data = [
            'c_contact_types' => $c_contact_types,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_c_contact_type(Request $request)
    {
        if ($request->value != $request->name) {
            $c_contact_type = \App\Model\CContactType::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if ($c_contact_type) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_c_contact_type(Request $request)
    {
        $c_contact_type = \App\Model\CContactType::select('id', 'name')->find($request->id);

        return response($c_contact_type);
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
        $c_contact_type = new \App\Model\CContactType();
        $c_contact_type->name = $request->name;

        if ($c_contact_type->save()) {
            $code = 'C-COT'.sprintf('%03d', $c_contact_type->id);
            $c_contact_type->code = $code;
            $c_contact_type->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_contact_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$c_contact_type->id.','.str_replace(',', ' ', $c_contact_type->code).','.str_replace(',', ' ', $c_contact_type->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'C-Contact Type created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'C-Contact Type creation failed',
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
        $c_contact_type = \App\Model\CContactType::find($id);
        $c_contact_type->name = $request->name;

        if ($c_contact_type->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_contact_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$c_contact_type->id.','.str_replace(',', ' ', $c_contact_type->code).','.str_replace(',', ' ', $c_contact_type->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'C-Contact Type updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'C-Contact Type updation failed',
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
        $c_contact_type = \App\Model\CContactType::find($id);
        $c_contact_type->is_delete = 1;

        if ($c_contact_type->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/c_contact_type_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$c_contact_type->id.',,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'C-Contact Type deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'C-Contact Type deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
