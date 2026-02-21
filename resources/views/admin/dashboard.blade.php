@extends('layouts.admin')

@section('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card h3 {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .stat-card.income {
            border-left: 5px solid #10b981;
        }

        .stat-card.income .value {
            color: #10b981;
        }

        .stat-card.expense {
            border-left: 5px solid #ef4444;
        }

        .stat-card.expense .value {
            color: #ef4444;
        }

        .stat-card.profit {
            border-left: 5px solid #3b82f6;
        }

        .stat-card.profit .value {
            color: #3b82f6;
        }

        .chart-container {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard Overview</h1>
        <p style="color: #64748b; font-size: 0.95rem;">Ringkasan performa bisnis Anda secara real-time.</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Pemasukan -->
        <div class="stat-card income">
            <h3>Total Pemasukan</h3>
            <div class="value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            <small style="color: #64748b; margin-top: 5px;">Total omzet kotor</small>
        </div>
        <!-- Pengeluaran -->
        <div class="stat-card expense">
            <h3>Total Pengeluaran</h3>
            <div class="value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            <small style="color: #64748b; margin-top: 5px;">Biaya operasional</small>
        </div>
        <!-- Laba -->
        <div class="stat-card profit">
            <h3>Laba Bersih</h3>
            <div class="value">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
            <small style="color: #64748b; margin-top: 5px;">(Pemasukan - Pengeluaran)</small>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-container">
        <div class="chart-header">
            <div class="chart-title">Tren Penjualan (30 Hari Terakhir)</div>
            <div style="font-size: 0.85rem; color: #64748b;">
                Update Terakhir: {{ now()->format('H:i') }}
            </div>
        </div>
        <div style="position: relative; height: 400px; width: 100%;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');

            // Data from Controller
            const labels = @json($chartDates);
            const data = @json($chartIncome);

            // Gradient Fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (IDR)',
                        data: data,
                        borderWidth: 3,
                        borderColor: '#3b82f6', // Primary Blue
                        backgroundColor: gradient,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Curvy lines
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: {
                                size: 13,
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            bodyFont: {
                                size: 14,
                                weight: 'bold',
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            border: {
                                dash: [4, 4]
                            },
                            grid: {
                                color: '#f1f5f9',
                                borderDash: [4, 4]
                            },
                            ticks: {
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                color: '#64748b',
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + 'jt';
                                    if (value >= 1000) return (value / 1000) + 'rb';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection