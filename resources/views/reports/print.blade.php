<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            margin: 0;
            padding: 30px;
        }
        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 22px;
            color: #0f172a;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 15px;
        }
        th {
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            padding: 10px;
            text-align: left;
            font-weight: 700;
            color: #475569;
        }
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 10px;
            color: #334155;
        }
        tr:hover td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: 700;
        }
        .text-emerald {
            color: #059669;
        }
        .text-rose {
            color: #dc2626;
        }
        .text-indigo {
            color: #4f46e5;
        }
        .tfoot {
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 2px solid #cbd5e1;
        }
        .category-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        .category-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 6px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-info { background-color: #e0f2fe; color: #075985; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .action-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        .btn {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        @media print {
            body {
                padding: 0;
            }
            .action-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name', 'Cashflow') }}</h1>
        <p>{{ $title }}</p>
    </div>

    @if($reportType === 'cashflow')
        <!-- CASHFLOW DATA -->
        <table>
            <thead>
                <tr>
                    <th>
                        {{ $data['filterType'] === 'daily' ? 'Tanggal' : ($data['filterType'] === 'monthly' ? 'Bulan' : 'Tahun') }}
                    </th>
                    <th class="text-right">Pemasukan</th>
                    <th class="text-right">Pengeluaran</th>
                    <th class="text-right">Selisih (Net Flow)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalIn = 0;
                    $totalOut = 0;
                @endphp
                @forelse($data['items'] as $item)
                    @php
                        $income = (float) $item->total_income;
                        $expense = (float) $item->total_expense;
                        $net = $income - $expense;
                        $totalIn += $income;
                        $totalOut += $expense;
                    @endphp
                    <tr>
                        <td class="font-bold">
                            @if($data['filterType'] === 'daily')
                                {{ $item->date->format('d M Y') }}
                            @elseif($data['filterType'] === 'monthly')
                                @php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    echo $months[$item->month] ?? $item->month;
                                @endphp
                            @else
                                {{ $item->year }}
                            @endif
                        </td>
                        <td class="text-right text-emerald">Rp {{ number_format($income, 0, ',', '.') }}</td>
                        <td class="text-right text-rose">Rp {{ number_format($expense, 0, ',', '.') }}</td>
                        <td class="text-right font-bold {{ $net >= 0 ? 'text-indigo' : 'text-rose' }}">
                            Rp {{ number_format($net, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center font-bold">Tidak ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($data['items']) > 0)
                <tfoot>
                    <tr class="tfoot">
                        <td>TOTAL</td>
                        <td class="text-right text-emerald">Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
                        <td class="text-right text-rose">Rp {{ number_format($totalOut, 0, ',', '.') }}</td>
                        <td class="text-right text-indigo">Rp {{ number_format($totalIn - $totalOut, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

    @elseif($reportType === 'category')
        <!-- CATEGORY DATA -->
        <div class="category-grid">
            <!-- Income Categories -->
            <div>
                <div class="category-title text-emerald">Pemasukan</div>
                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $subtotalIn = 0; @endphp
                        @forelse($data['income'] as $item)
                            @php $subtotalIn += (float) $item->total; @endphp
                            <tr>
                                <td class="font-bold">{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
                                <td class="text-right text-emerald font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center font-bold">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($data['income']) > 0)
                        <tfoot>
                            <tr class="tfoot">
                                <td>SUBTOTAL</td>
                                <td class="text-right text-emerald">Rp {{ number_format($subtotalIn, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Expense Categories -->
            <div>
                <div class="category-title text-rose">Pengeluaran</div>
                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $subtotalOut = 0; @endphp
                        @forelse($data['expense'] as $item)
                            @php $subtotalOut += (float) $item->total; @endphp
                            <tr>
                                <td class="font-bold">{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
                                <td class="text-right text-rose font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center font-bold">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($data['expense']) > 0)
                        <tfoot>
                            <tr class="tfoot">
                                <td>SUBTOTAL</td>
                                <td class="text-right text-rose">Rp {{ number_format($subtotalOut, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    @else
        <!-- PAYROLL DATA -->
        <table>
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Status</th>
                    <th class="text-center">Jumlah Karyawan</th>
                    <th class="text-right">Total Pembayaran</th>
                    <th>Akun Pembayaran</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['items'] as $item)
                    <tr>
                        <td class="font-bold">
                            @php
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                echo ($months[$item['period']->month] ?? '') . ' ' . $item['period']->year;
                            @endphp
                        </td>
                        <td>
                            <span class="badge {{ $item['period']->status === 'paid' ? 'badge-success' : ($item['period']->status === 'processed' ? 'badge-info' : 'badge-warning') }}">
                                {{ $item['period']->status === 'paid' ? 'Dibayar' : ($item['period']->status === 'processed' ? 'Diproses' : 'Draft') }}
                            </span>
                        </td>
                        <td class="text-center">{{ $item['total_employees'] }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($item['total_net_salary'], 0, ',', '.') }}</td>
                        <td>{{ $item['account_name'] }}</td>
                        <td>{{ $item['paid_at'] ? $item['paid_at']->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center font-bold">Belum ada periode penggajian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- Floating print button -->
    <div class="action-bar">
        <button onclick="window.print()" class="btn">Cetak PDF / Printer</button>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
