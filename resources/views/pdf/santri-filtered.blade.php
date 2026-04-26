<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Santri - {{ $santri->nama }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #16a34a; padding-bottom: 8px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #555; }
        .section-title { margin-top: 12px; margin-bottom: 6px; padding: 5px 8px; background: #16a34a; color: #fff; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 3px 6px; vertical-align: top; }
        .meta td:first-child { width: 30%; font-weight: bold; color: #444; }
        .grid th, .grid td { border: 1px solid #ddd; padding: 4px 6px; font-size: 10px; }
        .grid th { background: #f0fdf4; text-align: left; }
        .muted { color: #777; font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Santri</h1>
        <p>{{ $pondokNama }}</p>
        <p>Kelas: {{ $kelasNama }} | Tahun Ajaran: {{ $tahunAjaranNama }}</p>
    </div>

    <div class="section-title">Profil Santri</div>
    <table class="meta">
        <tr><td>NIS</td><td>{{ $santri->nis ?? '-' }}</td></tr>
        <tr><td>Nama</td><td>{{ $santri->nama }}</td></tr>
        <tr><td>Jenis Kelamin</td><td>{{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><td>Tempat/Tanggal Lahir</td><td>{{ $santri->tempat_lahir ?? '-' }}, {{ $santri->tanggal_lahir ?? '-' }}</td></tr>
        <tr><td>Alamat</td><td>{{ $santri->alamat ?? '-' }}</td></tr>
    </table>

    <div class="section-title">Data Orang Tua</div>
    <table class="meta">
        <tr><td>Ayah</td><td>{{ $ayah?->nama ?? '-' }}</td></tr>
        <tr><td>Ibu</td><td>{{ $ibu?->nama ?? '-' }}</td></tr>
        <tr><td>Wali</td><td>{{ $wali?->nama ?? '-' }}</td></tr>
    </table>

    <div class="section-title">Riwayat Hafalan</div>
    @if($hafalans->count() > 0)
        <table class="grid">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Range Ayat</th>
                    <th>Kategori</th>
                    <th>Nilai</th>
                    <th>Ustadz</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hafalans as $index => $hafalan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $hafalan->tanggal_setor?->format('Y-m-d') }}</td>
                        <td>
                            {{ $hafalan->dariSurah?->name ?? '-' }}:{{ $hafalan->dari_ayat }}
                            -
                            {{ $hafalan->sampaiSurah?->name ?? '-' }}:{{ $hafalan->sampai_ayat }}
                        </td>
                        <td>{{ $hafalan->kategori }}</td>
                        <td>{{ $hafalan->nilai }}</td>
                        <td>{{ $hafalan->ustadz?->nama ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada data hafalan pada filter ini.</p>
    @endif

    <p class="muted">Dicetak pada {{ now()->format('Y-m-d H:i:s') }}.</p>
</body>
</html>
