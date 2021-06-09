<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class DataController extends Controller
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
    public function contact_contact_types()
    {
        $contact_types = \App\Model\CContactType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();

        return response($contact_types);
    }

    public function item_categories()
    {
        $main_item_categories = \App\Model\MainItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $sub_item_categories = \App\Model\SubItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $data = [
            'main_item_categories' => $main_item_categories,
            'sub_item_categories' => $sub_item_categories,
        ];

        return response($data);
    }

    public function get_customers(Request $request)
    {
        $customers = \App\Model\Contact::select('id', 'name', 'contact_no', 'email', 'address')
            ->where(function ($q) use ($request) {
                count($request->type) > 0 ? $q->whereIn('contact_type_id', $request->type) : '';
            })
            ->where('name', 'like', '%'.$request->name.'%')
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        return response($customers);
    }

    public function find_customer(Request $request)
    {
        $customer = \App\Model\Contact::select('id', 'name', 'contact_no', 'email', 'address')
            ->where(function ($q) use ($request) {
                count($request->type) > 0 ? $q->whereIn('contact_type_id', $request->type) : '';
            })
            ->where('name', $request->name)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();

        return response($customer);
    }

    public function get_technical_teams(Request $request)
    {
        $technical_teams = \App\Model\TechnicalTeam::select('id', 'name')
            ->where('name', 'like', '%'.$request->name.'%')
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        return response($technical_teams);
    }

    public function find_technical_team(Request $request)
    {
        $technical_team = \App\Model\TechnicalTeam::select('id', 'name')
            ->where('name', $request->name)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();

        return response($technical_team);
    }

    public function get_job_nos(Request $request)
    {
        $jobs = \App\Model\Job::select('id', 'inquiry_id', 'job_no')
            ->with(['Inquiry' => function ($query) {
                $query->select('id', 'contact_id')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('job_no', 'like', '%'.$request->job_no.'%')
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->orderBy('job_no')
            ->get();

        return response($jobs);
    }

    public function find_job_no(Request $request)
    {
        $job = \App\Model\Job::select('id', 'inquiry_id', 'job_no')
            ->with(['Inquiry' => function ($query) {
                $query->select('id', 'contact_id')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('job_no', $request->job_no)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->first();

        return response($job);
    }

    public function get_tech_response_nos(Request $request)
    {
        $tech_responses = \App\Model\TechResponse::select('id', 'contact_id', 'tech_response_no')
            ->with(['Contact' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('tech_response_no', 'like', '%'.$request->tech_response_no.'%')
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->orderBy('tech_response_no')
            ->get();

        return response($tech_responses);
    }

    public function find_tech_response_no(Request $request)
    {
        $tech_response = \App\Model\TechResponse::select('id', 'contact_id', 'tech_response_no')
            ->with(['Contact' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('tech_response_no', $request->tech_response_no)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->first();

        return response($tech_response);
    }

    public function validate_contact_name(Request $request)
    {
        $contact = \App\Model\Contact::where('name', $request->name)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();
        if ($contact) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_job_no(Request $request)
    {
        $job = \App\Model\Job::where('job_no', $request->job_no)
            ->where('is_completed', 0)
            ->where('is_delete', 0)
            ->first();
        if ($job) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_technical_team_name(Request $request)
    {
        $technical_team = \App\Model\TechnicalTeam::where('name', $request->name)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();
        if ($technical_team) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_good_request_no(Request $request)
    {
        $good_request = \App\Model\GoodRequest::where('good_request_no', $request->good_request_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();
        if ($good_request) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_purchase_order_no(Request $request)
    {
        $purchase_order = \App\Model\PurchaseOrder::where('purchase_order_no', $request->purchase_order_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();
        if ($purchase_order) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function get_document_types()
    {
        $document_types = \App\Model\DocumentType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();

        return response($document_types);
    }

    public function validate_item_code(Request $request)
    {
        $item = \App\Model\Item::where('code', $request->code)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();
        if ($item) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_item_name(Request $request)
    {
        $item = \App\Model\Item::where('name', $request->name)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();
        if ($item) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function get_item_codes(Request $request)
    {
        $items = \App\Model\Item::where('code', 'like', '%'.$request->code.'%')
            ->where(function ($q) use ($request) {
                $request->main_category != '' ? $q->where('main_category_id', $request->main_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sub_category != '' ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        $data = [];
        foreach ($items as $item) {
            if ($request->type == 0) {
                $max_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('available_quantity', '>', 0)->where('is_delete', 0)->max('rate');
                $last_purchase_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
                $rate = $max_rate ? $max_rate : $last_purchase_rate ? $last_purchase_rate->rate : $item->rate;
            } else {
                $rate = $item->rate;
            }

            $row = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'main_category_id' => $item->MainItemCategory ? $item->MainItemCategory->id : '',
                'main_category_name' => $item->MainItemCategory ? $item->MainItemCategory->name : '',
                'sub_category_id' => $item->SubItemCategory ? $item->SubItemCategory->id : '',
                'sub_category_name' => $item->SubItemCategory ? $item->SubItemCategory->name : '',
                'unit_type' => $item->UnitType ? $item->UnitType->code : '',
                'rate' => $rate,
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'stock' => $item->stock,
                'is_serial' => $item->is_serial,
                'is_warranty' => $item->is_warranty,
            ];
            array_push($data, $row);
        }

        return response($data);
    }

    public function find_item_code(Request $request)
    {
        $item = \App\Model\Item::where('code', $request->code)
            ->where(function ($q) use ($request) {
                $request->main_category != '' ? $q->where('main_category_id', $request->main_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sub_category != '' ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();

        $data = null;
        if ($item) {
            if ($request->type == 0) {
                $max_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('available_quantity', '>', 0)->where('is_delete', 0)->max('rate');
                $last_purchase_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
                $rate = $max_rate ? $max_rate : $last_purchase_rate ? $last_purchase_rate->rate : $item->rate;
            } else {
                $rate = $item->rate;
            }

            $data = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'main_category_id' => $item->MainItemCategory ? $item->MainItemCategory->id : '',
                'main_category_name' => $item->MainItemCategory ? $item->MainItemCategory->name : '',
                'sub_category_id' => $item->SubItemCategory ? $item->SubItemCategory->id : '',
                'sub_category_name' => $item->SubItemCategory ? $item->SubItemCategory->name : '',
                'unit_type' => $item->UnitType ? $item->UnitType->code : '',
                'rate' => $rate,
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'stock' => $item->stock,
                'is_serial' => $item->is_serial,
                'is_warranty' => $item->is_warranty,
            ];
        }

        return response($data);
    }

    public function get_item_names(Request $request)
    {
        $items = \App\Model\Item::where('name', 'like', '%'.$request->name.'%')
            ->where(function ($q) use ($request) {
                $request->main_category != '' ? $q->where('main_category_id', $request->main_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sub_category != '' ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        $data = [];
        foreach ($items as $item) {
            if ($request->type == 0) {
                $max_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('available_quantity', '>', 0)->where('is_delete', 0)->max('rate');
                $last_purchase_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
                $rate = $max_rate ? $max_rate : $last_purchase_rate ? $last_purchase_rate->rate : $item->rate;
            } else {
                $rate = $item->rate;
            }

            $row = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'main_category_id' => $item->MainItemCategory ? $item->MainItemCategory->id : '',
                'main_category_name' => $item->MainItemCategory ? $item->MainItemCategory->name : '',
                'sub_category_id' => $item->SubItemCategory ? $item->SubItemCategory->id : '',
                'sub_category_name' => $item->SubItemCategory ? $item->SubItemCategory->name : '',
                'unit_type' => $item->UnitType ? $item->UnitType->code : '',
                'rate' => $rate,
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'stock' => $item->stock,
                'is_serial' => $item->is_serial,
                'is_warranty' => $item->is_warranty,
            ];
            array_push($data, $row);
        }

        return response($data);
    }

    public function find_item_name(Request $request)
    {
        $item = \App\Model\Item::where('name', $request->name)
            ->where(function ($q) use ($request) {
                $request->main_category != '' ? $q->where('main_category_id', $request->main_category) : '';
            })
            ->where(function ($q) use ($request) {
                $request->sub_category != '' ? $q->where('sub_category_id', $request->sub_category) : '';
            })
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->first();

        $data = null;
        if ($item) {
            if ($request->type == 0) {
                $max_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('available_quantity', '>', 0)->where('is_delete', 0)->max('rate');
                $last_purchase_rate = \App\Model\GoodReceiveDetails::where('item_id', $item->id)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
                $rate = $max_rate ? $max_rate : $last_purchase_rate ? $last_purchase_rate->rate : $item->rate;
            } else {
                $rate = $item->rate;
            }

            $data = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'main_category_id' => $item->MainItemCategory ? $item->MainItemCategory->id : '',
                'main_category_name' => $item->MainItemCategory ? $item->MainItemCategory->name : '',
                'sub_category_id' => $item->SubItemCategory ? $item->SubItemCategory->id : '',
                'sub_category_name' => $item->SubItemCategory ? $item->SubItemCategory->name : '',
                'unit_type' => $item->UnitType ? $item->UnitType->code : '',
                'rate' => $rate,
                'model_no' => $item->model_no,
                'brand' => $item->brand,
                'origin' => $item->origin,
                'stock' => $item->stock,
                'is_serial' => $item->is_serial,
                'is_warranty' => $item->is_warranty,
            ];
        }

        return response($data);
    }

    public function get_installation_rates()
    {
        $installation_rates = \App\Model\InstallationRate::select('id', 'code', 'name', 'installation_cost', 'labour', 'rate')->where('is_delete', 0)->orderBy('name')->get();

        return response($installation_rates);
    }

    public function validate_installation_quantity(Request $request)
    {
        $valid = false;

        if ($request->item_id != '') {
            $insallation_value = 0;
            $quotations = \App\Model\Quotation::where('inquiry_id', $request->inquiry_id)
                ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $quotation_cost_sheets = \App\Model\QuotationCostSheet::where('quotation_id', $quotation->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($quotation_cost_sheets as $quotation_cost_sheet) {
                    $insallation_value += $quotation_cost_sheet->CostSheet->installation_value;
                }
            }

            $assigned_item_value = 0;
            $installation_sheet_details = \App\Model\InstallationSheetDetails::whereHas('InstallationSheet', function ($query) use ($request) {
                $query->where('inquiry_id', $request->inquiry_id);
            })
                ->where('installation_sheet_id', '!=', $request->installation_sheet_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($installation_sheet_details as $installation_sheet_detail) {
                $assigned_item_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
            }
            $installation_sheet_details = \App\Model\InstallationSheetDetails::where('installation_sheet_id', $request->installation_sheet_id)
                ->where('id', '!=', $request->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($installation_sheet_details as $installation_sheet_detail) {
                $assigned_item_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
            }

            $balance_value = $insallation_value - $assigned_item_value - ($request->rate * $request->quantity);

            $valid = $balance_value >= 0 ? true : false;
        }

        if ($valid) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_tech_response_installation_quantity(Request $request)
    {
        $valid = false;

        if ($request->item_id != '') {
            $insallation_value = 0;
            $quotations = \App\Model\TechResponseQuotation::where('tech_response_id', $request->tech_response_id)
                //                    ->where('is_confirmed', 1)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                $insallation_value += $quotation->installation_charge;
            }

            $assigned_item_value = 0;
            $installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::whereHas('TechResponseInstallationSheet', function ($query) use ($request) {
                $query->where('tech_response_id', $request->tech_response_id);
            })
                ->where('tech_response_installation_sheet_id', '!=', $request->tech_response_installation_sheet_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($installation_sheet_details as $installation_sheet_detail) {
                $assigned_item_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
            }
            $installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::where('tech_response_installation_sheet_id', $request->tech_response_installation_sheet_id)
                ->where('id', '!=', $request->id)
                ->where('is_delete', 0)
                ->get();
            foreach ($installation_sheet_details as $installation_sheet_detail) {
                $assigned_item_value += $installation_sheet_detail->rate * $installation_sheet_detail->quantity;
            }

            $balance_value = $insallation_value - $assigned_item_value - ($request->rate * $request->quantity);

            $valid = $balance_value >= 0 ? true : false;
        }

        if ($valid) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function get_good_request_nos(Request $request)
    {
        $good_requests = \App\Model\GoodRequest::select('id', 'good_request_no')
            ->where('good_request_no', 'like', '%'.$request->good_request_no.'%')
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->orderBy('good_request_no')
            ->get();

        return response($good_requests);
    }

    public function find_good_request_no(Request $request)
    {
        $good_request = \App\Model\GoodRequest::select('id', 'good_request_no')
            ->where('good_request_no', $request->good_request_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();

        return response($good_request);
    }

    public function get_purchase_order_nos(Request $request)
    {
        $purchase_orders = \App\Model\PurchaseOrder::select('id', 'contact_id', 'purchase_order_no')
            ->with(['Contact' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('purchase_order_no', 'like', '%'.$request->purchase_order_no.'%')
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->orderBy('purchase_order_no')
            ->get();

        return response($purchase_orders);
    }

    public function find_purchase_order_no(Request $request)
    {
        $purchase_order = \App\Model\PurchaseOrder::select('id', 'contact_id', 'purchase_order_no')
            ->with(['Contact' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('purchase_order_no', $request->purchase_order_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();

        return response($purchase_order);
    }

    public function get_payment_modes()
    {
        $payment_modes = \App\Model\PaymentMode::select('id', 'name')->orderBy('name')->get();

        return response($payment_modes);
    }

    public function get_item_issues(Request $request)
    {
        $item_issues = \App\Model\ItemIssue::select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'issued_to')
            ->with(['Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(['Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(['Contact' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                    }]);
            }])
            ->with(['TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('item_issue_no', 'like', '%'.$request->item_issue_no.'%')
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->orderBy('item_issue_no')
            ->get();

        return response($item_issues);
    }

    public function find_item_issue(Request $request)
    {
        $item_issue = \App\Model\ItemIssue::select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'issued_to')
            ->with(['Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(['Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(['Contact' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                    }]);
            }])
            ->with(['TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('item_issue_no', $request->item_issue_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();

        return response($item_issue);
    }

    public function get_fault_types()
    {
        $fault_types = \App\Model\TechResponseFault::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();

        return response($fault_types);
    }

    public function get_driving_types()
    {
        $driving_types = \App\Model\DrivingType::select('id', 'name')->orderBy('name')->get();

        return response($driving_types);
    }

    public function get_filter_data()
    {
        $inquiry_types = \App\Model\IInquiryType::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $sales_team = \App\Model\SalesTeam::select('id', 'name')->where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        $inquiry_status = \App\Model\InquiryStatus::select('id', 'name')->get();
        $job_status = \App\Model\JobStatus::select('id', 'name')->get();
        $fault_types = \App\Model\TechResponseFault::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $tech_response_status = \App\Model\TechResponseStatus::select('id', 'name')->get();
        $mode_of_inquries = \App\Model\IModeOfInquiry::select('id', 'name')->where('is_delete', 0)->orderBy('name')->get();

        $data = [
            'inquiry_types' => $inquiry_types,
            'sales_team' => $sales_team,
            'inquiry_status' => $inquiry_status,
            'job_status' => $job_status,
            'fault_types' => $fault_types,
            'tech_response_status' => $tech_response_status,
            'mode_of_inquries' => $mode_of_inquries,
        ];

        return response($data);
    }

    public function get_petty_cash_issue_nos(Request $request)
    {
        $petty_cash_issues = \App\Model\PettyCashIssue::select('id', 'petty_cash_issue_type_id', 'document_id', 'petty_cash_issue_no', 'issued_to')
            ->with(['ItemIssueType' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(['Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(['Contact' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                    }]);
            }])
            ->with(['TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('petty_cash_issue_no', 'like', '%'.$request->petty_cash_issue_no.'%')
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->orderBy('petty_cash_issue_no')
            ->get();

        return response($petty_cash_issues);
    }

    public function find_petty_cash_issue_no(Request $request)
    {
        $petty_cash_issue = \App\Model\PettyCashIssue::select('id', 'petty_cash_issue_type_id', 'document_id', 'petty_cash_issue_no', 'issued_to')
            ->with(['ItemIssueType' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(['Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(['Contact' => function ($query) {
                                $query->select('id', 'name');
                            }]);
                    }]);
            }])
            ->with(['TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(['Contact' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('petty_cash_issue_no', $request->petty_cash_issue_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();

        return response($petty_cash_issue);
    }

    public function get_inventory_data()
    {
        $inventory_locations = \App\Model\InventoryLocation::select('id', 'code', 'name')->orderBy('name')->get();
        $inventory_types = \App\Model\InventoryType::select('id', 'code', 'name')->orderBy('name')->get();
        $data = [
            'inventory_locations' => $inventory_locations,
            'inventory_types' => $inventory_types,
        ];

        return response($data);
    }

    public function get_inventory_codes(Request $request)
    {
        $inventory_registers = \App\Model\InventoryRegister::select('id', 'code', 'inventory_type_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks')
            ->with(['InventoryType' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('code', 'like', '%'.$request->code.'%')
            ->where(function ($q) use ($request) {
                $request->inventory_location != '' ? $q->where('inventory_location_id', $request->inventory_location) : '';
                $request->inventory_type != '' ? $q->where('inventory_type_id', $request->inventory_type) : '';
            })
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        return response($inventory_registers);
    }

    public function find_inventory_code(Request $request)
    {
        $inventory_register = \App\Model\InventoryRegister::select('id', 'code', 'inventory_type_id', 'name', 'model_no', 'imei', 'serial_no', 'credit_limit', 'remarks')
            ->with(['InventoryType' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('code', $request->code)
            ->where(function ($q) use ($request) {
                $request->inventory_location != '' ? $q->where('inventory_location_id', $request->inventory_location) : '';
                $request->inventory_type != '' ? $q->where('inventory_type_id', $request->inventory_type) : '';
            })
            ->where('is_delete', 0)
            ->first();

        return response($inventory_register);
    }

    public function get_inventory_issues(Request $request)
    {
        $inventory_issues = \App\Model\InventoryIssue::select('id', 'inventory_issue_no', 'issued_to')
            ->where('inventory_issue_no', 'like', '%'.$request->inventory_issue_no.'%')
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->orderBy('inventory_issue_no')
            ->get();

        return response($inventory_issues);
    }

    public function find_inventory_issue(Request $request)
    {
        $inventory_issue = \App\Model\InventoryIssue::select('id', 'inventory_issue_no', 'issued_to')
            ->where('inventory_issue_no', $request->inventory_issue_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();

        return response($inventory_issue);
    }
}
