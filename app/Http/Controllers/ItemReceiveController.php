<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class ItemReceiveController extends Controller
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

        return view('stock.item_receive', $data);
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

        $data['item_receive_id'] = $request->id;

        return view('stock.item_receive_detail', $data);
    }

    public function validate_item_issue_no(Request $request)
    {
        $item_issue = \App\Model\ItemIssue::where('item_issue_no', $request->item_issue_no)
            ->where('is_posted', 1)
            ->where('is_delete', 0)
            ->first();

        if ($item_issue && $item_issue->item_issue_type_id == 1 && $item_issue->Job->is_completed == 0) {
            $response = 'true';
        } else if ($item_issue && $item_issue->item_issue_type_id == 2 && $item_issue->TechResponse->is_completed == 0) {
            $response = 'true';
        } else if ($item_issue && ($item_issue->item_issue_type_id == 3 || $item_issue->item_issue_type_id == 4)) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_item_code(Request $request)
    {
        if ($request->code != $request->item_code) {
            $item_issue_detail = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->where('id', $request->item_issue_id);
            })
                ->whereHas('Item', function ($query) use ($request) {
                    $query->where('code', $request->code);
                })
                ->where('is_delete', 0)
                ->first();
            $item_receive_detail = \App\Model\ItemReceiveDetails::where('item_receive_id', $request->item_receive_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->first();
            if ($item_issue_detail && !$item_receive_detail) {
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
            $item_issue_detail = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
                $query->where('id', $request->item_issue_id);
            })
                ->whereHas('Item', function ($query) use ($request) {
                    $query->where('name', $request->name);
                })
                ->where('is_delete', 0)
                ->first();
            $item_receive_detail = \App\Model\ItemReceiveDetails::where('item_receive_id', $request->item_receive_id)
                ->where('item_id', $request->item_id)
                ->where('is_delete', 0)
                ->first();
            if ($item_issue_detail && !$item_receive_detail) {
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
        $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use ($request) {
            $query->where('id', $request->item_issue_id);
        })
            ->where('item_id', $request->item_id)
            ->where('is_delete', 0)
            ->get();
        $total_quantity = 0;
        foreach ($item_issue_details as $item_issue_detail) {
            $total_quantity += $item_issue_detail->quantity;
        }
        $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use ($request) {
            $query->where('item_issue_id', $request->item_issue_id);
        })
            ->where('id', '!=', $request->item_receive_id)
            ->where('item_id', $request->item_id)
            ->where('is_delete', 0)
            ->get();
        foreach ($item_receive_details as $item_receive_detail) {
            $total_quantity -= $item_receive_detail->quantity;
        }

        if ($total_quantity >= $request->quantity) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function validate_serial_no(Request $request)
    {
        $item_issue_breakdown = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $request->item_issue_id && $request->item_issue_id != '' ? $q->where('item_issue_id', $request->item_issue_id) : '';
            })
                ->where(function ($q) use ($request) {
                    $request->item_id && $request->item_id != '' ? $q->where('item_id', $request->item_id) : '';
                });
        })
            ->whereHas('GoodReceiveBreakdown', function ($query) use ($request) {
                $query->where('serial_no', $request->serial_no)
                    ->where('is_issued', 1);
            })
            ->where('type', 1)
            ->where('is_delete', 0)
            ->first();
        if ($item_issue_breakdown) {
            $response = 'true';
        } else {
            $response = 'false';
        }

        echo $response;
    }

    public function get_data()
    {
        $main_item_categories = \App\Model\MainItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();
        $sub_item_categories = \App\Model\SubItemCategory::select('id', 'code', 'name')->where('is_delete', 0)->orderBy('name')->get();

        $data = array(
            'main_item_categories' => $main_item_categories,
            'sub_item_categories' => $sub_item_categories
        );

        return response($data);
    }

    public function get_serial_nos(Request $request)
    {
        $item_issue_breakdowns = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $request->item_issue_id && $request->item_issue_id != '' ? $q->where('item_issue_id', $request->item_issue_id) : '';
            })
                ->where(function ($q) use ($request) {
                    $request->item_id && $request->item_id != '' ? $q->where('item_id', $request->item_id) : '';
                });
        })
            ->whereHas('GoodReceiveBreakdown', function ($query) use ($request) {
                $query->where('serial_no', 'like', '%' . $request->serial_no . '%')->where('is_issued', 1);
            })
            ->with(array('GoodReceiveBreakdown' => function ($query) {
                $query->select('id', 'serial_no')->orderBy('serial_no');
            }))
            ->where('type', 1)
            ->where('is_delete', 0)
            ->get();
        return response($item_issue_breakdowns);
    }

    public function find_serial_no(Request $request)
    {
        $item_issue_breakdown = \App\Model\ItemIssueBreakdown::whereHas('ItemIssueDetails', function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $request->item_issue_id && $request->item_issue_id != '' ? $q->where('item_issue_id', $request->item_issue_id) : '';
            })
                ->where(function ($q) use ($request) {
                    $request->item_id && $request->item_id != '' ? $q->where('item_id', $request->item_id) : '';
                });
        })
            ->whereHas('GoodReceiveBreakdown', function ($query) use ($request) {
                $query->where('serial_no', 'like', '%' . $request->serial_no . '%')->where('is_issued', 1);
            })
            ->with(array('GoodReceiveBreakdown' => function ($query) {
                $query->select('id', 'serial_no')->orderBy('serial_no');
            }))
            ->where('type', 1)
            ->where('is_delete', 0)
            ->first();
        return response($item_issue_breakdown);
    }

    public function item_receive_list(Request $request)
    {
        $item_receives = \App\Model\ItemReceive::select('id', 'item_issue_id', 'item_receive_no', 'item_receive_date_time', 'remarks', 'item_receive_value', 'is_posted')
            ->with(array('ItemIssue' => function ($query) {
                $query->select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'issued_to')
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
            ->whereBetween('item_receive_date_time', array($request->from . ' 00:01', $request->to . ' 23:59'))
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'item_receives' => $item_receives,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_item_receive(Request $request)
    {
        $item_receive = \App\Model\ItemReceive::select('id', 'item_issue_id', 'item_receive_no', 'item_receive_date_time', 'remarks', 'item_receive_value', 'is_posted')
            ->with(array('ItemIssue' => function ($query) {
                $query->select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'issued_to')
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
            ->find($request->id);

        $data = array(
            'item_receive' => $item_receive,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function find_item_receive_detail(Request $request)
    {
        $item_receive_detail = \App\Model\ItemReceiveDetails::select('id', 'item_receive_id', 'item_id', 'quantity')
            ->with(array('ItemReceive' => function ($query) {
                $query->select('id', 'item_issue_id', 'item_receive_no', 'item_receive_date_time', 'remarks', 'item_receive_value', 'is_posted')
                    ->with(array('ItemIssue' => function ($query) {
                        $query->select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'issued_to')
                            ->with(array('Job' => function ($query) {
                                $query->select('id', 'job_no');
                            }))
                            ->with(array('TechResponse' => function ($query) {
                                $query->select('id', 'tech_response_no');
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
            ->with(array('ItemReceiveBreakdown' => function ($query) {
                $query->select('id', 'item_receive_detail_id', 'type', 'detail_id', 'quantity')
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
        return response($item_receive_detail);
    }

    public function item_receive_detail_list(Request $request)
    {
        $item_receive_details = \App\Model\ItemReceiveDetails::select('id', 'item_receive_id', 'item_id', 'quantity')
            ->with(array('ItemReceive' => function ($query) {
                $query->select('id', 'item_issue_id', 'item_receive_no', 'item_receive_date_time', 'remarks', 'item_receive_value', 'is_posted')
                    ->with(array('ItemIssue' => function ($query) {
                        $query->select('id', 'item_issue_type_id', 'document_id', 'item_issue_no', 'issued_to')
                            ->with(array('Job' => function ($query) {
                                $query->select('id', 'job_no');
                            }))
                            ->with(array('TechResponse' => function ($query) {
                                $query->select('id', 'tech_response_no');
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
            ->with(array('ItemReceiveBreakdown' => function ($query) {
                $query->select('id', 'item_receive_detail_id', 'type', 'detail_id', 'quantity')
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
            ->where('item_receive_id', $request->id)
            ->where('is_delete', 0)
            ->get();

        $data = array(
            'item_receive_details' => $item_receive_details,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );

        return response($data);
    }

    public function post_item_receive(Request $request)
    {
        $item_receive = \App\Model\ItemReceive::find($request->id);
        $is_posted = $item_receive->is_posted == 0 ? true : false;
        $item_receive->is_posted = 1;
        $item_receive->save();

        if ($is_posted) {
            if ($item_receive->ItemIssue->item_issue_type_id == 1) {
                $inquiry = \App\Model\Inquiry::whereHas('Job', function ($query) use ($item_receive) {
                    $query->where('id', $item_receive->ItemIssue->document_id);
                })
                    ->where('is_delete', 0)
                    ->first();
                if ($inquiry) {
                    $data = array(
                        'customer' => $inquiry->Contact->name,
                        'name' => $inquiry->SalesTeam->name,
                        'email' => $inquiry->SalesTeam->email,
                        'item_receive' => $item_receive
                    );

                    Mail::send('emails.item_receive_details', $data, function ($message) use ($data) {
                        $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                        $message->to($data['email'], $data['name']);
                        $message->to('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                        $message->to('palitha@m3force.com', 'Palitha Wickramathunga');
                        $message->cc('stores@m3force.com', 'Nalin Silva');
                        $message->subject('Item Return Details (' . $data['customer'] . ')');
                    });
                }
            } else if ($item_receive->ItemIssue->item_issue_type_id == 2) {
                $tech_response = \App\Model\TechResponse::find($item_receive->ItemIssue->document_id);
                if ($tech_response) {
                    $data = array(
                        'customer' => $tech_response->Contact->name,
                        'name' => 'Sanjaya Perera',
                        'email' => 'sanjaya@m3force.com',
                        'item_receive' => $item_receive
                    );

                    Mail::send('emails.item_receive_details', $data, function ($message) use ($data) {
                        $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                        $message->to($data['email'], $data['name']);
                        $message->to('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                        $message->to('palitha@m3force.com', 'Palitha Wickramathunga');
                        $message->cc('stores@m3force.com', 'Nalin Silva');
                        $message->subject('Item Return Details (' . $data['customer'] . ')');
                    });
                }
            } else {
                $data = array(
                    'customer' => $item_receive->ItemIssue->issued_to,
                    'name' => 'All',
                    'item_receive' => $item_receive
                );

                Mail::send('emails.item_receive_details', $data, function ($message) use ($data) {
                    $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                    $message->to('nilmini@m3force.com', 'Nilmini');
                    $message->to('chinthaka@m3force.com', 'Chinthaka Deshapriya');
                    $message->to('palitha@m3force.com', 'Palitha Wickramathunga');
                    $message->cc('stores@m3force.com', 'Nalin Silva');
                    $message->subject('Item Return Details (' . $data['customer'] . ')');
                });
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Posted,' . $item_receive->id . ',,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
            fclose($myfile);

            $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::whereHas('ItemReceiveDetails', function ($query) use ($item_receive) {
                $query->where('item_receive_id', $item_receive->id)->where('is_delete', 0);
            })
                ->where('is_delete', 0)
                ->get();
            foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                if ($item_receive_breakdown->type == 1) {
                    $good_rececive_breakdown = \App\Model\GoodReceiveBreakdown::find($item_receive_breakdown->detail_id);
                    $good_rececive_breakdown->is_issued = 0;
                    $good_rececive_breakdown->save();

                    $good_receive_detail = \App\Model\GoodReceiveDetails::find($good_rececive_breakdown->good_receive_detail_id);
                    $good_receive_detail->available_quantity += $item_receive_breakdown->quantity;
                    $good_receive_detail->save();

                    $item = \App\Model\Item::find($good_receive_detail->item_id);
                    $item->stock += $item_receive_breakdown->quantity;
                    $item->save();

                    $good_receive_breakdowns = \App\Model\GoodReceiveBreakdown::where('main_id', $item_receive_breakdown->detail_id)
                        ->where('id', '!=', $item_receive_breakdown->detail_id)
                        ->where('is_delete', 0)
                        ->update(['is_issued' => 0]);
                } else if ($item_receive_breakdown->type == 0) {
                    $good_receive_detail = \App\Model\GoodReceiveDetails::find($item_receive_breakdown->detail_id);
                    $good_receive_detail->available_quantity += $item_receive_breakdown->quantity;
                    $good_receive_detail->save();

                    $item = \App\Model\Item::find($good_receive_detail->item_id);
                    $item->stock += $item_receive_breakdown->quantity;
                    $item->save();
                }
            }

            $result = array(
                'response' => true,
                'message' => 'Item Receive posted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item Receive post failed'
            );
        }

        echo json_encode($result);
    }

    public function print_item_receive(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $item_receive = \App\Model\ItemReceive::find($request->id);
        $data['item_receive'] = $item_receive;
        $title = $item_receive ? 'Item Receive Details ' . $item_receive->item_receive_no : 'Item Receive Details';

        $html = view('stock.item_receive_pdf', $data);

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
        $item_receive = \App\Model\ItemReceive::find($request->item_receive_id);

        $item_issue_id = isset($request->item_issue['id']) ? $request->item_issue['id'] : 0;
        if (!$item_receive) {
            $item_receive = new \App\Model\ItemReceive();
            $last_id = 0;
            $last_item_receive = \App\Model\ItemReceive::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_item_receive ? $last_item_receive->id : $last_id;
            $item_receive->item_receive_no = 'IR/' . date('m') . '/' . date('y') . '/' . $item_issue_id . '/' . sprintf('%05d', $last_id + 1);
        }

        $item_receive->item_issue_id = $item_issue_id;
        $item_receive->item_receive_date_time = date('Y-m-d', strtotime($request->item_receive_date)) . ' ' . $request->item_receive_time;
        $item_receive->remarks = $request->remarks;

        if ($item_receive->save()) {
            $item_receive_detail_id = '';
            if (isset($request->item['id'])) {
                $old_item_receive_detail = \App\Model\ItemReceiveDetails::where('item_receive_id', $item_receive->id)
                    ->where('item_id', $request->item['id'])
                    ->first();

                $item_receive_detail = $old_item_receive_detail ? $old_item_receive_detail : new \App\Model\ItemReceiveDetails();
                $item_receive_detail->item_receive_id = $item_receive->id;
                $item_receive_detail->item_id = $request->item['id'];
                $item_receive_detail->quantity = $request->quantity;
                $item_receive_detail->is_delete = 0;
                $item_receive_detail->save();

                $item_receive_detail_id = $item_receive_detail->id;

                if ($request->item['is_serial'] == 1) {
                    foreach ($request->serial_details as $detail) {
                        $old_item_receive_breakdown = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                            ->where('type', 1)
                            ->where('detail_id', $detail['id'])
                            ->first();
                        $item_receive_breakdown = $old_item_receive_breakdown ? $old_item_receive_breakdown : new \App\Model\ItemReceiveBreakdown();
                        $item_receive_breakdown->item_receive_detail_id = $item_receive_detail->id;
                        $item_receive_breakdown->type = 1;
                        $item_receive_breakdown->detail_id = $detail['id'];
                        $item_receive_breakdown->quantity = 1;
                        $item_receive_breakdown->is_delete = 0;
                        $item_receive_breakdown->save();
                    }
                } else {
                    $allocate_quantity = $request->quantity;
                    $item_issue_breakdown_ids = array();
                    while ($allocate_quantity > 0) {
                        $item_issue_breakdown = \App\Model\ItemIssueBreakdown::whereNotIn('id', $item_issue_breakdown_ids)
                            ->whereHas('ItemIssueDetails', function ($query) use ($item_receive_detail) {
                                $query->where('item_issue_id', $item_receive_detail->ItemReceive->item_issue_id)
                                    ->where('item_id', $item_receive_detail->item_id)
                                    ->where('is_delete', 0);
                            })
                            ->where('type', 0)
                            ->where('is_delete', 0)
                            ->orderBy('quantity', 'DESC')
                            ->first();
                        if ($item_issue_breakdown) {
                            $old_item_receive_breakdown = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                                ->where('type', 0)
                                ->where('detail_id', $item_issue_breakdown->GoodReceiveDetails->id)
                                ->first();
                            $item_receive_breakdown = $old_item_receive_breakdown ? $old_item_receive_breakdown : new \App\Model\ItemReceiveBreakdown();
                            $item_receive_breakdown->item_receive_detail_id = $item_receive_detail->id;
                            $item_receive_breakdown->type = 0;
                            $item_receive_breakdown->detail_id = $item_issue_breakdown->GoodReceiveDetails->id;

                            if ($item_issue_breakdown->quantity >= $allocate_quantity) {
                                $item_receive_breakdown->quantity = $allocate_quantity;
                                $allocate_quantity = 0;
                            } else {
                                $item_receive_breakdown->quantity = $item_issue_breakdown->quantity;
                                $allocate_quantity -= $item_issue_breakdown->quantity;
                            }

                            $item_receive_breakdown->is_delete = 0;
                            $item_receive_breakdown->save();

                            array_push($item_issue_breakdown_ids, $item_issue_breakdown->id);
                        }
                    }
                }
            }

            $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::whereHas('ItemReceiveDetails', function ($query) use ($item_receive) {
                $query->where('item_receive_id', $item_receive->id)->where('is_delete', 0);
            })
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                $total_value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
            }
            $item_receive->item_receive_value = $total_value;
            $item_receive->save();

            if ($item_receive_detail_id != '') {
                $item_receive_detail = \App\Model\ItemReceiveDetails::find($item_receive_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $item_receive->id . ',' . $item_receive->item_receive_no . ',' . $item_receive->item_issue_id . ',' . $item_receive->item_receive_date_time . ',' . $item_receive->item_receive_value . ',' . str_replace(',', ' ', $item_receive->remarks) . ',' . $item_receive_detail->id . ',' . $item_receive_detail->item_id . ',' . $item_receive_detail->quantity . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,' . $item_receive->id . ',' . $item_receive->item_receive_no . ',' . $item_receive->item_issue_id . ',' . $item_receive->item_receive_date_time . ',' . $item_receive->item_receive_value . ',' . str_replace(',', ' ', $item_receive->remarks) . ',,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            }

            $result = array(
                'response' => true,
                'message' => 'Item Receive Detail created successfully',
                'data' => $item_receive->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item Receive Detail creation failed'
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
        $item_receive = \App\Model\ItemReceive::find($request->item_receive_id);
        $item_receive->item_issue_id = isset($request->item_issue['id']) ? $request->item_issue['id'] : 0;
        $item_receive->item_receive_no = $request->item_receive_no;
        $item_receive->item_receive_date_time = date('Y-m-d', strtotime($request->item_receive_date)) . ' ' . $request->item_receive_time;
        $item_receive->remarks = $request->remarks;

        if ($item_receive->save()) {
            $item_receive_detail_id = '';
            if (isset($request->item['id'])) {
                $item_receive_detail = \App\Model\ItemReceiveDetails::find($id);
                $item_receive_detail->is_delete = 1;
                $item_receive_detail->save();
                $item_receive_breakdown = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                    ->where('is_delete', 0)
                    ->update(['is_delete' => 1]);

                $old_item_receive_detail = \App\Model\ItemReceiveDetails::where('item_receive_id', $item_receive->id)
                    ->where('item_id', $request->item['id'])
                    ->first();

                $item_receive_detail = $old_item_receive_detail ? $old_item_receive_detail : new \App\Model\ItemReceiveDetails();
                $item_receive_detail->item_receive_id = $item_receive->id;
                $item_receive_detail->item_id = $request->item['id'];
                $item_receive_detail->quantity = $request->quantity;
                $item_receive_detail->is_delete = 0;
                $item_receive_detail->save();

                $item_receive_detail_id = $item_receive_detail->id;

                if ($request->item['is_serial'] == 1) {
                    foreach ($request->serial_details as $detail) {
                        $old_item_receive_breakdown = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                            ->where('type', 1)
                            ->where('detail_id', $detail['id'])
                            ->first();
                        $item_receive_breakdown = $old_item_receive_breakdown ? $old_item_receive_breakdown : new \App\Model\ItemReceiveBreakdown();
                        $item_receive_breakdown->item_receive_detail_id = $item_receive_detail->id;
                        $item_receive_breakdown->type = 1;
                        $item_receive_breakdown->detail_id = $detail['id'];
                        $item_receive_breakdown->quantity = 1;
                        $item_receive_breakdown->is_delete = 0;
                        $item_receive_breakdown->save();
                    }
                } else {
                    $allocate_quantity = $request->quantity;
                    $item_issue_breakdown_ids = array();
                    while ($allocate_quantity > 0) {
                        $item_issue_breakdown = \App\Model\ItemIssueBreakdown::whereNotIn('id', $item_issue_breakdown_ids)
                            ->whereHas('ItemIssueDetails', function ($query) use ($item_receive_detail) {
                                $query->where('item_issue_id', $item_receive_detail->ItemReceive->item_issue_id)
                                    ->where('item_id', $item_receive_detail->item_id)
                                    ->where('is_delete', 0);
                            })
                            ->where('type', 0)
                            ->where('is_delete', 0)
                            ->orderBy('quantity', 'DESC')
                            ->first();
                        if ($item_issue_breakdown) {
                            $old_item_receive_breakdown = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)
                                ->where('type', 0)
                                ->where('detail_id', $item_issue_breakdown->GoodReceiveDetails->id)
                                ->first();
                            $item_receive_breakdown = $old_item_receive_breakdown ? $old_item_receive_breakdown : new \App\Model\ItemReceiveBreakdown();
                            $item_receive_breakdown->item_receive_detail_id = $item_receive_detail->id;
                            $item_receive_breakdown->type = 0;
                            $item_receive_breakdown->detail_id = $item_issue_breakdown->GoodReceiveDetails->id;

                            if ($item_issue_breakdown->quantity >= $allocate_quantity) {
                                $item_receive_breakdown->quantity = $allocate_quantity;
                                $allocate_quantity = 0;
                            } else {
                                $item_receive_breakdown->quantity = $item_issue_breakdown->quantity;
                                $allocate_quantity -= $item_issue_breakdown->quantity;
                            }

                            $item_receive_breakdown->is_delete = 0;
                            $item_receive_breakdown->save();

                            array_push($item_issue_breakdown_ids, $item_issue_breakdown->id);
                        }
                    }
                }
            }

            $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::whereHas('ItemReceiveDetails', function ($query) use ($item_receive) {
                $query->where('item_receive_id', $item_receive->id)->where('is_delete', 0);
            })
                ->where('is_delete', 0)
                ->get();
            $total_value = 0;
            foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                $total_value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
            }
            $item_receive->item_receive_value = $total_value;
            $item_receive->save();

            if ($item_receive_detail_id != '') {
                $item_receive_detail = \App\Model\ItemReceiveDetails::find($item_receive_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $item_receive->id . ',' . $item_receive->item_receive_no . ',' . $item_receive->item_issue_id . ',' . $item_receive->item_receive_date_time . ',' . $item_receive->item_receive_value . ',' . str_replace(',', ' ', $item_receive->remarks) . ',' . $item_receive_detail->id . ',' . $item_receive_detail->item_id . ',' . $item_receive_detail->quantity . ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            } else {
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,' . $item_receive->id . ',' . $item_receive->item_receive_no . ',' . $item_receive->item_issue_id . ',' . $item_receive->item_receive_date_time . ',' . $item_receive->item_receive_value . ',' . str_replace(',', ' ', $item_receive->remarks) . ',,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);
            }

            $result = array(
                'response' => true,
                'message' => 'Item Receive Detail updated successfully',
                'data' => $item_receive->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Item Receive Detail updation failed'
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
            $item_receive = \App\Model\ItemReceive::find($id);
            $item_receive->is_delete = 1;

            if ($item_receive->save()) {
                $item_receive_detail = \App\Model\ItemReceiveDetails::find($item_receive_detail_id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $item_receive->id . ',,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $item_receive_details = \App\Model\ItemReceiveDetails::where('item_receive_id', $item_receive->id)->where('is_delete', 0)->get();
                foreach ($item_receive_details as $item_receive_detail) {
                    $item_receive_detail->is_delete = 1;
                    $item_receive_detail->save();

                    $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)->where('is_delete', 0)->get();
                    foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                        $item_receive_breakdown->is_delete = 1;
                        $item_receive_breakdown->save();
                    }
                }

                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::whereHas('ItemReceiveDetails', function ($query) use ($item_receive) {
                    $query->where('item_receive_id', $item_receive->id)->where('is_delete', 0);
                })
                    ->where('is_delete', 0)
                    ->get();
                $total_value = 0;
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    $total_value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                }
                $item_receive->item_receive_value = $total_value;
                $item_receive->save();

                $result = array(
                    'response' => true,
                    'message' => 'Item Receive deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Item Receive deletion failed'
                );
            }
        } else if ($request->type == 1) {
            $item_receive_detail = \App\Model\ItemReceiveDetails::find($id);
            $item_receive_detail->quantity = 0;
            $item_receive_detail->is_delete = 1;

            if ($item_receive_detail->save()) {
                $item_receive_detail = \App\Model\ItemReceiveDetails::find($item_receive_detail->id);
                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/m3force/public/assets/system_logs/item_receive_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,' . $item_receive_detail->item_receive_id . ',,,,,,' . $item_receive_detail->id . ',,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',', ' ', session()->get('username')) . PHP_EOL);
                fclose($myfile);

                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::where('item_receive_detail_id', $item_receive_detail->id)->where('is_delete', 0)->get();
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    $item_receive_breakdown->is_delete = 1;
                    $item_receive_breakdown->save();
                }

                $item_receive = \App\Model\ItemReceive::find($item_receive_detail->item_receive_id);
                $item_receive_breakdowns = \App\Model\ItemReceiveBreakdown::whereHas('ItemReceiveDetails', function ($query) use ($item_receive) {
                    $query->where('item_receive_id', $item_receive->id)->where('is_delete', 0);
                })
                    ->where('is_delete', 0)
                    ->get();
                $total_value = 0;
                foreach ($item_receive_breakdowns as $item_receive_breakdown) {
                    $total_value += $item_receive_breakdown->type == 1 ? $item_receive_breakdown->GoodReceiveBreakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity : $item_receive_breakdown->GoodReceiveDetails->rate * $item_receive_breakdown->quantity;
                }
                $item_receive->item_receive_value = $total_value;
                $item_receive->save();

                $result = array(
                    'response' => true,
                    'message' => 'Item Receive Detail deleted successfully'
                );
            } else {
                $result = array(
                    'response' => false,
                    'message' => 'Item Receive Detail deletion failed'
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
