# PHP_Laravel12_Mollie

## Introduction

PHP_Laravel12_Mollie is a Laravel 12 project that demonstrates the integration of the Mollie Payment Gateway in a web application.

This project allows users to securely make payments using Mollie, a popular payment provider, and handle the full payment flow from initiation to verification. It also records transaction details in the database to track payment status.

Key goals of this project:

- Learn how to integrate a real-world payment gateway in Laravel.

- Understand the full payment flow: Create → Redirect → Verify.

- Handle payment statuses like success, failed, and pending.

- Use clean MVC architecture with secure API key management via .env files.

---

## Project Overview

This Laravel project demonstrates a complete payment system with the following functionality:

1) Payment Initiation

- Users enter an amount and submit the payment request.

2) Mollie Checkout Redirection

- The application creates a payment via Mollie API and redirects the user to the Mollie checkout page.

3) Payment Status Handling

- After the user completes the payment, the app verifies the payment status (paid, failed, or pending) and updates the database accordingly.

4) Database Storage

- All payment transactions are stored in a MySQL database with fields: payment_id, amount, and status.

5) Secure API Integration

- The Mollie API key is stored securely in the .env file, ensuring that sensitive credentials are not hardcoded.

6) Web Interface

- A simple and clean front-end using Tailwind CSS, providing a user-friendly payment experience.

---

## Features

* Laravel 12 setup
* Mollie payment gateway integration
* Payment flow (Create → Redirect → Verify)
* Success & failure handling
* Clean MVC architecture
* Secure API usage with environment variables

---

## Requirements

* PHP >= 8.2
* Composer
* Laravel 12
* MySQL
* Mollie Account (Test API Key)

---

## Step 1: Create Laravel 12 Project

Run the following command:

```bash
composer create-project laravel/laravel PHP_Laravel12_Mollie "12.*"
```

Move into the project:

```bash
cd PHP_Laravel12_Mollie
```

---

## Step 2: Install Mollie Package

Install official Mollie Laravel package:

```bash
composer require mollie/laravel-mollie
```

---

## Step 3: Publish Mollie Config

```bash
php artisan vendor:publish --provider="Mollie\Laravel\MollieServiceProvider"
```

---

## Step 4: Mollie API Key Setup

### 1) Create Mollie Account

- Go to: https://www.mollie.com

- Click Sign Up

- Complete registration (email + password)

Note: Mollie may not fully support some countries (like India), but you can still access test mode.

### 2) Access Developer Dashboard

Login to your Mollie account

Navigate to:

```
Dashboard → Developers → API Keys
```
### 3) Copy Test API Key

You will see two types of keys:

```
test_xxxxxxxxxxxxx   ← Use this for development
live_xxxxxxxxxxxxx   ← Use this for production
```
- Copy the test key

---

## Step 5: Database Setup

Update .env

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_mollie
DB_USERNAME=root
DB_PASSWORD=
```
---

## Step 6: Add Mollie API Key

Update `.env` file:

```env
MOLLIE_KEY=your_test_api_key_here
```

You can get this from Mollie Dashboard.

---

## Step 7: Configure Services

Update `config/services.php`:

```php
'mollie' => [
    'key' => env('MOLLIE_KEY'),
],
```

---

## Step 8: Update mollie.php

Update `config/mollie.php`:

```php
'key' => env('MOLLIE_KEY', 'test_xxxxxxxxxxxxxxxxxxxxxxxxxx'),
```
---

## Step 9: Create Migration (Payments Table)

```bash
php artisan make:migration create_payments_table
```

### database/migrations/xxxx_create_payments_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable();
            $table->string('amount');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

## Step 10: Create Model

```bash
php artisan make:model Payment
```

### app/Models/Payment.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['payment_id', 'amount', 'status'];
}
```

---

## Step 11: Create Controller

```bash
php artisan make:controller PaymentController
```

### app/Http/Controllers/PaymentController.php

```php
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
```

---

## Step 12: Define Routes

### routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'index']);
Route::post('/pay', [PaymentController::class, 'pay'])->name('payment.pay');
Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
```

---

## Step 13: Create Views

### resources/views/payment.blade.php

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mollie Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">

<div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">💳 Make Payment</h2>

    <!-- Error Message -->
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('payment.pay') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-gray-600 mb-2 font-medium">Enter Amount (€)</label>
            <input type="number" name="amount" step="0.01" min="1" placeholder="e.g. 10.00" required
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
            Pay Now 🚀
        </button>
    </form>

    <p class="text-sm text-gray-500 text-center mt-6">Secure payment powered by Mollie</p>
</div>

</body>
</html>
```

---

### resources/views/success.blade.php

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-xl rounded-2xl p-10 text-center max-w-md w-full">

        <!-- Icon -->
        <div class="text-6xl mb-4">
            ✅
        </div>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-gray-800 mb-3">
            Payment Successful!
        </h2>

        <p class="text-gray-600 mb-6">
            Thank you for your payment. Your transaction has been completed successfully.
        </p>

        <!-- Button -->
        <a href="{{ url('/') }}" 
           class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
            Make Another Payment
        </a>

    </div>

</body>
</html>
```

---

## Step 14: Run Project

```bash
php artisan serve
```

Visit:

```
http://127.0.0.1:8000
```

---

## Payment Flow Explained

1. User enters amount
2. Request goes to controller
3. Mollie creates payment
4. User redirected to Mollie checkout
5. After payment:

   * Redirect to success page
  

---

## Output

<img src="screenshots/Screenshot 2026-03-18 131057.png" width="1000">

<img src="screenshots/Screenshot 2026-03-18 131122.png" width="1000">

<img src="screenshots/Screenshot 2026-03-18 131232.png" width="1000">

<img src="screenshots/Screenshot 2026-03-18 131248.png" width="1000">

<img src="screenshots/Screenshot 2026-03-18 131306.png" width="1000">

---

## Project Structure

```
PHP_Laravel12_Mollie/
│
├── app/
│   ├── Models/
│   │   └── Payment.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── PaymentController.php
│
├── config/
│   ├── services.php
│   └── Mollie.php
│
├── database/
│   └── migrations/
│       └── create_payments_table.php
│
├── resources/
│   └── views/
│       ├── payment.blade.php
│       └── success.blade.php
│
├── routes/
│   └── web.php
│
├── .env
└── composer.json
```

---

Your PHP_Laravel12_Mollie Project is now ready!



