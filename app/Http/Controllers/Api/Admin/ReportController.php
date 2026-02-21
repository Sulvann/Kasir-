<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show the report filter page.
     */
    public function index()
    {
        return view('admin.reports');
    }

    /**
     * Generate and download the PDF report.
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // transactions (Pemasukan)
        $incomes = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed') // Adjusted to match DB status 'completed'
            ->get();

        // expenses (Pengeluaran)
        $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate Totals
        $totalIncome = $incomes->sum('total_amount');
        $totalExpense = $expenses->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('admin.reports_pdf', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
        ]);

        $fileName = 'Laporan_Keuangan_' . $startDate->format('dmY') . '-' . $endDate->format('dmY') . '.pdf';

        return $pdf->download($fileName);
    }
}
