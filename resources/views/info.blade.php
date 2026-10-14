<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi - Simple POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <div class="w-full max-w-md bg-white rounded-md shadow p-6">
        <h1 class="text-xl font-bold text-slate-800 mb-2">Informasi Simple POS</h1>
        <p class="text-sm text-slate-600 mb-4">
            Selamat datang di Simple POS! Sistem kasir dan manajemen transaksi point of sale sederhana, cepat, dan mudah digunakan.
        </p>

        <div class="bg-slate-50 border rounded p-4 mb-4 text-sm text-slate-700 space-y-2">
            <h2 class="font-semibold text-slate-900">Peran Pengguna:</h2>
            <ul class="list-disc list-inside space-y-1">
                <li><strong>Admin:</strong> Manajemen produk, kategori, dan transaksi.</li>
                <li><strong>Manager:</strong> Memantau riwayat transaksi.</li>
                <li><strong>Kasir:</strong> Melakukan pencatatan transaksi penjualan.</li>
            </ul>
        </div>

        <p class="text-xs text-slate-500 mb-4">
            Halaman informasi ini hanya dapat diakses oleh tamu (belum login).
        </p>

        <a href="{{ route('login') }}" class="block text-center w-full bg-slate-900 text-white rounded-md py-2 text-sm font-medium hover:bg-slate-800 transition">
            Ke Halaman Login
        </a>
    </div>
</body>
</html>
