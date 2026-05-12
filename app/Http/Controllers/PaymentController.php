<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $payments = Payment::when($search, function ($query) use ($search) {
            $query->where('payment_id', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('amount', 'like', "%{$search}%");
        })
            ->oldest()
            ->paginate(5);

        return view('payment', compact('payments'));
    }

    public function pay(Request $request)
    {
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

            "redirectUrl" => route('payment.success'),
        ]);

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

        $record = Payment::latest()->first();

        if (!$record) {
            return redirect('/')->with('error', 'No payment found');
        }

        $payment = $mollie->payments->get($record->payment_id);

        if ($payment->isPaid()) {

            $record->update([
                'status' => 'paid'
            ]);

            return redirect('/')->with('success', 'Payment successful');
        } elseif ($payment->isFailed()) {

            $record->update([
                'status' => 'failed'
            ]);

            return redirect('/')->with('error', 'Payment failed');
        }

        return redirect('/')->with('error', 'Payment pending');
    }

    public function destroy($id)
    {
        Payment::findOrFail($id)->delete();

        return redirect('/')->with('success', 'Payment deleted successfully');
    }
}