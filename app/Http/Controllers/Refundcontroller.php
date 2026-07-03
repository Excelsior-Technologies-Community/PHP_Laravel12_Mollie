<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use App\Models\Payment;

class RefundController extends Controller
{
    public function refund(Request $request, $id)
    {
        $record = Payment::findOrFail($id);

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $record->remainingRefundable(),
        ]);

        if ($record->status !== 'paid') {
            return redirect('/')->with('error', 'Only paid payments can be refunded');
        }

        $mollie = Mollie::api();
        $molliePayment = $mollie->payments->get($record->payment_id);

        $molliePayment->refund([
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($request->refund_amount, 2, '.', ''),
            ],
        ]);

        $newRefundedAmount = $record->refunded_amount + $request->refund_amount;

        $record->update([
            'refunded_amount' => $newRefundedAmount,
            'refund_status' => $newRefundedAmount >= $record->amount ? 'fully_refunded' : 'partially_refunded',
        ]);

        return redirect('/')->with('success', 'Refund processed successfully');
    }
}