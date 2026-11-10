@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-lg mx-auto p-6 bg-white rounded-md shadow-md mt-6">
    <h1 class="text-lg font-semibold mb-4">Tambah Produk Baru</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 p-3 rounded-md text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select name="category_id" required class="w-full border rounded-md px-3 py-2 text-sm">
                <option value="">Pilih Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
            <input type="number" name="price" value="{{ old('price') }}" required
                class="w-full border rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" name="stock" value="{{ old('stock') }}" required
                class="w-full border rounded-md px-3 py-2 text-sm">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm">Batal</a>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection