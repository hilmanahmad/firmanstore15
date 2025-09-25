<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $transactionSvc;
    public function __construct(TransactionService $transaction)
    {
        $this->transactionSvc = $transaction;
    }

    public function index(Request $request)
    {
        $data = [];
        if ($request->_token) {
            $data = Transaction::query()->with('detail')->whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date);
            if ($request->customer_id) {
                $data = $data->where('customer_id', $request->customer_id);
            }
            if ($request->item_id) {
                $data = $data->whereHas('detail', function ($q) use ($request) {
                    $q->where('item_id', $request->item_id);
                });
            }
            $data = $data->orderBy('created_at', 'DESC')->get();
        }
        return view('report.transaction', [
            'title' => 'Laporan Transaksi',
            'active' => 'report-transaction',
            'request' => $request,
            'data' => $data
        ]);
    }
}
