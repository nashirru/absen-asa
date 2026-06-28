<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $payrollDetail->employee->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .slip-container {
            max-width: 750px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
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
    <div class="slip-container">
        <!-- Slip Header Table -->
        <table style="width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="text-align: left; vertical-align: top;">
                    <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; font-family: 'Helvetica', 'Arial', sans-serif;">Cashflow</h1>
                    <p style="margin: 3px 0 0 0; font-size: 11px; color: #64748b;">Perusahaan {{ config('app.name', 'Keuangan') }} &mdash; Slip Gaji Resmi</p>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px;">Slip Gaji</h2>
                    <p style="margin: 4px 0 0 0; font-size: 11px; color: #475569; font-weight: bold;">
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            echo ($months[$payrollDetail->payrollPeriod->month] ?? '') . ' ' . $payrollDetail->payrollPeriod->year;
                        @endphp
                    </p>
                </td>
            </tr>
        </table>

        <!-- Employee Details Table -->
        <table style="width: 100%; background: #f8fafc; border: 1px solid #f1f5f9; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 4px 0;">
                    <span style="color: #64748b; font-weight: 500;">Nama Karyawan:</span><br>
                    <span style="font-weight: 600; color: #0f172a; font-size: 12px;">{{ $payrollDetail->employee->name }}</span>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 4px 0;">
                    <span style="color: #64748b; font-weight: 500;">Tanggal Bayar:</span><br>
                    <span style="font-weight: 600; color: #0f172a; font-size: 12px;">{{ $payrollDetail->paid_at ? $payrollDetail->paid_at->format('d/m/Y') : 'Belum Dibayar' }}</span>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 4px 0;">
                    <span style="color: #64748b; font-weight: 500;">Jabatan:</span><br>
                    <span style="font-weight: 600; color: #0f172a;">{{ $payrollDetail->employee->position }}</span>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 4px 0;">
                    <span style="color: #64748b; font-weight: 500;">Departemen:</span><br>
                    <span style="font-weight: 600; color: #0f172a;">{{ $payrollDetail->employee->department ?? '-' }}</span>
                </td>
            </tr>
        </table>

        <!-- Salary Breakdown Table -->
        <table style="width: 100%; margin-bottom: 25px; border-collapse: collapse;">
            <tr>
                <!-- Earnings Column -->
                <td style="width: 48%; vertical-align: top;">
                    <h3 style="font-size: 12px; font-weight: 700; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px; text-transform: uppercase;">Penerimaan (Earnings)</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; color: #475569;">Gaji Pokok</td>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; text-align: right; font-weight: 600;">Rp {{ number_format($payrollDetail->base_salary, 0, ',', '.') }}</td>
                        </tr>
                        @foreach($allowances as $allowance)
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; color: #475569;">{{ $allowance->name }}</td>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; text-align: right; font-weight: 600;">Rp {{ number_format($allowance->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        @if($payrollDetail->bonus > 0)
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; color: #475569;">Bonus Tambahan</td>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; text-align: right; font-weight: 600; color: #10b981;">Rp {{ number_format($payrollDetail->bonus, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
                <!-- Empty spacer column -->
                <td style="width: 4%;"></td>
                <!-- Deductions Column -->
                <td style="width: 48%; vertical-align: top;">
                    <h3 style="font-size: 12px; font-weight: 700; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px; text-transform: uppercase;">Potongan (Deductions)</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        @forelse($deductions as $deduction)
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; color: #475569;">{{ $deduction->name }}</td>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; text-align: right; font-weight: 600; color: #ef4444;">Rp {{ number_format($deduction->amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; color: #475569; font-style: italic;">Tidak ada potongan</td>
                            <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0; text-align: right; font-weight: 600;">Rp 0</td>
                        </tr>
                        @endforelse
                    </table>
                </td>
            </tr>
        </table>

        <!-- Summary Box Table -->
        <table style="width: 100%; background: #4f46e5; color: #ffffff; border-radius: 8px; padding: 15px 20px; margin-bottom: 30px; border-collapse: collapse;">
            <tr>
                <td style="font-size: 13px; font-weight: 600; opacity: 0.9; text-align: left; vertical-align: middle;">Gaji Bersih (Net Take-Home Pay)</td>
                <td style="font-size: 20px; font-weight: 700; text-align: right; vertical-align: middle;">Rp {{ number_format($payrollDetail->net_salary, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Signatures Table -->
        <table style="width: 100%; margin-top: 40px; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="color: #64748b; font-weight: 500; margin-bottom: 50px;">Diterima Oleh,</div>
                    <div style="font-weight: 700; color: #0f172a; display: inline-block; border-top: 1px solid #cbd5e1; padding-top: 6px; width: 180px;">{{ $payrollDetail->employee->name }}</div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="color: #64748b; font-weight: 500; margin-bottom: 50px;">Disetujui Oleh,</div>
                    <div style="font-weight: 700; color: #0f172a; display: inline-block; border-top: 1px solid #cbd5e1; padding-top: 6px; width: 180px;">Manajemen Cashflow</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dihasilkan secara otomatis oleh sistem {{ config('app.name', 'Cashflow') }}
        </div>
    </div>
</body>
</html>