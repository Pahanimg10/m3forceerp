<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class RegionController extends Controller
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

        return view('master.region', $data);
    }

    public function region_list()
    {
        $regions = \App\Model\Region::select('id', 'code', 'name')->where('is_delete', 0)->get();

        $data = [
            'regions' => $regions,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_region(Request $request)
    {
        if ($request->value != $request->name) {
            $region = \App\Model\Region::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if ($region) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_region(Request $request)
    {
        $region = \App\Model\Region::select('id', 'name')->find($request->id);

        return response($region);
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
        $region = new \App\Model\Region();
        $region->name = $request->name;

        if ($region->save()) {
            $code = 'RG'.sprintf('%03d', $region->id);
            $region->code = $code;
            $region->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/region_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$region->id.','.str_replace(',', ' ', $region->code).','.str_replace(',', ' ', $region->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Region created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Region creation failed',
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
        $region = \App\Model\Region::find($id);
        $region->name = $request->name;

        if ($region->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/region_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$region->id.','.str_replace(',', ' ', $region->code).','.str_replace(',', ' ', $region->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);
            $result = [
                'response' => true,
                'message' => 'Region updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Region updation failed',
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
        $region = \App\Model\Region::find($id);
        $region->is_delete = 1;

        if ($region->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/region_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$region->id.',,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);
            $result = [
                'response' => true,
                'message' => 'Region deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Region deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
