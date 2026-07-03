<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Analytics</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gradient-to-br from-indigo-500 to-purple-700 min-h-screen py-10 px-4">

    <div class="max-w-6xl mx-auto">

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white">📊 Payment Analytics</h1>
            <a href="{{ url('/') }}" class="bg-white text-indigo-700 px-4 py-2 rounded-lg font-semibold">
                Back to Payments
            </a>
        </div>

        <div class="grid md:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-indigo-600">€{{ number_format($totalRevenue, 2) }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <p class="text-gray-500 text-sm">Total Refunded</p>
                <p class="text-3xl font-bold text-red-500">€{{ number_format($totalRefunded, 2) }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <p class="text-gray-500 text-sm">Successful Payments</p>
                <p class="text-3xl font-bold text-green-600">{{ $paidCount }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <p class="text-gray-500 text-sm">Failed Payments</p>
                <p class="text-3xl font-bold text-red-600">{{ $failedCount }}</p>
            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-8">

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Monthly Revenue</h2>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Success vs Failed</h2>
                <canvas id="statusChart"></canvas>
            </div>

        </div>

    </div>

    <script>
        const monthlyData = @json($monthly);

        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(m => m.month),
                datasets: [{
                    label: 'Revenue (€)',
                    data: monthlyData.map(m => m.revenue),
                    backgroundColor: '#4f46e5'
                }]
            },
            options: { responsive: true }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Paid', 'Failed', 'Pending'],
                datasets: [{
                    data: [{{ $paidCount }}, {{ $failedCount }}, {{ $pendingCount }}],
                    backgroundColor: ['#16a34a', '#dc2626', '#f59e0b']
                }]
            },
            options: { responsive: true }
        });
    </script>

</body>
</html>