
@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
    <h1 class="text-lg font-semibold mb-4">
        Import Produk dari CSV
    </h1>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('import.store') }}"
          enctype="multipart/form-data"
          class="max-w-md">
        @csrf

        <label class="block mb-3">
            <span class="text-sm font-medium">File CSV</span>
            <input type="file"
                   name="csv"
                   accept=".csv"
                   required
                   class="mt-1 block w-full text-sm">

            @error('csv')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </label>

        <p class="text-xs text-slate-500 mb-3">
            Kolom wajib: name, category_id, price.
        </p>

        <button type="submit"
                class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">
            Import
        </button>
    </form>

    @if ($importErrors->isNotEmpty())
        <div class="mt-8 max-w-2xl">
            <h2 class="text-sm font-semibold text-red-600 mb-3">Daftar Baris yang Gagal Di-import:</h2>
            <div class="bg-white border border-red-200 rounded-md overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-red-50 border-b border-red-200">
                        <tr>
                            <th class="px-4 py-2 text-red-800">Baris ke-</th>
                            <th class="px-4 py-2 text-red-800">Pesan Error</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100">
                        @foreach ($importErrors as $err)
                            <tr>
                                <td class="px-4 py-2 font-medium text-red-600">{{ $err->row_number }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $err->error_message }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-slate-500 mt-2">*Refresh halaman ini beberapa detik setelah melakukan import untuk melihat riwayat error terbaru.</p>
        </div>
    @endif
@endsection
