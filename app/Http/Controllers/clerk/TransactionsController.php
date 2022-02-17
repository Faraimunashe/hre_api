<?php

namespace App\Http\Controllers\clerk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index()
    {
        return view('clerk.transactions');
    }
}
