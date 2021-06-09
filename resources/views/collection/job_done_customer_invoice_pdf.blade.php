@extends('layouts.print')

@section('title')
<title>M3Force | Print Job Done Customer</title>
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
        padding: 5px;
    }
    #detail_table thead th,
    #detail_table tbody td,
    #detail_table tbody th,
    #detail_table tfoot th,
    #detail_table tfoot td{
        vertical-align: middle;
    }
    
    .panel-body p,
    .panel-body li{
        font-family: Verdana, Geneva, sans-serif; 
        font-size: 12px;
    }
</style>

<?php 
    if ($job_done_customer_invoice){ 
        $job_card_ids = [];
        foreach ($job_done_customer_invoice->Quotation->QuotationJobCard as $detail){
            array_push($job_card_ids, $detail['id']);
        }
        $cost_sheet_ids = [];
        foreach ($job_done_customer_invoice->Quotation->QuotationCostSheet as $detail){
            array_push($cost_sheet_ids, $detail['id']);
        }

        $usd = false;
        $usd_rate = 0;
        $currency = 'LKR';
        if($job_done_customer_invoice->Quotation->is_currency == 0){
            $usd = true;
            $usd_rate = $job_done_customer_invoice->Quotation->usd_rate;
            $currency = 'USD';
        }

        $main_items = $sub_items = [];
        $job_card_details = \App\Model\QuotationJobCardDetails::whereIn('quotation_job_card_id', $job_card_ids)
                ->where('is_delete', 0)
                ->get();
        foreach ($job_card_details as $job_card_detail){
            $margin = ($job_card_detail->margin + 100)/100;
            $value = $usd ? ($job_card_detail->rate * $margin * $job_card_detail->quantity)/$usd_rate : $job_card_detail->rate * $margin * $job_card_detail->quantity;
            $row = [
                'description' => $job_card_detail->Item->name,
                'model_no' => $job_card_detail->Item->model_no,
                'brand' => $job_card_detail->Item->brand,
                'origin' => $job_card_detail->Item->origin,
                'unit_type' => $job_card_detail->Item->UnitType->code,
                'rate' => $value/$job_card_detail->quantity,
                'quantity' => $job_card_detail->quantity,
                'value' => $value
            ];
            if($job_card_detail->is_main == 1){
                array_push($main_items, $row);
            } else{
                array_push($sub_items, $row);
            }
        }       
        
        $cost_sheet_details = \App\Model\QuotationCostSheet::whereIn('id', $cost_sheet_ids)
                ->where('is_delete', 0)
                ->get();
        $rate_ids = $rate_meters = [];
        foreach ($cost_sheet_details as $main_cost_sheet_detail){
            if($main_cost_sheet_detail->InstallationRate && !in_array($main_cost_sheet_detail->InstallationRate->id, $rate_ids)){
                $meters = 0;
                foreach ($cost_sheet_details as $sub_cost_sheet_detail){
                    if($main_cost_sheet_detail->InstallationRate->id == $sub_cost_sheet_detail->InstallationRate->id){
                        $meters += $sub_cost_sheet_detail->meters;
                    }
                }
                $row = [
                    'installation_name' => $main_cost_sheet_detail->InstallationRate->name,
                    'installation_rate' => $usd ? $main_cost_sheet_detail->InstallationRate->rate/$usd_rate : $main_cost_sheet_detail->InstallationRate->rate,
                    'meters' => $meters
                ];
                array_push($rate_meters, $row);
                array_push($rate_ids, $main_cost_sheet_detail->InstallationRate->id);
            }
        }

        $manday_rate = \App\Model\Rate::find(1);
        $excavation_work = $transport = $food = $accommodation = $bata = $other_expenses = 0;
        foreach ($cost_sheet_details as $cost_sheet_detail){
            $excavation_work += $usd ? $cost_sheet_detail->excavation_work/$usd_rate : $cost_sheet_detail->excavation_work;
            $transport += $usd ? ($cost_sheet_detail->transport+($cost_sheet_detail->traveling_mandays*$manday_rate->value))/$usd_rate : $cost_sheet_detail->transport+($cost_sheet_detail->traveling_mandays*$manday_rate->value);
            $food += $usd ? $cost_sheet_detail->food/$usd_rate : $cost_sheet_detail->food;
            $accommodation += $usd ? $cost_sheet_detail->accommodation/$usd_rate : $cost_sheet_detail->accommodation;
            $bata += $usd ? $cost_sheet_detail->bata/$usd_rate : $cost_sheet_detail->bata;
            $other_expenses += $usd ? $cost_sheet_detail->other_expenses/$usd_rate : $cost_sheet_detail->other_expenses;
        }

        $package_exist = $special_exist = false;
        $package_description = $special_description = '';
        $package_percentage = $special_percentage = 0;
        foreach ($job_done_customer_invoice->Quotation->QuotationDiscount as $detail){
            if($detail['discount_type_id'] == 1){
                $package_exist = true;
                $package_description = $detail['description'];
                $package_percentage = $detail['percentage'];
            } else if($detail['discount_type_id'] == 2){
                $special_exist = true;
                $special_description = $detail['description'];
                $special_percentage = $detail['percentage'];
            }
        }

        $nbt_exist = $svat_exist = $vat_exist = false;
        $nbt_description = $svat_description = $vat_description = '';
        $nbt_percentage = $svat_percentage = $vat_percentage = 0;
        foreach ($job_done_customer_invoice->JobDoneCustomer->Contact->ContactTax as $detail){
            if ($detail['CTaxType']){
                if($detail['CTaxType']['id'] == 1 || $detail['CTaxType']['id'] == 4){
                    $nbt_exist = true;
                    $nbt_description = $detail['CTaxType']['code'];
                    $nbt_percentage = $detail['CTaxType']['percentage'];
                } else if($detail['CTaxType']['id'] == 2){
                    $svat_exist = true;
                    $svat_description = $detail['CTaxType']['code'];
                    $svat_percentage = $detail['CTaxType']['percentage'];
                } else if($detail['CTaxType']['id'] == 3){
                    $vat_exist = true;
                    $vat_description = $detail['CTaxType']['code'];
                    $vat_percentage = $detail['CTaxType']['percentage'];
                }
            }
        }

        $colspan = 6;
        if($job_done_customer_invoice->Quotation->show_brand == 1){
            $colspan++;
        }
        if($job_done_customer_invoice->Quotation->show_origin == 1){
            $colspan++;
        }
        
        $invoice_type = $vat_exist ? 'Tax ' : '';
        $invoice_type = $svat_exist ? 'SVAT ' : $invoice_type;
        $invoice_type = !$nbt_exist && !$svat_exist && !$vat_exist ? '' : $invoice_type;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;"><?php echo $invoice_type; ?> Invoice</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td><?php echo $job_done_customer_invoice->JobDoneCustomer->Contact->is_group == 0 ? $job_done_customer_invoice->JobDoneCustomer->Contact->invoice_name : $job_done_customer_invoice->JobDoneCustomer->Contact->CGroup->invoice_name; ?></td>
                <td>Invoice No</td>
                <td><?php echo $job_done_customer_invoice->invoice_no; ?></td>
            </tr>
            <tr>
                <td>Customer Address</td>
                <td><?php echo $job_done_customer_invoice->JobDoneCustomer->Contact->is_group == 0 ? $job_done_customer_invoice->JobDoneCustomer->Contact->invoice_delivering_address : $job_done_customer_invoice->JobDoneCustomer->Contact->CGroup->invoice_delivering_address; ?></td>
                <td>Date</td>
                <td><?php echo $job_done_customer_invoice->invoice_date; ?></td>
            </tr>
            <tr>
                <td>Location Name</td>
                <td><?php echo $job_done_customer_invoice->JobDoneCustomer->Contact->name; ?></td>
                <td>Customer Email</td>
                <td><?php echo $job_done_customer_invoice->JobDoneCustomer->Contact->is_group == 0 ? $job_done_customer_invoice->JobDoneCustomer->Contact->invoice_email : $job_done_customer_invoice->JobDoneCustomer->Contact->CGroup->invoice_email; ?></td>
            </tr>
            <tr>
                <td>Customer VAT No</td>
                <td><?php echo $job_done_customer_invoice->JobDoneCustomer->Contact->is_group == 0 ? $job_done_customer_invoice->JobDoneCustomer->Contact->vat_no : $job_done_customer_invoice->JobDoneCustomer->Contact->CGroup->vat_no; ?></td>
                <td>Customer SVAT No</td>
                <td><?php echo $job_done_customer_invoice->JobDoneCustomer->Contact->is_group == 0 ? $job_done_customer_invoice->JobDoneCustomer->Contact->svat_no : $job_done_customer_invoice->JobDoneCustomer->Contact->CGroup->svat_no; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-body">
        <table id="detail_table">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">No#</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Description</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Model No</th>
                    <?php if($job_done_customer_invoice->Quotation->show_brand == 1){ ?>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Brand</th>
                    <?php } ?>
                    <?php if($job_done_customer_invoice->Quotation->show_origin == 1){ ?>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Origin</th>
                    <?php } ?>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Unit Type</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Rate (<?php echo $currency; ?>)</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Quantity</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Value (<?php echo $currency; ?>)</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $s = 1;
                $count = 1;
                $equipment_installation_total = 0;
                foreach ($main_items as $main_item){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $main_item['value'];
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $main_item['description']; ?></td>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $main_item['model_no']; ?></td>
                    <?php if($job_done_customer_invoice->Quotation->show_brand == 1){ ?>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $main_item['brand']; ?></td>
                    <?php } ?>
                    <?php if($job_done_customer_invoice->Quotation->show_origin == 1){ ?>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $main_item['origin']; ?></td>
                    <?php } ?>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $main_item['unit_type']; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($main_item['rate'], 2); ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $main_item['quantity']; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($main_item['value'], 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                }
                if($package_exist){ 
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Package Price</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                    $s++;
                    $discount_value = $equipment_installation_total * $package_percentage / 100;
                    $equipment_installation_total -= $discount_value;
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="text-align: right; vertical-align: middle; background-color: #fdff32;"><?php echo $package_description; ?></th>
                    <th style="text-align: right; vertical-align: middle; background-color: #fdff32;"><?php echo number_format($discount_value, 2); ?></th>
                </tr>
            <?php
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Sign up fee -Package Cost</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <td colspan="<?php echo $colspan+1; ?>" style="<?php echo $style; ?> vertical-align: middle;"><u>Extra Equipment</u></td>
                </tr>
            <?php
                    $s++;
                }
                foreach ($sub_items as $sub_item){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $sub_item['value'];
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $sub_item['description']; ?></td>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $sub_item['model_no']; ?></td>
                    <?php if($job_done_customer_invoice->Quotation->show_brand == 1){ ?>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $sub_item['brand']; ?></td>
                    <?php } ?>
                    <?php if($job_done_customer_invoice->Quotation->show_origin == 1){ ?>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $sub_item['origin']; ?></td>
                    <?php } ?>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $sub_item['unit_type']; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($sub_item['rate'], 2); ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $sub_item['quantity']; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($sub_item['value'], 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                }
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Total - Equipment</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                $s++;
        
                foreach ($rate_meters as $rate_meter){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $installation_value = $rate_meter['installation_rate'] * $rate_meter['meters'];
                    $equipment_installation_total += $installation_value;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Installation <?php echo $rate_meter['installation_name'].' X '.$rate_meter['meters'].' Meters'; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($installation_value, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                }
        
                $engineering_commissioning = 0;
                if($job_done_customer_invoice->Quotation->show_excavation_work == 1){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $excavation_work;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Excavation Work</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($excavation_work, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                } else{
                    $engineering_commissioning += $excavation_work;
                }
                if($job_done_customer_invoice->Quotation->show_transport == 1){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $transport;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Transport</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($transport, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                } else{
                    $engineering_commissioning += $transport;
                }
                if($job_done_customer_invoice->Quotation->show_food == 1){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $food;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Food</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($food, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                } else{
                    $engineering_commissioning += $food;
                }
                if($job_done_customer_invoice->Quotation->show_accommodation == 1){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $accommodation;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Accommodation</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($accommodation, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                } else{
                    $engineering_commissioning += $accommodation;
                }
                if($job_done_customer_invoice->Quotation->show_bata == 1){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $bata;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Bata</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($bata, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                } else{
                    $engineering_commissioning += $bata;
                }
                if($job_done_customer_invoice->Quotation->show_other_expenses == 1){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                    $equipment_installation_total += $other_expenses;
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Other Expenses</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($other_expenses, 2); ?></td>
                </tr>
            <?php
                    $s++;
                    $count++;
                } else{
                    $engineering_commissioning += $other_expenses;
                }
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                    <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Engineering & Commissioning</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($engineering_commissioning, 2); ?></td>
                </tr>
            <?php
                $s++;
                $count++;
                $equipment_installation_total += $engineering_commissioning;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Total - Equipment & Installation</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                if($special_exist){
                    $discount_value = $equipment_installation_total * $special_percentage / 100;
                    $equipment_installation_total -= $discount_value;  
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle; background-color: #fdff32;"><?php echo $special_description; ?></th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; background-color: #fdff32;"><?php echo number_format($discount_value, 2); ?></th>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"></th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                }
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
        
                if($nbt_exist){
                    $nbt_value = $equipment_installation_total * $nbt_percentage / 100;
                    $equipment_installation_total += $nbt_value; 
            ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $nbt_percentage.'% '.$nbt_description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($nbt_value, 2); ?></td>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"></th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                }
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
                if($svat_exist){
                    $svat_value = $equipment_installation_total * $svat_percentage / 100;  
            ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $svat_percentage.'% '.$svat_description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($svat_value, 2); ?></td>
                </tr>
            <?php
                }
                if($vat_exist){
                    $vat_value = $equipment_installation_total * $vat_percentage / 100;
                    $equipment_installation_total += $vat_value;   
            ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo $vat_percentage.'% '.$vat_description; ?></td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($vat_value, 2); ?></td>
                </tr>
            <?php
                }
                if(!$nbt_exist && !$svat_exist && !$vat_exist){  
                    $s++;
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : ''; 
            ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">VAT EXEMPTED</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"></td>
                </tr>
            <?php
                }
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Grand Total – Equipment & Installation</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
            ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Payment Received</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($job_done_customer_invoice->payment_received, 2); ?></td>
                </tr>
            <?php
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
                $equipment_installation_total -= $job_done_customer_invoice->payment_received;  
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Balance Due</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            </tbody>
        </table>        
    </div> 
</div>

<div class="panel panel-default" style="margin-top: 30px;">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; font-family: Verdana, Geneva, sans-serif; font-size: 12px;">Special Notes</h4>
    </div>
    <div class="panel-body">
        <ul>
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">Cheques to be drawn in favour of <strong>M3FORCE (PVT) LTD</strong>.</li>
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">People's Bank - Wellawatte (Acc No: 145100140012106)</li>
            <li style="font-family: Verdana, Geneva, sans-serif; font-size: 10px;">This is a computer generated Invoice and bears no Signature.</li>
        </ul>
    </div> 
</div>
<?php } ?>
@endsection