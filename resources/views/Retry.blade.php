<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-red-500 to-orange-500 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-2xl rounded-2xl p-10 text-center max-w-md w-full">

        <div class="text-6xl mb-4">😕</div>

        <h2 class="text-2xl font-bold text-gray-800 mb-3">
            Payment {{ ucfirst($record->status) }}
        </h2>

        <p class="text-gray-600 mb-6">
            Something went wrong with your last payment. No worries, you can try again below.
        </p>

        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-6">
            <p class="text-yellow-700 font-semibold">
                Use code <span class="font-mono">{{ $record->promo_code }}</span> for 10% off
            </p>
            <p class="text-yellow-600 text-sm mt-1">
                New amount: €{{ number_format($discountedAmount, 2) }} instead of €{{ number_format($record->amount, 2) }}
            </p>
        </div>

        <form action="{{ route('payment.retryPay', $record->id) }}" method="POST" class="space-y-4">
            @csrf

            <label class="flex items-center justify-center gap-2 text-gray-700">
                <input type="checkbox" name="use_discount" value="1" checked>
                Apply discount code
            </label>

            <button type="submit"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold transition">
                Try Again 🔁
            </button>
        </form>

        <a href="{{ url('/') }}" class="block mt-4 text-sm text-gray-500 hover:underline">
            Cancel and go home
        </a>

    </div>

</body>
</html>