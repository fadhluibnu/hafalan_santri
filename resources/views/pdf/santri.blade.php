@extends('pdf.layout')
@section('title', 'Data Santri - ' . $santri->nama)
@section('content')
    <div class="header">
        <h1>DATA SANTRI</h1>
        <h2>{{ $pondokNama }}</h2>
    </div>

    <!-- Data Diri -->
    <div class="section">
        <div class="section-title">DATA DIRI SANTRI</div>
        <table class="data-table">
            <tr>
                <td class="label">NIS</td>
                <td class="value">{{ $santri->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="value">{{ $santri->nama }}</td>
            </tr>
            <tr>
                <td class="label">Nama Panggilan</td>
                <td class="value">{{ $santri->panggilan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="value">{{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="value">{{ $santri->tempat_lahir ?? '-' }}, {{ $santri->tanggal_lahir ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status Mukim</td>
                <td class="value">{{ $santri->status_mukim ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="value">{{ $santri->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="value">{{ $kelasNama }}</td>
            </tr>
            <tr>
                <td class="label">No. Handphone</td>
                <td class="value">{{ $santri->handphone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="value">{{ $santri->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Data Keluarga -->
    <div class="section">
        <div class="section-title">DATA KELUARGA</div>
        <div class="grid-2">
            <div class="col">
                <strong style="color: #16a34a;">Ayah</strong>
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="value">{{ $ayah->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">{{ $ayah->status ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pekerjaan</td>
                        <td class="value">{{ $ayah->pekerjaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Handphone</td>
                        <td class="value">{{ $ayah->handphone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col">
                <strong style="color: #16a34a;">Ibu</strong>
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="value">{{ $ibu->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">{{ $ibu->status ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pekerjaan</td>
                        <td class="value">{{ $ibu->pekerjaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Handphone</td>
                        <td class="value">{{ $ibu->handphone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @if($wali && $wali->nama)
            <div style="margin-top: 10px;">
                <strong style="color: #16a34a;">Wali</strong>
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="value">{{ $wali->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hubungan</td>
                        <td class="value">{{ $wali->status_hubungan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Handphone</td>
                        <td class="value">{{ $wali->handphone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>

    <!-- Data Kesehatan -->
    @if($kesehatan)
        <div class="section">
            <div class="section-title">DATA KESEHATAN</div>
            <table class="data-table">
                <tr>
                    <td class="label">Golongan Darah</td>
                    <td class="value">{{ $kesehatan->golongan_darah ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Berat Badan</td>
                    <td class="value">{{ $kesehatan->berat_badan ?? '-' }} kg</td>
                </tr>
                <tr>
                    <td class="label">Tinggi Badan</td>
                    <td class="value">{{ $kesehatan->tinggi_badan ?? '-' }} cm</td>
                </tr>
                <tr>
                    <td class="label">Riwayat Penyakit</td>
                    <td class="value">{{ $kesehatan->riwayat_penyakit ?? '-' }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Riwayat Setoran -->
    <div class="section">
        <div class="section-title">RIWAYAT SETORAN HAFALAN (20 Terakhir)</div>
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
            <p style="text-align: center; color: #999; padding: 20px;">Belum ada data setoran hafalan.</p>
        @endif
    </div>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} | Sistem Hafalan Santri
    </div>
@endsection
