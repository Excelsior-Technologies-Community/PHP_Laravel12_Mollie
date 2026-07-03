<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use App\Models\Payment;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $paymentId = $request->input('id');

        $record = Payment::where('payment_id', $paymentId)->first();

        if (!$record) {
            return response()->json(['message' => 'not found'], 404);
        }

        $mollie = Mollie::api();
        $molliePayment = $mollie->payments->get($paymentId);

        if ($molliePayment->isPaid()) {

            $record->update(['status' => 'paid']);

            if (!$record->invoice_sent && $record->customer_email) {
                Mail::to($record->customer_email)->send(new InvoiceMail($record));
                $record->update(['invoice_sent' => true]);
            }

        } elseif ($molliePayment->isFailed()) {
            $record->update(['status' => 'failed']);
        } elseif ($molliePayment->isExpired()) {
            $record->update(['status' => 'expired']);
        } elseif ($molliePayment->isCanceled()) {
            $record->update(['status' => 'canceled']);
        }

        return response()->json(['message' => 'ok']);
    }
}