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