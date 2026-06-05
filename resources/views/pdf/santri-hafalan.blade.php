<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Hafalan - {{ $santri->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 14px;
            color: #16a34a;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 11px;
            font-weight: normal;
            color: #666;
        }

        .section {
            margin-bottom: 12px;
        }

        .section-title {
            background-color: #22c55e;
            color: white;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .data-table .label {
            width: 30%;
            font-weight: bold;
            color: #555;
        }

        .data-table .value {
            width: 70%;
        }

        .hafalan-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .hafalan-table th,
        .hafalan-table td {
            border: 1px solid #ddd;
            padding: 3px 5px;
            text-align: left;
        }

        .hafalan-table th {
            background-color: #22c55e;
            color: white;
            font-weight: bold;
        }

        .hafalan-table tr:nth-child(even) {
            background-color: #f0fdf4;
        }

        .stats {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .stat-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
        }

        .stat-label {
            font-size: 8px;
            color: #666;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PROGRESS HAFALAN SANTRI</h1>
        <h2>{{ $pondokNama }}</h2>
    </div>

    <!-- Data Santri -->
    <div class="section">
        <div class="section-title">DATA SANTRI</div>
        <table class="data-table">
            <tr>
                <td class="label">NIS</td>
                <td class="value">{{ $santri->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="value">{{ $santri->nama }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="value">{{ $kelasNama }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="value">{{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
        </table>
    </div>

    <!-- Statistik -->
    <div class="stats">
        <div class="stat-box">
            <div class="stat-number">{{ $hafalans->count() }}</div>
            <div class="stat-label">Total Setoran</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">
                {{ $hafalans->filter(function ($h) {
    return in_array($h->nilai, ['A', 'B', 'baik']); })->count() }}</div>
            <div class="stat-label">Nilai Baik</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $hafalans->pluck('juz')->unique()->count() }}</div>
            <div class="stat-label">Juz Disetor</div>
        </div>
    </div>

    <!-- Riwayat Hafalan -->
    <div class="section">
        <div class="section-title">RIWAYAT SETORAN HAFALAN</div>
        @if($hafalans->count() > 0)
            <table class="hafalan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th>Juz</th>
                        <th>Dari Surat</th>
                        <th>Sampai Surat</th>
                        <th>Kategori</th>
                        <th>Nilai</th>
                        <th>Ustadz</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hafalans as $index => $hafalan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $hafalan->kelas->nama ?? '-' }}</td>
                            <td>{{ $hafalan->tanggal_setor }}</td>
                            <td>{{ $hafalan->juz }}</td>
                            <td>{{ $hafalan->dariSurah->name ?? '-' }} : {{ $hafalan->dari_ayat }}</td>
                            <td>{{ $hafalan->sampaiSurah->name ?? '-' }} : {{ $hafalan->sampai_ayat }}</td>
                            <td>{{ $hafalan->kategori }}</td>
                            <td>{{ $hafalan->nilai }}</td>
                            <td>{{ $hafalan->ustadz->nama ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #999; padding: 15px;">Belum ada data setoran hafalan.</p>
        @endif
    </div>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} | Sistem Hafalan Santri
    </div>
</body>

</html>
