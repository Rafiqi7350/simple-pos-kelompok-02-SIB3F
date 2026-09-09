<nav class="bg-slate-900 text-white">
    <div class="container mx-auto px-6 py-3 flex gap-6 items-center">
        <span class="font-semibold text-lg">Simple POS</span>
        <a href="{{ route('pos.create') }}" 
           class="hover:underline {{ request()->routeIs('pos.create') ? 'font-bold underline' : '' }}">
            Kasir
        </a>
        <a href="{{ route('transactions.index') }}" 
           class="hover:underline {{ request()->routeIs('transactions.index') ? 'font-bold underline' : '' }}">
            Transaksi
        </a>
    </div>
</nav>