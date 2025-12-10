<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\Hiring;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function payment()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Deposit Methods';

        $paymentData = session('payment_data');

        if (!$paymentData) {
            $notify[] = ['error', 'Session Invalidate'];
            return redirect()->route('user.home')->withNotify($notify);
        }

        $amount = $paymentData['amount'];
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'amount'));
    }

    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Deposit Methods';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
        ]);


        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $data = new Deposit();
        $paymentData = session('payment_data');
        $amount      = $request->amount;

        if ($paymentData) {

            $column = array_keys($paymentData)[0];
            $value  = array_values($paymentData)[0];

            if ($column == 'hiring_id') {
                $order = Hiring::findOrFail($value);
            } elseif ($column == 'order_id') {
                $order = Order::findOrFail($value);
            } else {
                abort(401);
            }

            if ($order->payment_status == Status::PAYMENT_SUCCESS) {
                $notify[] = ['error', 'Payment for this order has already been completed'];
                return back()->withNotify($notify);
            }

            $amount = $order->amount;
            $data->$column = $value;

            if($request->amount != $order->amount){
                $notify[] = ['error', 'Invalid amount'];
                return back()->withNotify($notify);
            }
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = route('user.deposit.history');
        $data->failed_url = route('user.deposit.history');
        $data->save();

        session()->forget('payment_data');
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status',Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(@$data->session){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $trxDetails = 'Deposited via ' . $deposit->gatewayCurrency()->name;

            if ($deposit->hiring_id || $deposit->order_id) {
                $trxDetails = 'Payment completed ' . $deposit->gatewayCurrency()->name;
            }

            $transaction               = new Transaction();
            $transaction->user_id      = $deposit->user_id;
            $transaction->amount       = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $deposit->charge;
            $transaction->trx_type     = '+';
            $transaction->details      = $trxDetails;
            $transaction->trx          = $deposit->trx;
            $transaction->remark       = 'deposit';
            $transaction->save();


            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);

            if ($deposit->hiring_id) {

                $hiring = Hiring::find($deposit->hiring_id);
                $hiring->payment_status = Status::PAYMENT_SUCCESS;
                $hiring->save();

                $user->balance -= $hiring->amount;
                $user->save();

                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'A new hiring requested by ' . $user->username;
                $adminNotification->click_url = urlPath('admin.hiring.detail', $hiring->id);
                $adminNotification->save();

                $transaction                = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount        = $hiring->amount;
                $transaction->post_balance  = $user->balance;
                $transaction->trx_type      = '-';
                $transaction->details       = 'Payment pay for completing a new hiring task';
                $transaction->trx           = getTrx();
                $transaction->remark        = 'hiring_payment';
                $transaction->save();






                $general = gs();
                notify($hiring->influencer, 'HIRING_PENDING', [
                    'username'      => $user->username,
                    'title'         => $hiring->title,
                    'site_currency' => $general->cur_text,
                    'amount'        => showAmount($hiring->amount),
                    'hiring_no'     => $hiring->hiring_no,
                ]);
            } elseif ($deposit->order_id) {

                $order = Order::find($deposit->order_id);
                $order->payment_status = Status::PAYMENT_SUCCESS;
                $order->save();

                $user->balance -= $order->amount;
                $user->save();

                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'A new order placed by ' . $user->username;
                $adminNotification->click_url = urlPath('admin.order.detail', $order->id);
                $adminNotification->save();

                $transaction                = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount        = $order->amount;
                $transaction->post_balance  = $user->balance;
                $transaction->trx_type      = '-';
                $transaction->details       = 'Payment pay for completing a new service order';
                $transaction->trx           = getTrx();
                $transaction->remark        = 'order_payment';
                $transaction->save();


                $general = gs();
                notify($order->influencer, 'ORDER_PLACED', [
                    'username'      => $user->username,
                    'title'         => $order->title,
                    'site_currency' => $general->cur_text,
                    'amount'        => showAmount($order->amount),
                    'order_no'      => $order->order_no,
                ]);
            }


        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method','gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from '.$data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details',$data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount,currencyFormat:false),
            'amount' => showAmount($data->amount,currencyFormat:false),
            'charge' => showAmount($data->charge,currencyFormat:false),
            'rate' => showAmount($data->rate,currencyFormat:false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }


}
