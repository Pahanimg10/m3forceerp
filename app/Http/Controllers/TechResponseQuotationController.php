<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TechResponseQuotationController extends Controller
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
    public function add_new(Request $request)
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

        $data['tech_response_quotation_id'] = $request->id;
        $data['tech_response_id'] = $request->tech_response_id;
        $data['view'] = $request->view;

        return view('tech_response.tech_response_quotation_detail', $data);
    }

    public function get_data(Request $request)
    {
        $tech_response_job_cards = \App\Model\TechResponseJobCard::select('id', 'tech_response_job_card_no', 'tech_response_job_card_value')
            ->where('tech_response_id', $request->id)
            ->where('is_used', 0)
            ->where('is_delete', 0)
            ->orderBy('tech_response_job_card_no')
            ->get();
        $discount_types = \App\Model\DiscountType::all();

        $data = [
            'tech_response_job_cards' => $tech_response_job_cards,
            'discount_types' => $discount_types,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function tech_response_quotation_list(Request $request)
    {
        $tech_response_quotations = \App\Model\TechResponseQuotation::select('id', 'tech_response_id', 'tech_response_quotation_no', 'tech_response_quotation_date_time', 'remarks', 'tech_response_quotation_value', 'is_confirmed', 'user_id')
            ->with(['User' => function ($query) {
                $query->select('id', 'first_name');
            }])
            ->where('tech_response_id', $request->id)
            ->where('is_delete', 0)
            ->get();
        $tech_response = \App\Model\TechResponse::select('id', 'tech_response_no')->find($request->id);

        $data = [
            'tech_response_quotations' => $tech_response_quotations,
            'tech_response' => $tech_response,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function find_tech_response_quotation(Request $request)
    {
        $tech_response_quotation = \App\Model\TechResponseQuotation::select('id', 'tech_response_id', 'tech_response_quotation_no', 'tech_response_quotation_date_time', 'remarks', 'special_notes', 'show_installation_charge', 'installation_charge_text', 'show_brand', 'show_origin', 'show_transport', 'is_currency', 'usd_rate', 'installation_charge', 'transport_charge', 'attendance_fee', 'tech_response_quotation_value', 'is_confirmed')
            ->with(['TechResponseQuotationDiscount' => function ($query) {
                $query->select('id', 'tech_response_quotation_id', 'discount_type_id', 'description', 'percentage')
                    ->with(['DiscountType' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->with(['TechResponseQuotationJobCard' => function ($query) {
                $query->select('id', 'tech_response_quotation_id', 'tech_response_job_card_id')
                    ->with(['TechResponseJobCard' => function ($query) {
                        $query->select('id', 'tech_response_job_card_no', 'tech_response_job_card_value');
                    }]);
            }])
            ->find($request->id);

        return response($tech_response_quotation);
    }

    public function discount_detail(Request $request)
    {
        $discounts = \App\Model\TechResponseQuotationDiscount::select('id', 'tech_response_quotation_id', 'discount_type_id', 'description', 'percentage')
            ->with(['DiscountType' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('tech_response_quotation_id', $request->id)
            ->where('is_delete', 0)
            ->get();
        $tech_response_quotation = \App\Model\TechResponseQuotation::select('id', 'is_confirmed')->find($request->id);

        $data = [
            'discounts' => $discounts,
            'tech_response_quotation' => $tech_response_quotation,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

        return response($data);
    }

    public function confirm_tech_response_quotation(Request $request)
    {
        $tech_response_quotation = \App\Model\TechResponseQuotation::find($request->id);
        $tech_response_quotation->is_confirmed = 1;

        if ($tech_response_quotation->save()) {
            $tech_response_status = new \App\Model\TechResponseDetails();
            $tech_response_status->tech_response_id = $tech_response_quotation->tech_response_id;
            $tech_response_status->update_date_time = date('Y-m-d H:i');
            $tech_response_status->tech_response_status_id = 6;
            $tech_response_status->job_scheduled_date_time = '';
            $tech_response_status->is_chargeable = 0;
            $tech_response_status->remarks = $tech_response_quotation->tech_response_quotation_no;
            $tech_response_status->user_id = $request->session()->get('users_id');
            $tech_response_status->save();

            $tech_response_quotation_job_cards = \App\Model\TechResponseQuotationJobCard::where('tech_response_quotation_id', $tech_response_quotation->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($tech_response_quotation_job_cards as $tech_response_quotation_job_card) {
                $tech_response_job_card = \App\Model\TechResponseJobCard::find($tech_response_quotation_job_card->tech_response_job_card_id);
                $tech_response_job_card->is_used = 1;
                $tech_response_job_card->is_posted = 1;
                $tech_response_job_card->is_approved = 1;
                $tech_response_job_card->save();
            }

            $tech_response_value = 0;
            $tech_response_quotations = \App\Model\TechResponseQuotation::where('tech_response_id', $tech_response_quotation->tech_response_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($tech_response_quotations as $tech_response_quotation) {
                $tech_response_value += $tech_response_quotation->tech_response_quotation_value;
            }

            $tech_response = \App\Model\TechResponse::find($tech_response_quotation->tech_response_id);
            if ($tech_response) {
                $tech_response->tech_response_value = $tech_response_value;
                $tech_response->save();
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_quotation_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Confirmed,'.$tech_response_quotation->id.',,,,,,,,,,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $tech_response = \App\Model\TechResponse::find($tech_response_quotation->tech_response_id);
            $data = [
                'id' => $tech_response->id,
                'type' => 2,
                'customer_name' => $tech_response->Contact->name,
                'customer_address' => $tech_response->Contact->address,
            ];

            Mail::send('emails.installation_update_notification', $data, function ($message) {
                $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                $message->to('stores@m3force.com', 'Nalin Silva');
                $message->to('procurement@m3force.com', 'Deepal Gunasekera');
                $message->subject('M3Force Customer Installation Update Details');
            });

            $result = [
                'response' => true,
                'message' => 'Tech Response Quotation confirmed successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Tech Response Quotation confirm failed',
            ];
        }

        echo json_encode($result);
    }

    public function revise_tech_response_quotation(Request $request)
    {
        $tech_response_quotation = \App\Model\TechResponseQuotation::find($request->id);
        $tech_response_quotation->is_confirmed = 0;

        if ($tech_response_quotation->save()) {
            $tech_response_status = new \App\Model\TechResponseDetails();
            $tech_response_status->tech_response_id = $tech_response_quotation->tech_response_id;
            $tech_response_status->update_date_time = date('Y-m-d H:i');
            $tech_response_status->tech_response_status_id = 5;
            $tech_response_status->job_scheduled_date_time = '';
            $tech_response_status->is_chargeable = 0;
            $tech_response_status->remarks = $tech_response_quotation->tech_response_quotation_no;
            $tech_response_status->user_id = $request->session()->get('users_id');
            $tech_response_status->save();

            $tech_response_quotation_job_cards = \App\Model\TechResponseQuotationJobCard::where('tech_response_quotation_id', $tech_response_quotation->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($tech_response_quotation_job_cards as $tech_response_quotation_job_card) {
                $tech_response_job_card = \App\Model\TechResponseJobCard::find($tech_response_quotation_job_card->tech_response_job_card_id);
                $tech_response_job_card->is_used = 0;
                $tech_response_job_card->is_posted = 0;
                $tech_response_job_card->is_approved = 0;
                $tech_response_job_card->save();
            }

            $tech_response_value = 0;
            $tech_response_quotations = \App\Model\TechResponseQuotation::where('tech_response_id', $tech_response_quotation->tech_response_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($tech_response_quotations as $tech_response_quotation) {
                $tech_response_value += $tech_response_quotation->tech_response_quotation_value;
            }

            $tech_response = \App\Model\TechResponse::find($tech_response_quotation->tech_response_id);
            if ($tech_response) {
                $tech_response->tech_response_value = $tech_response_value;
                $tech_response->save();
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_quotation_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Revised,'.$tech_response_quotation->id.',,,,,,,,,,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'Tech Response Quotation revised successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Tech Response Quotation revise failed',
            ];
        }

        echo json_encode($result);
    }

    public function preview_tech_response_quotation(Request $request)
    {
        $tech_response_job_card_ids = [];
        foreach ($request->data['tech_response_job_cards'] as $detail) {
            if ($detail['selected']) {
                array_push($tech_response_job_card_ids, $detail['id']);
            }
        }

        $usd = false;
        $usd_rate = 0;
        $currency = 'LKR';
        if (! $request->data['is_currency']) {
            $usd = true;
            $usd_rate = $request->data['usd_rate'];
            $currency = 'USD';
        }

        $main_items = $sub_items = [];
        $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::whereIn('tech_response_job_card_id', $tech_response_job_card_ids)
            ->where('is_delete', 0)
            ->get();
        foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {
            if ($tech_response_job_card_detail->is_chargeable == 1) {
                $margin = ($tech_response_job_card_detail->margin + 100) / 100;
                $value = $usd ? ($tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity) / $usd_rate : $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
                $row = [
                    'description' => $tech_response_job_card_detail->Item->name,
                    'model_no' => $tech_response_job_card_detail->Item->model_no,
                    'brand' => $tech_response_job_card_detail->Item->brand,
                    'origin' => $tech_response_job_card_detail->Item->origin,
                    'unit_type' => $tech_response_job_card_detail->Item->UnitType->code,
                    'rate' => $value / $tech_response_job_card_detail->quantity,
                    'quantity' => $tech_response_job_card_detail->quantity,
                    'value' => $value,
                ];
                if ($tech_response_job_card_detail->is_main == 1) {
                    array_push($main_items, $row);
                } else {
                    array_push($sub_items, $row);
                }
            }
        }

        $installation_charge = $usd ? $request->data['installation_charge'] / $usd_rate : $request->data['installation_charge'];
        $transport_charge = $usd ? $request->data['transport_charge'] / $usd_rate : $request->data['transport_charge'];
        $attendance_fee = $usd ? $request->data['attendance_fee'] / $usd_rate : $request->data['attendance_fee'];

        $package_exist = $special_exist = false;
        $package_description = $special_description = '';
        $package_percentage = $special_percentage = 0;
        foreach ($request->data['discount_data'] as $detail) {
            if ($detail['discount_type_id'] == 1) {
                $package_exist = true;
                $package_description = $detail['description'];
                $package_percentage = $detail['percentage'];
            } elseif ($detail['discount_type_id'] == 2) {
                $special_exist = true;
                $special_description = $detail['description'];
                $special_percentage = $detail['percentage'];
            }
        }

        $nbt_exist = $svat_exist = $vat_exist = false;
        $nbt_description = $svat_description = $vat_description = '';
        $nbt_percentage = $svat_percentage = $vat_percentage = 0;
        foreach ($request->data['customer_tax'] as $detail) {
            if ($detail['c_tax_type']) {
                if ($detail['c_tax_type']['id'] == 1) {
                    $nbt_exist = true;
                    $nbt_description = $detail['c_tax_type']['code'];
                    $nbt_percentage = $detail['c_tax_type']['percentage'];
                } elseif ($detail['c_tax_type']['id'] == 2) {
                    $svat_exist = true;
                    $svat_description = $detail['c_tax_type']['code'];
                    $svat_percentage = $detail['c_tax_type']['percentage'];
                } elseif ($detail['c_tax_type']['id'] == 3) {
                    $vat_exist = true;
                    $vat_description = $detail['c_tax_type']['code'];
                    $vat_percentage = $detail['c_tax_type']['percentage'];
                }
            }
        }

        $colspan = 6;
        if ($request->data['show_brand']) {
            $colspan++;
        }
        if ($request->data['show_origin']) {
            $colspan++;
        }

        $view = '
                <table id="data_table" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">No#</th>
                            <th style="text-align: center; vertical-align: middle;">Description</th>
                            <th style="text-align: center; vertical-align: middle;">Model No</th>
            ';
        $view .= $request->data['show_brand'] ? '<th style="text-align: center; vertical-align: middle;">Brand</th>' : '';
        $view .= $request->data['show_origin'] ? '<th style="text-align: center; vertical-align: middle;">Origin</th>' : '';
        $view .= '
                            <th style="text-align: center; vertical-align: middle;">Unit Type</th>
                            <th style="text-align: center; vertical-align: middle;">Rate ('.$currency.')</th>
                            <th style="text-align: center; vertical-align: middle;">Quantity</th>
                            <th style="text-align: center; vertical-align: middle;">Value ('.$currency.')</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

        $count = 1;
        $equipment_installation_total = 0;
        foreach ($main_items as $main_item) {
            $equipment_installation_total += $main_item['value'];
            $view .= '
                <tr>
                    <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                    <td style="vertical-align: middle;">'.$main_item['description'].'</td>
                    <td style="vertical-align: middle;">'.$main_item['model_no'].'</td>
                ';
            $view .= $request->data['show_brand'] ? '<td style="vertical-align: middle;">'.$main_item['brand'].'</td>' : '';
            $view .= $request->data['show_origin'] ? '<td style="vertical-align: middle;">'.$main_item['origin'].'</td>' : '';
            $view .= '
                    <td style="text-align: center; vertical-align: middle;">'.$main_item['unit_type'].'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($main_item['rate'], 2).'</td>
                    <td style="text-align: right; vertical-align: middle;">'.$main_item['quantity'].'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($main_item['value'], 2).'</td>
                </tr>
                ';
            $count++;
        }

        if ($package_exist) {
            $view .= '
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">Package Price</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
                ';
            $discount_value = $equipment_installation_total * $package_percentage / 100;
            $equipment_installation_total -= $discount_value;
            $view .= '
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle; background-color: #fdff32;">'.$package_description.'</th>
                    <th style="text-align: right; vertical-align: middle; background-color: #fdff32;">'.number_format($discount_value, 2).'</th>
                </tr>
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">Sign up fee -Package Cost</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
                <tr>
                    <td colspan="'.($colspan + 1).'" style="vertical-align: middle;"><u>Extra Equipment</u></td>
                </tr>
                ';
        }

        foreach ($sub_items as $sub_item) {
            $equipment_installation_total += $sub_item['value'];
            $view .= '
                <tr>
                    <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                    <td style="vertical-align: middle;">'.$sub_item['description'].'</td>
                    <td style="vertical-align: middle;">'.$sub_item['model_no'].'</td>
                ';
            $view .= $request->data['show_brand'] ? '<td style="vertical-align: middle;">'.$sub_item['brand'].'</td>' : '';
            $view .= $request->data['show_origin'] ? '<td style="vertical-align: middle;">'.$sub_item['origin'].'</td>' : '';
            $view .= '
                    <td style="text-align: center; vertical-align: middle;">'.$sub_item['unit_type'].'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($sub_item['rate'], 2).'</td>
                    <td style="text-align: right; vertical-align: middle;">'.$sub_item['quantity'].'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($sub_item['value'], 2).'</td>
                </tr>
                ';
            $count++;
        }

        if (count($main_items) == 0 && count($sub_items) == 0) {
            $view .= '
                    <tr>
                        <td colspan="'.$colspan.'" style="text-align: center; vertical-align: middle;">No Equipments</td>
                        <td style="text-align: right; vertical-align: middle;">'.number_format($equipment_installation_total, 2).'</td>
                    </tr>
                ';
        }

        $view .= '
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">Total - Equipment</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
            ';

        if (! $request->data['show_installation_charge'] && ! $request->data['show_transport']) {
            $view .= '
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                        <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">Engineering, Installation, Transport & Attendance Fee</td>
                        <td style="text-align: right; vertical-align: middle;">'.number_format($installation_charge + $transport_charge + $attendance_fee, 2).'</td>
                    </tr>
                ';
            $count++;
        } else {
            if ($request->data['show_installation_charge'] && $request->data['show_transport']) {
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">'.$request->data['installation_charge_text'].'</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($installation_charge, 2).'</td>
                        </tr>
                    ';
                $count++;
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">Transport Charge</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($transport_charge, 2).'</td>
                        </tr>
                    ';
                $count++;
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">Engineering & Attendance Fee</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($attendance_fee, 2).'</td>
                        </tr>
                    ';
                $count++;
            } elseif ($request->data['show_installation_charge']) {
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">'.$request->data['installation_charge_text'].'</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($installation_charge, 2).'</td>
                        </tr>
                    ';
                $count++;
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">Engineering, Transport & Attendance Fee</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($transport_charge + $attendance_fee, 2).'</td>
                        </tr>
                    ';
                $count++;
            } elseif ($request->data['show_transport']) {
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">Transport Charge</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($transport_charge, 2).'</td>
                        </tr>
                    ';
                $count++;
                $view .= '
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">'.$count.'</td>
                            <td colspan="'.($colspan - 1).'" style="vertical-align: middle;">Engineering, Installation & Attendance Fee</td>
                            <td style="text-align: right; vertical-align: middle;">'.number_format($installation_charge + $attendance_fee, 2).'</td>
                        </tr>
                    ';
                $count++;
            }
        }

        $equipment_installation_total += $installation_charge + $transport_charge + $attendance_fee;
        $view .= '
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">Total - Equipment & Installation</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
            ';

        if ($special_exist) {
            $discount_value = $equipment_installation_total * $special_percentage / 100;
            $equipment_installation_total -= $discount_value;
            $view .= '
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle; background-color: #fdff32;">'.$special_description.'</th>
                    <th style="text-align: right; vertical-align: middle; background-color: #fdff32;">'.number_format($discount_value, 2).'</th>
                </tr>
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;"></th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
                ';
        }

        if ($nbt_exist) {
            $nbt_value = $equipment_installation_total * $nbt_percentage / 100;
            $equipment_installation_total += $nbt_value;
            $view .= '
                <tr>
                    <td colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">'.$nbt_percentage.'% '.$nbt_description.'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($nbt_value, 2).'</td>
                </tr>
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;"></th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
                ';
        }
        if ($svat_exist) {
            $svat_value = $equipment_installation_total * $svat_percentage / 100;
            $view .= '
                <tr>
                    <td colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">'.$svat_percentage.'% '.$svat_description.'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($svat_value, 2).'</td>
                </tr>
                ';
        }
        if ($vat_exist) {
            $vat_value = $equipment_installation_total * $vat_percentage / 100;
            $equipment_installation_total += $vat_value;
            $view .= '
                <tr>
                    <td colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">'.$vat_percentage.'% '.$vat_description.'</td>
                    <td style="text-align: right; vertical-align: middle;">'.number_format($vat_value, 2).'</td>
                </tr>
                ';
        }
        if (! $nbt_exist && ! $svat_exist && ! $vat_exist) {
            $view .= '
                <tr>
                    <td colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">VAT EXEMPTED</td>
                    <td style="text-align: right; vertical-align: middle;"></td>
                </tr>
                ';
        }

        $view .= '
                <tr>
                    <th colspan="'.$colspan.'" style="text-align: right; vertical-align: middle;">Grand Total – Equipment & Installation</th>
                    <th style="text-align: right; vertical-align: middle; border-top: 1px double black; border-bottom: 3px double black;">'.number_format($equipment_installation_total, 2).'</th>
                </tr>
            ';

        $view .= '
                    </tbody>
                </table>
            ';

        $result = [
            'view' => $view,
            'tech_response_quotation_value' => $equipment_installation_total,
        ];

        echo json_encode($result);
    }

    public function print_tech_response_quotation(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $tech_response_quotation = \App\Model\TechResponseQuotation::find($request->id);
        $data['tech_response_quotation'] = $tech_response_quotation;
        $title = $tech_response_quotation ? 'Tech Response Quotation Details '.$tech_response_quotation->tech_response_quotation_no : 'Tech Response Quotation Details';

        $html = view('tech_response.tech_response_quotation_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="'.$title.'.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Portrait',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8,
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
        $valid = true;

        $tech_response_job_card_ids = [];
        foreach ($request->tech_response_job_cards as $detail) {
            if ($detail['selected']) {
                array_push($tech_response_job_card_ids, $detail['id']);
            }
        }

        $usd = false;
        $usd_rate = 0;
        if (! $request->is_currency) {
            $usd = true;
            $usd_rate = $request->usd_rate;
        }

        $equipment_installation_total = 0;
        $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::whereIn('tech_response_job_card_id', $tech_response_job_card_ids)
            ->where('is_delete', 0)
            ->get();
        foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {
            if ($tech_response_job_card_detail->is_chargeable == 1) {
                $item_margin = $tech_response_job_card_detail->Item && ($tech_response_job_card_detail->Item->main_category_id == 2 || $tech_response_job_card_detail->Item->main_category_id == 12 || $tech_response_job_card_detail->Item->main_category_id == 14) ? 10 : 30;
                $margin = ($item_margin + 100) / 100;
                $value = $usd ? ($tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity) / $usd_rate : $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
                $equipment_installation_total += $value;
            }
        }

        $installation_transport_attendence = $request->installation_charge + $request->transport_charge + $request->attendance_fee;
        $equipment_installation_total += $usd ? $installation_transport_attendence / $usd_rate : $installation_transport_attendence;

        foreach ($request->customer_tax as $detail) {
            if ($detail['c_tax_type']) {
                if ($detail['c_tax_type']['id'] == 1 || $detail['c_tax_type']['id'] == 3) {
                    $equipment_installation_total += $equipment_installation_total * $detail['c_tax_type']['percentage'] / 100;
                }
            }
        }

        if (floor($equipment_installation_total) - floor($request->tech_response_quotation_value) > 0) {
            $valid = false;
        }

        if ($valid) {
            $tech_response_quotation = new \App\Model\TechResponseQuotation();
            $tech_response_quotation->tech_response_id = $request->tech_response_id;

            $last_id = 0;
            $last_tech_response_quotation = \App\Model\TechResponseQuotation::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_tech_response_quotation ? $last_tech_response_quotation->id : $last_id;
            $tech_response_quotation->tech_response_quotation_no = 'TR/QT/'.date('m').'/'.date('y').'/'.$request->tech_response_id.'/'.sprintf('%05d', $last_id + 1);

            $tech_response_quotation->tech_response_quotation_date_time = date('Y-m-d', strtotime($request->tech_response_quotation_date)).' '.$request->tech_response_quotation_time;
            $tech_response_quotation->remarks = $request->remarks;
            $tech_response_quotation->special_notes = $request->special_notes;
            $tech_response_quotation->show_installation_charge = $request->show_installation_charge ? 1 : 0;
            $tech_response_quotation->installation_charge_text = $request->installation_charge_text;
            $tech_response_quotation->show_brand = $request->show_brand ? 1 : 0;
            $tech_response_quotation->show_origin = $request->show_origin ? 1 : 0;
            $tech_response_quotation->show_transport = $request->show_transport ? 1 : 0;
            $tech_response_quotation->is_currency = $request->is_currency ? 1 : 0;
            $tech_response_quotation->usd_rate = $request->usd_rate;
            $tech_response_quotation->installation_charge = $request->installation_charge;
            $tech_response_quotation->transport_charge = $request->transport_charge;
            $tech_response_quotation->attendance_fee = $request->attendance_fee;
            $tech_response_quotation->tech_response_quotation_value = $request->tech_response_quotation_value;
            $tech_response_quotation->user_id = $request->session()->get('users_id');

            if ($tech_response_quotation->save()) {
                $tech_response_job_card_ids = '';
                foreach ($request->tech_response_job_cards as $tech_response_job_card) {
                    if ($tech_response_job_card['selected']) {
                        $tech_response_quotation_job_card = new \App\Model\TechResponseQuotationJobCard();
                        $tech_response_quotation_job_card->tech_response_quotation_id = $tech_response_quotation->id;
                        $tech_response_quotation_job_card->tech_response_job_card_id = $tech_response_job_card['id'];
                        $tech_response_quotation_job_card->save();

                        $tech_response_job_card_ids .= $tech_response_job_card_ids != '' ? '|'.$tech_response_quotation_job_card->tech_response_job_card_id : $tech_response_quotation_job_card->tech_response_job_card_id;
                    }
                }
                $discount_details = '';
                foreach ($request->discount_data as $details) {
                    $tech_response_quotation_discount = new \App\Model\TechResponseQuotationDiscount();
                    $tech_response_quotation_discount->tech_response_quotation_id = $tech_response_quotation->id;
                    $tech_response_quotation_discount->discount_type_id = $details['discount_type_id'];
                    $tech_response_quotation_discount->description = $details['description'];
                    $tech_response_quotation_discount->percentage = $details['percentage'];
                    $tech_response_quotation_discount->save();

                    $discount_details .= $discount_details != '' ? '|'.$tech_response_quotation_discount->discount_type_id.'-'.$tech_response_quotation_discount->percentage : $tech_response_quotation_discount->discount_type_id.'-'.$tech_response_quotation_discount->percentage;
                }

                $tech_response_status = new \App\Model\TechResponseDetails();
                $tech_response_status->tech_response_id = $tech_response_quotation->tech_response_id;
                $tech_response_status->update_date_time = date('Y-m-d H:i');
                $tech_response_status->tech_response_status_id = 3;
                $tech_response_status->job_scheduled_date_time = '';
                $tech_response_status->is_chargeable = 0;
                $tech_response_status->remarks = $tech_response_quotation->tech_response_quotation_no;
                $tech_response_status->user_id = $request->session()->get('users_id');
                $tech_response_status->save();

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_quotation_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$tech_response_quotation->id.','.$tech_response_quotation->tech_response_id.','.$tech_response_quotation->tech_response_quotation_no.','.$tech_response_quotation->tech_response_quotation_date_time.','.str_replace(',', ' ', $tech_response_quotation->remarks).','.str_replace(',', ' ', $tech_response_quotation->special_notes).','.$tech_response_quotation->show_installation_charge.','.str_replace(',', ' ', $tech_response_quotation->installation_charge_text).','.$tech_response_quotation->show_brand.','.$tech_response_quotation->show_origin.','.$tech_response_quotation->show_transport.','.$tech_response_quotation->is_currency.','.$tech_response_quotation->usd_rate.','.$tech_response_quotation->installation_charge.','.$tech_response_quotation->transport_charge.','.$tech_response_quotation->attendance_fee.','.$tech_response_quotation->tech_response_quotation_value.','.$tech_response_job_card_ids.','.$discount_details.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Tech Response Quotation created successfully',
                    'data' => $tech_response_quotation->id,
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Tech Response Quotation creation failed',
                ];
            }
        } else {
            $result = [
                'response' => false,
                'message' => 'Tech Response Quotation does not meet the profit margin',
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
        $valid = true;

        $tech_response_job_card_ids = [];
        foreach ($request->tech_response_job_cards as $detail) {
            if ($detail['selected']) {
                array_push($tech_response_job_card_ids, $detail['id']);
            }
        }

        $usd = false;
        $usd_rate = 0;
        if (! $request->is_currency) {
            $usd = true;
            $usd_rate = $request->usd_rate;
        }

        $equipment_installation_total = 0;
        $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::whereIn('tech_response_job_card_id', $tech_response_job_card_ids)
            ->where('is_delete', 0)
            ->get();
        foreach ($tech_response_job_card_details as $tech_response_job_card_detail) {
            if ($tech_response_job_card_detail->is_chargeable == 1) {
                $item_margin = $tech_response_job_card_detail->Item && ($tech_response_job_card_detail->Item->main_category_id == 2 || $tech_response_job_card_detail->Item->main_category_id == 12 || $tech_response_job_card_detail->Item->main_category_id == 14) ? 10 : 30;
                $margin = ($item_margin + 100) / 100;
                $value = $usd ? ($tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity) / $usd_rate : $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
                $equipment_installation_total += $value;
            }
        }

        $installation_transport_attendence = $request->installation_charge + $request->transport_charge + $request->attendance_fee;
        $equipment_installation_total += $usd ? $installation_transport_attendence / $usd_rate : $installation_transport_attendence;

        foreach ($request->customer_tax as $detail) {
            if ($detail['c_tax_type']) {
                if ($detail['c_tax_type']['id'] == 1 || $detail['c_tax_type']['id'] == 3) {
                    $equipment_installation_total += $equipment_installation_total * $detail['c_tax_type']['percentage'] / 100;
                }
            }
        }

        if (floor($equipment_installation_total) - floor($request->tech_response_quotation_value) > 0) {
            $valid = false;
        }

        if ($valid) {
            $tech_response_quotation = \App\Model\TechResponseQuotation::find($request->tech_response_quotation_id);
            $tech_response_quotation->tech_response_id = $request->tech_response_id;
            $tech_response_quotation->tech_response_quotation_no = $request->tech_response_quotation_no;
            $tech_response_quotation->tech_response_quotation_date_time = date('Y-m-d', strtotime($request->tech_response_quotation_date)).' '.$request->tech_response_quotation_time;
            $tech_response_quotation->remarks = $request->remarks;
            $tech_response_quotation->special_notes = $request->special_notes;
            $tech_response_quotation->show_installation_charge = $request->show_installation_charge ? 1 : 0;
            $tech_response_quotation->installation_charge_text = $request->installation_charge_text;
            $tech_response_quotation->show_brand = $request->show_brand ? 1 : 0;
            $tech_response_quotation->show_origin = $request->show_origin ? 1 : 0;
            $tech_response_quotation->show_transport = $request->show_transport ? 1 : 0;
            $tech_response_quotation->is_currency = $request->is_currency ? 1 : 0;
            $tech_response_quotation->usd_rate = $request->usd_rate;
            $tech_response_quotation->installation_charge = $request->installation_charge;
            $tech_response_quotation->transport_charge = $request->transport_charge;
            $tech_response_quotation->attendance_fee = $request->attendance_fee;
            $tech_response_quotation->tech_response_quotation_value = $request->tech_response_quotation_value;
            $tech_response_quotation->user_id = $request->session()->get('users_id');

            if ($tech_response_quotation->save()) {
                $tech_response_quotation_job_cards = \App\Model\TechResponseQuotationJobCard::where('tech_response_quotation_id', $tech_response_quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($tech_response_quotation_job_cards as $tech_response_quotation_job_card) {
                    $tech_response_quotation_job_card->is_delete = 1;
                    $tech_response_quotation_job_card->save();
                }
                $tech_response_quotation_discounts = \App\Model\TechResponseQuotationDiscount::where('tech_response_quotation_id', $tech_response_quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($tech_response_quotation_discounts as $tech_response_quotation_discount) {
                    $tech_response_quotation_discount->is_delete = 1;
                    $tech_response_quotation_discount->save();
                }

                $tech_response_job_card_ids = '';
                foreach ($request->tech_response_job_cards as $tech_response_job_card) {
                    if ($tech_response_job_card['selected']) {
                        $tech_response_quotation_job_card = \App\Model\TechResponseQuotationJobCard::where('tech_response_quotation_id', $tech_response_quotation->id)
                            ->where('tech_response_job_card_id', $tech_response_job_card['id'])
                            ->first();
                        $tech_response_quotation_job_card = $tech_response_quotation_job_card ? $tech_response_quotation_job_card : new \App\Model\TechResponseQuotationJobCard();
                        $tech_response_quotation_job_card->tech_response_quotation_id = $tech_response_quotation->id;
                        $tech_response_quotation_job_card->tech_response_job_card_id = $tech_response_job_card['id'];
                        $tech_response_quotation_job_card->is_delete = 0;
                        $tech_response_quotation_job_card->save();

                        $tech_response_job_card_ids .= $tech_response_job_card_ids != '' ? '|'.$tech_response_job_card['id'] : $tech_response_job_card['id'];
                    }
                }
                $discount_details = '';
                foreach ($request->discount_data as $details) {
                    $tech_response_quotation_discount = \App\Model\TechResponseQuotationDiscount::where('tech_response_quotation_id', $tech_response_quotation->id)
                        ->where('discount_type_id', $details['discount_type_id'])
                        ->first();
                    $tech_response_quotation_discount = $tech_response_quotation_discount ? $tech_response_quotation_discount : new \App\Model\TechResponseQuotationDiscount();
                    $tech_response_quotation_discount->tech_response_quotation_id = $tech_response_quotation->id;
                    $tech_response_quotation_discount->discount_type_id = $details['discount_type_id'];
                    $tech_response_quotation_discount->description = $details['description'];
                    $tech_response_quotation_discount->percentage = $details['percentage'];
                    $tech_response_quotation_discount->is_delete = 0;
                    $tech_response_quotation_discount->save();

                    $discount_details .= $discount_details != '' ? '|'.$details['discount_type_id'].'-'.$details['percentage'] : $details['discount_type_id'].'-'.$details['percentage'];
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_quotation_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$tech_response_quotation->id.','.$tech_response_quotation->tech_response_id.','.$tech_response_quotation->tech_response_quotation_no.','.$tech_response_quotation->tech_response_quotation_date_time.','.str_replace(',', ' ', $tech_response_quotation->remarks).','.str_replace(',', ' ', $tech_response_quotation->special_notes).','.$tech_response_quotation->show_installation_charge.','.str_replace(',', ' ', $tech_response_quotation->installation_charge_text).','.$tech_response_quotation->show_brand.','.$tech_response_quotation->show_origin.','.$tech_response_quotation->show_transport.','.$tech_response_quotation->is_currency.','.$tech_response_quotation->usd_rate.','.$tech_response_quotation->installation_charge.','.$tech_response_quotation->transport_charge.','.$tech_response_quotation->attendance_fee.','.$tech_response_quotation->tech_response_quotation_value.','.$tech_response_job_card_ids.','.$discount_details.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Tech Response Quotation updated successfully',
                    'data' => $tech_response_quotation->id,
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Tech Response Quotation updation failed',
                ];
            }
        } else {
            $result = [
                'response' => false,
                'message' => 'Tech Response Quotation does not meet the profit margin',
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
    public function destroy($id, Request $request)
    {
        $tech_response_quotation = \App\Model\TechResponseQuotation::find($id);
        $tech_response_quotation->is_delete = 1;

        if ($tech_response_quotation->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/tech_response_quotation_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$tech_response_quotation->id.',,,,,,,,,,,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);
            $result = [
                'response' => true,
                'message' => 'Tech Response Quotation deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Tech Response Quotation deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
