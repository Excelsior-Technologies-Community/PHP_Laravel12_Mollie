<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function pay(Request $request)
    {
        //  Validate input
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $mollie = Mollie::api();

        $payment = $mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($request->amount, 2, '.', '')
            ],
            "description" => "Laravel Mollie Payment",

            //  Redirect only (no webhook to avoid error)
            "redirectUrl" => route('payment.success'),
        ]);

        //  Save payment
        Payment::create([
            'payment_id' => $payment->id,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function success()
    {
        $mollie = Mollie::api();

        // Get latest payment (simple logic)
        $record = Payment::latest()->first();

        if (!$record) {
            return redirect('/')->with('error', 'No payment found');
        }

        $payment = $mollie->payments->get($record->payment_id);

        if ($payment->isPaid()) {
            $record->update(['status' => 'paid']);
            return view('success');
        } elseif ($payment->isFailed()) {
            $record->update(['status' => 'failed']);
            return redirect('/')->with('error', 'Payment failed');
        }

        return redirect('/')->with('error', 'Payment pending');
    }
}