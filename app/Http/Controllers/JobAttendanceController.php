<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class JobAttendanceController extends Controller
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
        
        return view('job.job_attendance', $data);
    }

    public function job_attendance_list(Request $request)
    {
        $data = $technical_team_id = array();
        $job_attendances = \App\Model\JobAttendance::whereBetween('attended_date', array($request->from, $request->to))
                ->where('is_delete', 0)
                ->get();
        foreach ($job_attendances as $main_job_attendance){
            if(!in_array($main_job_attendance->technical_team_id, $technical_team_id)){
                $total_mandays = 0;
                foreach ($job_attendances as $sub_job_attendance){
                    if($main_job_attendance->technical_team_id == $sub_job_attendance->technical_team_id){
                        $total_mandays += $sub_job_attendance->mandays;
                    }
                }
                $row = array(
                    'technical_team' => $main_job_attendance->TechnicalTeam ? $main_job_attendance->TechnicalTeam->name : '',
                    'mandays' => $total_mandays
                );
                array_push($data, $row);
                array_push($technical_team_id, $main_job_attendance->technical_team_id);
            }
        }
        
        return response($data);
    }

    public function add_new()
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
        
        return view('job.job_attendance_details', $data);
    }

    public function find_job_attendance_detail(Request $request)
    {
        $job_attendance = \App\Model\JobAttendance::select('id', 'attended_date', 'technical_team_id', 'job_type_id', 'job_id', 'mandays')
                ->with(array('JobType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('TechnicalTeam' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('Job' => function($query) {
                    $query->select('id', 'inquiry_id', 'job_no')
                        ->with(array('Inquiry' => function($query) {
                            $query->select('id', 'contact_id')
                                    ->with(array('Contact' => function($query) {
                                        $query->select('id', 'name');
                                    }));
                        }));
                }))
                ->with(array('TechResponse' => function($query) {
                    $query->select('id', 'contact_id', 'tech_response_no')
                            ->with(array('Contact' => function($query) {
                                $query->select('id', 'name');
                            }));
                }))
                ->find($request->id);
        
        return response($job_attendance);
    }

    public function validate_job_no(Request $request)
    {
        $exist = false;
        if($request->job_type == 1){            
            $job = \App\Model\Job::where('job_no', $request->job_no)
                    ->where('is_delete', 0)
                    ->first();
            $exist = $job ? true : false;
        } else if($request->job_type == 2){            
            $tech_response = \App\Model\TechResponse::where('tech_response_no', $request->job_no)
                    ->where('is_delete', 0)
                    ->first();
            $exist = $tech_response ? true : false;
        }
        
        if($exist){
            $response = 'true';
        } else{
            $response = 'false';
        }
            
        echo $response;
    }

    public function job_attendance_detail_list(Request $request)
    {
        $job_attendances = \App\Model\JobAttendance::select('id', 'attended_date', 'technical_team_id', 'job_type_id', 'job_id', 'mandays')
                ->with(array('JobType' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('TechnicalTeam' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('Job' => function($query) {
                    $query->select('id', 'inquiry_id', 'job_no')
                        ->with(array('Inquiry' => function($query) {
                            $query->select('id', 'contact_id')
                                    ->with(array('Contact' => function($query) {
                                        $query->select('id', 'name');
                                    }));
                        }));
                }))
                ->with(array('TechResponse' => function($query) {
                    $query->select('id', 'contact_id', 'tech_response_no')
                            ->with(array('Contact' => function($query) {
                                $query->select('id', 'name');
                            }));
                }))
                ->whereBetween('attended_date', array($request->from, $request->to))
                ->where('technical_team_id', $request->technical_team_id)
                ->where('is_delete', 0)
                ->get();
                
        $data = array(
            'job_attendances' => $job_attendances,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function print_work_sheet()
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $html = view('job.work_sheet_pdf', $data);
        
        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="Installation Work Sheet.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Landscape',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }

    public function print_tech_work_sheet()
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $html = view('job.tech_work_sheet_pdf', $data);
        
        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="Tech Response Work Sheet.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Landscape',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8
        ];
        echo $snappy->getOutputFromHtml($html, $options);
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
        $job_attendance = new \App\Model\JobAttendance();
        $job_attendance->attended_date = date('Y-m-d', strtotime($request->attended_date));
        $job_attendance->technical_team_id = isset($request->technical_team['id']) ? $request->technical_team['id'] : 0;
        $job_attendance->job_type_id = isset($request->job_type['id']) ? $request->job_type['id'] : 0;
        $job_attendance->job_id = isset($request->job_no['id']) ? $request->job_no['id'] : 0;
        $job_attendance->mandays = $request->mandays;
        
        if(isset($request->technical_team['id']) && isset($request->job_type['id']) && isset($request->job_no['id']) && $job_attendance->save()){    
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_attendance_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $job_attendance->id. ',' . $job_attendance->attended_date. ',' . $job_attendance->technical_team_id. ',' . $job_attendance->job_type_id. ',' . $job_attendance->job_id. ',' . $job_attendance->mandays. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Job Attendance Detail created successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Job Attendance Detail creation failed'
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
        $job_attendance = \App\Model\JobAttendance::find($id);
        $job_attendance->attended_date = date('Y-m-d', strtotime($request->attended_date));
        $job_attendance->technical_team_id = isset($request->technical_team['id']) ? $request->technical_team['id'] : 0;
        $job_attendance->job_type_id = isset($request->job_type['id']) ? $request->job_type['id'] : 0;
        $job_attendance->job_id = isset($request->job_no['id']) ? $request->job_no['id'] : 0;
        $job_attendance->mandays = $request->mandays;
        
        if(isset($request->technical_team['id']) && isset($request->job_type['id']) && isset($request->job_no['id']) && $job_attendance->save()){    
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_attendance_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $job_attendance->id. ',' . $job_attendance->attended_date. ',' . $job_attendance->technical_team_id. ',' . $job_attendance->job_type_id. ',' . $job_attendance->job_id. ',' . $job_attendance->mandays. ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Job Attendance Detail updated successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Job Attendance Detail updation failed'
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
        $job_attendance = \App\Model\JobAttendance::find($id);
        $job_attendance->is_delete = 1;
        
        if($job_attendance->save()){ 
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_attendance_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $job_attendance->id. ',,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $result = array(
                'response' => true,
                'message' => 'Job Attendance Detail deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Job Attendance Detail deletion failed'
            );
        }

        echo json_encode($result);
    }
}