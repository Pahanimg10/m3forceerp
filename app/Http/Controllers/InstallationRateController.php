<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class InstallationRateController extends Controller
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

        return view('master.installation_rate', $data);
    }

    public function installation_rate_list()
    {
        $installation_rates = \App\Model\InstallationRate::select('id', 'code', 'name', 'installation_cost', 'labour', 'rate')->where('is_delete', 0)->get();

        $data = [
            'installation_rates' => $installation_rates,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_installation_rate(Request $request)
    {
        if ($request->value != $request->name) {
            $installation_rate = \App\Model\InstallationRate::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if ($installation_rate) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_installation_rate(Request $request)
    {
        $installation_rate = \App\Model\InstallationRate::select('id', 'code', 'name', 'installation_cost', 'labour', 'rate')->find($request->id);

        return response($installation_rate);
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
        $installation_rate = new \App\Model\InstallationRate();
        $installation_rate->name = $request->name;
        $installation_rate->installation_cost = $request->installation_cost && $request->installation_cost != '' ? $request->installation_cost : 0;
        $installation_rate->labour = $request->labour && $request->labour != '' ? $request->labour : 0;
        $installation_rate->rate = $request->rate && $request->rate != '' ? $request->rate : 0;

        if ($installation_rate->save()) {
            $code = 'IR'.sprintf('%03d', $installation_rate->id);
            $installation_rate->code = $code;
            $installation_rate->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_rate_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$installation_rate->id.','.$installation_rate->code.','.str_replace(',', ' ', $installation_rate->name).','.$installation_rate->installation_cost.','.$installation_rate->labour.','.$installation_rate->rate.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Installation Rate created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Installation Rate creation failed',
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
        $installation_rate = \App\Model\InstallationRate::find($id);
        $installation_rate->name = $request->name;
        $installation_rate->installation_cost = $request->installation_cost && $request->installation_cost != '' ? $request->installation_cost : 0;
        $installation_rate->labour = $request->labour && $request->labour != '' ? $request->labour : 0;
        $installation_rate->rate = $request->rate && $request->rate != '' ? $request->rate : 0;

        if ($installation_rate->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_rate_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$installation_rate->id.','.$installation_rate->code.','.str_replace(',', ' ', $installation_rate->name).','.$installation_rate->installation_cost.','.$installation_rate->labour.','.$installation_rate->rate.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Installation Rate updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Installation Rate updation failed',
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
        $installation_rate = \App\Model\InstallationRate::find($id);
        $installation_rate->is_delete = 1;

        if ($installation_rate->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/installation_rate_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$installation_rate->id.',,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Installation Rate deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Installation Rate deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
