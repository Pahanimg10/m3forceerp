<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class CompanyController extends Controller
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
        
        return view('master.company', $data);
    }

    public function find_company(Request $request)
    {
        $company = \App\Model\Company::find($request->id);
        if(!$company){
            $company = new \App\Model\Company();
            $company->save();
        }
        $company = \App\Model\Company::select('id', 'company_name', 'phone_number', 'hotline_number', 'email', 'website', 'address_line_1', 'address_line_2', 'address_line_3', 'reg_number', 'svat', 'vat', 'company_image')
                ->find($company->id);
        return response($company);
    }

    public function image_upload() 
    {
        if(!empty($_FILES['image'])){
            $ext = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
            $image = time().'.'.$ext;
            move_uploaded_file($_FILES["image"]["tmp_name"], 'assets/images/company/'.$image);
            $result = array(
                'response' => true,
                'message' => 'success',
                'image' => $image
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Image Is Empty'
            );
        }

        echo json_encode($result);
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
        //
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
        $company = \App\Model\Company::find(1);
        $company->company_name = $request->company_name;
        $company->phone_number = $request->phone_number;
        $company->hotline_number = $request->hotline_number;
        $company->email = $request->email;
        $company->website = $request->website;
        $company->address_line_1 = $request->address_line_1;
        $company->address_line_2 = $request->address_line_2;
        $company->address_line_3 = $request->address_line_3;
        $company->reg_number = $request->reg_number;
        $company->svat = $request->svat;
        $company->vat = $request->vat;
        $company->company_image = $request->image;
        
        if($company->save()) {   
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/company_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $company->id. ',' . str_replace(',',' ',$company->company_name). ',' . str_replace(',',' ',$company->phone_number). ',' . str_replace(',',' ',$company->hotline_number). ',' . str_replace(',',' ',$company->email). ',' . str_replace(',',' ',$company->website). ',' . str_replace(',',' ',$company->address_line_1). ',' . str_replace(',',' ',$company->address_line_2). ',' . str_replace(',',' ',$company->address_line_3). ',' . str_replace(',',' ',$company->reg_number). ',' . str_replace(',',' ',$company->svat). ',' . str_replace(',',' ',$company->vat). ',' . str_replace(',',' ',$company->company_image). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Company updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Company updation failed'
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
        //
    }
}
