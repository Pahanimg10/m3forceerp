<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class JobPositionController extends Controller
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

        return view('master.job_position', $data);
    }

    public function job_position_list()
    {
        $job_positions = \App\Model\JobPosition::select('id', 'name', 'code')->where('is_delete', 0)->get();

        $data = [
            'job_positions' => $job_positions,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_job_position(Request $request)
    {
        if ($request->value != $request->code) {
            $job_position = \App\Model\JobPosition::where('code', $request->code)
                    ->where('is_delete', 0)
                    ->first();
            if ($job_position) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_job_position(Request $request)
    {
        $job_position = \App\Model\JobPosition::select('id', 'name', 'code')->find($request->id);

        return response($job_position);
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
        $job_position = new \App\Model\JobPosition();
        $job_position->name = $request->name;
        $job_position->code = $request->code;

        if ($job_position->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_position_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$job_position->id.','.str_replace(',', ' ', $job_position->code).','.str_replace(',', ' ', $job_position->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Job Position created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Job Position creation failed',
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
        $job_position = \App\Model\JobPosition::find($id);
        $job_position->name = $request->name;
        $job_position->code = $request->code;

        if ($job_position->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_position_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$job_position->id.','.str_replace(',', ' ', $job_position->code).','.str_replace(',', ' ', $job_position->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Job Position updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Job Position updation failed',
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
        $job_position = \App\Model\JobPosition::find($id);
        $job_position->is_delete = 1;

        if ($job_position->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_position_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$job_position->id.',,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Job Position deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Job Position deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
