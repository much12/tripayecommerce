<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Services\TripayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(protected TripayService $tripay)
    {
    }

    /**
     * Tampilkan halaman checkout untuk sebuah produk.
     */
    public function create(Product $product): Response
    {
        // Ambil channel pembayaran aktif; sederhanakan datanya untuk frontend.
        $channels = collect($this->tripay->paymentChannels())
            ->map(fn ($c) => [
                'code' => $c['code'] ?? null,
                'name' => $c['name'] ?? null,
                'group' => $c['group'] ?? null,
                'icon_url' => $c['icon_url'] ?? null,
            ])
            ->filter(fn ($c) => $c['code'])
            ->values();

        return Inertia::render('Checkout', [
            'product' => $product->only('id', 'sku', 'name', 'price'),
            'channels' => $channels,
        ]);
    }

    /**
     * Proses checkout: buat transaksi TriPay & simpan invoice.
     */
    public function store(Request $request): RedirectResponse|\Illuminate\Http\Response
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:20'],
            'method' => ['required', 'string', 'max:50'],
        ], [], [
            'buyer_email' => 'email',
            'buyer_phone' => 'nomor HP',
            'method' => 'metode pembayaran',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $amount = (int) $product->price;
        $merchantRef = 'INV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        $payload = [
            'method' => $validated['method'],
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => Str::before($validated['buyer_email'], '@'),
            'customer_email' => $validated['buyer_email'],
            'customer_phone' => $validated['buyer_phone'],
            'order_items' => [
                [
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'price' => $amount,
                    'quantity' => 1,
                ],
            ],
            'callback_url' => route('tripay.callback'),
            'return_url' => route('checkout.finish'),
            'expired_time' => now()->addDay()->timestamp,
            'signature' => $this->tripay->signature($merchantRef, $amount),
        ];

        $response = $this->tripay->createTransaction($payload);
        $body = $response->json();

        if (! $response->successful() || ! ($body['success'] ?? false)) {
            $message = $body['message'] ?? 'Gagal membuat transaksi. Periksa konfigurasi TriPay.';

            return back()->withErrors(['method' => $message])->withInput();
        }

        $data = $body['data'];

        Invoice::create([
            'product_id' => $product->id,
            'tripay_reference' => $data['reference'] ?? null,
            'buyer_email' => $validated['buyer_email'],
            'buyer_phone' => $validated['buyer_phone'],
            'raw_response' => $body,
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'payment_method' => $data['payment_method'] ?? $validated['method'],
            'checkout_url' => $data['checkout_url'] ?? null,
            'status' => $data['status'] ?? 'UNPAID',
        ]);

        // Arahkan pembeli ke halaman pembayaran TriPay (redirect eksternal).
        return Inertia::location($data['checkout_url']);
    }

    /**
     * Halaman setelah pembeli kembali dari TriPay (return_url).
     */
    public function finish(Request $request): Response
    {
        $invoice = Invoice::where('tripay_reference', $request->query('tripay_reference'))
            ->orWhere('merchant_ref', $request->query('tripay_merchant_ref'))
            ->latest()
            ->first();

        if ($invoice && $invoice->tripay_reference) {
            $response = $this->tripay->detailTransaction($invoice->tripay_reference);
            
            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['success']) && $body['success'] === true && isset($body['data']['status'])) {
                    $apiStatus = $body['data']['status'];
                    if ($invoice->status !== $apiStatus) {
                        $invoice->update(['status' => $apiStatus]);
                    }
                }
            }
        }

        return Inertia::render('CheckoutFinish', [
            'invoice' => $invoice?->only('merchant_ref', 'tripay_reference', 'amount', 'status', 'buyer_email'),
        ]);
    }
}
