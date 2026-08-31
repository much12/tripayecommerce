<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard with summary statistics.
     */
    public function index(): Response
    {
        $recentInvoices = Invoice::latest()->take(5)->get()->map(function ($invoice) {
            $status = match ($invoice->status) {
                'PAID' => 'Selesai',
                'UNPAID' => 'Diproses',
                'EXPIRED', 'FAILED', 'REFUND' => 'Dibatalkan',
                default => 'Diproses',
            };

            return [
                'id' => $invoice->merchant_ref,
                'customer' => $invoice->buyer_email ?? 'Guest',
                'total' => $invoice->amount,
                'status' => $status,
                'date' => $invoice->created_at->format('d M Y'),
            ];
        });

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'revenue' => (int) Invoice::where('status', 'PAID')->sum('amount'),
                'orders' => Invoice::count(),
                'products' => Product::count(),
                'customers' => User::where('role', 'user')->count(),
            ],
            'recentOrders' => $recentInvoices,
        ]);
    }
}
