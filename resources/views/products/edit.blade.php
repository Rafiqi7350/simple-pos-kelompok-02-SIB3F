@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-xl font-bold mb-4">Edit Produk #{{ $product->id }}</h1>

    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kategori</label>
            <select name="category_id" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-500 @enderror">
            @error('price')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stock') border-red-500 @enderror">
            @error('stock')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Perbarui Produk
            </button>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection