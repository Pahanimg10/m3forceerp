<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
    public function credit_supplier()
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

        return view('payment.credit_supplier', $data);
    }

    public function credit_supplier_list()
    {
        $credit_suppliers = \App\Model\CreditSupplier::select('id', 'contact_id', 'update_date', 'pending_amount')
                ->with(['Contact' => function ($query) {
                    $query->select('id', 'code', 'name', 'address', 'contact_no', 'email');
                }])
//                ->where('pending_amount', '>', 0)
                ->where('is_delete', 0)
                ->get();

        return response($credit_suppliers);
    }

    public function credit_supplier_detail(Request $request)
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

        $data['credit_supplier_id'] = $request->id;

        return view('payment.credit_supplier_detail', $data);
    }

    public function get_credit_supplier_detail(Request $request)
    {
        $credit_supplier = \App\Model\CreditSupplier::select('id', 'contact_id')
                ->with(['Contact' => function ($query) {
                    $query->select('id', 'name', 'contact_no', 'email', 'address');
                }])
                ->find($request->id);
        $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::select('id', 'credit_supplier_id', 'good_rececive_id')
                ->with(['GoodReceive' => function ($query) {
                    $query->select('id', 'good_receive_no', 'good_receive_date_time', 'good_receive_value');
                }])
                ->where('credit_supplier_id', $request->id)
                ->where('is_delete', 0)
                ->get();
        $credit_supplier_payments = \App\Model\CreditSupplierPayment::select('id', 'credit_supplier_id', 'payment_date_time', 'payment_no', 'amount')
                ->where('credit_supplier_id', $request->id)
                ->where('is_delete', 0)
                ->get();

        $data = [
            'credit_supplier' => $credit_supplier,
            'credit_supplier_good_receives' => $credit_supplier_good_receives,
            'credit_supplier_payments' => $credit_supplier_payments,
            'permission' => ! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')) && ! in_array(3, session()->get('user_group')),
        ];

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

        $data['credit_supplier_payment_id'] = $request->id;
        $data['credit_supplier_id'] = $request->credit_supplier_id;

        return view('payment.credit_supplier_payment', $data);
    }

    public function find_credit_supplier_payment(Request $request)
    {
        $credit_supplier_payment = \App\Model\CreditSupplierPayment::select('id', 'credit_supplier_id', 'payment_mode_id', 'payment_date_time', 'payment_no', 'amount', 'cheque_no', 'bank', 'remarks')
                ->with(['PaymentMode' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->find($request->id);

        return response($credit_supplier_payment);
    }

    public function print_credit_supplier_good_receive(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $credit_supplier_good_receive = \App\Model\CreditSupplierGoodReceive::find($request->id);
        $data['credit_supplier_good_receive'] = $credit_supplier_good_receive;
        $title = $credit_supplier_good_receive ? 'Credit Supplier Good Receive Details '.$credit_supplier_good_receive->GoodReceive->good_receive_no : 'Credit Supplier Good Receive Details';

        $html = view('payment.credit_supplier_good_receive_pdf', $data);

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
            'footer-font-size' => 8,
        ];
        echo $snappy->getOutputFromHtml($html, $options);
    }

    public function print_credit_supplier_payment(Request $request)
    {
        $data['company'] = \App\Model\Company::find(1);

        $credit_supplier_payment = \App\Model\CreditSupplierPayment::find($request->id);
        $data['credit_supplier_payment'] = $credit_supplier_payment;
        $title = $credit_supplier_payment ? 'Credit Supplier Payment Details '.$credit_supplier_payment->payment_no : 'Credit Supplier Payment Details';

        $html = view('payment.credit_supplier_payment_pdf', $data);

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
        $payment = new \App\Model\CreditSupplierPayment();
        $payment->credit_supplier_id = $request->credit_supplier_id;
        $payment->payment_mode_id = isset($request->payment_mode['id']) ? $request->payment_mode['id'] : 0;

        $last_id = 0;
        $last_payment = \App\Model\CreditSupplierPayment::select('id')->where('credit_supplier_id', $request->credit_supplier_id)->where('is_delete', 0)->orderBy('id', 'desc')->first();
        $last_id = $last_payment ? $last_payment->id : $last_id;
        $payment->payment_no = 'P/S/'.date('m').'/'.date('y').'/'.$request->credit_supplier_id.'/'.sprintf('%05d', $last_id + 1);
        $payment->payment_date_time = date('Y-m-d', strtotime($request->payment_date)).' '.$request->payment_time;
        $payment->amount = $request->amount;
        $payment->cheque_no = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 1 ? $request->cheque_no : '';
        $payment->bank = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 3 ? $request->bank : '';
        $payment->remarks = $request->remarks;

        if ($payment->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/payment_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Created,'.$payment->id.','.$payment->credit_supplier_id.','.$payment->payment_mode_id.','.$payment->payment_no.','.$payment->payment_date_time.','.$payment->amount.','.str_replace(',', ' ', $payment->cheque_no).','.str_replace(',', ' ', $payment->bank).','.str_replace(',', ' ', $payment->remarks).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $totatl_good_receive = $total_payment = 0;
            $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_good_receives as $credit_supplier_good_receive) {
                $totatl_good_receive += $credit_supplier_good_receive->GoodReceive->good_receive_value;
            }
            $credit_supplier_payments = \App\Model\CreditSupplierPayment::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_payments as $credit_supplier_payment) {
                $total_payment += $credit_supplier_payment->amount;
            }

            $credit_supplier = \App\Model\CreditSupplier::find($payment->credit_supplier_id);
            if ($credit_supplier) {
                $credit_supplier->update_date = date('Y-m-d');
                $credit_supplier->pending_amount = $totatl_good_receive - $total_payment;
                $credit_supplier->save();
            }
            $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_good_receives as $credit_supplier_good_receive) {
                if ($credit_supplier_good_receive->GoodReceive->good_receive_value <= $total_payment) {
                    $credit_supplier_good_receive->payment_done = $credit_supplier_good_receive->GoodReceive->good_receive_value;
                    $credit_supplier_good_receive->is_settled = 1;
                    $total_payment -= $credit_supplier_good_receive->GoodReceive->good_receive_value;
                } else {
                    $credit_supplier_good_receive->payment_done = $total_payment;
                    $credit_supplier_good_receive->is_settled = 0;
                    $total_payment = 0;
                }
                $credit_supplier_good_receive->save();
            }

            $result = [
                'response' => true,
                'message' => 'Payment created successfully',
                'data' => $payment->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Payment creation failed',
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
        $payment = \App\Model\CreditSupplierPayment::find($id);
        $payment->credit_supplier_id = $request->credit_supplier_id;
        $payment->payment_mode_id = isset($request->payment_mode['id']) ? $request->payment_mode['id'] : 0;
        $payment->payment_no = $request->payment_no;
        $payment->payment_date_time = date('Y-m-d', strtotime($request->payment_date)).' '.$request->payment_time;
        $payment->amount = $request->amount;
        $payment->cheque_no = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 1 ? $request->cheque_no : '';
        $payment->bank = isset($request->payment_mode['id']) && $request->payment_mode['id'] == 3 ? $request->bank : '';
        $payment->remarks = $request->remarks;

        if ($payment->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/payment_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Updated,'.$payment->id.','.$payment->credit_supplier_id.','.$payment->payment_mode_id.','.$payment->payment_no.','.$payment->payment_date_time.','.$payment->amount.','.str_replace(',', ' ', $payment->cheque_no).','.str_replace(',', ' ', $payment->bank).','.str_replace(',', ' ', $payment->remarks).','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $totatl_good_receive = $total_payment = 0;
            $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_good_receives as $credit_supplier_good_receive) {
                $totatl_good_receive += $credit_supplier_good_receive->GoodReceive->good_receive_value;
            }
            $credit_supplier_payments = \App\Model\CreditSupplierPayment::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_payments as $credit_supplier_payment) {
                $total_payment += $credit_supplier_payment->amount;
            }

            $credit_supplier = \App\Model\CreditSupplier::find($payment->credit_supplier_id);
            if ($credit_supplier) {
                $credit_supplier->pending_amount = $totatl_good_receive - $total_payment;
                $credit_supplier->save();
            }
            $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_good_receives as $credit_supplier_good_receive) {
                if ($credit_supplier_good_receive->GoodReceive->good_receive_value <= $total_payment) {
                    $credit_supplier_good_receive->payment_done = $credit_supplier_good_receive->GoodReceive->good_receive_value;
                    $credit_supplier_good_receive->is_settled = 1;
                    $total_payment -= $credit_supplier_good_receive->GoodReceive->good_receive_value;
                } else {
                    $credit_supplier_good_receive->payment_done = $total_payment;
                    $credit_supplier_good_receive->is_settled = 0;
                    $total_payment = 0;
                }
                $credit_supplier_good_receive->save();
            }

            $result = [
                'response' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment->id,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Payment updation failed',
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
    public function destroy($id)
    {
        $payment = \App\Model\CreditSupplierPayment::find($id);
        $payment->is_delete = 1;

        if ($payment->save()) {
            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/payment_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Deleted,'.$payment->id.',,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $totatl_good_receive = $total_payment = 0;
            $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_good_receives as $credit_supplier_good_receive) {
                $totatl_good_receive += $credit_supplier_good_receive->GoodReceive->good_receive_value;
            }
            $credit_supplier_payments = \App\Model\CreditSupplierPayment::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_payments as $credit_supplier_payment) {
                $total_payment += $credit_supplier_payment->amount;
            }

            $credit_supplier = \App\Model\CreditSupplier::find($payment->credit_supplier_id);
            if ($credit_supplier) {
                $credit_supplier->pending_amount = $totatl_good_receive - $total_payment;
                $credit_supplier->save();
            }
            $credit_supplier_good_receives = \App\Model\CreditSupplierGoodReceive::where('credit_supplier_id', $payment->credit_supplier_id)
                    ->where('is_delete', 0)
                    ->get();
            foreach ($credit_supplier_good_receives as $credit_supplier_good_receive) {
                if ($credit_supplier_good_receive->GoodReceive->good_receive_value <= $total_payment) {
                    $credit_supplier_good_receive->payment_done = $credit_supplier_good_receive->GoodReceive->good_receive_value;
                    $credit_supplier_good_receive->is_settled = 1;
                    $total_payment -= $credit_supplier_good_receive->GoodReceive->good_receive_value;
                } else {
                    $credit_supplier_good_receive->payment_done = $total_payment;
                    $credit_supplier_good_receive->is_settled = 0;
                    $total_payment = 0;
                }
                $credit_supplier_good_receive->save();
            }

            $result = [
                'response' => true,
                'message' => 'Payment deleted successfully',
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Payment deletion failed',
            ];
        }

        echo json_encode($result);
    }
}
