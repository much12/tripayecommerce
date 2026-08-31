<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TripayService
{
    protected string $apiKey;

    protected string $privateKey;

    protected string $merchantCode;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('tripay.api_key');
        $this->privateKey = (string) config('tripay.private_key');
        $this->merchantCode = (string) config('tripay.merchant_code');

        $mode = config('tripay.mode', 'sandbox');
        $this->baseUrl = config("tripay.base_url.{$mode}");
    }

    /**
     * Buat signature transaksi: HMAC-SHA256 dari (merchant_code + merchant_ref + amount).
     */
    public function signature(string $merchantRef, int $amount): string
    {
        return hash_hmac(
            'sha256',
            $this->merchantCode . $merchantRef . $amount,
            $this->privateKey,
        );
    }

    /**
     * Ambil daftar channel pembayaran yang aktif untuk merchant ini.
     *
     * @return array<int, array<string, mixed>>
     */
    public function paymentChannels(): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/merchant/payment-channel");

        if ($response->successful() && $response->json('success')) {
            return $response->json('data', []);
        }

        return [];
    }

    /**
     * Buat transaksi baru di TriPay.
     *
     * @param  array<string, mixed>  $payload
     */
    public function createTransaction(array $payload): Response
    {
        return Http::withToken($this->apiKey)
            ->asForm()
            ->post("{$this->baseUrl}/transaction/create", $payload);
    }

    /**
     * Validasi signature callback dari TriPay.
     */
    public function isValidCallbackSignature(string $rawBody, string $signatureHeader): bool
    {
        $localSignature = hash_hmac('sha256', $rawBody, $this->privateKey);

        return hash_equals($localSignature, $signatureHeader);
    }

    /**
     * Ambil detail transaksi dari TriPay berdasarkan reference
     */
    public function detailTransaction(string $reference): Response
    {
        return Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/transaction/detail", [
                'reference' => $reference,
            ]);
    }
}
