<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApiClientDemo extends Command
{
    protected $signature = 'api:demo';
    protected $description = 'Demonstrate consuming the Simple POS API as an external client';

    public function handle(): int
    {
        $baseUrl = 'http://127.0.0.1:8000';
        $apiUrl = "{$baseUrl}/api/v1";

        $when = fn ($e) => $e instanceof ConnectionException || in_array($e->response->status(), [429, 502, 503, 504]);
        $backoff = fn (int $attempt) => $attempt * 1000;

        // Langkah 1: Simulasi login dengan kata sandi yang salah secara sengaja
        $this->info('--- Langkah 1: Pengujian Login dengan Kata Sandi Salah ---');

        try {
            $login = Http::acceptJson()->post("{$apiUrl}/login", [
                'email' => 'kasir@pos.test',
                'password' => 'kata-sandi-salah', // Password salah secara sengaja
            ]);
        } catch (ConnectionException $e) {
            $this->error("Gagal terhubung ke API server di {$baseUrl}. Pastikan 'php artisan serve' sedang berjalan.");
            return self::FAILURE;
        }

        // Tangkap respons kegagalan dari percobaan login
        if ($login->failed()) {
            $this->warn("Uji coba login gagal sesuai harapan (HTTP Status: {$login->status()}).");
            $this->line("Pesan respons API: " . ($login->json('message') ?? 'Tidak ada pesan error.'));
            $this->error('Menghentikan demo pada langkah pertama. Proses tidak dilanjutkan ke langkah berikutnya untuk menghindari penggunaan token kosong.');
            return self::FAILURE;
        }

        $token = $login->json('token');
        $this->info("Login berhasil, token: {$token}");

        $products = Http::acceptJson()->withToken($token)
            ->retry(3, $backoff, $when, throw: false)
            ->get("{$apiUrl}/products");

        if ($products->failed()) {
            $this->error('Token kedaluwarsa/dicabut, atau server sedang bermasalah.');
            return self::FAILURE;
        }

        $this->info('Jumlah produk: ' . count($products->json('data')));

        $productId = Product::first()->id;
        $payload = ['items' => [['product_id' => $productId, 'qty' => 1]]];
        $key = (string) Str::uuid();

        $transaction = Http::acceptJson()->withToken($token)
            ->withHeaders(['Idempotency-Key' => $key])
            ->retry(3, $backoff, $when, throw: false)
            ->post("{$apiUrl}/transactions", $payload);

        match ($transaction->status()) {
            201 => $this->info('Transaksi tersimpan: ' . json_encode($transaction->json('data'))),
            401 => $this->error('Token tidak valid, login ulang.'),
            422 => $this->error('Input ditolak: ' . $transaction->json('message')),
            default => $this->error('Error lain: ' . $transaction->status()),
        };

        // Simulasi ulangi klik bayar karena lemot
        $retry = Http::acceptJson()->withToken($token)
            ->withHeaders(['Idempotency-Key' => $key])
            ->retry(3, $backoff, $when, throw: false)
            ->post("{$apiUrl}/transactions", $payload);

        $this->info('Ulang dengan key yang sama: ' . $retry->status() . ', replayed = ' . $retry->header('Idempotent-Replayed'));

        return self::SUCCESS;
    }
}
