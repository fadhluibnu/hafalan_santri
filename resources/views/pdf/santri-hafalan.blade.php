@extends('pdf.layout')
@section('title', 'Progress Hafalan - ' . $santri->nama)
@section('content')
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
@endsection
