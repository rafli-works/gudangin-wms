<?php

namespace App\Http\Controllers;

use App\DTOs\SaleData;
use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Exceptions\SaleException;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StockOutController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function create()
    {
        return view('sales.create');
    }

    public function store(StoreSaleRequest $request, SaleService $saleService)
    {
        try {
            $validated = $request->validated();
            $validated['created_by'] = Auth::id();
            $validated['status'] = SaleStatus::COMPLETED->value;
            $validated['payment_method'] = PaymentMethod::CASH->value;

            $saleData = SaleData::fromArray($validated);

            $sale = $saleService->createSale($saleData);

            Cache::forget('dashboard_warehouse_metrics');
            Cache::forget('dashboard_low_stock');

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stok keluar berhasil dicatat! Stok inventaris gudang telah berkurang.',
                    'data' => $sale,
                    'redirect' => route('stock-out.create')
                ], 201);
            }

            return redirect()->route('stock-out.create')
                ->with('success', 'Stok keluar berhasil dicatat! Stok inventaris gudang telah berkurang.');

        } catch (SaleException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product.unit', 'customer', 'creator']);
        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['items.product.unit', 'customer', 'creator']);
        return view('sales.print', compact('sale'));
    }
}

