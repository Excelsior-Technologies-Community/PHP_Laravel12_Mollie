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