<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            background-color: #fdfdfd;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #F58220;
            padding-bottom: 12px;
        }

        .header h1 {
            color: #F58220;
            margin: 0;
            font-size: 26px;
        }

        .header p {
            margin: 5px 0;
            color: #777;
            font-size: 11px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            background-color: #f1f1f1;
            padding: 15px 20px;
            border-left: 5px solid #F58220;
            margin-bottom: 25px;
        }

        .info-left, .info-right {
            flex: 1;
        }

        .info-item {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .info-value {
            color: #555;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9.5px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ccc;
            padding: 6px 5px;
        }

        .data-table th {
            background-color: #F58220;
            color: white;
            font-weight: bold;
        }

        .data-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-success {
            color: #28a745;
            font-weight: bold;
        }

        .status-failed {
            color: #dc3545;
            font-weight: bold;
        }

        .summary-section {
            margin-top: 30px;
            background-color: #fff8f0;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        }

        .summary-title {
            font-size: 15px;
            color: #F58220;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #F58220;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .summary-content {
            display: flex;
            justify-content: space-around;
        }

        .summary-item {
            text-align: center;
        }

        .summary-value {
            font-size: 17px;
            font-weight: bold;
            color: #333;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
        }

        .no-data {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            color: #888;
            font-style: italic;
            border: 1px dashed #ccc;
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .col-kategori { width: 14%; }
        .col-id { width: 12%; }
        .col-tanggal { width: 10%; }
        .col-waktu { width: 8%; }
        .col-status { width: 10%; }
        .col-metode { width: 15%; }
        .col-total { width: 15%; }
        .col-platform { width: 16%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Platform: {{ $platform }}</p>
        <p>Digenerate pada: {{ $date }}</p>
    </div>

    <div class="info-section">
        <div class="info-left">
            <div class="info-item">
                <span class="info-label">Total Transaksi:</span>
                <span class="info-value">{{ $total }} record</span>
            </div>
            <div class="info-item">
                <span class="info-label">Platform:</span>
                <span class="info-value">{{ $platform }}</span>
            </div>
        </div>
        <div class="info-right">
            <div class="info-item">
                <span class="info-label">Total Pendapatan:</span>
                <span class="info-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value status-success">Berhasil Digenerate</span>
            </div>
        </div>
    </div>

    @if(count($data) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-kategori">Kategori</th>
                    <th class="col-id">ID Pesanan</th>
                    <th class="col-tanggal">Tanggal</th>
                    <th class="col-waktu">Waktu</th>
                    <th class="col-status">Status</th>
                    <th class="col-metode">Metode Pembayaran</th>
                    <th class="col-total">Total</th>
                    @if($platform === 'Semua Platform')
                        <th class="col-platform">Platform</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->kategori ?? '-' }}</td>
                    <td class="text-center">{{ $item->id_pesanan }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                    <td class="text-center">
                        @if($item->status)
                            <span class="status-success">Sukses</span>
                        @else
                            <span class="status-failed">Gagal</span>
                        @endif
                    </td>
                    <td>{{ $item->metode_pembayaran }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    @if($platform === 'Semua Platform')
                        <td class="text-center">{{ $item->platform_name ?? '-' }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-title">RINGKASAN LAPORAN</div>
            <div class="summary-content">
                <div class="summary-item">
                    <div class="summary-value">{{ $total }}</div>
                    <div class="summary-label">Total Transaksi</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    <div class="summary-label">Total Pendapatan</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ $platform }}</div>
                    <div class="summary-label">Platform</div>
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <p><strong>Tidak ada data transaksi untuk ditampilkan</strong></p>
            <p>Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem pada {{ $date }}</p>
        <p>© {{ date('Y') }} - Laporan Transaksi - All rights reserved.</p>
    </div>
</body>
</html>