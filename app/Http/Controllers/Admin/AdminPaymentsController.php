<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\System\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function browse(Request $request)
    {
        roles()->checkAccessWithAbort('payments.browse');

        $items = payments()->getList($request);

        return view('admin.app.payments.browse', [
            'items' => $items,
            'sort_fields' => payments()->getSortFields(),
        ]);
    }

    public function read(Transaction $transaction)
    {
        roles()->checkAccessWithAbort('payments.read');
        $transaction->load('user');
        return view('admin.app.payments.read', compact('transaction'));
    }

    public function accept(Transaction $transaction, Request $request)
    {
        roles()->checkAccessWithAbort('payments.accept');

        $result = payments()->accept($transaction);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => 'completed'
                ]
            ]);
        }

        return back()->with('success', $result['message']);
    }

    public function refund(Transaction $transaction, Request $request)
    {
        roles()->checkAccessWithAbort('payments.refund');

        $reason = (string)$request->input('reason');
        $result = payments()->refund($transaction, $reason);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => 'failed'
                ]
            ]);
        }

        return back()->with('success', $result['message']);
    }

    public function changeBalance(Request $request)
    {
        roles()->checkAccessWithAbort('payments.balance');

        $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'amount' => ['required','numeric'],
            'reason' => ['nullable','string','max:500'],
        ]);

        $amount = (float)$request->input('amount');
        $result = payments()->changeBalance((int)$request->input('user_id'), $amount, (string)$request->input('reason'));

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'user_id' => $result['user']->id,
                    'new_balance' => $result['user']->balance,
                    'amount' => $result['amount']
                ]
            ]);
        }

        return back()->with('success', $result['message']);
    }

    public function balanceForm()
    {
        roles()->checkAccessWithAbort('payments.balance');

        return view('admin.app.payments.balance');
    }
}


