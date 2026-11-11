<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ApiTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = new User();
        $this->kasir->name = 'Kasir Test';
        $this->kasir->email = 'kasir.test@pos.test';
        $this->kasir->password = bcrypt('password');
        $this->kasir->role = 'kasir';
        $this->kasir->save();

        $category = Category::first();
        if (!$category) {
            $category = new Category();
            $category->name = 'Umum';
            $category->save();
        }

        $this->product = new Product();
        $this->product->category_id = $category->id;
        $this->product->name = 'Kopi Susu Test';
        $this->product->price = 15000;
        $this->product->stock = 50;
        $this->product->save();
    }

    public function test_transaksi_berhasil_disimpan(): void
    {
        $token = $this->kasir->createToken('test', ['transactions:create'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Idempotency-Key' => 'test-key-1',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions', [
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 2],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'total']]);
    }

    public function test_transaksi_gagal_jika_product_id_tidak_valid(): void
    {
        $token = $this->kasir->createToken('test', ['transactions:create'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Idempotency-Key' => 'test-key-invalid',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions', [
            'items' => [
                ['product_id' => 99999, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_transaksi_gagal_tanpa_idempotency_key(): void
    {
        $token = $this->kasir->createToken('test', ['transactions:create'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->postJson('/api/transactions', [
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(400);
    }

    public function test_replay_idempotency_mengembalikan_transaksi_yang_sama(): void
    {
        $token = $this->kasir->createToken('test', ['transactions:create'])->plainTextToken;
        $headers = [
            'Authorization' => "Bearer {$token}",
            'Idempotency-Key' => 'test-key-replay',
            'Accept' => 'application/json',
        ];

        $payload = [
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ];

        $res1 = $this->withHeaders($headers)->postJson('/api/transactions', $payload);
        $res2 = $this->withHeaders($headers)->postJson('/api/transactions', $payload);

        $res1->assertStatus(201);
        $res2->assertStatus(201)
            ->assertHeader('Idempotent-Replayed', 'true');
        $this->assertEquals($res1->json('data.id'), $res2->json('data.id'));
    }

    public function test_transaksi_ditolak_jika_token_tidak_punya_ability(): void
    {
        $token = $this->kasir->createToken('bot', ['products:read'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Idempotency-Key' => 'test-key-no-ability',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions', [
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(403);
    }
    public function test_token_yang_sudah_kedaluwarsa_menghasilkan_status_401(): void
    {
        Carbon::setTestNow('2025-01-01 12:00:00');

        try {
            $token = $this->kasir->createToken(
                'test',
                ['transactions:create'],
                now()->addMinutes(30)
            )->plainTextToken;

            Carbon::setTestNow(now()->addMinutes(31));

            $response = $this->withHeaders([
                'Authorization' => "Bearer {$token}",
                'Idempotency-Key' => 'test-key-expired',
                'Accept' => 'application/json',
            ])->postJson('/api/v1/transactions', [
                'items' => [
                    ['product_id' => $this->product->id, 'qty' => 1],
                ],
            ]);

            $response->assertStatus(401);
        } finally {
            Carbon::setTestNow();
        }
    }
}