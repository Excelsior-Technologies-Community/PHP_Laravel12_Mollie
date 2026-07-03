<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payment;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        $pdf = Pdf::loadView('invoice.pdf', ['payment' => $this->payment]);

        return $this->subject('Your Payment Invoice #' . $this->payment->id)
            ->view('emails.invoice')
            ->attachData($pdf->output(), 'invoice-' . $this->payment->id . '.pdf');
    }
}