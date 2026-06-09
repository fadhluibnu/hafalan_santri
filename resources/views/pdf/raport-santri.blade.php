@extends('pdf.layout')
@section('title', 'Raport - ' . $santri->nama)
@section('content')
    @php
        $formatNilaiUjian = function ($nilaiLabel, $snapshot, $nilaiAngka = null) {
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
        <h1>Raport Santri</h1>
        <p>{{ $pondokNama }}</p>
    </div>

    <div class="section-title">Identitas</div>
    <table class="meta">
        <tr><td>NIS</td><td>{{ $santri->nis }}</td></tr>
        <tr><td>Nama</td><td>{{ $santri->nama }}</td></tr>
        <tr><td>Kelas</td><td>{{ $placement->kelas?->nama ?? '-' }}</td></tr>
        <tr><td>Tahun Ajaran</td><td>{{ $placement->tahunAjaran?->nama ?? '-' }}</td></tr>
    </table>

    <div class="section-title">Ringkasan Hafalan</div>
    <table class="grid">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Range Ayat</th>
                <th>Kategori</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hafalans as $index => $hafalan)
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
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data hafalan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Nilai Ujian</div>
    <table class="grid">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ujian</th>
                <th>Tanggal</th>
                <th>Nilai</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ujianNilais as $index => $nilai)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $nilai->ujian?->nama ?? '-' }}</td>
                    <td>{{ $nilai->ujian?->tanggal_ujian?->format('Y-m-d') }}</td>
                    <td>{{ $formatNilaiUjian($nilai->nilai_label, $nilai->ujian?->skema_snapshot ?? [], $nilai->nilai_angka) }}</td>
                    <td>{{ $nilai->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data ujian pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($keterangan))
        <div class="section-title" style="margin-top: 15px;">Keterangan Tambahan</div>
        <p style="font-size: 11px; color: #444; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9fafb; white-space: pre-wrap;">{{ $keterangan }}</p>
    @endif

    <p class="muted">Dicetak pada {{ now()->format('Y-m-d H:i:s') }}.</p>
@endsection
