<?php

namespace Tests\Feature;

use App\Models\Hafalan;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Pondok;
use App\Models\QuranSurah;
use App\Models\Santri;
use App\Models\SkemaPenilaian;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianNilai;
use App\Models\User;
use App\Models\Ustadz;
use App\Services\LaporanWaliService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji penyusunan data "Laporan Perkembangan Santri" untuk wali/orang tua.
 *
 * Fokusnya pada aturan yang mudah salah: pemilihan penerima laporan, prefiks
 * Alm./Almh., periode 7 bulan yang melintasi tahun, sifat kumulatif kolom juz,
 * dan penggabungan data ujian ke kolom Keterangan serta Pembimbing.
 */
class LaporanWaliServiceTest extends TestCase
{
    use RefreshDatabase;

    private Pondok $pondok;
    private TahunAjaran $tahunAjaran;
    private Kelas $kelas;
    private Santri $santri;
    private QuranSurah $surah;
    private SkemaPenilaian $skema;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pondok = Pondok::create(['nama' => 'Pondok Uji']);
        $this->tahunAjaran = TahunAjaran::create([
            'pondok_id' => $this->pondok->id,
            'nama' => '2025/2026',
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2026-06-30',
            'is_active' => true,
        ]);
        $this->kelas = Kelas::create([
            'pondok_id' => $this->pondok->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'nama' => 'Kelas Uji',
        ]);
        $this->santri = $this->buatSantri('S001', 'Haji Azkawira Zainal');
        $this->surah = QuranSurah::create(['name' => 'Al-Fatihah', 'jumlah_ayat' => 7]);
        $this->skema = SkemaPenilaian::create([
            'pondok_id' => $this->pondok->id,
            'nama' => 'Skema Uji',
            'tipe' => 'numeric',
            'is_active' => true,
        ]);
    }

    private function service(): LaporanWaliService
    {
        return app(LaporanWaliService::class);
    }

    private function buatSantri(string $nis, string $nama, ?int $pondokId = null): Santri
    {
        return Santri::create([
            'nis' => $nis,
            'pondok_id' => $pondokId ?? $this->pondok->id,
            'nama' => $nama,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '2010-01-01',
            'status_mukim' => 'Mukim',
            'kondisi' => 'Sehat',
            'warga_negara' => 'Indonesia',
            'alamat' => 'Alamat',
            'anak_ke' => 1,
            'jumlah_saudara' => 0,
            'status_anak' => 'Kandung',
            'foto' => 'santri_foto/default.png',
            'status_santri' => 'aktif',
        ]);
    }

    private function buatOrangTua(string $tipe, string $nama, string $status = 'Hidup', string $hp = '0811'): OrangTua
    {
        return OrangTua::create([
            'santri_id' => $this->santri->id,
            'tipe' => $tipe,
            'nama' => $nama,
            'status' => $status,
            'status_hubungan' => 'Kandung',
            'handphone' => $hp,
        ]);
    }

    private function buatUstadz(string $nama, string $nip): Ustadz
    {
        $user = User::create([
            'username' => 'u' . $nip,
            'email' => strtolower($nip) . '@example.test',
            'password' => bcrypt('secret'),
            'role' => 'ustadz',
        ]);

        return Ustadz::create([
            'user_id' => $user->id,
            'pondok_id' => $this->pondok->id,
            'nip' => $nip,
            'nama' => $nama,
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'status_menikah' => 'Menikah',
            'alamat' => 'Alamat',
            'no_identitas' => '123',
            'no_telpon' => '024',
            'no_handphone' => '0812',
            'email' => strtolower($nip) . '@example.test',
            'tanggal_kerja' => '2020-01-01',
        ]);
    }

    private function buatHafalan(
        string $tanggal,
        int $juz,
        string $kategori,
        ?Ustadz $ustadz = null,
        ?string $catatan = null
    ): Hafalan {
        $ustadz ??= $this->buatUstadz('Ustadz Default', 'UST' . fake()->unique()->numerify('###'));

        return Hafalan::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $ustadz->id,
            'kelas_id' => $this->kelas->id,
            'tanggal_setor' => $tanggal,
            'juz' => $juz,
            'dari_surat' => $this->surah->id,
            'dari_ayat' => 1,
            'sampai_surat' => $this->surah->id,
            'sampai_ayat' => 7,
            'kategori' => $kategori,
            'nilai' => 'A',
            'catatan' => $catatan,
        ]);
    }

    private function buatUjian(string $tanggal, string $nama, Ustadz $ustadz): Ujian
    {
        $ujian = Ujian::create([
            'pondok_id' => $this->pondok->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kelas_id' => $this->kelas->id,
            'ustadz_id' => $ustadz->id,
            'skema_penilaian_id' => $this->skema->id,
            'nama' => $nama,
            'tanggal_ujian' => $tanggal,
            'skema_snapshot' => ['id' => $this->skema->id, 'tipe' => 'numeric', 'items' => []],
            'status' => 'selesai',
        ]);

        UjianNilai::create([
            'ujian_id' => $ujian->id,
            'santri_id' => $this->santri->id,
            'nilai_angka' => 88,
        ]);

        return $ujian;
    }

    // ---------------------------------------------------------------- periode

    public function test_periode_selalu_tujuh_bulan_dan_boleh_melintasi_tahun(): void
    {
        $bulan = $this->service()->buildBulanList('2026-06');

        $this->assertCount(7, $bulan);
        $this->assertSame('2025-12', $bulan->first()['key']);
        $this->assertSame('2026-06', $bulan->last()['key']);
        $this->assertSame('Desember', $bulan->first()['label']);
        $this->assertSame('Juni', $bulan->last()['label']);
    }

    public function test_bulan_tidak_valid_jatuh_ke_bulan_berjalan(): void
    {
        foreach (['bukan-bulan', '2026-13-01', '', null] as $masukan) {
            $bulan = $this->service()->buildBulanList($masukan);

            $this->assertCount(7, $bulan);
            $this->assertSame(now()->format('Y-m'), $bulan->last()['key']);
        }
    }

    public function test_laporan_menghasilkan_tepat_tujuh_baris(): void
    {
        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');

        $this->assertNotNull($data);
        $this->assertCount(7, $data['baris']);
    }

    // ------------------------------------------------------- pemilihan wali

    public function test_wali_diprioritaskan_di_atas_ayah_dan_ibu(): void
    {
        $this->buatOrangTua('Ibu', 'Ibu Aminah');
        $this->buatOrangTua('Ayah', 'Bapak Dariola');
        $this->buatOrangTua('Wali', 'Pak Wali', 'Hidup', '0899');

        $wali = $this->service()->pilihWali($this->santri->fresh('orangTuas'));

        $this->assertSame('Pak Wali', $wali['nama']);
        $this->assertSame('0899', $wali['handphone']);
    }

    public function test_ayah_dipakai_bila_wali_tidak_ada(): void
    {
        $this->buatOrangTua('Ibu', 'Ibu Aminah');
        $this->buatOrangTua('Ayah', 'Bapak Dariola');

        $wali = $this->service()->pilihWali($this->santri->fresh('orangTuas'));

        $this->assertSame('Bapak Dariola', $wali['nama']);
    }

    public function test_ibu_dipakai_bila_wali_dan_ayah_tidak_ada(): void
    {
        $this->buatOrangTua('Ibu', 'Ibu Aminah');

        $wali = $this->service()->pilihWali($this->santri->fresh('orangTuas'));

        $this->assertSame('Ibu Aminah', $wali['nama']);
    }

    public function test_prefiks_alm_untuk_ayah_almarhum(): void
    {
        $this->buatOrangTua('Ayah', 'Dariola Yusharyahya', 'Almarhum');

        $wali = $this->service()->pilihWali($this->santri->fresh('orangTuas'));

        $this->assertSame('Alm. Dariola Yusharyahya', $wali['nama']);
    }

    public function test_prefiks_almh_untuk_ibu_almarhumah(): void
    {
        $this->buatOrangTua('Ibu', 'Aminah', 'Almarhum');

        $wali = $this->service()->pilihWali($this->santri->fresh('orangTuas'));

        $this->assertSame('Almh. Aminah', $wali['nama']);
    }

    public function test_prefiks_tidak_digandakan_bila_sudah_tertulis(): void
    {
        $this->buatOrangTua('Ayah', 'Alm. Dariola', 'Almarhum');

        $wali = $this->service()->pilihWali($this->santri->fresh('orangTuas'));

        $this->assertSame('Alm. Dariola', $wali['nama']);
    }

    public function test_santri_tanpa_orang_tua_tidak_menggagalkan_laporan(): void
    {
        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');

        $this->assertSame('-', $data['wali']['nama']);
        $this->assertSame('-', $data['wali']['handphone']);
        $this->assertCount(7, $data['baris']);
    }

    // ------------------------------------------------- agregasi bulanan

    public function test_dua_ejaan_ziyadah_dihitung_sebagai_ziyadah(): void
    {
        // Form Guru menyimpan 'ziadah', form Ustadz menyimpan 'Ziyadah'.
        $this->buatHafalan('2026-06-05', 18, 'ziadah');
        $this->buatHafalan('2026-06-10', 18, 'Ziyadah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('2', $juni['ziyadah']);
    }

    public function test_murojaah_kosong_bila_tidak_ada_datanya(): void
    {
        $this->buatHafalan('2026-06-05', 18, 'Ziyadah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('', $juni['murojaah']);
    }

    public function test_murojaah_dihitung_bila_ada_datanya(): void
    {
        $this->buatHafalan('2026-06-05', 18, 'Murojaah');
        $this->buatHafalan('2026-06-06', 18, 'murojaah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('2', $juni['murojaah']);
    }

    public function test_bulan_tanpa_setoran_menghasilkan_baris_kosong(): void
    {
        $this->buatHafalan('2026-04-10', 17, 'Ziyadah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $mei = collect($data['baris'])->firstWhere('bulan_key', '2026-05');

        $this->assertSame('', $mei['ziyadah']);
        $this->assertSame('', $mei['tanggal']);
        $this->assertSame('', $mei['pembimbing']);
        $this->assertSame('', $mei['keterangan']);
        // Baris tetap ada, hanya isinya kosong — seperti Maret & Mei pada contoh.
        $this->assertSame('Mei', $mei['bulan']);
    }

    public function test_juz_bersifat_kumulatif_dan_tidak_turun(): void
    {
        $this->buatHafalan('2026-01-10', 15, 'Ziyadah');
        $this->buatHafalan('2026-03-10', 17, 'Ziyadah');
        // April juz lebih rendah; capaian tertinggi tidak boleh turun.
        $this->buatHafalan('2026-04-10', 12, 'Murojaah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $baris = collect($data['baris'])->keyBy('bulan_key');

        $this->assertSame('15', $baris['2026-01']['juz']);
        $this->assertSame('17', $baris['2026-03']['juz']);
        $this->assertSame('17', $baris['2026-04']['juz']);
    }

    public function test_juz_memperhitungkan_capaian_sebelum_periode(): void
    {
        // Setoran jauh sebelum periode laporan.
        $this->buatHafalan('2025-08-10', 14, 'Ziyadah');
        $this->buatHafalan('2026-06-10', 15, 'Ziyadah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $baris = collect($data['baris'])->keyBy('bulan_key');

        // Desember tidak ada setoran, tapi capaian 14 juz sudah terjadi sebelumnya.
        $this->assertSame('', $baris['2025-12']['juz'], 'Bulan tanpa aktivitas tetap kosong.');
        $this->assertSame('15', $baris['2026-06']['juz']);
    }

    public function test_tanggal_memakai_setoran_terakhir_pada_bulan_itu(): void
    {
        $this->buatHafalan('2026-06-02', 18, 'Ziyadah');
        $this->buatHafalan('2026-06-15', 18, 'Ziyadah');

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('15-06-2026', $juni['tanggal']);
    }

    public function test_pembimbing_memakai_ustadz_dengan_setoran_terbanyak(): void
    {
        $cahyono = $this->buatUstadz('Ustadz Cahyono', 'UST100');
        $fajar = $this->buatUstadz('Ustadz Fajar', 'UST200');

        $this->buatHafalan('2026-06-01', 18, 'Ziyadah', $cahyono);
        $this->buatHafalan('2026-06-02', 18, 'Ziyadah', $cahyono);
        $this->buatHafalan('2026-06-03', 18, 'Ziyadah', $fajar);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('Ustadz Cahyono', $juni['pembimbing']);
    }

    // ------------------------------------------------- integrasi data ujian

    public function test_nama_ujian_masuk_kolom_keterangan(): void
    {
        $penguji = $this->buatUstadz('Ustadz Penguji', 'UST300');
        $this->buatUjian('2026-06-20', 'Ujian Akhir Semester (UAS)', $penguji);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertStringContainsString('Ujian Akhir Semester (UAS)', $juni['keterangan']);
    }

    public function test_keterangan_menggabungkan_catatan_hafalan_dan_nama_ujian(): void
    {
        $penguji = $this->buatUstadz('Ustadz Penguji', 'UST310');
        $this->buatHafalan('2026-06-05', 18, 'Ziyadah', null, 'Ziyadah 1 juz');
        $this->buatUjian('2026-06-20', 'Ujian Semester', $penguji);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertStringContainsString('Ziyadah 1 juz', $juni['keterangan']);
        $this->assertStringContainsString('Ujian Semester', $juni['keterangan']);
    }

    public function test_pembimbing_memakai_penguji_pada_bulan_ada_ujian(): void
    {
        $penyetor = $this->buatUstadz('Ustadz Penyetor', 'UST320');
        $penguji = $this->buatUstadz('Ustadz Penguji', 'UST330');

        $this->buatHafalan('2026-06-05', 18, 'Ziyadah', $penyetor);
        $this->buatUjian('2026-06-20', 'Ujian Semester', $penguji);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('Ustadz Penguji', $juni['pembimbing']);
    }

    public function test_pembimbing_kembali_ke_penyetor_pada_bulan_tanpa_ujian(): void
    {
        $penyetor = $this->buatUstadz('Ustadz Penyetor', 'UST340');
        $penguji = $this->buatUstadz('Ustadz Penguji', 'UST350');

        $this->buatHafalan('2026-05-05', 17, 'Ziyadah', $penyetor);
        $this->buatUjian('2026-06-20', 'Ujian Semester', $penguji);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $mei = collect($data['baris'])->firstWhere('bulan_key', '2026-05');

        $this->assertSame('Ustadz Penyetor', $mei['pembimbing']);
    }

    public function test_santri_punya_ujian_tanpa_hafalan_tetap_muncul(): void
    {
        // Kondisi ini nyata pada data sekarang: ujian mencakup lebih banyak
        // santri daripada hafalan.
        $penguji = $this->buatUstadz('Ustadz Penguji', 'UST360');
        $this->buatUjian('2026-06-20', 'Ujian Semester', $penguji);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('', $juni['ziyadah']);
        $this->assertSame('', $juni['tanggal']);
        $this->assertSame('Ustadz Penguji', $juni['pembimbing']);
        $this->assertStringContainsString('Ujian Semester', $juni['keterangan']);
    }

    public function test_ujian_di_luar_periode_tidak_ikut(): void
    {
        $penguji = $this->buatUstadz('Ustadz Penguji', 'UST370');
        $this->buatUjian('2025-10-20', 'Ujian Tengah Semester', $penguji);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');

        foreach ($data['baris'] as $row) {
            $this->assertStringNotContainsString('Ujian Tengah Semester', $row['keterangan']);
        }
    }

    // ----------------------------------------------------- batas antar pondok

    public function test_santri_pondok_lain_tidak_bisa_diambil(): void
    {
        $pondokLain = Pondok::create(['nama' => 'Pondok Lain']);
        $this->buatSantri('S999', 'Santri Pondok Lain', $pondokLain->id);

        $this->assertNull($this->service()->getLaporanData($this->pondok->id, 'S999', '2026-06'));
    }

    public function test_nis_tidak_dikenal_menghasilkan_null(): void
    {
        $this->assertNull($this->service()->getLaporanData($this->pondok->id, 'TIDAK-ADA', '2026-06'));
    }

    public function test_hafalan_santri_lain_tidak_tercampur(): void
    {
        $lain = $this->buatSantri('S002', 'Santri Lain');
        $ustadz = $this->buatUstadz('Ustadz X', 'UST400');

        Hafalan::create([
            'santri_id' => $lain->id,
            'ustadz_id' => $ustadz->id,
            'kelas_id' => $this->kelas->id,
            'tanggal_setor' => '2026-06-10',
            'juz' => 30,
            'dari_surat' => $this->surah->id,
            'dari_ayat' => 1,
            'sampai_surat' => $this->surah->id,
            'sampai_ayat' => 7,
            'kategori' => 'Ziyadah',
            'nilai' => 'A',
        ]);

        $data = $this->service()->getLaporanData($this->pondok->id, 'S001', '2026-06');
        $juni = collect($data['baris'])->firstWhere('bulan_key', '2026-06');

        $this->assertSame('', $juni['juz']);
        $this->assertSame('', $juni['ziyadah']);
    }
}
