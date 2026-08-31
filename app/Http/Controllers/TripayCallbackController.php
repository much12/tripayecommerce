<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\TripayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripayCallbackController extends Controller
{
    public function __construct(protected TripayService $tripay)
    {
    }

    /**
     * Terima callback notifikasi transaksi dari TriPay.
     */
    public function handle(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Callback-Signature', '');

        // Verifikasi signature untuk memastikan callback benar dari TriPay.
        if (! $this->tripay->isValidCallbackSignature($rawBody, $signature)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $payload = json_decode($rawBody, true);
        $merchantRef = $payload['merchant_ref'] ?? null;
        $status = $payload['status'] ?? null;

        $invoice = Invoice::where('merchant_ref', $merchantRef)->first();

        if (! $invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
        }

        $invoice->update([
            'status' => $status ?? $invoice->status,
            'raw_response' => $payload,
            'paid_at' => $status === 'PAID' ? now() : $invoice->paid_at,
        ]);

        return response()->json(['success' => true]);
    }
}
