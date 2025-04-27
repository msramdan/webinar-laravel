<!DOCTYPE html>
<html>

<head>
    <title>Laporan Presensi</title>
    <style>
        :root {
            --primary-color: #2563eb;
            /* Changed to a more professional blue */
            --secondary-color: #f8fafc;
            --accent-color: #10b981;
            --text-color: #1e293b;
            --border-color: #e2e8f0;
            --header-bg: #ffffff;
            --table-header-bg: #2563eb;
            --table-header-text: #ffffff;
            --table-row-hover: #f1f5f9;
            --danger-color: #dc2626;
            --warning-color: #f59e0b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: var(--text-color);
            line-height: 1.5;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }

        @page {
            size: A4;
            margin: 1.5cm;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .logo {
            height: 50px;
        }

        .title {
            color: var(--primary-color);
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
            margin-top: 4px;
        }

        .info {
            background-color: var(--secondary-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .info p {
            margin: 8px 0;
            display: flex;
        }

        .info p strong {
            min-width: 160px;
            color: #475569;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        thead {
            background-color: var(--table-header-bg);
            color: var(--table-header-text);
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: var(--secondary-color);
        }

        tr:hover {
            background-color: var(--table-row-hover);
        }

        .status-present {
            color: var(--accent-color);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .status-present:before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--accent-color);
            margin-right: 8px;
        }

        .status-absent {
            color: var(--danger-color);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .status-absent:before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--danger-color);
            margin-right: 8px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            font-size: 11px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
        }

        .page-number:after {
            content: counter(page);
        }

        /* Additional professional touches */
        .text-muted {
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #ecfdf5;
            color: #059669;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Print-specific styles */
        @media print {
            body {
                padding: 0;
            }

            .header {
                margin-top: 0;
            }

            table {
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>

</head>

<body>
    <center>
        <div class="header">
            <div>
                <h1 class="title">Laporan Presensi Peserta</h1>
                <p class="subtitle">Sistem Manajemen Kehadiran Digital</p>
            </div>
        </div>
    </center>
    <div class="info">
        <p><strong>Nama Sesi:</strong> {{ $sesi->nama_sesi }}</p>
        <p><strong>Tanggal Pelaksanaan:</strong> {{ date('d M Y H:i', strtotime($sesi->tanggal_pelaksanaan)) }}</p>
        <p><strong>Jumlah Peserta:</strong> {{ count($peserta) }} Orang</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>No Telepon</th>
                <th>Status</th>
                <th>Waktu Presensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peserta as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->no_telepon }}</td>
                    <td class="{{ $p->status_presensi == 'Hadir' ? 'status-present' : 'status-absent' }}">
                        {{ $p->status_presensi }}
                    </td>
                    <td>
                        @if ($p->waktu_presensi)
                            {{ date('d M Y H:i', strtotime($p->waktu_presensi)) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak pada: {{ date('d M Y H:i') }}</div>
        <div>Halaman <span class="page-number"></span></div>
    </div>
</body>

</html>
