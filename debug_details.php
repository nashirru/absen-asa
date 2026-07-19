<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$details = \App\Models\PayrollDetail::with('karyawan.user', 'payrollPeriod')->get()->toArray();
foreach ($details as $d) {
    echo "Detail ID: {$d['id']}, Period ID: {$d['payroll_period_id']} ({$d['payroll_period']['month']}/{$d['payroll_period']['year']}), Karyawan ID: {$d['karyawan_id']}, Name: " . ($d['karyawan']['user']['name'] ?? 'N/A') . "\n";
}
