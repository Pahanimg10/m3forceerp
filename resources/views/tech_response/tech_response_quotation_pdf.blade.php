@extends('layouts.print')

@section('title')
<title>M3Force | Print Tech Response Quotation</title>
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

<?php if ($tech_response_quotation){ ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; float: right; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Tech Response Quotation</h4>
    </div>
    <div class="panel-body">
        <table id="header_table">
            <tr>
                <td>Customer Name</td>
                <td><?php echo $tech_response_quotation->TechResponse->Contact->name; ?></td>
                <td>Tech Response Quotation No</td>
                <td><?php echo $tech_response_quotation->tech_response_quotation_no; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><?php echo $tech_response_quotation->TechResponse->Contact->address; ?></td>
                <td>Date & Time</td>
                <td><?php echo $tech_response_quotation->tech_response_quotation_date_time; ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo $tech_response_quotation->TechResponse->Contact->email; ?></td>
                <td>Remarks</td>
                <td><?php echo $tech_response_quotation->remarks; ?></td>
            </tr>
            <tr>
                <td>Contact No</td>
                <td><?php echo $tech_response_quotation->TechResponse->Contact->contact_no; ?></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</div>

<?php
    $tech_response_job_card_ids = array();
    foreach ($tech_response_quotation->TechResponseQuotationJobCard as $detail){
        array_push($tech_response_job_card_ids, $detail['tech_response_job_card_id']);
    }
        
    $usd = false;
    $usd_rate = 0;
    $currency = 'LKR';
    if($tech_response_quotation->is_currency == 0){
        $usd = true;
        $usd_rate = $tech_response_quotation->usd_rate;
        $currency = 'USD';
    }
        
    $main_items = $sub_items = array();
    $tech_response_job_card_details = \App\Model\TechResponseJobCardDetails::whereIn('tech_response_job_card_id', $tech_response_job_card_ids)
            ->where('is_delete', 0)
            ->get();
    foreach ($tech_response_job_card_details as $tech_response_job_card_detail){
        if($tech_response_job_card_detail->is_chargeable == 1){
            $margin = ($tech_response_job_card_detail->margin + 100)/100;
            $value = $usd ? ($tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity)/$usd_rate : $tech_response_job_card_detail->rate * $margin * $tech_response_job_card_detail->quantity;
            $row = array(
                'description' => $tech_response_job_card_detail->Item->name,
                'model_no' => $tech_response_job_card_detail->Item->model_no,
                'brand' => $tech_response_job_card_detail->Item->brand,
                'origin' => $tech_response_job_card_detail->Item->origin,
                'unit_type' => $tech_response_job_card_detail->Item->UnitType->code,
                'rate' => $value/$tech_response_job_card_detail->quantity,
                'quantity' => $tech_response_job_card_detail->quantity,
                'value' => $value
            );
            if($tech_response_job_card_detail->is_main == 1){
                array_push($main_items, $row);
            } else{
                array_push($sub_items, $row);
            }
        }
    } 
        
    $installation_charge = $usd ? $tech_response_quotation->installation_charge/$usd_rate : $tech_response_quotation->installation_charge;
    $transport_charge = $usd ? $tech_response_quotation->transport_charge/$usd_rate : $tech_response_quotation->transport_charge;
    $attendance_fee = $usd ? $tech_response_quotation->attendance_fee/$usd_rate : $tech_response_quotation->attendance_fee;

    $package_exist = $special_exist = false;
    $package_description = $special_description = '';
    $package_percentage = $special_percentage = 0;
    foreach ($tech_response_quotation->TechResponseQuotationDiscount as $detail){
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
    if($tech_response_quotation->TechResponse->Contact->is_group == 1){
        foreach ($tech_response_quotation->TechResponse->Contact->CGroup->CGroupTax as $detail){
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
    } else{
        foreach ($tech_response_quotation->TechResponse->Contact->ContactTax as $detail){
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
    }
        
    $colspan = 6;
    if($tech_response_quotation->show_brand == 1){
        $colspan++;
    }
    if($tech_response_quotation->show_origin == 1){
        $colspan++;
    }
?>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-body">
        <table id="detail_table">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">No#</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Description</th>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Model No</th>
                    <?php if($tech_response_quotation->show_brand == 1){ ?>
                    <th style="text-align: center; vertical-align: middle; background-color: #e3e0dd;">Brand</th>
                    <?php } ?>
                    <?php if($tech_response_quotation->show_origin == 1){ ?>
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
                    <?php if($tech_response_quotation->show_brand == 1){ ?>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $main_item['brand']; ?></td>
                    <?php } ?>
                    <?php if($tech_response_quotation->show_origin == 1){ ?>
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
                    <?php if($tech_response_quotation->show_brand == 1){ ?>
                    <td style="<?php echo $style; ?> vertical-align: middle;"><?php echo $sub_item['brand']; ?></td>
                    <?php } ?>
                    <?php if($tech_response_quotation->show_origin == 1){ ?>
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
                if(count($main_items) == 0 && count($sub_items) == 0){
                    $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: center; vertical-align: middle;">No Equipments</td>
                    <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($equipment_installation_total, 2); ?></td>
                </tr>
            <?php
                    $s++;
                }
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Total - Equipment</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>



                
            <?php if($tech_response_quotation->show_installation_charge != 1 && $tech_response_quotation->show_transport != 1){ 
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
            ?>
            <tr>
                <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Engineering, Installation, Transport & Attendance Fee</td>
                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($transport_charge+$installation_charge+$attendance_fee, 2); ?></td>
            </tr>
            <?php
                } else{
                    if($tech_response_quotation->show_installation_charge == 1 && $tech_response_quotation->show_transport == 1){ 
                        $s++;
                        $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                            <tr>
                                <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                                <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;"><?php echo $tech_response_quotation->installation_charge_text; ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($installation_charge, 2); ?></td>
                            </tr>
                        <?php
                $s++;
                $count++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                ?>
                    <tr>
                        <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                        <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Transport Charge</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($transport_charge, 2); ?></td>
                    </tr>
                <?php
                $s++;
                $count++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                ?>
                    <tr>
                        <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                        <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Engineering & Attendance Fee</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($attendance_fee, 2); ?></td>
                    </tr>
                <?php
                    } else if($tech_response_quotation->show_installation_charge == 1){ 
                        $s++;
                        $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                            <tr>
                                <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                                <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;"><?php echo $tech_response_quotation->installation_charge_text; ?></td>
                                <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($installation_charge, 2); ?></td>
                            </tr>
                        <?php
                $s++;
                $count++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                ?>
                    <tr>
                        <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                        <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Engineering, Transport & Attendance Fee</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($attendance_fee, 2); ?></td>
                    </tr>
                <?php
                    } else if($tech_response_quotation->show_transport == 1){ 
                        $s++;
                        $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                        ?>
                    <tr>
                        <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                        <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Transport Charge</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($transport_charge, 2); ?></td>
                    </tr>
                <?php
                $s++;
                $count++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';
                ?>
                    <tr>
                        <td style="<?php echo $style; ?> text-align: center; vertical-align: middle;"><?php echo $count; ?></td>
                        <td colspan="<?php echo $colspan-1; ?>" style="<?php echo $style; ?> vertical-align: middle;">Engineering, Installation & Attendance Fee</td>
                        <td style="<?php echo $style; ?> text-align: right; vertical-align: middle;"><?php echo number_format($attendance_fee, 2); ?></td>
                    </tr>
                <?php
                    }
                }
                $equipment_installation_total += $transport_charge+$installation_charge+$attendance_fee;
                $s++;
                $count++;
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
                <td colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?>">VAT EXEMPTED</td>
                <td style="<?php echo $style; ?>"></td>
            </tr>
            <?php
                }
                $s++;
                $style = $s%2 == 0 ? 'background-color: #e3e0dd;' : '';  
            ?>
                <tr>
                    <th colspan="<?php echo $colspan; ?>" style="<?php echo $style; ?> text-align: right; vertical-align: middle;">Grand Total – Equipment & Installation</th>
                    <th style="<?php echo $style; ?> text-align: right; vertical-align: middle; border-top: 1px double black; border-bottom: 3px double black;"><?php echo number_format($equipment_installation_total, 2); ?></th>
                </tr>
            </tbody>
        </table>        
    </div> 
</div>
<?php if($tech_response_quotation->special_notes && preg_replace('/\s+/', '', $tech_response_quotation->special_notes) != ''){ ?>
<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-heading">
        <h4 style="margin-top: 5px; margin-bottom: 5px; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">Special Notes</h4>
    </div>
    <div class="panel-body">
        <p><?php echo $tech_response_quotation->special_notes; ?></p>
    </div>
</div>
<?php 
    }
} 
?>
@endsection