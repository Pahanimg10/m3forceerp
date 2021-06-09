<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class IModeOfInquiryController extends Controller
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

        return view('master.i_mode_of_inquiry', $data);
    }

    public function i_mode_of_inquiry_list()
    {
        $i_mode_of_inquiries = \App\Model\IModeOfInquiry::select('id', 'code', 'name')->where('is_delete', 0)->get();

        $data = [
            'i_mode_of_inquiries' => $i_mode_of_inquiries,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function validate_i_mode_of_inquiry(Request $request)
    {
        if ($request->value != $request->name) {
            $i_mode_of_inquiry = \App\Model\IModeOfInquiry::where('name', $request->name)
                    ->where('is_delete', 0)
                    ->first();
            if ($i_mode_of_inquiry) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function find_i_mode_of_inquiry(Request $request)
    {
        $i_mode_of_inquiry = \App\Model\IModeOfInquiry::select('id', 'name')->find($request->id);

        return response($i_mode_of_inquiry);
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
        $i_mode_of_inquiry = new \App\Model\IModeOfInquiry();
        $i_mode_of_inquiry->name = $request->name;

        if ($i_mode_of_inquiry->save()) {
            $code = 'I-MOI'.sprintf('%03d', $i_mode_of_inquiry->id);
            $i_mode_of_inquiry->code = $code;
            $i_mode_of_inquiry->save();

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/i_mode_of_inquiry_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$i_mode_of_inquiry->id.','.str_replace(',', ' ', $i_mode_of_inquiry->code).','.str_replace(',', ' ', $i_mode_of_inquiry->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'I-Mode Of Inquiry created successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'I-Mode Of Inquiry creation failed',
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
        $i_mode_of_inquiry = \App\Model\IModeOfInquiry::find($id);
        $i_mode_of_inquiry->name = $request->name;

        if ($i_mode_of_inquiry->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/i_mode_of_inquiry_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$i_mode_of_inquiry->id.','.str_replace(',', ' ', $i_mode_of_inquiry->code).','.str_replace(',', ' ', $i_mode_of_inquiry->name).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'I-Mode Of Inquiry updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'I-Mode Of Inquiry updation failed',
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
        $i_mode_of_inquiry = \App\Model\IModeOfInquiry::find($id);
        $i_mode_of_inquiry->is_delete = 1;

        if ($i_mode_of_inquiry->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/i_mode_of_inquiry_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$i_mode_of_inquiry->id.',,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Mode Of Inquiry deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Mode Of Inquiry deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
