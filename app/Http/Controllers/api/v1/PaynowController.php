<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Balances;
use App\Models\Houses;
use App\Models\Logs;
use App\Models\Paynowlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Paynow\Payments\Paynow;
use Validator;

class PaynowController extends Controller
{
    public function payBill(Request $request)
    {
        ini_set('max_execution_time', 300);
        //start validation
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|between:2,10099.99',
        ]);


        $account = Accounts::where('account_number', Auth::user()->acc_id)->first();
        $balance = Balances::where('acc_id', $account->id)->first();

        //$startingBalance = $account->amount;
        $wallet = "ecocash";

        //get all data ready
        $email = Auth::user()->email;
        $phone = $request->phone;
        $amount = $request->amount;

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
        $invoice_name = "hre_bill_inv " . time();
        $payment = $paynow->createPayment($invoice_name, $email);

        $payment->add("Rent and rates", $amount);

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
                    $log->start_balance = $balance->amount;
                    $log->end_balance = $balance->amount;
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
}
