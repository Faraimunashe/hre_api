<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function logs()
    {
        $logs = Logs::where('acc_id', Auth::user()->acc_id)->get();

        if (is_null($logs)) {
            return response()->json([
                'message' => 'FAILED',
                'logs' => [
                    'status' => 'nothing to show'
                ]
            ]);
        }

        return response()->json([
            'message' => 'SUCCESS',
            'logs' => Logs::where('acc_id', Auth::user()->acc_id)->get()
        ]);
    }
}
