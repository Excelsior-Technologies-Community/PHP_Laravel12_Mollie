<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use App\Models\Payment;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function isLocalHost(): bool
    {
        return str_contains(config('app.url'), 'localhost') || str_contains(config('app.url'), '127.0.0.1');
    }

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
            'amount' => 'required|numeric|min:1|max:10000',
            'email' => 'required|email',
        ]);

        $mollie = Mollie::api();

        $paymentData = [
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($request->amount, 2, '.', '')
            ],

            "description" => "Laravel Mollie Payment",

            "redirectUrl" => route('payment.success'),
        ];

        if (!$this->isLocalHost()) {
            $paymentData["webhookUrl"] = route('payment.webhook');
        }

        $payment = $mollie->payments->create($paymentData);

        Payment::create([
            'payment_id' => $payment->id,
            'amount' => $request->amount,
            'customer_email' => $request->email,
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

        $molliePayment = $mollie->payments->get($record->payment_id);

        if ($molliePayment->isPaid()) {

            $record->update([
                'status' => 'paid'
            ]);

            if (!$record->invoice_sent && $record->customer_email) {
                Mail::to($record->customer_email)->send(new InvoiceMail($record));
                $record->update(['invoice_sent' => true]);
            }

            return redirect('/')->with('success', 'Payment successful');

        } elseif ($molliePayment->isFailed() || $molliePayment->isExpired() || $molliePayment->isCanceled()) {

            $record->update([
                'status' => $molliePayment->isExpired() ? 'expired' : ($molliePayment->isCanceled() ? 'canceled' : 'failed'),
                'promo_code' => $record->promo_code ?? 'TRYAGAIN10',
                'retry_count' => $record->retry_count + 1,
            ]);

            return redirect()->route('payment.retry', $record->id);
        }

        return redirect('/')->with('error', 'Payment pending');
    }

    public function retry($id)
    {
        $record = Payment::findOrFail($id);

        $discountedAmount = round($record->amount * 0.9, 2);

        return view('retry', compact('record', 'discountedAmount'));
    }

    public function retryPay(Request $request, $id)
    {
        $record = Payment::findOrFail($id);

        $useDiscount = $request->has('use_discount');
        $amount = $useDiscount ? round($record->amount * 0.9, 2) : $record->amount;

        $mollie = Mollie::api();

        $paymentData = [
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($amount, 2, '.', '')
            ],

            "description" => "Laravel Mollie Payment (Retry)",

            "redirectUrl" => route('payment.success'),
        ];

        if (!$this->isLocalHost()) {
            $paymentData["webhookUrl"] = route('payment.webhook');
        }

        $payment = $mollie->payments->create($paymentData);

        Payment::create([
            'payment_id' => $payment->id,
            'amount' => $amount,
            'customer_email' => $record->customer_email,
            'status' => 'pending',
            'promo_code' => $useDiscount ? $record->promo_code : null,
        ]);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function destroy($id)
    {
        Payment::findOrFail($id)->delete();

        return redirect('/')->with('success', 'Payment deleted successfully');
    }
}