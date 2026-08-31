<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'revenue' => 48750000,
                'orders' => 342,
                'products' => Product::count(),
                'customers' => User::where('role', 'user')->count(),
            ],
            'recentOrders' => [
                ['id' => 'INV-1042', 'customer' => 'Andi Saputra', 'total' => 349000, 'status' => 'Selesai', 'date' => '31 Agu 2026'],
                ['id' => 'INV-1041', 'customer' => 'Siti Rahma', 'total' => 189000, 'status' => 'Dikirim', 'date' => '31 Agu 2026'],
                ['id' => 'INV-1040', 'customer' => 'Budi Hartono', 'total' => 504000, 'status' => 'Diproses', 'date' => '30 Agu 2026'],
                ['id' => 'INV-1039', 'customer' => 'Dewi Lestari', 'total' => 229000, 'status' => 'Selesai', 'date' => '30 Agu 2026'],
                ['id' => 'INV-1038', 'customer' => 'Rian Pratama', 'total' => 89000, 'status' => 'Dibatalkan', 'date' => '29 Agu 2026'],
            ],
        ]);
    }
}
