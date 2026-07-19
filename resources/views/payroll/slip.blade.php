<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $employeeName }} - {{ $payrollDetail->payrollPeriod->month }}/{{ $payrollDetail->payrollPeriod->year }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .slip-container {
            background: #ffffff;
            width: 100%;
            max-width: 800px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            padding: 40px;
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            position: relative;
        }
        .slip-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.025em;
        }
        .company-info p {
            margin: 4px 0 0 0;
            font-size: 14px;
            color: #64748b;
        }
        .slip-title {
            text-align: right;
        }
        .slip-title h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .slip-title p {
            margin: 4px 0 0 0;
            font-size: 14px;
            color: #475569;
            font-weight: 500;
        }
        .employee-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid #f1f5f9;
        }
        .detail-group {
            display: flex;
            font-size: 14px;
            line-height: 1.5;
        }
        .detail-label {
            width: 140px;
            color: #64748b;
            font-weight: 500;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 600;
        }
        .salary-breakdown {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .item-name {
            color: #475569;
        }
        .item-value {
            font-weight: 600;
            color: #0f172a;
        }
        .summary-box {
            background: #4f46e5;
            color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .summary-label {
            font-size: 16px;
            font-weight: 600;
            opacity: 0.9;
        }
        .summary-value {
            font-size: 26px;
            font-weight: 700;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
        }
        .signature-title {
            color: #64748b;
            margin-bottom: 70px;
            font-weight: 500;
        }
        .signature-name {
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            display: inline-block;
            padding-bottom: 4px;
            min-width: 180px;
        }
        .action-bar {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            gap: 12px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .btn-secondary {
            background-color: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                color: #000000;
            }
            .slip-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .action-bar {
                display: none;
            }
            .employee-details {
                background: transparent;
                border: 1px solid #cbd5e1;
            }
        }
    </style>
</head>
<body>

    <div class="slip-container">
        <!-- Header -->
        @php
            $slipLogo = \App\Models\Setting::get('slip_logo');
            $slipSubtitle = \App\Models\Setting::get('slip_subtitle', 'Sistem Penggajian & Arus Kas Digital');
            $appName = \App\Models\Setting::get('app_name', config('app.name', 'MeBoX'));
        @endphp
        <div class="slip-header">
            <div class="company-info">
                @if($slipLogo && file_exists(public_path('uploads/logo/' . $slipLogo)))
                    <img src="{{ asset('uploads/logo/' . $slipLogo) }}" style="max-height: 50px; max-width: 220px; object-fit: contain; margin-bottom: 6px;" alt="Logo Perusahaan">
                @else
                    <h1>{{ $appName }}</h1>
                @endif
                <p>{{ $slipSubtitle }}</p>
            </div>
            <div class="slip-title">
                <h2>Slip Gaji Karyawan</h2>
                <p>Periode: 
                    @php
                        $months = [
                            1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
                            5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
                            9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
                        ];
                        echo ($months[$payrollDetail->payrollPeriod->month] ?? "") . " " . $payrollDetail->payrollPeriod->year;
                    @endphp
                </p>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="employee-details">
            <div class="detail-group">
                <span class="detail-label">Nama Karyawan</span>
                <span class="detail-value">: {{ $employeeName }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Tanggal Bayar</span>
                <span class="detail-value">: {{ $payrollDetail->paid_at ? $payrollDetail->paid_at->format('d/m/Y H:i') : 'Belum Dibayar' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Jabatan</span>
                <span class="detail-value">: {{ $payrollDetail->karyawan?->jabatan ?? '-' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Departemen</span>
                <span class="detail-value">: {{ $payrollDetail->karyawan?->divisi ?? '-' }}</span>
            </div>
        </div>

        <!-- Salary Breakdown -->
        <div class="salary-breakdown">
            <!-- Earnings -->
            <div>
                <h3 class="section-title">Penerimaan (Earnings)</h3>
                <ul class="item-list">
                    <li class="item-row">
                        <span class="item-name">Gaji Pokok</span>
                        <span class="item-value">Rp {{ number_format($payrollDetail->base_salary, 0, ',', '.') }}</span>
                    </li>
                    @foreach($allowances as $allowance)
                    <li class="item-row">
                        <span class="item-name">{{ $allowance->name }}</span>
                        <span class="item-value">Rp {{ number_format($allowance->amount, 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                    @if($payrollDetail->bonus > 0)
                    <li class="item-row">
                        <span class="item-name">Bonus Tambahan</span>
                        <span class="item-value">Rp {{ number_format($payrollDetail->bonus, 0, ',', '.') }}</span>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Deductions -->
            <div>
                <h3 class="section-title">Potongan (Deductions)</h3>
                <ul class="item-list">
                    @forelse($deductions as $deduction)
                    <li class="item-row">
                        <span class="item-name">{{ $deduction->name }}</span>
                        <span class="item-value">Rp {{ number_format($deduction->amount, 0, ',', '.') }}</span>
                    </li>
                    @empty
                    <li class="item-row">
                        <span class="item-name" style="font-style: italic;">Tidak ada potongan</span>
                        <span class="item-value">Rp 0</span>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Net Salary Box -->
        <div class="summary-box">
            <span class="summary-label">Gaji Bersih (Net Take-Home Pay)</span>
            <span class="summary-value">Rp {{ number_format($payrollDetail->net_salary, 0, ',', '.') }}</span>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div>
                <div class="signature-title" style="margin-bottom: 10px;">Diterima Oleh,</div>
                <div style="height: 60px; display: flex; justify-content: center; align-items: center; margin-bottom: 10px;"></div>
                <div class="signature-name">{{ $employeeName }}</div>
            </div>
            <div>
                <div class="signature-title" style="margin-bottom: 10px;">Disetujui Oleh,</div>
                @php
                    $ttdDigital = \App\Models\Setting::get('ttd_digital');
                    $ttdNama = \App\Models\Setting::get('ttd_nama', 'Manajemen LPK Asa');
                @endphp
                <div style="height: 60px; display: flex; justify-content: center; align-items: center; margin-bottom: 10px;">
                    @if($ttdDigital && file_exists(public_path('uploads/ttd/' . $ttdDigital)))
                        <img src="{{ asset('uploads/ttd/' . $ttdDigital) }}" style="max-height: 60px; max-width: 150px; object-fit: contain;" alt="Tanda Tangan Digital">
                    @endif
                </div>
                <div class="signature-name">{{ $ttdNama }}</div>
            </div>
        </div>
    </div>

    <!-- Floating Actions -->
    <div class="action-bar">
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        <button onclick="window.print()" class="btn btn-primary">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak Slip
        </button>
    </div>

    <script>
        // Auto trigger printer dialog on page load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
