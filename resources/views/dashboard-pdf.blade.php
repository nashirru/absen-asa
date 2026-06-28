<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dashboard Finansial</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .header {
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #0f172a;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
            color: #64748b;
        }
        .report-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .report-title h2 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            color: #d97706;
        }
        .report-title p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #475569;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 3px solid #f59e0b;
            padding-left: 6px;
            text-transform: uppercase;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .stats-card {
            width: 33.33%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #fafafa;
        }
        .stats-card.income {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
        }
        .stats-card.expense {
            background-color: #fef2f2;
            border-color: #fecaca;
        }
        .stats-card.balance {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .stats-title {
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            margin: 0 0 4px 0;
        }
        .stats-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-weight: bold;
            text-align: left;
            color: #475569;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
        }
        .data-table .num {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f1f5f9;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #075985; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top;">
                    <h1>Cashflow</h1>
                    <p>Sistem Penggajian & Arus Kas Digital</p>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <p style="font-size: 9px; color: #64748b;">Tanggal Cetak: {{ $printed_at }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h2>Laporan Ringkasan Dashboard Finansial</h2>
        <p>Bulan: {{ $month_label }}</p>
    </div>

    <div class="section-title">Ringkasan Finansial Bulan Ini</div>
    <table class="stats-table">
        <tr>
            <td class="stats-card income">
                <p class="stats-title" style="color: #065f46;">Pemasukan Bulan Ini</p>
                <p class="stats-value" style="color: #047857;">Rp {{ number_format($summary['income'], 0, ',', '.') }}</p>
            </td>
            <td style="width: 10px;"></td>
            <td class="stats-card expense">
                <p class="stats-title" style="color: #991b1b;">Pengeluaran Bulan Ini</p>
                <p class="stats-value" style="color: #b91c1c;">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</p>
            </td>
            <td style="width: 10px;"></td>
            <td class="stats-card balance">
                <p class="stats-title" style="color: #075985;">Saldo Bersih Bulan Ini</p>
                <p class="stats-value" style="color: #0369a1;">Rp {{ number_format($summary['net'], 0, ',', '.') }}</p>
            </td>
        </tr>
    </table>

    <div class="section-title">Saldo Per Rekening / Akun</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Akun</th>
                <th style="width: 80px;">Tipe</th>
                <th>Keterangan</th>
                <th class="num" style="width: 120px;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($accounts as $acc)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td style="font-weight: 500;">{{ $acc->name }}</td>
                    <td>
                        @if($acc->type === 'cash')
                            <span class="badge badge-success">Kas</span>
                        @else
                            <span class="badge badge-info">Bank</span>
                        @endif
                    </td>
                    <td>{{ $acc->description ?? '-' }}</td>
                    <td class="num" style="font-weight: bold;">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; font-style: italic;">Tidak ada data akun.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Saldo Gabungan:</td>
                <td class="num">Rp {{ number_format($summary['total_balance'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title" style="margin-top: 0;">Tren Arus Kas (12 Bulan Terakhir)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="num" style="color: #047857;">Pemasukan</th>
                <th class="num" style="color: #b91c1c;">Pengeluaran</th>
                <th class="num">Selisih (Net)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashflow_trend as $trend)
                <tr>
                    <td>{{ $trend['month'] }}</td>
                    <td class="num" style="color: #047857;">Rp {{ number_format($trend['income'], 0, ',', '.') }}</td>
                    <td class="num" style="color: #b91c1c;">Rp {{ number_format($trend['expense'], 0, ',', '.') }}</td>
                    <td class="num" style="font-weight: bold; color: {{ $trend['net'] >= 0 ? '#0369a1' : '#b91c1c' }}">
                        {{ $trend['net'] >= 0 ? '+' : '' }} Rp {{ number_format($trend['net'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">5 Transaksi Terbaru</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Akun</th>
                <th>Keterangan</th>
                <th class="num">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($latest_transactions as $tx)
                <tr>
                    <td>{{ $tx->date ? $tx->date->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($tx->type === 'income')
                            <span class="badge badge-success">Pemasukan</span>
                        @elseif($tx->type === 'expense')
                            <span class="badge badge-danger">Pengeluaran</span>
                        @else
                            <span class="badge badge-warning">Transfer</span>
                        @endif
                    </td>
                    <td>{{ $tx->category?->name ?? 'Tanpa Kategori' }}</td>
                    <td>{{ $tx->account?->name ?? '-' }}</td>
                    <td>{{ $tx->description ?? '-' }}</td>
                    <td class="num" style="font-weight: bold; color: {{ $tx->type === 'income' ? '#047857' : '#b91c1c' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh sistem {{ config('app.name', 'Cashflow') }}
    </div>
</body>
</html>
