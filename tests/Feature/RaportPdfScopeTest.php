<?php

namespace Tests\Feature;

use App\Models\AdminCabang;
use App\Models\Hafalan;
use App\Models\Kelas;
use App\Models\Pondok;
use App\Models\QuranSurah;
use App\Models\Santri;
use App\Models\SantriKelas;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menjaga konteks pondok tidak hilang di jalur cetak raport.
 *
 * Bug yang ditutup tes ini: halaman preview raport tidak meneruskan `pondok_id`
 * ke komponennya, sehingga tombol Export PDF membuat URL tanpa `pondok_id` dan
 * super admin selalu mendapat 422 "Pilih pondok terlebih dahulu."
 *
 * Perhatikan bahwa memanggil endpoint PDF dengan parameter lengkap TIDAK akan
 * menangkap bug tersebut. Yang putus adalah kontrak antara controller dan
 * komponen, jadi props `filters` itulah yang harus diperiksa.
 */
class RaportPdfScopeTest extends TestCase
{
    use RefreshDatabase;

    private Pondok $pondokA;
    private Pondok $pondokB;
    private TahunAjaran $tahunAjaranA;
    private Kelas $kelasA;
    private Santri $santriA;
    private Santri $santriB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pondokA = Pondok::create(['nama' => 'Pondok A']);
        $this->pondokB = Pondok::create(['nama' => 'Pondok B']);

        [$this->tahunAjaranA, $this->kelasA, $this->santriA] = $this->siapkanPondok($this->pondokA, 'A001', 'Santri A');
        [, , $this->santriB] = $this->siapkanPondok($this->pondokB, 'B001', 'Santri B');
    }

    /**
     * @return array{0: TahunAjaran, 1: Kelas, 2: Santri}
     */
    private function siapkanPondok(Pondok $pondok, string $nis, string $nama): array
    {
        $tahunAjaran = TahunAjaran::create([
            'pondok_id' => $pondok->id,
            'nama' => '2025/2026',
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2026-06-30',
            'is_active' => true,
        ]);

        $kelas = Kelas::create([
            'pondok_id' => $pondok->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama' => 'Kelas 1',
        ]);

        $santri = Santri::create([
            'nis' => $nis,
            'pondok_id' => $pondok->id,
            'kelas_id' => $kelas->id,
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

        SantriKelas::create([
            'santri_id' => $santri->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'status' => 'aktif',
            'tanggal_masuk' => '2025-07-01',
        ]);

        return [$tahunAjaran, $kelas, $santri];
    }

    private function buatSuperAdmin(): User
    {
        return User::create([
            'username' => 'sa' . fake()->unique()->numerify('###'),
            'email' => 'sa' . fake()->unique()->numerify('###') . '@example.test',
            'password' => bcrypt('secret'),
            'role' => 'super_admin',
        ]);
    }

    private function buatAdminCabang(Pondok $pondok): User
    {
        $user = User::create([
            'username' => 'ac' . fake()->unique()->numerify('###'),
            'email' => 'ac' . fake()->unique()->numerify('###') . '@example.test',
            'password' => bcrypt('secret'),
            'role' => 'admin_cabang',
        ]);

        AdminCabang::create([
            'user_id' => $user->id,
            'pondok_id' => $pondok->id,
            'name' => 'Admin ' . $pondok->nama,
            'phone' => '08120000000',
        ]);

        return $user;
    }

    /**
     * Inti perbaikan: props `filters` pada halaman preview harus memuat
     * `pondok_id`, karena dari situlah tombol Export PDF menyusun URL-nya.
     */
    public function test_preview_meneruskan_pondok_id_ke_props(): void
    {
        $this->actingAs($this->buatSuperAdmin())
            ->get("/super-admin/laporan/raport/{$this->santriA->nis}/preview?pondok_id={$this->pondokA->id}&tahun_ajaran_id={$this->tahunAjaranA->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('filters.pondok_id', $this->pondokA->id)
                ->where('filters.tahun_ajaran_id', $this->tahunAjaranA->id)
            );
    }

    public function test_preview_admin_cabang_mengisi_pondok_id_otomatis(): void
    {
        // Admin cabang tidak mengirim pondok_id di URL; nilainya harus tetap
        // terisi dari record AdminCabang agar tautan Export PDF tetap utuh.
        $this->actingAs($this->buatAdminCabang($this->pondokA))
            ->get("/admin-cabang/laporan/raport/{$this->santriA->nis}/preview")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('filters.pondok_id', $this->pondokA->id));
    }

    public function test_super_admin_bisa_unduh_pdf_dengan_pondok_id(): void
    {
        $response = $this->actingAs($this->buatSuperAdmin())
            ->get("/super-admin/laporan/raport/{$this->santriA->nis}/pdf?pondok_id={$this->pondokA->id}");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_unduhan_tanpa_pondok_id_tetap_ditolak(): void
    {
        // Proteksi ini memang harus dipertahankan; yang diperbaiki adalah
        // frontend yang lupa mengirimkan pondok_id, bukan pemeriksaannya.
        $this->actingAs($this->buatSuperAdmin())
            ->get("/super-admin/laporan/raport/{$this->santriA->nis}/pdf")
            ->assertStatus(422);
    }

    public function test_admin_cabang_bisa_unduh_tanpa_pondok_id_di_url(): void
    {
        $this->actingAs($this->buatAdminCabang($this->pondokA))
            ->get("/admin-cabang/laporan/raport/{$this->santriA->nis}/pdf")
            ->assertOk();
    }

    public function test_admin_cabang_tidak_bisa_unduh_raport_pondok_lain(): void
    {
        $this->actingAs($this->buatAdminCabang($this->pondokA))
            ->get("/admin-cabang/laporan/raport/{$this->santriB->nis}/pdf")
            ->assertNotFound();
    }

    public function test_super_admin_tidak_bisa_memakai_pondok_id_yang_salah(): void
    {
        // pondok_id pondok B, tapi santrinya milik pondok A.
        $this->actingAs($this->buatSuperAdmin())
            ->get("/super-admin/laporan/raport/{$this->santriA->nis}/pdf?pondok_id={$this->pondokB->id}")
            ->assertNotFound();
    }
}
