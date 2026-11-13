<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\Category;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\CellAlignment;

class AuditReportController extends Controller
{
    /**
     * Display the warehouse stock audit report page.
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $search = $request->get('search');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $categories = Category::orderBy('name')->get();

        $query = Product::with(['category', 'unit'])
            ->withSum(['purchaseItems as total_inbound' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('purchase', function ($p) use ($startDate, $endDate) {
                    $p->whereBetween('purchase_date', [$startDate, $endDate])
                      ->where('status', \App\Enums\PurchaseStatus::RECEIVED);
                });
            }], 'quantity')
            ->withSum(['saleItems as total_outbound' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('sale', function ($s) use ($startDate, $endDate) {
                    $s->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                });
            }], 'quantity');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('reports.audit', compact('products', 'categories', 'categoryId', 'search', 'startDate', 'endDate'));
    }

    /**
     * Export stock audit report to Excel (.xlsx) using OpenSpout.
     */
    public function exportExcel(Request $request)
    {
        $categoryId = $request->get('category_id');
        $search = $request->get('search');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = Product::with(['category', 'unit'])
            ->withSum(['purchaseItems as total_inbound' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('purchase', function ($p) use ($startDate, $endDate) {
                    $p->whereBetween('purchase_date', [$startDate, $endDate])
                      ->where('status', \App\Enums\PurchaseStatus::RECEIVED);
                });
            }], 'quantity')
            ->withSum(['saleItems as total_outbound' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('sale', function ($s) use ($startDate, $endDate) {
                    $s->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                });
            }], 'quantity');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();

        $fileName = 'GudangIn_Audit_Stok_' . now()->format('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Writer();
        $writer->openToFile($tempPath);

        // Header Title Row
        $writer->addRow(Row::fromValues([
            'LAPORAN AUDIT STOK & MUTASI PERGUDANGAN - GUDANGIN'
        ]));
        $writer->addRow(Row::fromValues([
            'Periode Audit: ' . $startDate . ' s/d ' . $endDate,
            'Diexport oleh: ' . auth()->user()->name . ' (' . auth()->user()->role_label . ')',
            'Tanggal Cetak: ' . now()->format('d/m/Y H:i:s')
        ]));
        $writer->addRow(Row::fromValues([])); // Blank row

        // Table Column Headers
        $writer->addRow(Row::fromValues([
            'No',
            'Kode SKU',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Harga Beli Terakhir',
            'Harga Jual',
            'Total Masuk (Inbound)',
            'Total Keluar (Outbound)',
            'Sisa Stok Fisik',
            'Min. Stok',
            'Nilai Aset Stok (IDR)',
            'Status Audit'
        ]));

        $no = 1;
        $grandTotalAsset = 0;

        foreach ($products as $item) {
            $inbound = (int) ($item->total_inbound ?? 0);
            $outbound = (int) ($item->total_outbound ?? 0);
            $stock = (int) $item->quantity;
            $minStock = (int) $item->min_stock;
            $assetValue = $stock * (float) $item->purchase_price;
            $grandTotalAsset += $assetValue;

            $status = 'AMAN';
            if ($stock <= 0) {
                $status = 'HABIS (OUT OF STOCK)';
            } elseif ($stock <= $minStock) {
                $status = 'MENIPIS (LOW STOCK)';
            }

            $writer->addRow(Row::fromValues([
                $no++,
                $item->sku,
                $item->name,
                $item->category->name ?? '-',
                $item->unit->name ?? '-',
                (float) $item->purchase_price,
                (float) $item->selling_price,
                $inbound,
                $outbound,
                $stock,
                $minStock,
                $assetValue,
                $status,
            ]));
        }

        // Summary Total Row
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues([
            '', '', '', '', '', '', '', '', '', 'TOTAL NILAI ASET GUDANG:',
            '', $grandTotalAsset, ''
        ]));

        $writer->close();

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }
}

