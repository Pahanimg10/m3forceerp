<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class ItemIssueController extends Controller
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

        return view('stock.item_issue', $data);
    }

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

        $data['item_issue_id'] = $request->id;

        return view('stock.item_issue_detail', $data);
    }

    public function validate_document_no(Request $request)
    {
        $exist = false;
        if ($request->item_issue_type == 1) {
            $job = \App\Model\Job::where('job_no', $request->document)
                ->where('is_completed', 0)
                ->where('is_delete', 0)
                ->first();
            $exist = $job ? true : false;
        } else if ($request->item_issue_type == 2) {
            $tech_response = \App\Model\TechResponse::where('tech_response_no', $request->document)
                ->where('is_completed', 0)
                ->where('is_delete', 0)
                ->first();
            $exist = $tech_response ? true : false;
        }

        if ($exist) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_item_code(Request $request)
    {
        if ($request->code != $request->item_code) {
            $item = true;
            if ($request->item_issue_type == 1) {
                $job = \App\Model\Job::find($request->document_id);
                $item = \App\Model\JobCardDetails::select('id', 'job_card_id', 'item_id', 'quantity')
                    ->whereHas('JobCard', function ($query) use ($job) {
                        $query->where('inquiry_id', $job->inquiry_id);
                    })
                    ->whereHas('Item', function ($query) use ($request) {
                        $query->where('code', $request->code);
                    })
                    ->where('is_delete', 0)
                    ->first();
                if (!$item) {
                    $item = \App\Model\InstallationSheetDetails::select('id', 'installation_sheet_id', 'item_id', 'quantity')
                        ->whereHas('InstallationSheet', function ($query) use ($job) {
                            $query->where('inquiry_id', $job->inquiry_id)->where('is_posted', 1)->where('is_approved', 1);
                        })
                        ->whereHas('Item', function ($query) use ($request) {
                            $query->where('code', $request->code);
                        })
                        ->where('is_delete', 0)
                        ->first();
                }
            }
            if ($request->item_issue_type == 2) {
                $item = \App\Model\TechResponseJobCardDetails::select('id', 'tech_response_job_card_id', 'item_id', 'quantity')
                    ->whereHas('TechResponseJobCard', function ($query) use ($request) {
                        $query->where('tech_response_id', $request->document_id)->where('is_posted', 1)->where('is_approved', 1);
                    })
                    ->whereHas('Item', function ($query) use ($request) {
                        $query->where('code', $request->code);
                    })
                    ->where('is_delete', 0)
                    ->first();
                if (!$item) {
                    $item = \App\Model\TechResponseInstallationSheetDetails::select('id', 'tech_response_installation_sheet_id', 'item_id', 'quantity')
                        ->whereHas('TechResponseInstallationSheet', function ($query) use ($request) {
                            $query->where('tech_response_id', $request->document_id)->where('is_posted', 1)->where('is_approved', 1);
                        })
                        ->whereHas('Item', function ($query) use ($request) {
                            $query->where('code', $request->code);
                        })
                        ->where('is_delete', 0)
                        ->first();
                }
            }
            $item_issue_detail = \App\Model\ItemIssueDetails::select('id', 'item_issue_id', 'item_id')
                ->where('item_issue_id', $request->item_issue_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->first();
            if ($item && !$item_issue_detail) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function validate_item_name(Request $request)
    {
        if ($request->name != $request->item_name) {
            $item = true;
            if ($request->item_issue_type == 1) {
                $job = \App\Model\Job::find($request->document_id);
                $item = \App\Model\JobCardDetails::select('id', 'job_card_id', 'item_id', 'quantity')
                    ->whereHas('JobCard', function ($query) use ($job) {
                        $query->where('inquiry_id', $job->inquiry_id);
                    })
                    ->whereHas('Item', function ($query) use ($request) {
                        $query->where('name', $request->name);
                    })
                    ->where('is_delete', 0)
                    ->first();
                if (!$item) {
                    $item = \App\Model\InstallationSheetDetails::select('id', 'installation_sheet_id', 'item_id', 'quantity')
                        ->whereHas('InstallationSheet', function ($query) use ($job) {
                            $query->where('inquiry_id', $job->inquiry_id)->where('is_posted', 1)->where('is_approved', 1);
                        })
                        ->whereHas('Item', function ($query) use ($request) {
                            $query->where('name', $request->name);
                        })
                        ->where('is_delete', 0)
                        ->first();
                }
            }
            if ($request->item_issue_type == 2) {
                $item = \App\Model\TechResponseJobCardDetails::select('id', 'tech_response_job_card_id', 'item_id', 'quantity')
                    ->whereHas('TechResponseJobCard', function ($query) use ($request) {
                        $query->where('tech_response_id', $request->document_id)->where('is_posted', 1)->where('is_approved', 1);
                    })
                    ->whereHas('Item', function ($query) use ($request) {
                        $query->where('name', $request->name);
                    })
                    ->where('is_delete', 0)
                    ->first();
                if (!$item) {
                    $item = \App\Model\TechResponseInstallationSheetDetails::select('id', 'tech_response_installation_sheet_id', 'item_id', 'quantity')
                        ->whereHas('TechResponseInstallationSheet', function ($query) use ($request) {
                            $query->where('tech_response_id', $request->document_id)->where('is_posted', 1)->where('is_approved', 1);
                        })
                        ->whereHas('Item', function ($query) use ($request) {
                            $query->where('name', $request->name);
                        })
                        ->where('is_delete', 0)
                        ->first();
                }
            }
            $item_issue_detail = \App\Model\ItemIssueDetails::select('id', 'item_issue_id', 'item_id')
                ->where('item_issue_id', $request->item_issue_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->first();
            if ($item && !$item_issue_detail) {
                $response = 'true';
            } else {
                $response = 'false';
            }
        } else {
            $response = 'true';
        }

        echo $response;
    }

    public function validate_item_quantity(Request $request)
    {
        $exist = true;

        $item = \App\Model\Item::find($request->item_id);
        $exist = $item && $item->stock >= $request->quantity ? true : false;

        if ($exist && $request->item_issue_type == 1) {
            $job = \App\Model\Job::find($request->document_id);
            $total_quantity = 0;
            $items = \App\Model\JobCardDetails::select('id', 'job_card_id', 'item_id', 'quantity')
                ->whereHas('JobCard', function ($query) use ($job) {
                    $query->where('inquiry_id', $job->inquiry_id);
                })
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($items as $item) {
                $total_quantity += $item->quantity;
            }
            $items = \App\Model\InstallationSheetDetails::select('id', 'installation_sheet_id', 'item_id', 'quantity')
                ->whereHas('InstallationSheet', function ($query) use ($job) {
                    $query->where('inquiry_id', $job->inquiry_id)->where('is_posted', 1)->where('is_approved', 1);
                })
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($items as $item) {
                $total_quantity += $item->quantity;
            }

            $item_issue_ids = array();
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->where('item_issue_type_id', $request->item_issue_type)->where('document_id', $request->document_id)->where('is_posted', 1);
            })
                ->where('item_issue_id', '!=', $request->item_issue_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $total_quantity -= $item_issue_detail->quantity;
                if (!in_array($item_issue_detail->item_issue_id, $item_issue_ids)) {
                    array_push($item_issue_ids, $item_issue_detail->item_issue_id);
                }
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($item_issue_ids) {
                $query->whereIn('item_issue_id', $item_issue_ids)->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $total_quantity += $item_receive_detail->quantity;
            }

            $exist = $total_quantity >= $request->quantity ? true : false;
        }
        if ($exist && $request->item_issue_type == 2) {
            $total_quantity = 0;
            $items = \App\Model\TechResponseJobCardDetails::select('id', 'tech_response_job_card_id', 'item_id', 'quantity')
                ->whereHas('TechResponseJobCard', function ($query) use ($request) {
                    $query->where('tech_response_id', $request->document_id)->where('is_posted', 1)->where('is_approved', 1);
                })
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($items as $item) {
                $total_quantity += $item->quantity;
            }
            $items = \App\Model\TechResponseInstallationSheetDetails::select('id', 'tech_response_installation_sheet_id', 'item_id', 'quantity')
                ->whereHas('TechResponseInstallationSheet', function ($query) use ($request) {
                    $query->where('tech_response_id', $request->document_id)->where('is_posted', 1)->where('is_approved', 1);
                })
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($items as $item) {
                $total_quantity += $item->quantity;
            }

            $item_issue_ids = array();
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->where('item_issue_type_id', $request->item_issue_type)->where('document_id', $request->document_id)->where('is_posted', 1);
            })
                ->where('item_issue_id', '!=', $request->item_issue_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $total_quantity -= $item_issue_detail->quantity;
                if (!in_array($item_issue_detail->item_issue_id, $item_issue_ids)) {
                    array_push($item_issue_ids, $item_issue_detail->item_issue_id);
                }
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($item_issue_ids) {
                $query->whereIn('item_issue_id', $item_issue_ids)->where('is_posted', 1)->where('is_delete', 0);
            })
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $total_quantity += $item_receive_detail->quantity;
            }

            $exist = $total_quantity >= $request->quantity ? true : false;
        }

        if ($exist) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_serial_no(Request $request)
    {
        $serial_no = \App\Model\GoodReceiveBreakdown::where('serial_no', $request->serial_no)
            ->where('is_main', 1)
            ->where('is_issued', 0)
            ->where('is_delete', 0)
            ->first();
        if ($serial_no) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function get_data()
    {
        $item_issue_types = \App\Model\ItemIssueType::select('id', 'name')->orderBy('name')->get();
        $main_item_categories = \App\Model\MainItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $sub_item_categories = \App\Model\SubItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();

        $data = array(
            'item_issue_types' => $item_issue_types,
            'main_item_categories' => $main_item_categories,
            'sub_item_categories' => $sub_item_categories
        );

        return response($data);
    }

    public function get_serial_nos(Request $request)
    {
        $serial_nos = \App\Model\GoodReceiveBreakdown::select('id', 'serial_no')
            ->where('serial_no', 'like', '%' . $request->serial_no . '%')
            ->where('is_main', 1)
            ->where('is_issued', 0)
            ->where('is_delete', 0)
            ->orderBy('serial_no')
            ->get();
        return response($serial_nos);
    }

    public function find_serial_no(Request $request)
    {
        $serial_no = \App\Model\GoodReceiveBreakdown::select('id', 'serial_no')
            ->where('serial_no', $request->serial_no)
            ->where('is_main', 1)
            ->where('is_issued', 0)
            ->where('is_delete', 0)
            ->first();
        return response($serial_no);
    }

    public function item_issue_list(Request $request)
    {
        $item_issues = \App\Model\ItemIssue::select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'item_issue_date_time', 'issued_to', 'remarks', 'item_issue_value', 'is_posted')
            ->with(array('ItemIssueType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(array('Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name');
                            }));
                    }));
            }))
            ->with(array('TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name');
                    }));
            }))
            ->whereBetween('item_issue_date_time', array($request->from . ' 00:01', $request->to . ' 23:59'))
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'item_issues' => $item_issues,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_item_issue(Request $request)
    {
        $item_issue = \App\Model\ItemIssue::select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'item_issue_date_time', 'issued_to', 'remarks', 'item_issue_value', 'is_posted')
            ->with(array('ItemIssueType' => function ($query) {
                $query->select('id', 'name');
            }))
            ->with(array('Job' => function ($query) {
                $query->select('id', 'inquiry_id', 'job_no')
                    ->with(array('Inquiry' => function ($query) {
                        $query->select('id', 'contact_id')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name');
                            }));
                    }));
            }))
            ->with(array('TechResponse' => function ($query) {
                $query->select('id', 'contact_id', 'tech_response_no')
                    ->with(array('Contact' => function ($query) {
                        $query->select('id', 'name');
                    }));
            }))
            ->find($request->id);

        $data = array(
            'item_issue' => $item_issue,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_item_issue_detail(Request $request)
    {
        $item_issue_detail = \App\Model\ItemIssueDetails::select('id', 'item_issue_id', 'item_id', 'quantity', 'warranty')
            ->with(array('ItemIssue' => function ($query) {
                $query->select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'item_issue_date_time', 'issued_to', 'remarks', 'item_issue_value', 'is_posted')
                    ->with(array('ItemIssueType' => function ($query) {
                        $query->select('id', 'name');
                    }))
                    ->with(array('Job' => function ($query) {
                        $query->select('id', 'inquiry_id', 'job_no')
                            ->with(array('Inquiry' => function ($query) {
                                $query->select('id', 'contact_id')
                                    ->with(array('Contact' => function ($query) {
                                        $query->select('id', 'name');
                                    }));
                            }));
                    }))
                    ->with(array('TechResponse' => function ($query) {
                        $query->select('id', 'contact_id', 'tech_response_no')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name');
                            }));
                    }));
            }))
            ->with(array('Item' => function ($query) {
                $query->select('id', 'main_category_id', 'sub_category_id', 'code', 'name', 'model_no', 'unit_type_id', 'is_serial', 'is_warranty')
                    ->with(array('MainItemCategory' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }))
                    ->with(array('SubItemCategory' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }))
                    ->with(array('UnitType' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }));
            }))
            ->with(array('ItemIssueBreakdown' => function ($query) {
                $query->select('id', 'item_issue_detail_id', 'type', 'detail_id', 'quantity')
                    ->with(array('GoodReceiveBreakdown' => function ($query) {
                        $query->select('id', 'good_receive_detail_id', 'serial_no')
                            ->with(array('GoodReceiveDetails' => function ($query) {
                                $query->select('id', 'model_no', 'brand', 'origin', 'rate');
                            }));
                    }))
                    ->with(array('GoodReceiveDetails' => function ($query) {
                        $query->select('id', 'model_no', 'brand', 'origin', 'rate');
                    }));
            }))
            ->find($request->id);
        return response($item_issue_detail);
    }

    public function item_issue_detail_list(Request $request)
    {
        $item_issue_details = \App\Model\ItemIssueDetails::select('id', 'item_issue_id', 'item_id', 'quantity', 'warranty')
            ->with(array('ItemIssue' => function ($query) {
                $query->select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'item_issue_date_time', 'issued_to', 'remarks', 'item_issue_value', 'is_posted')
                    ->with(array('ItemIssueType' => function ($query) {
                        $query->select('id', 'name');
                    }))
                    ->with(array('Job' => function ($query) {
                        $query->select('id', 'inquiry_id', 'job_no')
                            ->with(array('Inquiry' => function ($query) {
                                $query->select('id', 'contact_id')
                                    ->with(array('Contact' => function ($query) {
                                        $query->select('id', 'name');
                                    }));
                            }));
                    }))
                    ->with(array('TechResponse' => function ($query) {
                        $query->select('id', 'contact_id', 'tech_response_no')
                            ->with(array('Contact' => function ($query) {
                                $query->select('id', 'name');
                            }));
                    }));
            }))
            ->with(array('Item' => function ($query) {
                $query->select('id', 'main_category_id', 'sub_category_id', 'code', 'name', 'model_no', 'unit_type_id', 'is_serial', 'is_warranty')
                    ->with(array('MainItemCategory' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }))
                    ->with(array('SubItemCategory' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }))
                    ->with(array('UnitType' => function ($query) {
                        $query->select('id', 'code', 'name');
                    }));
            }))
            ->with(array('ItemIssueBreakdown' => function ($query) {
                $query->select('id', 'item_issue_detail_id', 'type', 'detail_id', 'quantity')
                    ->with(array('GoodReceiveBreakdown' => function ($query) {
                        $query->select('id', 'good_receive_detail_id', 'serial_no')
                            ->with(array('GoodReceiveDetails' => function ($query) {
                                $query->select('id', 'model_no', 'brand', 'origin', 'rate');
                            }));
                    }))
                    ->with(array('GoodReceiveDetails' => function ($query) {
                        $query->select('id', 'model_no', 'brand', 'origin', 'rate');
                    }));
            }))
            ->where('item_issue_id', $request->id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'item_issue_details' => $item_issue_details,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_balance_items(Request $request)
    {
        $data = array();
        $job_card_ids = $job_card_items = $installation_items = $issued_items = $received_items = array();

        if ($request->type == 1) {
            $job = \App\Model\Job::find($request->document_id);

            $quotations = \App\Model\Quotation::where(function ($q) use ($job) {
                $job ? $q->where('inquiry_id', $job->inquiry_id) : '';
            })
                ->where('is_confirmed', 1)
                ->where('is_revised', 0)
                ->where('is_delete', 0)
                ->get();
            foreach ($quotations as $quotation) {
                foreach ($quotation->QuotationJobCard as $detail) {
                    array_push($job_card_ids, $detail['id']);
                }
            }
            $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('id, item_id, SUM(quantity) as total_quantity')
                ->whereIn('quotation_job_card_id', $job_card_ids)
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($job_card_details as $job_card_detail) {
                $row = array(
                    'id' => $job_card_detail->Item->id,
                    'code' => $job_card_detail->Item->code,
                    'name' => $job_card_detail->Item->name,
                    'unit_type' => $job_card_detail->Item->UnitType->code,
                    'quantity' => $job_card_detail->total_quantity,
                    'stock' => $job_card_detail->Item->stock,
                    'is_serial' => $job_card_detail->Item->is_serial
                );
                array_push($job_card_items, $row);
            }
            $installation_sheet_details = \App\Model\InstallationSheetDetails::selectRaw('id, installation_sheet_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('InstallationSheet', function ($query) use ($job) {
                    $query->where('inquiry_id', $job->inquiry_id)->where('is_posted', 1)->where('is_approved', 1)->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($installation_sheet_details as $installation_sheet_detail) {
                $row = array(
                    'id' => $installation_sheet_detail->Item->id,
                    'code' => $installation_sheet_detail->Item->code,
                    'name' => $installation_sheet_detail->Item->name,
                    'unit_type' => $installation_sheet_detail->Item->UnitType->code,
                    'quantity' => $installation_sheet_detail->total_quantity,
                    'stock' => $installation_sheet_detail->Item->stock,
                    'is_serial' => $installation_sheet_detail->Item->is_serial
                );
                array_push($installation_items, $row);
            }
            $item_issue_details = \App\Model\ItemIssueDetails::selectRaw('id, item_issue_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('ItemIssue', function ($query) use ($job) {
                    $query->where('item_issue_type_id', 1)
                        ->where('document_id', $job->id)
                        ->where('is_posted', 1)
                        ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $row = array(
                    'id' => $item_issue_detail->Item->id,
                    'code' => $item_issue_detail->Item->code,
                    'name' => $item_issue_detail->Item->name,
                    'unit_type' => $item_issue_detail->Item->UnitType->code,
                    'quantity' => $item_issue_detail->total_quantity,
                    'stock' => $item_issue_detail->Item->stock,
                    'is_serial' => $item_issue_detail->Item->is_serial
                );
                array_push($issued_items, $row);
            }
            $item_issue_details = \App\Model\ItemIssueDetails::selectRaw('id, item_issue_id, item_id, SUM(quantity) as total_quantity')
                ->where('item_issue_id', $request->item_issue_id)
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $row = array(
                    'id' => $item_issue_detail->Item->id,
                    'code' => $item_issue_detail->Item->code,
                    'name' => $item_issue_detail->Item->name,
                    'unit_type' => $item_issue_detail->Item->UnitType->code,
                    'quantity' => $item_issue_detail->total_quantity,
                    'stock' => $item_issue_detail->Item->stock,
                    'is_serial' => $item_issue_detail->Item->is_serial
                );
                array_push($issued_items, $row);
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::selectRaw('id, item_receive_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('ItemReceive', function ($query) use ($job) {
                    $query->whereHas('ItemIssue', function ($query) use ($job) {
                        $query->where('item_issue_type_id', 1)
                            ->where('document_id', $job->id)
                            ->where('is_posted', 1)
                            ->where('is_delete', 0);
                    })
                        ->where('is_posted', 1)
                        ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $row = array(
                    'id' => $item_receive_detail->Item->id,
                    'code' => $item_receive_detail->Item->code,
                    'name' => $item_receive_detail->Item->name,
                    'unit_type' => $item_receive_detail->Item->UnitType->code,
                    'quantity' => $item_receive_detail->total_quantity,
                    'stock' => $item_receive_detail->Item->stock,
                    'is_serial' => $item_receive_detail->Item->is_serial
                );
                array_push($received_items, $row);
            }
        } else if ($request->type == 2) {
            $job_card_details = \App\Model\TechResponseJobCardDetails::selectRaw('id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('TechResponseJobCard', function ($query) use ($request) {
                    $query->where('tech_response_id', $request->document_id)
                        ->where('is_posted', 1)
                        ->where('is_approved', 1)
                        ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($job_card_details as $job_card_detail) {
                $row = array(
                    'id' => $job_card_detail->Item->id,
                    'code' => $job_card_detail->Item->code,
                    'name' => $job_card_detail->Item->name,
                    'unit_type' => $job_card_detail->Item->UnitType->code,
                    'quantity' => $job_card_detail->total_quantity,
                    'stock' => $job_card_detail->Item->stock,
                    'is_serial' => $job_card_detail->Item->is_serial
                );
                array_push($job_card_items, $row);
            }
            $installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::selectRaw('id, tech_response_installation_sheet_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('TechResponseInstallationSheet', function ($query) use ($request) {
                    $query->where('tech_response_id', $request->document_id)
                        ->where('is_posted', 1)
                        ->where('is_approved', 1)
                        ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($installation_sheet_details as $installation_sheet_detail) {
                $row = array(
                    'id' => $installation_sheet_detail->Item->id,
                    'code' => $installation_sheet_detail->Item->code,
                    'name' => $installation_sheet_detail->Item->name,
                    'unit_type' => $installation_sheet_detail->Item->UnitType->code,
                    'quantity' => $installation_sheet_detail->total_quantity,
                    'stock' => $installation_sheet_detail->Item->stock,
                    'is_serial' => $installation_sheet_detail->Item->is_serial
                );
                array_push($installation_items, $row);
            }
            $item_issue_details = \App\Model\ItemIssueDetails::selectRaw('id, item_issue_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('ItemIssue', function ($query) use ($request) {
                    $query->where('item_issue_type_id', 2)
                        ->where('document_id', $request->document_id)
                        ->where('is_posted', 1)
                        ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $row = array(
                    'id' => $item_issue_detail->Item->id,
                    'code' => $item_issue_detail->Item->code,
                    'name' => $item_issue_detail->Item->name,
                    'unit_type' => $item_issue_detail->Item->UnitType->code,
                    'quantity' => $item_issue_detail->total_quantity,
                    'stock' => $item_issue_detail->Item->stock,
                    'is_serial' => $item_issue_detail->Item->is_serial
                );
                array_push($issued_items, $row);
            }
            $item_issue_details = \App\Model\ItemIssueDetails::selectRaw('id, item_issue_id, item_id, SUM(quantity) as total_quantity')
                ->where('item_issue_id', $request->item_issue_id)
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($item_issue_details as $item_issue_detail) {
                $row = array(
                    'id' => $item_issue_detail->Item->id,
                    'code' => $item_issue_detail->Item->code,
                    'name' => $item_issue_detail->Item->name,
                    'unit_type' => $item_issue_detail->Item->UnitType->code,
                    'quantity' => $item_issue_detail->total_quantity,
                    'stock' => $item_issue_detail->Item->stock,
                    'is_serial' => $item_issue_detail->Item->is_serial
                );
                array_push($issued_items, $row);
            }
            $item_receive_details = \App\Model\ItemReceiveDetails::selectRaw('id, item_receive_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('ItemReceive', function ($query) use ($request) {
                    $query->whereHas('ItemIssue', function ($query) use ($request) {
                        $query->where('item_issue_type_id', 2)
                            ->where('document_id', $request->document_id)
                            ->where('is_posted', 1)
                            ->where('is_delete', 0);
                    })
                        ->where('is_posted', 1)
                        ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
            foreach ($item_receive_details as $item_receive_detail) {
                $row = array(
                    'id' => $item_receive_detail->Item->id,
                    'code' => $item_receive_detail->Item->code,
                    'name' => $item_receive_detail->Item->name,
                    'unit_type' => $item_receive_detail->Item->UnitType->code,
                    'quantity' => $item_receive_detail->total_quantity,
                    'stock' => $item_receive_detail->Item->stock,
                    'is_serial' => $item_receive_detail->Item->is_serial
                );
                array_push($received_items, $row);
            }
        }

        $request_ids = $request_items = array();
        foreach ($job_card_items as $job_card_main_item) {
            if (!in_array($job_card_main_item['id'], $request_ids)) {
                $total_qunatity = 0;
                foreach ($job_card_items as $job_card_sub_item) {
                    if ($job_card_main_item['id'] == $job_card_sub_item['id']) {
                        $total_qunatity += $job_card_sub_item['quantity'];
                    }
                }
                foreach ($installation_items as $installation_item) {
                    if ($job_card_main_item['id'] == $installation_item['id']) {
                        $total_qunatity += $installation_item['quantity'];
                    }
                }

                $row = array(
                    'id' => $job_card_main_item['id'],
                    'code' => $job_card_main_item['code'],
                    'name' => $job_card_main_item['name'],
                    'unit_type' => $job_card_main_item['unit_type'],
                    'quantity' => $total_qunatity,
                    'stock' => $job_card_main_item['stock'],
                    'is_serial' => $job_card_main_item['is_serial']
                );
                array_push($request_items, $row);
                array_push($request_ids, $job_card_main_item['id']);
            }
        }
        foreach ($installation_items as $installation_main_item) {
            if (!in_array($installation_main_item['id'], $request_ids)) {
                $total_qunatity = 0;
                foreach ($installation_items as $installation_sub_item) {
                    if ($installation_main_item['id'] == $installation_sub_item['id']) {
                        $total_qunatity += $installation_sub_item['quantity'];
                    }
                }

                $row = array(
                    'id' => $installation_main_item['id'],
                    'code' => $installation_main_item['code'],
                    'name' => $installation_main_item['name'],
                    'unit_type' => $installation_main_item['unit_type'],
                    'quantity' => $total_qunatity,
                    'stock' => $installation_main_item['stock'],
                    'is_serial' => $installation_main_item['is_serial']
                );
                array_push($request_items, $row);
                array_push($request_ids, $installation_main_item['id']);
            }
        }

        $item_issued_ids = $item_issued_details = array();
        foreach ($issued_items as $issued_item_main) {
            if (!in_array($issued_item_main['id'], $item_issued_ids)) {
                $total_qunatity = 0;
                foreach ($issued_items as $issued_item_sub) {
                    if ($issued_item_main['id'] == $issued_item_sub['id']) {
                        $total_qunatity += $issued_item_sub['quantity'];
                    }
                }
                foreach ($received_items as $received_item) {
                    if ($issued_item_main['id'] == $received_item['id']) {
                        $total_qunatity -= $received_item['quantity'];
                    }
                }

                $row = array(
                    'id' => $issued_item_main['id'],
                    'code' => $issued_item_main['code'],
                    'name' => $issued_item_main['name'],
                    'unit_type' => $issued_item_main['unit_type'],
                    'quantity' => $total_qunatity,
                    'stock' => $issued_item_main['stock'],
                    'is_serial' => $issued_item_main['is_serial']
                );
                array_push($item_issued_details, $row);
                array_push($item_issued_ids, $issued_item_main['id']);
            }
        }

        $count = 0;
        foreach ($request_items as $request_item) {
            $requested_quantity = $request_item['quantity'];
            foreach ($item_issued_details as $item_issued_detail) {
                if ($request_item['id'] == $item_issued_detail['id']) {
                    $requested_quantity -= $item_issued_detail['quantity'];
                }
            }
            if ($requested_quantity != 0 && $request_item['stock'] >= $requested_quantity) {
                $row = array(
                    'index' => $count,
                    'id' => $request_item['id'],
                    'is_serial' => $request_item['is_serial'],
                    'column' => $count + 1,
                    'code' => $request_item['code'],
                    'name' => $request_item['name'],
                    'unit_type' => $request_item['unit_type'],
                    'balance_quantity' => $requested_quantity,
                    'issue_quantity' => 0,
                    'issue_warranty' => 0
                );
                array_push($data, $row);
                $count++;
            }
        }

        return response($data);
    }

    public function bulk_item_issue(Request $request)
    {
        $exist = false;
        $item_issue = \App\Model\ItemIssue::find($request->item_issue_id);

        $item_issue_type_id = isset($request->item_issue_type['id']) ? $request->item_issue_type['id'] : 0;
        if (!$item_issue) {
            $exist = true;
            $item_issue = new \App\Model\ItemIssue();
            $last_id = 0;
            $last_item_issue = \App\Model\ItemIssue::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_item_issue ? $last_item_issue->id : $last_id;
            $item_issue_type = '';
            $item_issue_type = $item_issue_type_id == 1 ? 'JB' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 2 ? 'FC' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 3 ? 'OT' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 4 ? 'RE' : $item_issue_type;
            $item_issue->item_issue_no = 'IS/' . $item_issue_type . '/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
        }

        $item_issue->document_id = isset($request->document['id']) ? $request->document['id'] : 0;
        $item_issue->item_issue_type_id = $item_issue_type_id;
        $item_issue->item_issue_date_time = date('Y-m-d', strtotime($request->item_issue_date)) . ' ' . $request->item_issue_time;
        $item_issue->issued_to = $request->issued_to;
        $item_issue->remarks = $request->remarks;

        if ($item_issue->save()) {
            foreach ($request->bulk_list as $detail) {
                if ($detail['issue_quantity'] > 0) {
                    $old_item_issue_detail = \App\Model\ItemIssueDetails::where('item_issue_id', $item_issue->id)
                        ->where('item_id', $detail['id'])
                        ->first();
                    $item_issue_quantity = $detail['issue_quantity'];
                    if($old_item_issue_detail && $old_item_issue_detail->is_delete == 0){
                        $item_issue_quantity += $old_item_issue_detail->quantity;
                    }

                    $item_issue_detail = $old_item_issue_detail ? $old_item_issue_detail : new \App\Model\ItemIssueDetails();
                    $item_issue_detail->item_issue_id = $item_issue->id;
                    $item_issue_detail->item_id = $detail['id'];
                    $item_issue_detail->quantity = $item_issue_quantity;
                    $item_issue_detail->warranty = $detail['issue_warranty'];
                    $item_issue_detail->is_delete = 0;
                    $item_issue_detail->save();

                    $allocate_quantity = $item_issue_detail->quantity;
                    $good_receive_detail_ids = array();
                    while ($allocate_quantity > 0) {
                        $good_receive_detail = \App\Model\GoodReceiveDetails::whereNotIn('id', $good_receive_detail_ids)
                            ->where('item_id', $item_issue_detail->item_id)
                            ->where('available_quantity', '>', 0)
                            ->where('is_delete', 0)
                            ->with(array('GoodReceive' => function ($query) {
                                $query->orderBy('good_receive_date_time', 'ASC');
                            }))
                            ->first();
                        if ($good_receive_detail) {
                            if ($detail['is_serial'] == 1) {
                                $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('good_receive_detail_id', $good_receive_detail->id)
                                    ->where('is_main', 1)
                                    ->where('is_issued', 0)
                                    ->where('is_delete', 0)
                                    ->get();
                                foreach($good_receive_breakdowns as $good_receive_breakdown){
                                    $old_item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                                        ->where('type', 1)
                                        ->where('detail_id', $good_receive_breakdown->id)
                                        ->first();
                                    $item_issue_breakdown = $old_item_issue_breakdown ? $old_item_issue_breakdown : new \App\Model\ItemIssueBreakdown();
                                    $item_issue_breakdown->item_issue_detail_id = $item_issue_detail->id;
                                    $item_issue_breakdown->type = 1;
                                    $item_issue_breakdown->detail_id = $good_receive_breakdown->id;
                                    $item_issue_breakdown->quantity = 1;
                                    $item_issue_breakdown->is_delete = 0;
                                    $item_issue_breakdown->save();

                                    $allocate_quantity--;
                                    if($allocate_quantity == 0){
                                        break;
                                    }
                                }
                            } else {
                                $old_item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                                    ->where('type', 0)
                                    ->where('detail_id', $good_receive_detail->id)
                                    ->first();
                                $item_issue_breakdown = $old_item_issue_breakdown ? $old_item_issue_breakdown : new \App\Model\ItemIssueBreakdown();
                                $item_issue_breakdown->item_issue_detail_id = $item_issue_detail->id;
                                $item_issue_breakdown->type = 0;
                                $item_issue_breakdown->detail_id = $good_receive_detail->id;

                                if ($good_receive_detail->available_quantity >= $allocate_quantity) {
                                    $item_issue_breakdown->quantity = $allocate_quantity;
                                    $allocate_quantity = 0;
                                } else {
                                    $item_issue_breakdown->quantity = $good_receive_detail->available_quantity;
                                    $allocate_quantity -= $good_receive_detail->available_quantity;
                                }

                                $item_issue_breakdown->is_delete = 0;
                                $item_issue_breakdown->save();
                            }
                            array_push($good_receive_detail_ids, $good_receive_detail->id);
                        }
                    }
                }
            }
            $result = array(
                'response' => true,
                'message' => 'Bulk Item Issue Detail successfully',
                'data' => $item_issue->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Bulk Item Issue Detail failed'
            );
        }

        echo json_encode($result);
    }

    public function post_item_issue(Request $request)
    {
        $serial_exist = true;
        $item_exist = true;
        $error = '';

        $item_issue = \App\Model\ItemIssue::find($request->id);
        $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
            $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
        })
            ->where('is_delete', 0)
            ->get();
        foreach ($item_issue_breakdowns as $item_issue_breakdown) {
            if ($item_issue_breakdown->type == 1 && ($item_issue_breakdown->GoodReceiveBreakdown->is_issued != 0 || $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->available_quantity < $item_issue_breakdown->quantity)) {
                $serial_exist = false;
                $error .= $error == '' ? $item_issue_breakdown->ItemIssueDetails->Item->code . ':' . $item_issue_breakdown->GoodReceiveBreakdown->serial_no : ', ' . $item_issue_breakdown->ItemIssueDetails->Item->code . ':' . $item_issue_breakdown->GoodReceiveBreakdown->serial_no;
                //                $error .= ' '.$item_issue_breakdown->id;
            } else if ($item_issue_breakdown->type == 0 && $item_issue_breakdown->GoodReceiveDetails->available_quantity < $item_issue_breakdown->quantity) {
                $item_exist = false;
                $error .= $error == '' ? $item_issue_breakdown->ItemIssueDetails->Item->code : ', ' . $item_issue_breakdown->ItemIssueDetails->Item->code;
            }
        }

        if ($serial_exist && $item_exist) {
            $is_posted = $item_issue->is_posted == 0 ? true : false;
            $item_issue->is_posted = 1;
            $item_issue->save();
            if ($is_posted) {
                if ($item_issue->item_issue_type_id == 1) {
                    $inquiry = \App\Model\Inquiry::whereHas('Job', function ($query) use ($item_issue) {
                        $query->where('id', $item_issue->document_id);
                    })
                        ->where('is_delete', 0)
                        ->first();
                    if ($inquiry) {
                        $data = array(
                            'customer' => $inquiry->Contact->name,
                            'name' => $inquiry->SalesTeam->name,
                            'email' => $inquiry->SalesTeam->email,
                            'item_issue' => $item_issue
                        );

                        Mail::send('emails.item_issue_details', $data, function ($message) use ($data) {
                            $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                            $message->to($data['email'], $data['name']);
                            $message->cc('stores@m3force.com', 'Nalin Silva');
                            $message->subject('Item Issue Details (' . $data['customer'] . ')');
                        });
                    }
                } else if ($item_issue->item_issue_type_id == 2) {
                    $tech_response = \App\Model\TechResponse::find($item_issue->document_id);
                    if ($tech_response) {
                        $data = array(
                            'customer' => $tech_response->Contact->name,
                            'name' => 'Sanjaya Perera',
                            'email' => 'sanjaya@m3force.com',
                            'item_issue' => $item_issue
                        );

                        Mail::send('emails.item_issue_details', $data, function ($message) use ($data) {
                            $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                            $message->to($data['email'], $data['name']);
                            $message->cc('nilmini@m3force.com', 'Nilmini');
                            $message->cc('stores@m3force.com', 'Nalin Silva');
                            $message->subject('Item Issue Details (' . $data['customer'] . ')');
                        });
                    }
                } else {
                    $data = array(
                        'customer' => $item_issue->issued_to,
                        'name' => 'All',
                        'item_issue' => $item_issue
                    );

                    Mail::send('emails.item_issue_details', $data, function ($message) use ($data) {
                        $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                        $message->to('nilmini@m3force.com', 'Nilmini');
                        $message->to('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                        $message->to('palitha@m3force.com', 'Palitha Wickramathunga');
                        $message->cc('stores@m3force.com', 'Nalin Silva');
                        $message->subject('Item Issue Details (' . $data['customer'] . ')');
                    });
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Posted,' . $item_issue->id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
                    $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
                })
                    ->where('is_delete', 0)
                    ->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    if ($item_issue_breakdown->type == 1) {
                        $good_rececive_breakdown = \App\Model\GoodReceiveBreakdown::find($item_issue_breakdown->detail_id);
                        $good_rececive_breakdown->is_issued = 1;
                        $good_rececive_breakdown->save();

                        $good_receive_detail = \App\Model\GoodReceiveDetails::find($good_rececive_breakdown->good_receive_detail_id);
                        $good_receive_detail->available_quantity -= $item_issue_breakdown->quantity;
                        $good_receive_detail->save();

                        $item = \App\Model\Item::find($good_receive_detail->item_id);
                        $item->stock -= $item_issue_breakdown->quantity;
                        $item->save();

                        $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('main_id', $item_issue_breakdown->detail_id)
                            ->where('id', '!=', $item_issue_breakdown->detail_id)
                            ->where('is_delete', 0)
                            ->update(['is_issued' => 1]);
                    } else if ($item_issue_breakdown->type == 0) {
                        $good_receive_detail = \App\Model\GoodReceiveDetails::find($item_issue_breakdown->detail_id);
                        $good_receive_detail->available_quantity -= $item_issue_breakdown->quantity;
                        $good_receive_detail->save();

                        $item = \App\Model\Item::find($good_receive_detail->item_id);
                        $item->stock -= $item_issue_breakdown->quantity;
                        $item->save();
                    }
                }

                $result = array(
                    'response' => true,
                    'message' => 'Item Issue posted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Item Issue post failed'
                );
            }
        } else if (!$serial_exist) {
            $result = array(
                'response' => false,
                'message' => $error . ' Serial Nos does not exist in the stock.'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => $error . ' items does not exist in the stock.'
            );
        }

        echo json_encode($result);
    }

    public function print_item_issue(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $item_issue = \App\Model\ItemIssue::find($request->id);
        $data['item_issue'] = $item_issue;
        $title = $item_issue ? 'Item Issue Details ' . $item_issue->item_issue_no : 'Item Issue Details';

        $html = view('stock.item_issue_pdf', $data);

        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'] . '/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="' . $title . '.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 5,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Portrait',
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
        $exist = false;
        $item_issue = \App\Model\ItemIssue::find($request->item_issue_id);

        $item_issue_type_id = isset($request->item_issue_type['id']) ? $request->item_issue_type['id'] : 0;
        if (!$item_issue) {
            $exist = true;
            $item_issue = new \App\Model\ItemIssue();
            $last_id = 0;
            $last_item_issue = \App\Model\ItemIssue::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_item_issue ? $last_item_issue->id : $last_id;
            $item_issue_type = '';
            $item_issue_type = $item_issue_type_id == 1 ? 'JB' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 2 ? 'FC' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 3 ? 'OT' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 4 ? 'RE' : $item_issue_type;
            $item_issue->item_issue_no = 'IS/' . $item_issue_type . '/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
        }

        $item_issue->document_id = isset($request->document['id']) ? $request->document['id'] : 0;
        $item_issue->item_issue_type_id = $item_issue_type_id;
        $item_issue->item_issue_date_time = date('Y-m-d', strtotime($request->item_issue_date)) . ' ' . $request->item_issue_time;
        $item_issue->issued_to = $request->issued_to;
        $item_issue->remarks = $request->remarks;

        if ($item_issue->save()) {
            $item_issue_detail_id = '';
            if (isset($request->item['id'])) {
                $old_item_issue_detail = \App\Model\ItemIssueDetails::where('item_issue_id', $item_issue->id)
                    ->where('item_id', $request->item['id'])
                    ->first();

                $item_issue_detail = $old_item_issue_detail ? $old_item_issue_detail : new \App\Model\ItemIssueDetails();
                $item_issue_detail->item_issue_id = $item_issue->id;
                $item_issue_detail->item_id = $request->item['id'];
                $item_issue_detail->quantity = $request->quantity;
                $item_issue_detail->warranty = $request->warranty;
                $item_issue_detail->is_delete = 0;
                $item_issue_detail->save();

                $item_issue_detail_id = $item_issue_detail->id;

                if ($request->item['is_serial'] == 1) {
                    foreach ($request->serial_details as $detail) {
                        $old_item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                            ->where('type', 1)
                            ->where('detail_id', $detail['id'])
                            ->first();
                        $item_issue_breakdown = $old_item_issue_breakdown ? $old_item_issue_breakdown : new \App\Model\ItemIssueBreakdown();
                        $item_issue_breakdown->item_issue_detail_id = $item_issue_detail->id;
                        $item_issue_breakdown->type = 1;
                        $item_issue_breakdown->detail_id = $detail['id'];
                        $item_issue_breakdown->quantity = 1;
                        $item_issue_breakdown->is_delete = 0;
                        $item_issue_breakdown->save();
                    }
                } else {
                    $allocate_quantity = $request->quantity;
                    $good_receive_detail_ids = array();
                    while ($allocate_quantity > 0) {
                        $good_receive_detail = \App\Model\GoodReceiveDetails::whereNotIn('id', $good_receive_detail_ids)
                            ->where('item_id', $item_issue_detail->item_id)
                            ->where('available_quantity', '>', 0)
                            ->where('is_delete', 0)
                            ->with(array('GoodReceive' => function ($query) {
                                $query->orderBy('good_receive_date_time', 'ASC');
                            }))
                            ->first();
                        if ($good_receive_detail) {
                            $old_item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                                ->where('type', 0)
                                ->where('detail_id', $good_receive_detail->id)
                                ->first();
                            $item_issue_breakdown = $old_item_issue_breakdown ? $old_item_issue_breakdown : new \App\Model\ItemIssueBreakdown();
                            $item_issue_breakdown->item_issue_detail_id = $item_issue_detail->id;
                            $item_issue_breakdown->type = 0;
                            $item_issue_breakdown->detail_id = $good_receive_detail->id;

                            if ($good_receive_detail->available_quantity >= $allocate_quantity) {
                                $item_issue_breakdown->quantity = $allocate_quantity;
                                $allocate_quantity = 0;
                            } else {
                                $item_issue_breakdown->quantity = $good_receive_detail->available_quantity;
                                $allocate_quantity -= $good_receive_detail->available_quantity;
                            }

                            $item_issue_breakdown->is_delete = 0;
                            $item_issue_breakdown->save();

                            array_push($good_receive_detail_ids, $good_receive_detail->id);
                        }
                    }
                }
            }

            $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
                $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
            })
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                $total_value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
            }
            $item_issue->item_issue_value = $total_value;
            $item_issue->save();

            if ($item_issue_detail_id != '') {
                $item_issue_detail = \App\Model\ItemIssueDetails::find($item_issue_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $item_issue->id . ',' . $item_issue->item_issue_no . ',' . $item_issue->document_id . ',' . $item_issue->item_issue_type_id . ',' . $item_issue->item_issue_date_time . ',' . $item_issue->item_issue_value . ',' . str_replace(',', ' ', $item_issue->issued_to) . ',' . str_replace(',', ' ', $item_issue->remarks) . ',' . $item_issue_detail->id . ',' . $item_issue_detail->item_id . ',' . $item_issue_detail->quantity . ',' . $item_issue_detail->warranty . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $item_issue->id . ',' . $item_issue->item_issue_no . ',' . $item_issue->document_id . ',' . $item_issue->item_issue_type_id . ',' . $item_issue->item_issue_date_time . ',' . $item_issue->item_issue_value . ',' . str_replace(',', ' ', $item_issue->issued_to) . ',' . str_replace(',', ' ', $item_issue->remarks) . ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            }

            $result = array(
                'response' => true,
                'message' => 'Item Issue Detail created successfully',
                'data' => $item_issue->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item Issue Detail creation failed'
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
        $item_issue = \App\Model\ItemIssue::find($request->item_issue_id);
        $item_issue_type_id = isset($request->item_issue_type['id']) ? $request->item_issue_type['id'] : 0;
        if ($item_issue->item_issue_type_id != $item_issue_type_id) {
            $last_id = 0;
            $last_item_issue = \App\Model\ItemIssue::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_item_issue ? $last_item_issue->id : $last_id;
            $item_issue_type = '';
            $item_issue_type = $item_issue_type_id == 1 ? 'JB' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 2 ? 'FC' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 3 ? 'OT' : $item_issue_type;
            $item_issue_type = $item_issue_type_id == 4 ? 'RE' : $item_issue_type;
            $item_issue->item_issue_no = 'IS/' . $item_issue_type . '/' . date('m') . '/' . date('y') . '/' . sprintf('%05d', $last_id + 1);
        } else {
            $item_issue->item_issue_no = $request->item_issue_no;
        }
        $item_issue->item_issue_type_id = $item_issue_type_id;
        $item_issue->document_id = isset($request->document['id']) ? $request->document['id'] : 0;
        $item_issue->item_issue_date_time = date('Y-m-d', strtotime($request->item_issue_date)) . ' ' . $request->item_issue_time;
        $item_issue->issued_to = $request->issued_to;
        $item_issue->remarks = $request->remarks;

        if ($item_issue->save()) {
            $item_issue_detail_id = '';
            if (isset($request->item['id'])) {
                $item_issue_detail = \App\Model\ItemIssueDetails::find($id);
                $item_issue_detail->is_delete = 1;
                $item_issue_detail->save();
                $item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);

                $old_item_issue_detail = \App\Model\ItemIssueDetails::where('item_issue_id', $item_issue->id)
                    ->where('item_id', $request->item['id'])
                    ->first();

                $item_issue_detail = $old_item_issue_detail ? $old_item_issue_detail : new \App\Model\ItemIssueDetails();
                $item_issue_detail->item_issue_id = $item_issue->id;
                $item_issue_detail->item_id = $request->item['id'];
                $item_issue_detail->quantity = $request->quantity;
                $item_issue_detail->warranty = $request->warranty;
                $item_issue_detail->is_delete = 0;
                $item_issue_detail->save();

                $item_issue_detail_id = $item_issue_detail->id;

                if ($request->item['is_serial'] == 1) {
                    foreach ($request->serial_details as $detail) {
                        $old_item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                            ->where('type', 1)
                            ->where('detail_id', $detail['id'])
                            ->first();
                        $item_issue_breakdown = $old_item_issue_breakdown ? $old_item_issue_breakdown : new \App\Model\ItemIssueBreakdown();
                        $item_issue_breakdown->item_issue_detail_id = $item_issue_detail->id;
                        $item_issue_breakdown->type = 1;
                        $item_issue_breakdown->detail_id = $detail['id'];
                        $item_issue_breakdown->quantity = 1;
                        $item_issue_breakdown->is_delete = 0;
                        $item_issue_breakdown->save();
                    }
                } else {
                    $allocate_quantity = $request->quantity;
                    $good_receive_detail_ids = array();
                    while ($allocate_quantity > 0) {
                        $good_receive_detail = \App\Model\GoodReceiveDetails::whereNotIn('id', $good_receive_detail_ids)
                            ->where('item_id', $item_issue_detail->item_id)
                            ->where('available_quantity', '>', 0)
                            ->where('is_delete', 0)
                            ->with(array('GoodReceive' => function ($query) {
                                $query->orderBy('good_receive_date_time', 'ASC');
                            }))
                            ->first();
                        if ($good_receive_detail) {
                            $old_item_issue_breakdown = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)
                                ->where('type', 0)
                                ->where('detail_id', $good_receive_detail->id)
                                ->first();
                            $item_issue_breakdown = $old_item_issue_breakdown ? $old_item_issue_breakdown : new \App\Model\ItemIssueBreakdown();
                            $item_issue_breakdown->item_issue_detail_id = $item_issue_detail->id;
                            $item_issue_breakdown->type = 0;
                            $item_issue_breakdown->detail_id = $good_receive_detail->id;

                            if ($good_receive_detail->available_quantity >= $allocate_quantity) {
                                $item_issue_breakdown->quantity = $allocate_quantity;
                                $allocate_quantity = 0;
                            } else {
                                $item_issue_breakdown->quantity = $good_receive_detail->available_quantity;
                                $allocate_quantity -= $good_receive_detail->available_quantity;
                            }

                            $item_issue_breakdown->is_delete = 0;
                            $item_issue_breakdown->save();

                            array_push($good_receive_detail_ids, $good_receive_detail->id);
                        }
                    }
                }
            }

            $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
                $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
            })
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                $total_value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
            }
            $item_issue->item_issue_value = $total_value;
            $item_issue->save();

            if ($item_issue_detail_id != '') {
                $item_issue_detail = \App\Model\ItemIssueDetails::find($item_issue_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $item_issue->id . ',' . $item_issue->item_issue_no . ',' . $item_issue->document_id . ',' . $item_issue->item_issue_type_id . ',' . $item_issue->item_issue_date_time . ',' . $item_issue->item_issue_value . ',' . str_replace(',', ' ', $item_issue->issued_to) . ',' . str_replace(',', ' ', $item_issue->remarks) . ',' . $item_issue_detail->id . ',' . $item_issue_detail->item_id . ',' . $item_issue_detail->quantity . ',' . $item_issue_detail->warranty . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $item_issue->id . ',' . $item_issue->item_issue_no . ',' . $item_issue->document_id . ',' . $item_issue->item_issue_type_id . ',' . $item_issue->item_issue_date_time . ',' . $item_issue->item_issue_value . ',' . str_replace(',', ' ', $item_issue->issued_to) . ',' . str_replace(',', ' ', $item_issue->remarks) . ',,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            }

            $result = array(
                'response' => true,
                'message' => 'Item Issue Detail updated successfully',
                'data' => $item_issue->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item Issue Detail updation failed'
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
    public function destroy($id, Request $request)
    {
        if ($request->type == 0) {
            $item_issue = \App\Model\ItemIssue::find($id);
            $item_issue->is_delete = 1;

            if ($item_issue->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $item_issue->id . ',,,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $item_issue_details = \App\Model\ItemIssueDetails::where('item_issue_id', $item_issue->id)->where('is_delete', 0)->get();
                foreach ($item_issue_details as $item_issue_detail) {
                    $item_issue_detail->is_delete = 1;
                    $item_issue_detail->save();

                    $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)->where('is_delete', 0)->get();
                    foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                        $item_issue_breakdown->is_delete = 1;
                        $item_issue_breakdown->save();
                    }
                }

                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
                    $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
                })
                    ->where('is_delete', 0)
                    ->get();
                $total_value = 0;
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    $total_value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                }
                $item_issue->item_issue_value = $total_value;
                $item_issue->save();

                $result = array(
                    'response' => true,
                    'message' => 'Item Issue deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Item Issue deletion failed'
                );
            }
        } else if ($request->type == 1) {
            $item_issue_detail = \App\Model\ItemIssueDetails::find($id);
            $item_issue_detail->quantity = 0;
            $item_issue_detail->is_delete = 1;

            if ($item_issue_detail->save()) {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_issue_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $item_issue_detail->item_issue_id . ',,,,,,,,' . $item_issue_detail->id . ',,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::where('item_issue_detail_id', $item_issue_detail->id)->where('is_delete', 0)->get();
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    $item_issue_breakdown->is_delete = 1;
                    $item_issue_breakdown->save();
                }

                $item_issue = \App\Model\ItemIssue::find($item_issue_detail->item_issue_id);
                $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($item_issue) {
                    $query->where('item_issue_id', $item_issue->id)->where('is_delete', 0);
                })
                    ->where('is_delete', 0)
                    ->get();
                $total_value = 0;
                foreach ($item_issue_breakdowns as $item_issue_breakdown) {
                    $total_value += $item_issue_breakdown->type == 1 ? $item_issue_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity : $item_issue_breakdown->GoodReceiveDetails->rate * $item_issue_breakdown->quantity;
                }
                $item_issue->item_issue_value = $total_value;
                $item_issue->save();

                $result = array(
                    'response' => true,
                    'message' => 'Item Issue Detail deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Item Issue Detail deletion failed'
                );
            }
        } else {
            $result = array(
                'response' => false,
                'message' => 'Deletion failed'
            );
        }

        echo json_encode($result);
    }
}
