@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
    <h1 class="text-lg font-semibold mb-4">Laporan Penjualan</h1>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Filter (Tanggal & Kategori) -->
    <form method="GET" action="{{ route('reports.index') }}" class="bg-white p-4 rounded-md shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Filter Tanggal Mulai (From) -->
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" id="from" name="from" value="{{ $from ?? request('from') }}" class="w-full border-gray-300 rounded-md shadow-sm border p-2">
            </div>

            <!-- Filter Tanggal Selesai (To) -->
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="to" name="to" value="{{ $to ?? request('to') }}" class="w-full border-gray-300 rounded-md shadow-sm border p-2">
            </div>

            <!-- Filter Kategori (BARU) -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" id="category_id" class="w-full border-gray-300 rounded-md shadow-sm border p-2 bg-white">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ (isset($categoryId) ? $categoryId : request('category_id')) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition w-full">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Ringkasan / Kartu Total -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
            <p class="text-sm text-blue-600 font-medium">Total Penjualan</p>
            <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-md border border-green-100">
            <p class="text-sm text-green-600 font-medium">Jumlah Transaksi</p>
            <p class="text-2xl font-bold text-green-900">{{ number_format($jumlahTransaksi ?? 0) }}</p>
        </div>
    </div>

    <!-- Tombol Export CSV -->
    <div class="mb-4">
        <a href="{{ route('reports.export', request()->all()) }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition">
            Export CSV
        </a>
    </div>

@endsection