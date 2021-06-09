<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class PersonResponsibleController extends Controller
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

        return view('master.person_responsible', $data);
    }

    public function person_responsible_list()
    {
        $person_responsibles = \App\Model\PersonResponsible::select('id', 'code', 'name', 'title', 'contact_no', 'email', 'is_active')->where('is_delete', 0)->get();

        $data = [
            'person_responsibles' => $person_responsibles,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_person_responsible(Request $request)
    {
        if ($request->value != $request->name) {
            $person_responsible = \App\Model\PersonResponsible::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if ($person_responsible) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_person_responsible(Request $request)
    {
        $person_responsible = \App\Model\PersonResponsible::select('id', 'name', 'title', 'contact_no', 'email', 'head_name', 'head_contact_no', 'head_email', 'is_active')->find($request->id);

        return response($person_responsible);
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
        $person_responsible = new \App\Model\PersonResponsible();
        $person_responsible->name = $request->name;
        $person_responsible->title = $request->title;
        $person_responsible->contact_no = $request->contact_no;
        $person_responsible->email = $request->email;
        $person_responsible->head_name = $request->head_name;
        $person_responsible->head_contact_no = $request->head_contact_no;
        $person_responsible->head_email = $request->head_email;
        $person_responsible->is_active = $request->is_active;

        if ($person_responsible->save()) {
            $code = 'PR'.sprintf('%03d', $person_responsible->id);
            $person_responsible->code = $code;
            $person_responsible->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/person_responsible_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$person_responsible->id.','.$person_responsible->code.','.str_replace(',', ' ', $person_responsible->name).','.str_replace(',', ' ', $person_responsible->title).','.str_replace(',', ' ', $person_responsible->contact_no).','.str_replace(',', ' ', $person_responsible->email).','.str_replace(',', ' ', $person_responsible->head_name).','.str_replace(',', ' ', $person_responsible->head_contact_no).','.str_replace(',', ' ', $person_responsible->head_email).','.$person_responsible->is_active.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Person Responsible created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Person Responsible creation failed',
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
        $person_responsible = \App\Model\PersonResponsible::find($id);
        $person_responsible->name = $request->name;
        $person_responsible->title = $request->title;
        $person_responsible->contact_no = $request->contact_no;
        $person_responsible->email = $request->email;
        $person_responsible->head_name = $request->head_name;
        $person_responsible->head_contact_no = $request->head_contact_no;
        $person_responsible->head_email = $request->head_email;
        $person_responsible->is_active = $request->is_active;

        if ($person_responsible->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/person_responsible_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$person_responsible->id.','.$person_responsible->code.','.str_replace(',', ' ', $person_responsible->name).','.str_replace(',', ' ', $person_responsible->title).','.str_replace(',', ' ', $person_responsible->contact_no).','.str_replace(',', ' ', $person_responsible->email).','.str_replace(',', ' ', $person_responsible->head_name).','.str_replace(',', ' ', $person_responsible->head_contact_no).','.str_replace(',', ' ', $person_responsible->head_email).','.$person_responsible->is_active.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);
            $result = [
                'response' => true,
                'message' => 'Person Responsible updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Person Responsible updation failed',
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
        $person_responsible = \App\Model\PersonResponsible::find($id);
        $person_responsible->is_delete = 1;

        if ($person_responsible->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/person_responsible_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$person_responsible->id.',,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);
            $result = [
                'response' => true,
                'message' => 'Person Responsible deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Person Responsible deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
