<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Laporan Absensi</title>
<style>body{font-family:sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#2563EB;color:#fff}h1,h2{text-align:center}.summary{margin:20px 0}</style></head>
<body>
<h1>LPK Asa Hikari Mulya</h1>
<h2>Laporan Kehadiran - {{ ucfirst($period) }}</h2>
<div class="summary"><p>Total: {{ $total }} | Hadir: {{ $totalHadir }} | Persentase: {{ $persentase }}%</p></div>
<table><thead><tr><th>No</th><th>Nama</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Status</th></tr></thead>
<tbody>@foreach($absensi as $i => $a)<tr><td>{{ $i+1 }}</td><td>{{ $a->user->name ?? '-' }}</td><td>{{ $a->tanggal->format('d/m/Y') }}</td><td>{{ $a->jam_masuk ?? '-' }}</td><td>{{ $a->jam_keluar ?? '-' }}</td><td>{{ ucfirst($a->status) }}</td></tr>@endforeach</tbody></table>
<p style="margin-top:30px;text-align:right">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body></html>
