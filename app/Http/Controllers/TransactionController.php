<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::where('stock', '>', 0)->paginate(12);

        return view('pos.create', compact('products'));
    }

    public function store()
    {
        return 'Transaksi disimpan (belum ada logika penyimpanan)';
    }

    public function index()
    {
        // Ambil data transaksi terbaru dengan eager loading details dan product
        $transactions = Transaction::with('details.product')
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}