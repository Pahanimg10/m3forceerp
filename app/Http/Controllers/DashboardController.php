<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('user_access');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function change_password()
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

        return view('admin.change_password', $data);
    }

    public function job_positions_list()
    {
        $job_positions = \App\Model\JobPosition::select('id', 'name')
                ->where('is_delete', 0)
                ->orderBy('name')
                ->get();

        return response($job_positions);
    }

    public function validate_username(Request $request)
    {
        if ($request->old_value != $request->username) {
            $user = \App\Model\User::where('username', $request->username)
                    ->where('is_delete', 0)
                    ->first();
            if ($user) {
                $result = 'false';
            } else {
                $result = 'true';
            }
        } else {
            $result = 'true';
        }

        echo $result;
    }

    public function validate_old_password(Request $request)
    {
        $user = \App\Model\User::find(session()->get('users_id'));
        if ($user->password == md5(sha1($request->old_password))) {
            $result = 'true';
        } else {
            $result = 'false';
        }

        return $result;
    }

    public function update_new_password(Request $request)
    {
        $user = \App\Model\User::find(session()->get('users_id'));
        $user->password = md5(sha1($request->new_password));

        if ($user->save()) {
            $result = [
                'response' => true,
                'message' => 'Password updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Password updation failed',
            ];
        }

        echo json_encode($result);
    }

    public function find_user(Request $request)
    {
        $users = \App\Model\User::select('id', 'job_position_id', 'first_name', 'last_name', 'contact_no', 'email', 'username', 'user_image')
                ->with(['JobPosition' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['UserGroupPermission' => function ($query) {
                    $query->select('id', 'user_id', 'user_group_id');
                }])
                ->find($request->id);

        return response($users);
    }

    public function image_upload()
    {
        if (! empty($_FILES['image'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = time().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/users/'.$image);

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/user_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Image Uploaded,,,,,,,assets/images/users/'.str_replace(',', ' ', $image).',,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'success',
                'image' => $image,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Image Is Empty',
            ];
        }

        echo json_encode($result);
    }

    public function update_user_profile(Request $request)
    {
        $user = \App\Model\User::find($request->user_id);

        if ($user) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->contact_no = $request->contact_no;
            $user->email = $request->email;
            $user->job_position_id = $request->job_position['id'];
            $user->user_image = $request->image;
            $user->username = $request->username;
            $user->password = md5(sha1($request->new_password));
            $user->save();

            $permissions = \App\Model\User::find($user->id)->UserGroupPermission;
            $group_ids = [];
            foreach ($permissions as $permission) {
                array_push($group_ids, $permission->user_group_id);
            }

            session()->flush();
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

            $result = [
                'response' => true,
                'message' => 'Profile updated successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Profile updation failed',
            ];
        }

        echo json_encode($result);
    }

    public function user_profile()
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

        return view('admin.user_profile', $data);
    }

    public function get_bar_data(Request $request)
    {
        $bar_data = $ykeys = $labels = $barColors = [];
        $sales_teams = \App\Model\SalesTeam::where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        $start = (new \DateTime($request->from))->modify('first day of this month');
        $end = (new \DateTime($request->to))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $row = [];
            $row['month'] = $dt->format('Y M');
            foreach ($sales_teams as $sales_team) {
                $jobs = \App\Model\Job::whereHas('Inquiry', function ($query) use ($sales_team) {
                    $query->where('sales_team_id', $sales_team->id);
                })
                            ->whereBetween('job_date_time', [$dt->format('Y-m-01').' 00:01', $dt->format('Y-m-t').' 23:59'])
                            ->where('is_delete', 0)
                            ->get();
                $quoted_price = 0;
                $quotation_ids = [];
                foreach ($jobs as $job) {
                    $quotation = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                            ->where('is_confirmed', 1)
                            ->where('is_revised', 0)
                            ->where('is_delete', 0)
                            ->orderBy('quotation_date_time')
                            ->first();
                    if ($quotation) {
                        if (! in_array($quotation->id, $quotation_ids)) {
                            array_push($quotation_ids, $quotation->id);

                            $job_card_ids = [];
                            foreach ($quotation->QuotationJobCard as $detail) {
                                array_push($job_card_ids, $detail['id']);
                            }
                            $cost_sheet_ids = [];
                            foreach ($quotation->QuotationCostSheet as $detail) {
                                array_push($cost_sheet_ids, $detail['id']);
                            }

                            $usd = false;
                            $usd_rate = 0;
                            if ($quotation->is_currency == 0) {
                                $usd = true;
                                $usd_rate = $quotation->usd_rate;
                            }

                            $main_value = $quotation_value = 0;
                            $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                    ->where('is_delete', 0)
                                    ->get();
                            foreach ($job_card_details as $job_card_detail) {
                                $margin = ($job_card_detail->margin + 100) / 100;
                                $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) * $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                                if ($job_card_detail->is_main == 1) {
                                    $main_value += $value;
                                } else {
                                    $quotation_value += $value;
                                }
                            }

                            foreach ($quotation->QuotationDiscount as $detail) {
                                if ($detail['discount_type_id'] == 1) {
                                    $main_value = $main_value * (100 - $detail['percentage']) / 100;
                                }
                            }
                            $quotation_value += $main_value;

                            $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                    ->where('is_delete', 0)
                                    ->get();
                            $rate_ids = [];
                            foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                                if ($main_cost_sheet_detail->InstallationRate && ! in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                                    $meters = 0;
                                    foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                                        if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                            $meters += $sub_cost_sheet_detail->meters;
                                        }
                                    }

                                    $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate * $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                                    $quotation_value += $installation_rate * $meters;

                                    array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                                }
                            }

                            $manday_rate = \App\Model\Rate::find(1);
                            foreach ($cost_sheet_details as $cost_sheet_detail) {
                                $quotation_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                                $quotation_value += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                                $quotation_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                                $quotation_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                                $quotation_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                                $quotation_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;
                            }

                            foreach ($quotation->QuotationDiscount as $detail) {
                                if ($detail['discount_type_id'] == 2) {
                                    $quotation_value = $quotation_value * (100 - $detail['percentage']) / 100;
                                }
                            }

                            $quoted_price += $quotation_value;
                        }
                    }
                }

                $row[$sales_team->id] = number_format((float) $quoted_price, 2, '.', '');
            }

            array_push($bar_data, $row);
        }

        $sales_team_colors = ['#5780cd', '#a1ee33', '#f68a8e', '#b58096', '#f6f289', '#f0c060', '#637b80', '#5780cd', '#a1ee33', '#f68a8e', '#b58096', '#f6f289', '#f0c060', '#637b80'];

        foreach ($sales_teams as $index => $value) {
            array_push($ykeys, $value->id);
            array_push($labels, $value->name);
            array_push($barColors, $sales_team_colors[$index]);
        }

        $data = [
            'bar_data' => $bar_data,
            'ykeys' => $ykeys,
            'labels' => $labels,
            'barColors' => $barColors,
        ];

        return response($data);
    }

    public function get_donut_data(Request $request)
    {
        $donut_data = [];
        $target = $achieved = 0;
        $sales_team = \App\Model\SalesTeam::find($request->sales_team_id);
        $start = (new \DateTime($request->from))->modify('first day of this month');
        $end = (new \DateTime($request->to))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $jobs = \App\Model\Job::whereHas('Inquiry', function ($query) use ($sales_team) {
                $query->where(function ($q) use ($sales_team) {
                    $sales_team ? $q->where('sales_team_id', $sales_team->id) : '';
                });
            })
                        ->whereBetween('job_date_time', [$dt->format('Y-m-01').' 00:01', $dt->format('Y-m-t').' 23:59'])
                        ->where('is_delete', 0)
                        ->get();
            $quotation_ids = [];
            foreach ($jobs as $job) {
                $quotation = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                        ->where('is_confirmed', 1)
                        ->where('is_revised', 0)
                        ->where('is_delete', 0)
                        ->orderBy('quotation_date_time')
                        ->first();
                if ($quotation) {
                    if (! in_array($quotation->id, $quotation_ids)) {
                        array_push($quotation_ids, $quotation->id);

                        $job_card_ids = [];
                        foreach ($quotation->QuotationJobCard as $detail) {
                            array_push($job_card_ids, $detail['id']);
                        }
                        $cost_sheet_ids = [];
                        foreach ($quotation->QuotationCostSheet as $detail) {
                            array_push($cost_sheet_ids, $detail['id']);
                        }

                        $usd = false;
                        $usd_rate = 0;
                        if ($quotation->is_currency == 0) {
                            $usd = true;
                            $usd_rate = $quotation->usd_rate;
                        }

                        $main_value = $quotation_value = 0;
                        $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                ->where('is_delete', 0)
                                ->get();
                        foreach ($job_card_details as $job_card_detail) {
                            $margin = ($job_card_detail->margin + 100) / 100;
                            $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) * $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                            if ($job_card_detail->is_main == 1) {
                                $main_value += $value;
                            } else {
                                $quotation_value += $value;
                            }
                        }

                        foreach ($quotation->QuotationDiscount as $detail) {
                            if ($detail['discount_type_id'] == 1) {
                                $main_value = $main_value * (100 - $detail['percentage']) / 100;
                            }
                        }
                        $quotation_value += $main_value;

                        $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                ->where('is_delete', 0)
                                ->get();
                        $rate_ids = [];
                        foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                            if ($main_cost_sheet_detail->InstallationRate && ! in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                                $meters = 0;
                                foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                                    if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                        $meters += $sub_cost_sheet_detail->meters;
                                    }
                                }

                                $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate * $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                                $quotation_value += $installation_rate * $meters;

                                array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                            }
                        }

                        $manday_rate = \App\Model\Rate::find(1);
                        foreach ($cost_sheet_details as $cost_sheet_detail) {
                            $quotation_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                            $quotation_value += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                            $quotation_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                            $quotation_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                            $quotation_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                            $quotation_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;
                        }

                        foreach ($quotation->QuotationDiscount as $detail) {
                            if ($detail['discount_type_id'] == 2) {
                                $quotation_value = $quotation_value * (100 - $detail['percentage']) / 100;
                            }
                        }

                        $achieved += $quotation_value;
                    }
                }
            }

            $target += $sales_team ? $sales_team->sales_target : 0;
        }

        $pending = ($target - $achieved) > 0 ? ($target - $achieved) : 0;
        $row = [
            'label' => 'PENDING',
            'value' => number_format((float) $pending, 2, '.', ''),
        ];
        array_push($donut_data, $row);

        $row = [
            'label' => 'ACHIEVED',
            'value' => number_format((float) $achieved, 2, '.', ''),
        ];
        array_push($donut_data, $row);

        return response($donut_data);
    }

    public function get_sales_target_data(Request $request)
    {
        $sales_team = \App\Model\SalesTeam::find($request->sales_team_id);
        $start = (new \DateTime($request->from))->modify('first day of this month');
        $end = (new \DateTime($request->to))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);

        $target = $achieved = 0;
        foreach ($period as $dt) {
            $target += $sales_team ? $sales_team->sales_target : 0;
        }

        $view = '
                <table id="data_table" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;">
                    <tr>
                        <th style="text-align: right; vertical-align: middle; width: 80%;">Target</th>
                        <th style="text-align: right; vertical-align: middle; width: 20%;">'.number_format($target, 2).'</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="height: 10px; vertical-align: middle;"></th>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align: center; vertical-align: middle;">Sales</th>
                    </tr>
            ';
        foreach ($period as $dt) {
            $jobs = \App\Model\Job::whereHas('Inquiry', function ($query) use ($sales_team) {
                $query->where(function ($q) use ($sales_team) {
                    $sales_team ? $q->where('sales_team_id', $sales_team->id) : '';
                });
            })
                        ->whereBetween('job_date_time', [$dt->format('Y-m-01').' 00:01', $dt->format('Y-m-t').' 23:59'])
                        ->where('is_delete', 0)
                        ->get();
            $quotation_ids = [];
            foreach ($jobs as $job) {
                $quotation = \App\Model\Quotation::where('inquiry_id', $job->inquiry_id)
                        ->where('is_confirmed', 1)
                        ->where('is_revised', 0)
                        ->where('is_delete', 0)
                        ->orderBy('quotation_date_time')
                        ->first();
                if ($quotation) {
                    if (! in_array($quotation->id, $quotation_ids)) {
                        array_push($quotation_ids, $quotation->id);

                        $job_card_ids = [];
                        foreach ($quotation->QuotationJobCard as $detail) {
                            array_push($job_card_ids, $detail['id']);
                        }
                        $cost_sheet_ids = [];
                        foreach ($quotation->QuotationCostSheet as $detail) {
                            array_push($cost_sheet_ids, $detail['id']);
                        }

                        $usd = false;
                        $usd_rate = 0;
                        if ($quotation->is_currency == 0) {
                            $usd = true;
                            $usd_rate = $quotation->usd_rate;
                        }

                        $main_value = $quotation_value = 0;
                        $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                                ->where('is_delete', 0)
                                ->get();
                        foreach ($job_card_details as $job_card_detail) {
                            $margin = ($job_card_detail->margin + 100) / 100;
                            $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity) * $usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
                            if ($job_card_detail->is_main == 1) {
                                $main_value += $value;
                            } else {
                                $quotation_value += $value;
                            }
                        }

                        foreach ($quotation->QuotationDiscount as $detail) {
                            if ($detail['discount_type_id'] == 1) {
                                $main_value = $main_value * (100 - $detail['percentage']) / 100;
                            }
                        }
                        $quotation_value += $main_value;

                        $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                                ->where('is_delete', 0)
                                ->get();
                        $rate_ids = [];
                        foreach ($cost_sheet_details as $main_cost_sheet_detail) {
                            if ($main_cost_sheet_detail->InstallationRate && ! in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)) {
                                $meters = 0;
                                foreach ($cost_sheet_details as $sub_cost_sheet_detail) {
                                    if ($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id) {
                                        $meters += $sub_cost_sheet_detail->meters;
                                    }
                                }

                                $installation_rate = $usd ? $main_cost_sheet_detail->InstallationRate->rate * $usd_rate : $main_cost_sheet_detail->InstallationRate->rate;
                                $quotation_value += $installation_rate * $meters;

                                array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
                            }
                        }

                        $manday_rate = \App\Model\Rate::find(1);
                        foreach ($cost_sheet_details as $cost_sheet_detail) {
                            $quotation_value += $usd ? $cost_sheet_detail->excavation_work * $usd_rate : $cost_sheet_detail->excavation_work;
                            $quotation_value += $usd ? ($cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value)) * $usd_rate : $cost_sheet_detail->transport + ($cost_sheet_detail->traveling_mandays * $manday_rate->value);
                            $quotation_value += $usd ? $cost_sheet_detail->food * $usd_rate : $cost_sheet_detail->food;
                            $quotation_value += $usd ? $cost_sheet_detail->accommodation * $usd_rate : $cost_sheet_detail->accommodation;
                            $quotation_value += $usd ? $cost_sheet_detail->bata * $usd_rate : $cost_sheet_detail->bata;
                            $quotation_value += $usd ? $cost_sheet_detail->other_expenses * $usd_rate : $cost_sheet_detail->other_expenses;
                        }

                        foreach ($quotation->QuotationDiscount as $detail) {
                            if ($detail['discount_type_id'] == 2) {
                                $quotation_value = $quotation_value * (100 - $detail['percentage']) / 100;
                            }
                        }

                        $achieved += $quotation_value;

                        $view .= '
                                <tr>
                                    <td style="text-align: right; vertical-align: middle; width: 80%;">'.$quotation->Inquiry->Contact->name.' | '.$quotation->Inquiry->Contact->address.' | '.$quotation->Inquiry->IInquiryType->name.'</td>
                                    <td style="text-align: right; vertical-align: middle; width: 20%;">'.number_format($quotation_value, 2).'</td>
                                </tr>
                            ';
                    }
                }
            }
        }
        $pending = ($target - $achieved) > 0 ? ($target - $achieved) : 0;
        $view .= '
                    <tr>
                        <th style="text-align: right; vertical-align: middle; width: 80%;">Total</th>
                        <th style="text-align: right; vertical-align: middle; width: 20%;">'.number_format($achieved, 2).'</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="height: 10px; vertical-align: middle;"></th>
                    </tr>
                    <tr>
                        <th style="text-align: right; vertical-align: middle; width: 80%;">Pending</th>
                        <th style="text-align: right; vertical-align: middle; width: 20%; border-top: 1px double black; border-bottom: 3px double black;">'.number_format($pending, 2).'</th>
                    </tr>
                </table>
            ';

        $result = [
            'view' => $view,
        ];

        echo json_encode($result);
    }

    public function get_line_data(Request $request)
    {
        $line_data = [];
        $start = (new \DateTime($request->from))->modify('first day of this month');
        $end = (new \DateTime($request->to))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $collection = 0;
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::whereBetween('receipt_date_time', [$dt->format('Y-m-01').' 00:01', $dt->format('Y-m-t').' 23:59'])
                        ->where('is_delete', 0)
                        ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment) {
                $collection += $job_done_customer_payment->amount;
            }
            $monitoring_customer_payments = \App\Model\MonitoringCustomerPayment::whereBetween('receipt_date_time', [$dt->format('Y-m-01').' 00:01', $dt->format('Y-m-t').' 23:59'])
                        ->where('is_delete', 0)
                        ->get();
            foreach ($monitoring_customer_payments as $monitoring_customer_payment) {
                $collection += $monitoring_customer_payment->amount;
            }
            $tech_response_customer_payments = \App\Model\TechResponseCustomerPayment::whereBetween('receipt_date_time', [$dt->format('Y-m-01').' 00:01', $dt->format('Y-m-t').' 23:59'])
                        ->where('is_delete', 0)
                        ->get();
            foreach ($tech_response_customer_payments as $tech_response_customer_payment) {
                $collection += $tech_response_customer_payment->amount;
            }

            $row = [
                'month' => $dt->format('Y-m'),
                'collection' => number_format((float) $collection, 2, '.', ''),
            ];

            array_push($line_data, $row);
        }

        return response($line_data);
    }
}
