<?php

namespace App\Http\Controllers;

use App\DTOs\PurchaseData;
use App\Enums\PurchaseStatus;
use App\Exceptions\PurchaseException;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StockInController extends Controller
{
    protected PurchaseService $service;

    public function __construct(PurchaseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        return view('purchases.create', [
            'purchase' => new Purchase(),
            'statuses' => PurchaseStatus::cases(),
        ]);
    }

    public function store(StorePurchaseRequest $request)
    {
        try {
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $proofPath = $request->file('proof_image')->store('proofs', 'public');
            }

            $data = $request->validated();
            $data['proof_image'] = $proofPath;
            $data['status'] = PurchaseStatus::RECEIVED->value; // WMS: Barang fisik langsung diterima di gudang
            if (empty($data['invoice_number'])) {
                $data['invoice_number'] = 'INB-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            $purchaseData = PurchaseData::fromArray($data);

            $purchase = $this->service->createPurchase($purchaseData, Auth::id());

            return redirect()->route('stock-in.create')
                ->with('success', 'Stok masuk berhasil dicatat! Stok inventaris gudang dan dashboard telah terupdate.');

        } catch (PurchaseException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error mencatat Stock In: ' . $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'creator', 'items.product.unit']);
        return view('purchases.show', compact('purchase'));
    }
}

