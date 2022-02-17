<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Balances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($login)) {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Invalid login credintials'
            ]);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        $account_balance = Balances::where('acc_id', Auth::user()->acc_id)->first();
        if (is_null($account_balance)) {
            $new_balance = new Balances();
            $new_balance->acc_id = Auth::user()->acc_id;
            $new_balance->amount = 00.00;
        }

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Login successful',
            'data' => [
                'user' => Auth::user(),
                'access_token' => $accessToken,
                'account' => Accounts::where('id', Auth::user()->acc_id)->first(),
                'balance' => Balances::where('acc_id', Auth::user()->acc_id)->first()
            ]
        ]);
    }

    public function signup(Request $request)
    {
        $rules = [
            'name' => 'unique:users|required',
            'email' => 'unique:users|email|required',
            'password' => 'required|string',
            'acc_id' => 'unique:users|required|numeric'
        ];

        $input     = $request->only('name', 'email', 'password', 'acc_id');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response([
                'status' => 'FAILED',
                'message' => 'invalid details provided try again'
            ]);
        }

        $name = $request->name;
        $email    = $request->email;
        $password = $request->password;
        $user     = User::create(['acc_id' => $request->acc_id, 'name' => $name, 'email' => $email, 'password' => Hash::make($password)]);

        if (!$user) {
            return response([
                'status' => 'FAILED',
                'message' => 'Signup failed due to database'

            ]);
        }




        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'registration successful, login',
            'data' => [
                'user' => Auth::user()
            ]
        ]);
    }


    public function check_account(Request $request)
    {
        $rules = [
            'accnum' => 'numeric|required',
            'fname' => 'string|required',
            'lname' => 'required|string'
        ];

        $input     = $request->only('accnum', 'fname', 'lname');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response([
                'status' => 'FAILED',
                'message' => 'invalid details provided'
            ]);
        }

        $account = Accounts::where('account_number', $request->accnum)
            ->where('fname', $request->fname)
            ->where('lname', $request->lname)
            ->first();

        if (is_null($account)) {
            //return no user found
            return response([
                'status' => 'FAILED',
                'message' => 'No account with provided details was found!'
            ]);
        } else {
            //allow creation of account
            return response([
                'status' => 'SUCCESS',
                'message' => 'Account was found proceed to signup',
                'acc_id' => $account->id,
                'names' => $account->fname . ' ' . $account->lname
            ]);
        }
    }
}
