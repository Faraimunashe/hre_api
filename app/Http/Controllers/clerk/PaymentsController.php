<?php

namespace App\Http\Controllers\clerk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\Balances;
use App\Models\Accounts;
use App\Models\Paynowlog;
use Illuminate\Support\Facades\Auth;

class PaymentsController extends Controller
{
    public function index()
    {
        return view('clerk.payment');
    }

    public function payment(Request $request)
    {
        ini_set('max_execution_time', 300);
        //start validation
        $request->validate([
            'accnum' => 'required|numeric',
            'phone' => 'required|string',
            'amount' => 'required|numeric|between:2,10099.99',
            'method' => 'required|string'
        ]);

        $account = Accounts::where('account_number', $request->accnum)->first();
        $balance = Balances::where('acc_id', $account->id)->first();
        // $account = Accounts::select('accounts.id', 'balances.amount', 'balances.id as balanceid')
        //     ->join('balances', 'accounts.id', '=', 'balances.acc_id')
        //     ->where('accounts.account_number', $request->accnum)
        //     ->get()->first();

        //$startingBalance = $account->amount;
        $wallet = "ecocash";

        //get all data ready
        $email = Auth::user()->email;
        $phone = $request->phone;
        $amount = $request->amount;
        $accnum = $request->accnum;

        /*determine type of wallet*/
        if (strpos($phone, '071') === 0) {
            $wallet = "onemoney";
        }

        $paynow = new \Paynow\Payments\Paynow(
            "11336",
            "1f4b3900-70ee-4e4c-9df9-4a44490833b6",
            route('payment'),
            route('payment'),
        );

        // Create Payments
        $invoice_name = "Invoice " . time();
        $payment = $paynow->createPayment($invoice_name, $email);

        $payment->add("Residential Bill", $amount);

        $response = $paynow->sendMobile($payment, $phone, $wallet);


        // Check transaction success
        if ($response->success()) {

            $timeout = 9;
            $count = 0;

            while (true) {
                sleep(3);
                // Get the status of the transaction
                // Get transaction poll URL
                $pollUrl = $response->pollUrl();
                $status = $paynow->pollTransaction($pollUrl);

                // $status_update = $paynow->processStatusUpdate();
                echo $paynow->getResultUrl();
                //Check if paid
                if ($status->paid()) {
                    // Yay! Transaction was paid for
                    // You can update transaction status here
                    // Then route to a payment successful
                    $info = $status->data();

                    $paynowdb = new Paynowlog();
                    $paynowdb->reference = $info['reference'];
                    $paynowdb->paynow_reference = $info['paynowreference'];
                    $paynowdb->amount = $info['amount'];
                    $paynowdb->status = $info['status'];
                    $paynowdb->poll_url = $info['pollurl'];
                    $paynowdb->hash = $info['hash'];
                    $paynowdb->save();


                    $endbal = $balance - $info['amount'];

                    $log = new Logs();
                    $log->acc_id = $account->id;
                    $log->action = 'paid';
                    $log->amount = $info['amount'];
                    $log->start_balance = $balance->amount;
                    $log->end_balance = $endbal;
                    $log->status = $info['status'];
                    $log->method = $wallet;
                    $log->reference = $info['paynowreference'];
                    $log->save();

                    //update balance
                    $balance->amount = $endbal;
                    $balance->save();

                    return dd([
                        'status' => 'SUCCESS',
                        'message' => 'payment was successful'

                    ]);
                }


                $count++;
                if ($count > $timeout) {
                    $info = $status->data();

                    $paynowdb = new Paynowlog();
                    $paynowdb->reference = $info['reference'];
                    $paynowdb->paynow_reference = $info['paynowreference'];
                    $paynowdb->amount = $info['amount'];
                    $paynowdb->status = $info['status'];
                    $paynowdb->poll_url = $info['pollurl'];
                    $paynowdb->hash = $info['hash'];
                    $paynowdb->save();

                    $log = new Logs();
                    $log->acc_id = $account->id;
                    $log->action = 'payment';
                    $log->amount = $info['amount'];
                    $log->start_balance = 20000.50;
                    $log->end_balance = 20000.50;
                    $log->status = $info['status'];
                    $log->method = $wallet;
                    $log->reference = $info['paynowreference'];
                    $log->save();

                    return dd([
                        'status' => 'WARNING',
                        'message' => 'Time out',
                        'test' => $info,
                    ]);
                } //endif
            } //endwhile
        } //endif


        //total fail
        return dd([
            'status' => 'FAILED',
            'message' => 'Error processing payment'

        ]);
    }

    public function status(Request $request)
    {
        $reference = $request->reference;
        $paynow_reference = $request->paynowreference;
        $status = $request->status;
        $amount = $request->amount;
        $poll_url = $request->pollurl;
        $hash = $request->hash;

        $paynowsave = new Paynowlog();
        $paynowsave->reference = $reference;
        $paynowsave->paynow_reference = $paynow_reference;
        $paynowsave->status = $status;
        $paynowsave->amount = $amount;
        $paynowsave->poll_url = $poll_url;
        $paynowsave->hash = $hash;
        $paynowsave->save();

        return dd([
            'status' => 'Tasvikako kuUpadate url'
        ]);
    }
}
