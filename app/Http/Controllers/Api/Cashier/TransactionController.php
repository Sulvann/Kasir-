<?php

namespace App\Http\Controllers\Api\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'cash_amount' => 'required|integer|min:0',
            'payment_method' => 'required|in:cash,qris',
            'no_hp' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $transactionDetails = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi. Sisa: {$product->stock}");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                // Decrease stock
                $product->stock -= $item['quantity'];
                $product->save();

                $transactionDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Create Transaction
            $transaction = Transaction::create([
                'total_amount' => $totalAmount,
                'cash_amount' => $request->cash_amount,
                'payment_method' => $request->payment_method,
                'no_hp' => $request->no_hp,
                'status' => 'completed',
            ]);

            // Create Details
            foreach ($transactionDetails as $detail) {
                $detail['transaction_id'] = $transaction->id;
                TransactionDetail::create($detail);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil',
                'data' => $transaction->load('details.product')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaction::with('details.product')->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction
        ]);
    }
}
