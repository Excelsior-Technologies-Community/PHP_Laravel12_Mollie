<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Mollie Payment</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-500 to-purple-700 min-h-screen py-10 px-4">

    <div class="max-w-6xl mx-auto">

        <!-- Title -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">
                💳 Laravel Mollie Payment
            </h1>

            <p class="text-indigo-100">
                Secure online payment integration using Mollie
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">

            <!-- Payment Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">

                <h2 class="text-2xl font-bold mb-6 text-gray-800">
                    Make Payment
                </h2>

                <!-- Success -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Error -->
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Validation -->
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <ul class="list-disc ml-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('payment.pay') }}" method="POST" class="space-y-5">

                    @csrf

                    <div>
                        <label class="block mb-2 text-gray-700 font-semibold">
                            Enter Amount (€)
                        </label>

                        <input type="number" name="amount" step="0.01" min="1" placeholder="Enter amount"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">

                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-semibold transition">

                        Pay Now 🚀

                    </button>

                </form>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">

                <div class="flex justify-between items-center mb-5">

                    <h2 class="text-2xl font-bold text-gray-800">
                        Payment History
                    </h2>

                    <form method="GET">

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                            class="border px-4 py-2 rounded-lg">

                    </form>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full border-collapse">

                        <thead>
                            <tr class="bg-indigo-100 text-gray-700">
                                <th class="p-3 text-left">#</th>
                                <th class="p-3 text-left">Payment ID</th>
                                <th class="p-3 text-left">Amount</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($payments as $payment)

                                <tr class="border-b hover:bg-gray-50">

                                    <td class="p-3">
                                        {{ $payment->id }}
                                    </td>

                                    <td class="p-3">
                                        {{ $payment->payment_id }}
                                    </td>

                                    <td class="p-3">
                                        €{{ $payment->amount }}
                                    </td>

                                    <td class="p-3">

                                        @if($payment->status == 'paid')

                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                                Paid
                                            </span>

                                        @elseif($payment->status == 'failed')

                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                                Failed
                                            </span>

                                        @else

                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">
                                                Pending
                                            </span>

                                        @endif

                                    </td>

                                    <td class="p-3">

                                        <form action="{{ route('payment.delete', $payment->id) }}" method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button onclick="return confirm('Delete payment?')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center py-5 text-gray-500">
                                        No payments found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <!-- Pagination -->
                <div class="mt-5">
                    {{ $payments->links() }}
                </div>

            </div>

        </div>

    </div>

</body>

</html>