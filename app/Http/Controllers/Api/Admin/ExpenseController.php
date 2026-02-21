<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $expenses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);

        $expense = Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dicatat',
            'data' => $expense
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);

        $expense->update([
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil diperbarui',
            'data' => $expense
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $expense->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
