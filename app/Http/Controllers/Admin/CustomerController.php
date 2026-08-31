<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Tampilkan daftar pelanggan.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $customers = User::query()
            ->where('role', 'user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/Customers', [
            'customers' => $customers,
            'filters' => ['search' => $search],
        ]);
    }
}