<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Penggajian - {{ $periodName }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #0f172a;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #64748b;
        }
        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            color: #4f46e5;
        }
        .report-title p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #475569;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px;
            font-weight: bold;
            text-align: left;
            color: #475569;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
        }
        .data-table .num {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f1f5f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
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
                    <p style="font-size: 10px; color: #64748b;">Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h2>Rekapitulasi Penggajian Karyawan</h2>
        <p>Periode: {{ $periodName }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th class="num">Gaji Pokok</th>
                <th class="num">Tunjangan</th>
                <th class="num">Potongan</th>
                <th class="num">Bonus</th>
                <th class="num">Gaji Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $sumBase = 0;
                $sumAllow = 0;
                $sumDeduct = 0;
                $sumBonus = 0;
                $sumNet = 0;
            @endphp
            @forelse($details as $detail)
                @php
                    $sumBase += $detail->base_salary;
                    $sumAllow += $detail->total_allowance;
                    $sumDeduct += $detail->total_deduction;
                    $sumBonus += $detail->bonus;
                    $sumNet += $detail->net_salary;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td style="font-weight: 500;">{{ $detail->employee->name }}</td>
                    <td>{{ $detail->employee->position }}</td>
                    <td class="num">Rp {{ number_format($detail->base_salary, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($detail->total_allowance, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($detail->total_deduction, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($detail->bonus, 0, ',', '.') }}</td>
                    <td class="num" style="font-weight: bold;">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; font-style: italic;">Tidak ada rincian penggajian untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($details->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td class="num">Rp {{ number_format($sumBase, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($sumAllow, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($sumDeduct, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($sumBonus, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($sumNet, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 40px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <p style="margin-bottom: 60px;">Disetujui Oleh,</p>
                <p style="font-weight: bold; border-top: 1px solid #cbd5e1; display: inline-block; padding-top: 6px; width: 180px;">Manajemen Cashflow</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh sistem {{ config('app.name', 'Cashflow') }}
    </div>
</body>
</html>
