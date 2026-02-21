@extends('layouts.admin')

@section('styles')
    <style>
        .btn-primary {
            background: #0f172a;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #1e293b;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Laporan Keuangan</h1>
            <p class="text-secondary">Unduh laporan pemasukan dan pengeluaran dalam periode tertentu.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.reports.export') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                <i class="fas fa-file-pdf me-2"></i> Unduh Laporan PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-light border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Laporan</h5>
                    <p class="text-muted mb-2">Laporan yang diunduh mencakup:</p>
                    <ul class="text-muted small mb-0 ps-3">
                        <li class="mb-1">Daftar Pemasukan (Transaksi Sukses)</li>
                        <li class="mb-1">Daftar Pengeluaran Operasional</li>
                        <li class="mb-1">Ringkasan Total & Laba Rugi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection