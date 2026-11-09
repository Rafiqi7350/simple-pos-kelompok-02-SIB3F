<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::where('stock', '>', 0)->paginate(12);

        return view('pos.create', compact('products'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        $transaction = DB::transaction(function () use ($validated, $request) {

            $transaction = Transaction::create([
                'user_id' => $request->user() ? $request->user()->id : 1,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Jika produk tidak ditemukan, lempar validation exception agar merespons 422
                if (!$product) {
                    throw ValidationException::withMessages([
                        'items.0.product_id' => ['Produk tidak ditemukan.'],
                    ]);
                }

                $subtotal = $product->price * $item['qty'];

                $total += $subtotal;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ]);
            }

            $transaction->update([
                'total' => $total
            ]);

            return $transaction;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction->load('details')
            ], 201);
        }

        return redirect()
            ->route('pos.create')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index()
    {
        $transactions = Transaction::with('details.product', 'user')
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        $transaction = Transaction::with('details.product', 'user')->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $transaction
            ]);
        }

        return "Detail transaksi #{$id}";
    }
}