<?php

namespace App\Http\Controllers\clerk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounts;

class AccountsController extends Controller
{
    public function index()
    {
        return view('clerk.Accounts', [
            'accounts' => Accounts::all()
        ]);
    }

    public function newA()
    {
        return view('clerk.add-account');
    }

    public function addnew(Request $request)
    {
        $request->validate([
            'fname' => 'string|required',
            'lname' => 'string|required',
            'address' => 'string|required',
            'gender' => 'string|required|max:6|min:4'
        ]);

        //rand number
        function generateRandomString($length = 16)
        {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $accnum = generateRandomString();


        $checkAcc = Accounts::where('account_number', $accnum)->first();

        if (is_null($checkAcc)) {

            $acc = new Accounts();
            $acc->account_number = $accnum;
            $acc->fname = $request->fname;
            $acc->lname = $request->lname;
            $acc->address = $request->address;
            $acc->gender = $request->gender;
            $acc->save();

            return redirect()->back()->with('success', $accnum);
        }

        return redirect()->back()->with('error', 'Retry account number needs a retry');
    }
}
