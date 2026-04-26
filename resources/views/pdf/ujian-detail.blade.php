<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ujian - {{ $ujian['nama'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #2563eb; padding-bottom: 8px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        .grid th, .grid td { border: 1px solid #ddd; padding: 4px 6px; font-size: 10px; }
        .grid th { background: #eff6ff; text-align: left; }
        .meta td { padding: 3px 6px; }
        .meta td:first-child { width: 28%; font-weight: bold; color: #444; }
        .muted { color: #777; font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    @php
        $formatNilai = function (array $snapshot, $nilaiLabel, $nilaiAngka = null) {
            $label = strtoupper(trim((string) $nilaiLabel));
            if ($label !== '') {
                $rawItems = !empty($snapshot['items'])
                    ? $snapshot['items']
                    : collect($snapshot['labels'] ?? [])->map(function ($legacyLabel) {
                        return [
                            'singkatan' => $legacyLabel,
                            'nama' => $legacyLabel,
                        ];
                    })->all();

                $items = collect($rawItems)
                    ->map(function ($item) {
                        return [
                            'singkatan' => strtoupper(trim((string) ($item['singkatan'] ?? ''))),
                            'nama' => trim((string) ($item['nama'] ?? '')),
                        ];
                    })
                    ->filter(function ($item) {
                        return $item['singkatan'] !== '';
                    })
                    ->keyBy('singkatan');

                $found = $items->get($label);

                return $found && $found['nama'] !== ''
                    ? $label . ' - ' . $found['nama']
                    : $label;
            }

            if ($nilaiAngka !== null && $nilaiAngka !== '') {
                return (string) $nilaiAngka;
            }

            return '-';
        };
    @endphp

    <div class="header">
        <h1>Laporan Detail Ujian</h1>
        <p>{{ $ujian['nama'] }}</p>
    </div>

    <table class="meta">
        <tr><td>Kelas</td><td>{{ $ujian['kelas'] }}</td></tr>
        <tr><td>Tahun Ajaran</td><td>{{ $ujian['tahun_ajaran'] }}</td></tr>
        <tr><td>Tanggal Ujian</td><td>{{ $ujian['tanggal_ujian'] }}</td></tr>
        <tr><td>Penguji</td><td>{{ $ujian['ustadz'] }}</td></tr>
        <tr><td>Status</td><td>{{ $ujian['status'] }}</td></tr>
        <tr><td>Skema</td><td>{{ $ujian['skema_snapshot']['nama'] ?? '-' }}</td></tr>
    </table>

    <h3>Nilai Peserta</h3>
    <table class="grid">
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Nilai</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ujian['nilai'] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['nis'] ?? '-' }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $formatNilai($ujian['skema_snapshot'] ?? [], $row['nilai_label'] ?? null, $row['nilai_angka'] ?? null) }}</td>
                    <td>{{ $row['catatan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data nilai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="muted">Dicetak pada {{ now()->format('Y-m-d H:i:s') }}.</p>
</body>
</html>
