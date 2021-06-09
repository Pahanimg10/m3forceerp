@extends('layouts.print')

@section('title')
<title>M3Force | Print Item Issue Balance Details</title>
@endsection

@section('content')
<style>
    #header_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
    }
    #header_table tr{
        page-break-inside: avoid;
    }
    #header_table tfoot{
        display: table-row-group;
    }
    #header_table td:nth-child(1),
    #header_table td:nth-child(3){
        width: 15%;
        vertical-align: middle;
        white-space: nowrap;
    }
    #header_table td:nth-child(2),
    #header_table td:nth-child(4){
        width: 35%;
        vertical-align: middle;
    }
    
    #detail_table{
        width: 100%;
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
        border-collapse: collapse;
    }
    #detail_table tr{
        page-break-inside: avoid;
    }
    #detail_table tfoot{
        display: table-row-group;
    }
    #detail_table,
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th{
        border-left: 1px solid #ffffff;
        border-right: 1px solid #ffffff;
    }
    #detail_table thead tr,
    #detail_table tbody tr,
    #detail_table tfoot tr{
        border-top: 1px solid #ffffff;
        border-bottom: 1px solid #ffffff;
    }
    #detail_table thead th{
        text-align: center;
        padding: 5px;
        white-space: nowrap;
        background-color: #e3e0dd;
    }
    #detail_table tbody td,
    #detail_table tbody th{
        padding: 5px;
    }
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tfoot th,
    #detail_table tfoot td{
        vertical-align: middle;
    }
    #detail_table tbody td:nth-child(1){
        text-align: center;
        white-space: nowrap;
    }
    #detail_table tbody td:nth-child(2){
        white-space: nowrap;
    }
    #detail_table tbody td:nth-child(4),
    #detail_table tbody td:nth-child(5),
    #detail_table tbody td:nth-child(6),
    #detail_table tbody td:nth-child(7),
    #detail_table tbody td:nth-child(8),
    #detail_table tbody td:nth-child(9){
        text-align: right;
        white-space: nowrap;
    }
</style>

<?php 
if ($record){ 
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Item Issue Balance Details</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td><?php echo $record->Contact->name; ?></td>
                <td>Address</td>
                <td><?php echo $record->Contact->address; ?></td>
            </tr>
            <tr>
                <td>Contact No</td>
                <td><?php echo $record->Contact->contact_no; ?></td>
                <td>Email</td>
                <td><?php echo $record->Contact->email; ?></td>
            </tr>
            <tr>
                <td><?php echo $status_type == 1 ? 'Job No' : 'Tech Response No'; ?></td>
                <td>
                <?php
                    if($status_type == 1){
                        $job = App\Model\Job::where('inquiry_id', $record->id)->where('is_delete', 0)->first();
                        if($job){
                            echo $job->job_no;
                        } else{
                            echo $record->inquiry_no;
                        }
                    } else if($status_type == 2){
                        echo $record->tech_response_no;
                    }
                ?>
                </td>
                <td>Printed Time</td>
                <td><?php echo date('Y-m-d H:i'); ?></td>
            </tr>
        </table>
    </div>
</div>
<?php
    $job_card_ids = $job_card_items = $installation_items = $issued_items = array();
    if($status_type == 1){
        $quotations = \App\Model\Quotation::where(function($q) use($record){
                            $record ? $q->where('inquiry_id', $record->id) : '';
                        })
                        ->where('is_confirmed', 1)
                        ->where('is_revised', 0)
                        ->where('is_delete', 0)
                        ->get();
        foreach ($quotations as $quotation){ 
            foreach ($quotation->QuotationJobCard as $detail){
                array_push($job_card_ids, $detail['id']);
            }
        }
    
        $job_card_details = \App\Model\QuotationJobCardDetails::selectRaw('id, item_id, SUM(quantity) as total_quantity')
                ->whereIn('quotation_job_card_id', $job_card_ids)
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($job_card_details as $job_card_detail){
            $row = array(
                'id' => $job_card_detail->Item->id,
                'code' => $job_card_detail->Item->code,
                'name' => $job_card_detail->Item->name,
                'quantity' => $job_card_detail->total_quantity,
                'stock' => $job_card_detail->Item->stock
            );
            array_push($job_card_items, $row);
        }
    } else if($status_type == 2){
        $job_card_details = \App\Model\TechResponseJobCardDetails::selectRaw('id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('TechResponseJobCard', function ($query) use($record) {
                    $query->where('tech_response_id', $record->id)
                            ->where('is_posted', 1)
                            ->where('is_approved', 1)
                            ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($job_card_details as $job_card_detail){
            $row = array(
                'id' => $job_card_detail->Item->id,
                'code' => $job_card_detail->Item->code,
                'name' => $job_card_detail->Item->name,
                'quantity' => $job_card_detail->total_quantity,
                'stock' => $job_card_detail->Item->stock
            );
            array_push($job_card_items, $row);
        }
    }
    
    if($status_type == 1){
        $installation_sheet_details = \App\Model\InstallationSheetDetails::selectRaw('id, installation_sheet_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('InstallationSheet', function($query) use($record){
                    $query->where('inquiry_id', $record->id)->where('is_posted', 1)->where('is_approved', 1)->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($installation_sheet_details as $installation_sheet_detail){
            $row = array(
                'id' => $installation_sheet_detail->Item->id,
                'code' => $installation_sheet_detail->Item->code,
                'name' => $installation_sheet_detail->Item->name,
                'quantity' => $installation_sheet_detail->total_quantity,
                'stock' => $installation_sheet_detail->Item->stock
            );
            array_push($installation_items, $row);
        }
        $job = App\Model\Job::where('inquiry_id', $record->id)->where('is_delete', 0)->first();
        if($job){
            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use($job){
                        $query->where('item_issue_type_id', 1)
                                ->where('document_id', $job->id)
                                ->where('is_posted', 1)
                                ->where('is_delete', 0);
                    })
                    ->where('is_delete', 0)
                    ->groupBy('item_id')
                    ->get();
            foreach ($item_issue_details as $item_issue_detail){
                $row = array(
                    'id' => $item_issue_detail->Item->id,
                    'code' => $item_issue_detail->Item->code,
                    'name' => $item_issue_detail->Item->name,
                    'quantity' => 0,
                    'stock' => $item_issue_detail->Item->stock
                );
                array_push($issued_items, $row);
            }
        }
    } else if($status_type == 2){
        $installation_sheet_details = \App\Model\TechResponseInstallationSheetDetails::selectRaw('id, tech_response_installation_sheet_id, item_id, SUM(quantity) as total_quantity')
                ->whereHas('TechResponseInstallationSheet', function($query) use($record){
                    $query->where('tech_response_id', $record->id)->where('is_posted', 1)->where('is_approved', 1)->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($installation_sheet_details as $installation_sheet_detail){
            $row = array(
                'id' => $installation_sheet_detail->Item->id,
                'code' => $installation_sheet_detail->Item->code,
                'name' => $installation_sheet_detail->Item->name,
                'quantity' => $installation_sheet_detail->total_quantity,
                'stock' => $installation_sheet_detail->Item->stock
            );
            array_push($installation_items, $row);
        }
        
        $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use($record){
                    $query->where('item_issue_type_id', 2)
                            ->where('document_id', $record->id)
                            ->where('is_posted', 1)
                            ->where('is_delete', 0);
                })
                ->where('is_delete', 0)
                ->groupBy('item_id')
                ->get();
        foreach ($item_issue_details as $item_issue_detail){
            $row = array(
                'id' => $item_issue_detail->Item->id,
                'code' => $item_issue_detail->Item->code,
                'name' => $item_issue_detail->Item->name,
                'quantity' => 0,
                'stock' => $item_issue_detail->Item->stock
            );
            array_push($issued_items, $row);
        }
    }
    
    $request_ids = $request_items = array();
    foreach ($job_card_items as $job_card_main_item){
        if(!in_array($job_card_main_item['id'], $request_ids)){
            $total_qunatity = 0;
            foreach ($job_card_items as $job_card_sub_item){
                if($job_card_main_item['id'] == $job_card_sub_item['id']){
                    $total_qunatity += $job_card_sub_item['quantity'];
                }
            }
            foreach ($installation_items as $installation_item){
                if($job_card_main_item['id'] == $installation_item['id']){
                    $total_qunatity += $installation_item['quantity'];
                }
            }
            
            $row = array(
                'id' => $job_card_main_item['id'],
                'code' => $job_card_main_item['code'],
                'name' => $job_card_main_item['name'],
                'quantity' => $total_qunatity,
                'stock' => $job_card_main_item['stock']
            );
            array_push($request_items, $row);
            array_push($request_ids, $job_card_main_item['id']);
        }
    }
    foreach ($installation_items as $installation_main_item){
        if(!in_array($installation_main_item['id'], $request_ids)){
            $total_qunatity = 0;
            foreach ($installation_items as $installation_sub_item){
                if($installation_main_item['id'] == $installation_sub_item['id']){
                    $total_qunatity += $installation_sub_item['quantity'];
                }
            }
            
            $row = array(
                'id' => $installation_main_item['id'],
                'code' => $installation_main_item['code'],
                'name' => $installation_main_item['name'],
                'quantity' => $total_qunatity,
                'stock' => $installation_main_item['stock']
            );
            array_push($request_items, $row);
            array_push($request_ids, $installation_main_item['id']);
        }
    }
    foreach ($issued_items as $issued_item){
        if(!in_array($issued_item['id'], $request_ids)){
            $row = array(
                'id' => $issued_item['id'],
                'code' => $issued_item['code'],
                'name' => $issued_item['name'],
                'quantity' => 0,
                'stock' => $issued_item['stock']
            );
            array_push($request_items, $row);
            array_push($request_ids, $issued_item['id']);
        }
    }
?>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-body">
        <table id="detail_table">
            <thead>
                <tr>
                    <th rowspan="2">No#</th>
                    <th rowspan="2">Code</th>
                    <th rowspan="2">Description</th>
                    <th colspan="6">Quantity</th>
                    <th rowspan="2">Location</th>
                </tr>
                <tr>
                    <th>Requested</th>
                    <th>Issued</th>
                    <th>Received</th>
                    <th>Balance</th>
                    <th>Stock</th>
                    <th>Purchase</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $requested_item_ids = array();
            if(count($request_items) > 0){
                $s = 1;
                foreach($request_items as $index => $value){
                    array_push($requested_item_ids, $value['id']);
                    $style = $s%2 == 0 ? ' background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <td style="<?php echo $style; ?>"><?php echo $s; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value['code']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value['name']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value['quantity']; ?></td>
                    <td style="<?php echo $style; ?>">
                    <?php
                        $issued_quantity = 0;
                        if($status_type == 1){
                            $job = App\Model\Job::where('inquiry_id', $record->id)->where('is_delete', 0)->first();
                            if($job){
                                $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use($job){
                                            $query->where('item_issue_type_id', 1)
                                                    ->where('document_id', $job->id)
                                                    ->where('is_posted', 1)
                                                    ->where('is_delete', 0);
                                        })
                                        ->where('item_id', $value['id'])
                                        ->where('is_delete', 0)
                                        ->get();
                                foreach ($item_issue_details as $item_issue_detail){
                                    $issued_quantity += $item_issue_detail->quantity;
                                }
                            }
                        } else if($status_type == 2){
                            $item_issue_details = \App\Model\ItemIssueDetails::whereHas('ItemIssue', function ($query) use($record){
                                        $query->where('item_issue_type_id', 2)
                                                ->where('document_id', $record->id)
                                                ->where('is_posted', 1)
                                                ->where('is_delete', 0);
                                    })
                                    ->where('item_id', $value['id'])
                                    ->where('is_delete', 0)
                                    ->get();
                            foreach ($item_issue_details as $item_issue_detail){
                                $issued_quantity += $item_issue_detail->quantity;
                            }
                        }
                        echo $issued_quantity;
                    ?>
                    </td>
                    <td style="<?php echo $style; ?>">
                    <?php
                        $received_quantity = 0;
                        $item_issue_ids = array();
                        if($status_type == 1){
                            $job = App\Model\Job::where('inquiry_id', $record->id)->where('is_delete', 0)->first();
                            if($job){
                                $item_issues = App\Model\ItemIssue::where('item_issue_type_id', 1)
                                        ->where('document_id', $job->id)
                                        ->where('is_posted', 1)
                                        ->where('is_delete', 0)
                                        ->get();
                                foreach ($item_issues as $item_issue){
                                    if(!in_array($item_issue->id, $item_issue_ids)){
                                        array_push($item_issue_ids, $item_issue->id);
                                    }
                                }
                            }
                            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use($item_issue_ids){
                                            $query->whereIn('item_issue_id', $item_issue_ids)
                                                    ->where('is_posted', 1)
                                                    ->where('is_delete', 0);
                                        })
                                        ->where('item_id', $value['id'])
                                        ->where('is_delete', 0)
                                        ->get();
                            foreach ($item_receive_details as $item_receive_detail){
                                $received_quantity += $item_receive_detail->quantity;
                            }
                        } else if($status_type == 2){
                            $item_issues = App\Model\ItemIssue::where('item_issue_type_id', 2)
                                    ->where('document_id', $record->id)
                                    ->where('is_posted', 1)
                                    ->where('is_delete', 0)
                                    ->get();
                            foreach ($item_issues as $item_issue){
                                if(!in_array($item_issue->id, $item_issue_ids)){
                                    array_push($item_issue_ids, $item_issue->id);
                                }
                            }
                            $item_receive_details = \App\Model\ItemReceiveDetails::whereHas('ItemReceive', function ($query) use($item_issue_ids){
                                            $query->whereIn('item_issue_id', $item_issue_ids)
                                                    ->where('is_posted', 1)
                                                    ->where('is_delete', 0);
                                        })
                                        ->where('item_id', $value['id'])
                                        ->where('is_delete', 0)
                                        ->get();
                            foreach ($item_receive_details as $item_receive_detail){
                                $received_quantity += $item_receive_detail->quantity;
                            }
                        }
                        echo $received_quantity;
                    ?>
                    </td>
                    <td style="<?php echo $style; ?>"><?php echo $value['quantity']-$issued_quantity+$received_quantity; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo $value['stock']; ?></td>
                    <td style="<?php echo $style; ?>"><?php echo ($value['quantity']-$issued_quantity+$received_quantity) > $value['stock'] ? $value['quantity']-$issued_quantity+$received_quantity-$value['stock'] : 0; ?></td>
                    <td style="<?php echo $style; ?>">
                        <?php 
                            $good_receive_details = \App\Model\GoodReceiveDetails::with(array('GoodReceive' => function($query) {
                                        $query->where('is_posted', 1)->where('is_delete', 0)->orderBy('good_receive_date_time', 'asc');
                                    }))
                                    ->where('item_id', $value['id'])
                                    ->where('available_quantity', '>', 0)
                                    ->where('is_delete', 0)
                                    ->get();
                            $location = '';
                            foreach ($good_receive_details as $index => $value){
                                $location .= $location == '' ? $value->location : ' | '.$value->location;
                            }
                            echo $location;
                        ?>
                    </td>
                </tr>
            <?php     
                    $s++;
                }
            }
            ?>
            </tbody>
        </table>        
    </div> 
</div>
<?php
}
?>
@endsection