<?php

namespace App\Http\Controllers;

use App\Models\Category; 
use App\Imports\ProductsImport;
use App\Exports\SalesReportExport;
use App\Jobs\ProcessProductImport;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ImportError;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id'); 
        
        $query = $this->filteredQuery($from, $to, $categoryId);
        $categories = Category::all(); 

        return view('reports.index', [
            'totalPenjualan' => $query->sum('total'),
            'jumlahTransaksi' => $query->count(),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'categories' => $categories,  
            'categoryId' => $categoryId,  
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id'); 
        
        $query = $this->filteredQuery($from, $to, $categoryId);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Total']);

            foreach ($query->lazyById() as $t) {
                fputcsv($out, [$t->id, $t->created_at, $t->user->name, $t->total]);
            }

            fclose($out);
        }, 'laporan-penjualan.csv');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id');

        return Excel::download(
            new SalesReportExport($this->filteredQuery($from, $to, $categoryId)),
            'laporan-penjualan.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id');
        
        $filteredQuery = $this->filteredQuery($from, $to, $categoryId);

        $bestSellingProducts = TransactionDetail::query()
            ->select(
                'product_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('product', function ($pq) use ($categoryId) {
                    $pq->where('category_id', $categoryId);
                });
            })
            ->with(['product.category'])
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'totalPenjualan' => $filteredQuery->sum('total'),
            'jumlahTransaksi' => $filteredQuery->count(),
            'transactions' => $filteredQuery->latest()->limit(50)->get(),
            'bestSellingProducts' => $bestSellingProducts,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);

        return $pdf->download('laporan-penjualan.pdf');
    }

    public function importForm(): View
    {
        $importErrors = ImportError::all();
        
        return view('reports.import', compact('importErrors'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('csv')->store('imports');

        ProcessProductImport::dispatch($path);

        return back()->with('success', 'Import sedang diproses di background.');
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        return [$from, $to];
    }

    private function filteredQuery($from, $to, $categoryId = null): Builder
    {
        $query = Transaction::with('user')
            ->whereBetween('created_at', [$from, $to]);

        if ($categoryId) {
            $query->whereHas('details.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        return $query;
    }
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diimpor menggunakan Laravel Excel.');
    }
}