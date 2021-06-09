<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class TechnicalTeamController extends Controller
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

        return view('master.technical_team', $data);
    }

    public function technical_team_list()
    {
        $technical_teams = \App\Model\TechnicalTeam::select('id', 'code', 'epf_no', 'name', 'contact_no', 'nic', 'is_driving', 'is_active')
                ->with(['TechnicalTeamDrivingDetail' => function ($query) {
                    $query->select('id', 'technical_team_id', 'driving_type_id')
                            ->with(['DrivingType' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                }])
                ->where('is_delete', 0)
                ->get();

        $data = [
            'technical_teams' => $technical_teams,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_technical_team(Request $request)
    {
        if ($request->value != $request->epf_no) {
            $technical_team = \App\Model\TechnicalTeam::where('epf_no', $request->epf_no)
                    ->where('is_delete', 0)
                    ->first();
            if ($technical_team) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_technical_team(Request $request)
    {
        $technical_team = \App\Model\TechnicalTeam::select('id', 'epf_no', 'name', 'contact_no', 'nic', 'is_driving', 'is_active')
                ->with(['TechnicalTeamDrivingDetail' => function ($query) {
                    $query->select('id', 'technical_team_id', 'driving_type_id')
                            ->with(['DrivingType' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                }])
                ->find($request->id);

        return response($technical_team);
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
        $technical_team = new \App\Model\TechnicalTeam();
        $technical_team->epf_no = $request->epf_no;
        $technical_team->name = $request->name;
        $technical_team->contact_no = $request->contact_no;
        $technical_team->nic = $request->nic;
        $technical_team->is_driving = $request->is_driving;
        $technical_team->is_active = $request->is_active;

        if ($technical_team->save()) {
            $code = 'TEC'.sprintf('%03d', $technical_team->id);
            $technical_team->code = $code;
            $technical_team->save();

            $driving_type_ids = '';
            foreach ($request->driving_types as $detail) {
                if ($detail['selected']) {
                    $technical_team_driving_detail = new \App\Model\TechnicalTeamDrivingDetail();
                    $technical_team_driving_detail->technical_team_id = $technical_team->id;
                    $technical_team_driving_detail->driving_type_id = $detail['id'];
                    $technical_team_driving_detail->save();

                    $driving_type_ids .= $driving_type_ids != '' ? '|'.$technical_team_driving_detail->driving_type_id : $technical_team_driving_detail->driving_type_id;
                }
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/technical_team_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$technical_team->id.','.$technical_team->code.','.str_replace(',', ' ', $technical_team->epf_no).','.str_replace(',', ' ', $technical_team->name).','.str_replace(',', ' ', $technical_team->contact_no).','.str_replace(',', ' ', $technical_team->nic).','.$technical_team->is_driving.','.$technical_team->is_active.','.$driving_type_ids.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Technical Team created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Technical Team creation failed',
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
        $technical_team = \App\Model\TechnicalTeam::find($id);
        $technical_team->epf_no = $request->epf_no;
        $technical_team->name = $request->name;
        $technical_team->contact_no = $request->contact_no;
        $technical_team->nic = $request->nic;
        $technical_team->is_driving = $request->is_driving;
        $technical_team->is_active = $request->is_active;

        if ($technical_team->save()) {
            $technical_team_driving_details = \App\Model\TechnicalTeamDrivingDetail::where('technical_team_id', $technical_team->id)->where('is_delete', 0)->get();
            foreach ($technical_team_driving_details as $technical_team_driving_detail) {
                $technical_team_driving_detail->is_delete = 1;
                $technical_team_driving_detail->save();
            }

            $driving_type_ids = '';
            foreach ($request->driving_types as $detail) {
                if ($detail['selected']) {
                    $technical_team_driving_detail = \App\Model\TechnicalTeamDrivingDetail::where('technical_team_id', $technical_team->id)
                            ->where('driving_type_id', $detail['id'])
                            ->first();
                    $technical_team_driving_detail = $technical_team_driving_detail ? $technical_team_driving_detail : new \App\Model\TechnicalTeamDrivingDetail();
                    $technical_team_driving_detail->technical_team_id = $technical_team->id;
                    $technical_team_driving_detail->driving_type_id = $detail['id'];
                    $technical_team_driving_detail->is_delete = 0;
                    $technical_team_driving_detail->save();

                    $driving_type_ids .= $driving_type_ids != '' ? '|'.$detail['id'] : $detail['id'];
                }
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/technical_team_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$technical_team->id.','.$technical_team->code.','.str_replace(',', ' ', $technical_team->epf_no).','.str_replace(',', ' ', $technical_team->name).','.str_replace(',', ' ', $technical_team->contact_no).','.str_replace(',', ' ', $technical_team->nic).','.$technical_team->is_driving.','.$technical_team->is_active.','.$driving_type_ids.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Technical Team updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Technical Team updation failed',
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
        $technical_team = \App\Model\TechnicalTeam::find($id);
        $technical_team->is_delete = 1;

        if ($technical_team->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/technical_team_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$technical_team->id.',,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);
            $result = [
                'response' => true,
                'message' => 'Technical Team deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Technical Team deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
