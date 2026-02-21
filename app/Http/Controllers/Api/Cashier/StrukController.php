<?php

namespace App\Http\Controllers\Api\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class StrukController extends Controller
{
    public function index($id)
    {
        $transaction = Transaction::with(['items.product', 'user'])->findOrFail($id);

        return view('cashier.struk', [
            'transaction' => $transaction
        ]);
    }

    public function sendWhatsapp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'transaction_id' => 'required'
        ]);

        $transaction = Transaction::with(['items.product', 'user'])->findOrFail($request->transaction_id);

        // Generate PDF
        $pdf = Pdf::loadView('cashier.struk_pdf', ['transaction' => $transaction]);
        $pdfContent = $pdf->output();
        $filename = 'struk-' . $transaction->id . '.pdf';

        // Caption (Detailed)
        $caption = "*Warung Bakso Panjang Rezeki*\n";
        $caption .= "STRUK PEMBAYARAN\n";
        $caption .= "--------------------------------\n";
        $caption .= "No: #" . $transaction->id . "\n";
        $caption .= "Tgl: " . $transaction->created_at->format('d/m/Y H:i') . "\n";
        $caption .= "--------------------------------\n";

        foreach ($transaction->items as $item) {
            $caption .= $item->product->name . " x" . $item->quantity . "\n";
            $caption .= "Rp " . number_format($item->price * $item->quantity, 0, ',', '.') . "\n";
        }

        $caption .= "--------------------------------\n";
        $caption .= "TOTAL: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "\n";
        $caption .= "Metode: " . ($transaction->payment_method == 'cash' ? 'Cash' : 'Non-Tunai') . "\n";
        $caption .= "--------------------------------\n";
        $caption .= "Terima Kasih!";

        // Fonnte API (Send File) using cURL Native with DEBUG LOGGING
        $logPath = storage_path('logs/wa_debug.log');
        file_put_contents($logPath, "\n-----\n[" . date('Y-m-d H:i:s') . "] START Request for Phone: " . $request->phone . "\n", FILE_APPEND);

        $curl = curl_init();

        try {
            // Save PDF temporarily
            $tempPath = storage_path('app/public/temp-' . $filename);
            file_put_contents($tempPath, $pdfContent);

            if (file_exists($tempPath)) {
                file_put_contents($logPath, "PDF Saved to: $tempPath (" . filesize($tempPath) . " bytes)\n", FILE_APPEND);
            } else {
                file_put_contents($logPath, "ERROR: PDF file not found after save!\n", FILE_APPEND);
                throw new \Exception("Failed to save PDF temp file");
            }

            $curlData = array(
                'target' => $request->phone,
                'message' => $caption,
                'file' => new \CURLFile($tempPath, 'application/pdf', $filename),
            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30, // Increased timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $curlData,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: D7kpsS3ui66Md3DvWkUz'
                ),
                CURLOPT_SSL_VERIFYPEER => false, // Fix for local SSL issues
                CURLOPT_SSL_VERIFYHOST => false, // Fix for local SSL issues
            ));

            file_put_contents($logPath, "Executing CURL...\n", FILE_APPEND);
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            file_put_contents($logPath, "CURL Executed. HTTP Code: $httpCode\n", FILE_APPEND);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                file_put_contents($logPath, "CURL Error: $error_msg\n", FILE_APPEND);
                curl_close($curl);
                throw new \Exception("CURL Error: " . $error_msg);
            }

            curl_close($curl);

            // Delete temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
                file_put_contents($logPath, "Temp file deleted.\n", FILE_APPEND);
            }

            $body = json_decode($response, true);
            file_put_contents($logPath, "Response Body: " . substr((string) $response, 0, 200) . "...\n", FILE_APPEND);

            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'WhatsApp berhasil dikirim',
                    'debug' => $body
                ]);
            } else {
                return response()->json([
                    'message' => 'Gagal mengirim WhatsApp',
                    'debug' => $body
                ], 500);
            }
        } catch (\Throwable $e) {
            file_put_contents($logPath, "EXCEPTION: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);
            if (isset($curl) && is_resource($curl)) {
                curl_close($curl);
            }
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}