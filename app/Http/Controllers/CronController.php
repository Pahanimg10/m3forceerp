<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

class CronController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function generate_monitoring_invoice()
    {
        $invoice_contacts = \App\Model\ContactInvoiceMonth::where('month', date('n'))
                ->whereHas('Contact', function ($query){
                    $query->where('is_active', 1);
                })
                ->where('is_delete', 0)
                ->get();
        foreach ($invoice_contacts as $invoice_contact){                        
            $monitoring_customer = \App\Model\MonitoringCustomer::where('contact_id', $invoice_contact->contact_id)
                    ->where('is_group', 0)
                    ->where('is_delete', 0)
                    ->first();
            if(!$monitoring_customer){
                $monitoring_customer = new \App\Model\MonitoringCustomer();
                $monitoring_customer->contact_id = $invoice_contact->contact_id;
                $monitoring_customer->pending_amount = 0;
                $monitoring_customer->is_group = 0;
            }
            $monitoring_customer->update_date = date('Y-m-01');
            $monitoring_customer->save();
            
            $monitoring_customer_invoice = new \App\Model\MonitoringCustomerInvoice();
            $monitoring_customer_invoice->monitoring_customer_id = $monitoring_customer->id;
                            
            $last_id = 0;
            $last_monitoring_customer_invoice = \App\Model\MonitoringCustomerInvoice::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_monitoring_customer_invoice ? $last_monitoring_customer_invoice->id : $last_id;
                            
            $monitoring_customer_invoice->invoice_date = date('Y-m-01');
            $monitoring_customer_invoice->invoice_no = 'INV/MR/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id+1);
            $monitoring_customer_invoice->invoice_value = $monitoring_customer->Contact->monitoring_fee;
            $monitoring_customer_invoice->save();
            
            $totatl_invoices = $total_payment = 0;            
            $monitoring_customer_invoices = \App\Model\MonitoringCustomerInvoice::where('monitoring_customer_id', $monitoring_customer->id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($monitoring_customer_invoices as $monitoring_customer_invoice){
                $invoice_value = $monitoring_customer_invoice->invoice_value;
                if($monitoring_customer_invoice->MonitoringCustomer->is_group == 0){
                    foreach ($monitoring_customer_invoice->MonitoringCustomer->Contact->ContactTax as $detail){
                        if ($detail['CTaxType']){
                            if($detail['CTaxType']['id'] == 1){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            } else if($detail['CTaxType']['id'] == 3){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            }
                        }
                    }
                } else if($monitoring_customer_invoice->MonitoringCustomer->is_group == 1){
                    foreach ($monitoring_customer_invoice->MonitoringCustomer->CGroup->CGroupTax as $detail){
                        if ($detail['CTaxType']){
                            if($detail['CTaxType']['id'] == 1){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            } else if($detail['CTaxType']['id'] == 3){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            }
                        }
                    }
                }
                $totatl_invoices += $invoice_value;
            }
            $monitoring_customer_payments = \App\Model\MonitoringCustomerPayment::where('monitoring_customer_id', $monitoring_customer->id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($monitoring_customer_payments as $monitoring_customer_payment){
                $total_payment += $monitoring_customer_payment->amount;
            }
            $monitoring_customer->pending_amount = $totatl_invoices - $total_payment;
            $monitoring_customer->save();
        }
        
        $group_invoice_contacts = \App\Model\CGroupInvoiceMonth::where('month', date('n'))->where('is_delete', 0)->get();
        foreach ($group_invoice_contacts as $group_invoice_contact){                        
            $monitoring_customer = \App\Model\MonitoringCustomer::where('contact_id', $group_invoice_contact->group_id)
                    ->where('is_group', 1)
                    ->where('is_delete', 0)
                    ->first();
            if(!$monitoring_customer){
                $monitoring_customer = new \App\Model\MonitoringCustomer();
                $monitoring_customer->contact_id = $group_invoice_contact->group_id;
                $monitoring_customer->pending_amount = 0;
                $monitoring_customer->is_group = 1;
            }
            $monitoring_customer->update_date = date('Y-m-01');
            $monitoring_customer->save();
            
            $monitoring_customer_invoice = new \App\Model\MonitoringCustomerInvoice();
            $monitoring_customer_invoice->monitoring_customer_id = $monitoring_customer->id;
                            
            $last_id = 0;
            $last_monitoring_customer_invoice = \App\Model\MonitoringCustomerInvoice::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
            $last_id = $last_monitoring_customer_invoice ? $last_monitoring_customer_invoice->id : $last_id;
                            
            $monitoring_customer_invoice->invoice_date = date('Y-m-01');
            $monitoring_customer_invoice->invoice_no = 'INV/MR/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id+1);
            $monitoring_customer_invoice->invoice_value = $monitoring_customer->CGroup->monitoring_fee;
            $monitoring_customer_invoice->save();
            
            $totatl_invoices = $total_payment = 0;            
            $monitoring_customer_invoices = \App\Model\MonitoringCustomerInvoice::where('monitoring_customer_id', $monitoring_customer->id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($monitoring_customer_invoices as $monitoring_customer_invoice){
                $invoice_value = $monitoring_customer_invoice->invoice_value;
                if($monitoring_customer_invoice->MonitoringCustomer->is_group == 0){
                    foreach ($monitoring_customer_invoice->MonitoringCustomer->Contact->ContactTax as $detail){
                        if ($detail['CTaxType']){
                            if($detail['CTaxType']['id'] == 1){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            } else if($detail['CTaxType']['id'] == 3){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            }
                        }
                    }
                } else if($monitoring_customer_invoice->MonitoringCustomer->is_group == 1){
                    foreach ($monitoring_customer_invoice->MonitoringCustomer->CGroup->CGroupTax as $detail){
                        if ($detail['CTaxType']){
                            if($detail['CTaxType']['id'] == 1){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            } else if($detail['CTaxType']['id'] == 3){
                                $invoice_value += $invoice_value * $detail['CTaxType']['percentage'] / 100;
                            }
                        }
                    }
                }
                $totatl_invoices += $invoice_value;
            }
            $monitoring_customer_payments = \App\Model\MonitoringCustomerPayment::where('monitoring_customer_id', $monitoring_customer->id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($monitoring_customer_payments as $monitoring_customer_payment){
                $total_payment += $monitoring_customer_payment->amount;
            }
            $monitoring_customer->pending_amount = $totatl_invoices - $total_payment;
            $monitoring_customer->save();
        }
    }
}