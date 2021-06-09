<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use App\Model\Gmaterailgrns;
use App\Model\Gwipgrns;
use App\Model\Productions;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkUsername(Request $request)
    {
        $user = \App\Model\User::where('username', $request->username)
            ->where('is_delete', 0)
            ->with('JobPosition')
            ->first();

        return response($user);
    }

    public function validateUsername(Request $request)
    {
        $user = \App\Model\User::where('username', $request->username)
            ->where('is_delete', 0)
            ->first();
        if ($user) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function validateLogin(Request $request)
    {
        $user = \App\Model\User::where('username', $request->username)
            ->where('password', md5(sha1($request->password)))
            ->where('is_delete', 0)
            ->first();
        if ($user) {
            $permissions = \App\Model\User::find($user->id)->UserGroupPermission;
            $group_ids = [];
            foreach ($permissions as $permission) {
                array_push($group_ids, $permission->user_group_id);
            }

            $user_data = [
                'users_id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'position' => $user->JobPosition->name,
                'user_image' => $user->user_image,
                'username' => $user->username,
                'user_group' => $group_ids,
                'LoggedIn' => true,
            ];
            foreach ($user_data as $key => $value) {
                session()->put($key, $value);
            }

            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function home(Request $request)
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

        if ($request->type && $request->type == 1) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/main_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Log In,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.session()->get('username').PHP_EOL);
            fclose($myfile);
        }

        $inquiry_not_attended = $inquiry_pending = $business_pending = $job_new = $job_ongoing = 0;
        $inquiries = \App\Model\Inquiry::where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($inquiries as $inquiry) {
            if ($inquiry->is_first_call_done == 0) {
                $inquiry_not_attended++;
            } else {
                $inquiry_status = \App\Model\InquiryDetials::selectRaw('MAX(inquiry_status_id) AS inquiry_status_id')
                    ->where('inquiry_id', $inquiry->id)
                    ->where('is_delete', 0)
                    ->first();
                if ($inquiry_status && $inquiry_status->inquiry_status_id) {
                    $business_pending += $inquiry_status->inquiry_status_id == 14 ? 1 : 0;
                    $inquiry_pending += $inquiry_status->inquiry_status_id != 17 || $inquiry_status->inquiry_status_id != 18 ? 1 : 0;
                }
            }
        }

        $jobs = \App\Model\Job::where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        foreach ($jobs as $job) {
            if ($job->is_job_scheduled == 0) {
                $job_new++;
            } else {
                $job_ongoing++;
            }
        }

        $data['inquiry_not_attended'] = $inquiry_not_attended;
        $data['inquiry_pending'] = $inquiry_pending;
        $data['business_pending'] = $business_pending;
        $data['job_new'] = $job_new;
        $data['job_ongoing'] = $job_ongoing;

        $tech_responses = \App\Model\TechResponse::where('is_completed', 0)
            ->where('is_delete', 0)
            ->get();
        $tech_response_not_attended = $tech_response_ongoing = 0;
        foreach ($tech_responses as $tech_response) {
            $tech_response_status = \App\Model\TechResponseDetails::selectRaw('MAX(tech_response_status_id) AS tech_response_status_id')
                ->where('tech_response_id', $tech_response->id)
                ->where('is_delete', 0)
                ->first();
            if ($tech_response_status && $tech_response_status->tech_response_status_id) {
                $tech_response_not_attended += $tech_response_status->tech_response_status_id == 1 ? 1 : 0;
                $tech_response_ongoing += $tech_response_status->tech_response_status_id != 1 && $tech_response_status->tech_response_status_id < 12 ? 1 : 0;
            }
        }
        $data['tech_response_not_attended'] = $tech_response_not_attended;
        $data['tech_response_ongoing'] = $tech_response_ongoing;

        $job_done_customers = \App\Model\JobDoneCustomer::where('is_delete', 0)->get();
        $job_done_customer_pending_amount = 0;
        foreach ($job_done_customers as $job_done_customer) {
            $job_done_customer_pending_amount += $job_done_customer->pending_amount;
        }
        $monitoring_customers = \App\Model\MonitoringCustomer::where('is_delete', 0)->get();
        $monitoring_customer_pending_amount = 0;
        foreach ($monitoring_customers as $monitoring_customer) {
            $monitoring_customer_pending_amount += $monitoring_customer->pending_amount;
        }
        $tech_response_customers = \App\Model\TechResponseCustomer::where('is_delete', 0)->get();
        $tech_response_customer_pending_amount = 0;
        foreach ($tech_response_customers as $tech_response_customer) {
            $tech_response_customer_pending_amount += $tech_response_customer->pending_amount;
        }
        $credit_suppliers = \App\Model\CreditSupplier::where('is_delete', 0)->get();
        $credit_supplier_pending_amount = 0;
        foreach ($credit_suppliers as $credit_supplier) {
            $credit_supplier_pending_amount += $credit_supplier->pending_amount;
        }
        $data['job_done_customer'] = $job_done_customer_pending_amount;
        $data['monitoring_customer'] = $monitoring_customer_pending_amount;
        $data['tech_response_customer'] = $tech_response_customer_pending_amount;
        $data['credit_suppliers'] = $credit_supplier_pending_amount;

        return view('home', $data);
    }

    public function logout(Request $request)
    {
        $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/main_controller.csv', 'a+') or die('Unable to open/create file!');
        fwrite($myfile, 'Log Out,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.session()->get('username').PHP_EOL);
        fclose($myfile);

        session()->flush();

        return redirect('/');
    }
}
