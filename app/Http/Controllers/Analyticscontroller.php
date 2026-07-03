<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $totalRefunded = Payment::sum('refunded_amount');
        $paidCount = Payment::where('status', 'paid')->count();
        $failedCount = Payment::whereIn('status', ['failed', 'expired', 'canceled'])->count();
        $pendingCount = Payment::where('status', 'pending')->count();

        $monthly = Payment::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as revenue'),
            DB::raw('COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_count'),
            DB::raw('COUNT(CASE WHEN status IN ("failed","expired","canceled") THEN 1 END) as failed_count')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('analytics', compact(
            'totalRevenue',
            'totalRefunded',
            'paidCount',
            'failedCount',
            'pendingCount',
            'monthly'
        ));
    }
}