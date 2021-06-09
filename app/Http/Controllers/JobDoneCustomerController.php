<?php

namespace App\Http\Controllers;

require_once('ESMSWS.php');
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use Illuminate\Http\Request;

use App\Http\Requests;

class JobDoneCustomerController extends Controller
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
        
        return view('collection.job_done_customer', $data);
    }

    public function job_done_customer_list()
    {
        $job_done_customers = \App\Model\JobDoneCustomer::select('id', 'contact_id', 'update_date', 'pending_amount')
                ->with(array('Contact' => function($query) {
                    $query->select('id', 'code', 'name', 'address', 'contact_no', 'email');
                }))
//                ->where('pending_amount', '>', 0)
                ->where('is_delete', 0)
                ->get();
        return response($job_done_customers);
    }

    public function job_done_customer_detail(Request $request)
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
        
        $data['job_done_customer_id'] = $request->id;
        
        return view('collection.job_done_customer_detail', $data);
    }

    public function get_job_done_customer_detail(Request $request)
    {
        $job_done_customer = \App\Model\JobDoneCustomer::select('id', 'contact_id')
                ->with(array('Contact' => function($query) {
                    $query->select('id', 'name', 'contact_no', 'email', 'address');
                }))
                ->find($request->id);
        $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::select('id', 'job_done_customer_id', 'quotation_id', 'invoice_date', 'invoice_no')
                ->with(array('Quotation' => function($query) {
                    $query->select('id', 'quotation_no', 'quotation_date_time', 'quotation_value');
                }))
                ->where('job_done_customer_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::select('id', 'job_done_customer_id', 'receipt_date_time', 'receipt_no', 'amount')
                ->where('job_done_customer_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        
        $data = array(
            'job_done_customer' => $job_done_customer,
            'job_done_customer_invoices' => $job_done_customer_invoices,
            'job_done_customer_payments' => $job_done_customer_payments,
            'permission' => !in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')) && !in_array(3, session()->get('user_group'))
        );
        
        return response($data);
    }

    public function add_new_payment(Request $request)
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
        
        $data['job_done_customer_payment_id'] = $request->id;
        $data['job_done_customer_id'] = $request->job_done_customer_id;
        
        return view('collection.job_done_customer_payment', $data);
    }

    public function get_data()
    {
        $payment_modes = \App\Model\PaymentMode::select('id', 'name')->orderBy('name')->get();
        $collection_persons = \App\Model\CollectionPerson::select('id', 'name')->where('is_active', 1)->where('is_delete', 0)->orderBy('name')->get();
        
        $data = array(
            'payment_modes' => $payment_modes,
            'collection_persons' => $collection_persons
        );
        
        return response($data);
    }

    public function find_job_done_customer_payment(Request $request)
    {
        $job_done_customer_payment = \App\Model\JobDoneCustomerPayment::select('id', 'job_done_customer_id', 'payment_mode_id', 'collection_person_id', 'receipt_date_time', 'receipt_no', 'amount', 'cheque_no', 'bank', 'realize_date', 'remarks')
                ->with(array('PaymentMode' => function($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('CollectionPerson' => function($query) {
                    $query->select('id', 'name');
                }))
                ->find($request->id);
        return response($job_done_customer_payment);
    }
    
    public function print_job_done_customer_invoice(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $job_done_customer_invoice = \App\Model\JobDoneCustomerInvoice::find($request->id);
        $data['job_done_customer_invoice'] = $job_done_customer_invoice;
        $title = $job_done_customer_invoice ? 'Job Done Customer Invoice Details '.$job_done_customer_invoice->invoice_no : 'Job Done Customer Invoice Details';
        
        $html = view('collection.job_done_customer_invoice_pdf', $data);
        
        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="'.$title.'.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 0,
            'margin-left' => 10,
            'margin-bottom' => 15,
            'orientation' => 'Portrait',
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }
    
    public function print_job_done_customer_payment(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);
        
        $job_done_customer_payment = \App\Model\JobDoneCustomerPayment::find($request->id);
        $data['job_done_customer_payment'] = $job_done_customer_payment;
        $title = $job_done_customer_payment ? 'Job Done Customer Payment Details '.$job_done_customer_payment->receipt_no : 'Job Done Customer Payment Details';
        
        $html = view('collection.job_done_customer_payment_pdf', $data);
        
        $snappy = new \Knp\Snappy\Pdf($_SERVER['DOCUMENT_ROOT'].'/m3force/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        header('Content-Type: application/pdf');
        header('Content-Disposition: filename="'.$title.'.pdf"');
        $options = [
            'page-size' => 'A4',
            'margin-top' => 5,
            'margin-right' => 0,
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
        $payment = new \App\Model\JobDoneCustomerPayment();
        $payment->job_done_customer_id = $request->job_done_customer_id;
        $payment->payment_mode_id = isset($request->payment_mode['id']) ? $request->payment_mode['id'] : 0;
        $payment->collection_person_id = isset($request->collection_person['id']) ? $request->collection_person['id'] : 0;
        
        $last_id = 0;
        $last_payment = \App\Model\JobDoneCustomerPayment::select('id')->where('is_delete', 0)->orderBy('id', 'desc')->first();
        $last_id = $last_payment ? $last_payment->id : $last_id;
        $payment->receipt_no = 'REC/JB/'.date('m').'/'.date('y').'/'.sprintf('%05d', $last_id+1);
        $payment->receipt_date_time = date('Y-m-d', strtotime($request->receipt_date)).' '.$request->receipt_time;
        $payment->amount = $request->amount;
        $payment->cheque_no = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 1 ? $request->cheque_no : '';
        $payment->bank = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 3 ? $request->bank : '';
        $payment->realize_date = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 1 ? date('Y-m-d', strtotime($request->realize_date)) : '';
        $payment->remarks = $request->remarks;
        
        if($payment->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_done_customer_payment_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,' . $payment->id. ',' . $payment->job_done_customer_id. ',' . $payment->payment_mode_id. ',' . $payment->collection_person_id. ',' . $payment->receipt_no. ',' . $payment->receipt_date_time. ',' . $payment->amount. ',' . str_replace(',',' ',$payment->cheque_no). ',' . str_replace(',',' ',$payment->bank). ',' . $payment->realize_date. ',' . str_replace(',',' ',$payment->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $totatl_invoices = $total_payment = 0;            
            $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_invoices as $job_done_customer_invoice){
                $totatl_invoices += $job_done_customer_invoice->Quotation->quotation_value;
            }
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment){
                $total_payment += $job_done_customer_payment->amount;
            }
            
            $job_done_customer = \App\Model\JobDoneCustomer::find($payment->job_done_customer_id);
            if($job_done_customer){
                $job_done_customer->update_date = date('Y-m-d');
                $job_done_customer->pending_amount = $totatl_invoices - $total_payment;
                $job_done_customer->save();
            }
                        
            $total_payments = 0;
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment){
                $total_payments += $job_done_customer_payment->amount;
            }
            $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_invoices as $job_done_customer_invoice){
                if($job_done_customer_invoice->Quotation->quotation_value <= $total_payments){
                    $job_done_customer_invoice->payment_received = $job_done_customer_invoice->Quotation->quotation_value;
                    $job_done_customer_invoice->is_settled = 1;
                    $total_payments -= $job_done_customer_invoice->Quotation->quotation_value;
                } else{
                    $job_done_customer_invoice->payment_received = $total_payments;
                    $job_done_customer_invoice->is_settled = 0;
                    $total_payments = 0;
                }
                $job_done_customer_invoice->save();
            }
                    
            $result = array(
                'response' => true,
                'message' => 'Payment created successfully',
                'data' => $payment->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Payment creation failed'
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
        $payment = \App\Model\JobDoneCustomerPayment::find($id);
        $payment->job_done_customer_id = $request->job_done_customer_id;
        $payment->payment_mode_id = isset($request->payment_mode['id']) ? $request->payment_mode['id'] : 0;
        $payment->collection_person_id = isset($request->collection_person['id']) ? $request->collection_person['id'] : 0;
        $payment->receipt_no = $request->receipt_no;
        $payment->receipt_date_time = date('Y-m-d', strtotime($request->receipt_date)).' '.$request->receipt_time;
        $payment->amount = $request->amount;
        $payment->cheque_no = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 1 ? $request->cheque_no : '';
        $payment->bank = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 3 ? $request->bank : '';
        $payment->realize_date = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 1 ? date('Y-m-d', strtotime($request->realize_date)) : '';
        $payment->remarks = $request->remarks;
        
        if($payment->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_done_customer_payment_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,' . $payment->id. ',' . $payment->job_done_customer_id. ',' . $payment->payment_mode_id. ',' . $payment->collection_person_id. ',' . $payment->receipt_no. ',' . $payment->receipt_date_time. ',' . $payment->amount. ',' . str_replace(',',' ',$payment->cheque_no). ',' . str_replace(',',' ',$payment->bank). ',' . $payment->realize_date. ',' . str_replace(',',' ',$payment->remarks). ',' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $totatl_invoices = $total_payment = 0;            
            $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_invoices as $job_done_customer_invoice){
                $totatl_invoices += $job_done_customer_invoice->Quotation->quotation_value;
            }
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment){
                $total_payment += $job_done_customer_payment->amount;
            }
            
            $job_done_customer = \App\Model\JobDoneCustomer::find($payment->job_done_customer_id);
            if($job_done_customer){
                $job_done_customer->pending_amount = $totatl_invoices - $total_payment;
                $job_done_customer->save();
            }
                        
            $total_payments = 0;
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment){
                $total_payments += $job_done_customer_payment->amount;
            }
            $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_invoices as $job_done_customer_invoice){
                if($job_done_customer_invoice->Quotation->quotation_value <= $total_payments){
                    $job_done_customer_invoice->payment_received = $job_done_customer_invoice->Quotation->quotation_value;
                    $job_done_customer_invoice->is_settled = 1;
                    $total_payments -= $job_done_customer_invoice->Quotation->quotation_value;
                } else{
                    $job_done_customer_invoice->payment_received = $total_payments;
                    $job_done_customer_invoice->is_settled = 0;
                    $total_payments = 0;
                }
                $job_done_customer_invoice->save();
            }
             
            $result = array(
                'response' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment->id
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Payment updation failed'
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
    public function destroy($id)
    {
        $payment = \App\Model\JobDoneCustomerPayment::find($id);
        $payment->is_delete = 1;
        
        if($payment->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/job_done_customer_payment_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,' . $payment->id. ',,,,,,,,,,,' . date('Y-m-d H:i:s') . ',' . session()->get('users_id') . ',' . str_replace(',',' ',session()->get('username')) . PHP_EOL); 
            fclose($myfile);
            
            $totatl_invoices = $total_payment = 0;            
            $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_invoices as $job_done_customer_invoice){
                $totatl_invoices += $job_done_customer_invoice->Quotation->quotation_value;
            }
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment){
                $total_payment += $job_done_customer_payment->amount;
            }
            
            $job_done_customer = \App\Model\JobDoneCustomer::find($payment->job_done_customer_id);
            if($job_done_customer){
                $job_done_customer->pending_amount = $totatl_invoices - $total_payment;
                $job_done_customer->save();
            }
                        
            $total_payments = 0;
            $job_done_customer_payments = \App\Model\JobDoneCustomerPayment::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_payments as $job_done_customer_payment){
                $total_payments += $job_done_customer_payment->amount;
            }
            $job_done_customer_invoices = \App\Model\JobDoneCustomerInvoice::where('job_done_customer_id', $payment->job_done_customer_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($job_done_customer_invoices as $job_done_customer_invoice){
                if($job_done_customer_invoice->Quotation->quotation_value <= $total_payments){
                    $job_done_customer_invoice->payment_received = $job_done_customer_invoice->Quotation->quotation_value;
                    $job_done_customer_invoice->is_settled = 1;
                    $total_payments -= $job_done_customer_invoice->Quotation->quotation_value;
                } else{
                    $job_done_customer_invoice->payment_received = $total_payments;
                    $job_done_customer_invoice->is_settled = 0;
                    $total_payments = 0;
                }
                $job_done_customer_invoice->save();
            }
             
            $result = array(
                'response' => true,
                'message' => 'Payment deleted successfully'
            );
        } else {
            $result = array(
                'response' => false,
                'message' => 'Payment deletion failed'
            );
        }

        echo json_encode($result);
    }
}