<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    /**
     * Daftar invoice.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $invoices = Invoice::query()
            ->with('product:id,name,sku')
            ->when($search, function ($query) use ($search) {
                $query->where('merchant_ref', 'like', "%{$search}%")
                    ->orWhere('tripay_reference', 'like', "%{$search}%")
                    ->orWhere('buyer_email', 'like', "%{$search}%");
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/invoices/Index', [
            'invoices' => $invoices,
            'filters' => ['search' => $search, 'status' => $status],
            'summary' => [
                'total' => Invoice::count(),
                'paid' => Invoice::where('status', 'PAID')->count(),
                'unpaid' => Invoice::where('status', 'UNPAID')->count(),
                'revenue' => (int) Invoice::where('status', 'PAID')->sum('amount'),
            ],
        ]);
    }

    /**
     * Detail satu invoice.
     */
    public function show(Invoice $invoice): Response
    {
        $invoice->load('product:id,name,sku,price');

        return Inertia::render('admin/invoices/Show', [
            'invoice' => $invoice,
        ]);
    }
}
