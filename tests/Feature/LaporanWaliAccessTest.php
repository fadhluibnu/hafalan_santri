<?php

namespace Tests\Feature;

use App\Models\AdminCabang;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Pondok;
use App\Models\Santri;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji otorisasi dan pembatasan lingkup pondok pada endpoint laporan wali.
 *
 * Laporan ini memuat nama dan nomor HP wali santri, jadi kebocoran antar pondok
 * berarti kebocoran data pribadi. Karena itu batasannya diuji, bukan diasumsikan.
 */
class LaporanWaliAccessTest extends TestCase
{
    use RefreshDatabase;

    private Pondok $pondokA;
    private Pondok $pondokB;
    private Santri $santriA;
    private Santri $santriB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pondokA = Pondok::create(['nama' => 'Pondok A']);
        $this->pondokB = Pondok::create(['nama' => 'Pondok B']);

        $this->santriA = $this->buatSantri('A001', 'Santri A', $this->pondokA);
        $this->santriB = $this->buatSantri('B001', 'Santri B', $this->pondokB);
    }

    private function buatSantri(string $nis, string $nama, Pondok $pondok): Santri
    {
        $tahunAjaran = TahunAjaran::create([
            'pondok_id' => $pondok->id,
            'nama' => '2025/2026',
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2026-06-30',
            'is_active' => true,
        ]);
        Kelas::create([
            'pondok_id' => $pondok->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama' => 'Kelas 1',
        ]);

        $santri = Santri::create([
            'nis' => $nis,
            'pondok_id' => $pondok->id,
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

        OrangTua::create([
            'santri_id' => $santri->id,
            'tipe' => 'Ayah',
            'nama' => 'Bapak ' . $nama,
            'status' => 'Hidup',
            'status_hubungan' => 'Kandung',
            'handphone' => '08110000000',
        ]);

        return $santri;
    }

    private function buatUser(string $role, ?Pondok $pondok = null): User
    {
        $user = User::create([
            'username' => $role . fake()->unique()->numerify('###'),
            'email' => $role . fake()->unique()->numerify('###') . '@example.test',
            'password' => bcrypt('secret'),
            'role' => $role,
        ]);

        if ($role === 'admin_cabang' && $pondok) {
            AdminCabang::create([
                'user_id' => $user->id,
                'pondok_id' => $pondok->id,
                'name' => 'Admin ' . $pondok->nama,
                'phone' => '08120000000',
            ]);
        }

        if ($role === 'ustadz' && $pondok) {
            Ustadz::create([
                'user_id' => $user->id,
                'pondok_id' => $pondok->id,
                'nip' => 'UST' . fake()->unique()->numerify('###'),
                'nama' => 'Ustadz Uji',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'L',
                'status_menikah' => 'Menikah',
                'alamat' => 'Alamat',
                'no_identitas' => '123',
                'no_telpon' => '024',
                'no_handphone' => '0812',
                'email' => $user->email,
                'tanggal_kerja' => '2020-01-01',
            ]);
        }

        return $user;
    }

    public function test_super_admin_bisa_membuka_halaman_laporan(): void
    {
        $this->actingAs($this->buatUser('super_admin'))
            ->get("/super-admin/laporan/laporan-wali?pondok_id={$this->pondokA->id}")
            ->assertOk();
    }

    public function test_admin_cabang_bisa_membuka_halaman_laporan(): void
    {
        $this->actingAs($this->buatUser('admin_cabang', $this->pondokA))
            ->get('/admin-cabang/laporan/laporan-wali')
            ->assertOk();
    }

    public function test_ustadz_tidak_punya_route_laporan_wali(): void
    {
        // Route sengaja tidak didaftarkan pada grup ustadz.
        $this->actingAs($this->buatUser('ustadz', $this->pondokA))
            ->get('/ustadz/laporan/laporan-wali')
            ->assertNotFound();
    }

    public function test_ustadz_ditolak_saat_memakai_route_admin(): void
    {
        // Middleware IsSuperAdmin mengalihkan ke halaman home, bukan mengembalikan
        // 403, dan itu memang perilaku yang sudah berlaku di seluruh route admin.
        // Yang penting: ustadz tidak sampai ke halaman laporannya.
        $this->actingAs($this->buatUser('ustadz', $this->pondokA))
            ->get("/super-admin/laporan/laporan-wali?pondok_id={$this->pondokA->id}")
            ->assertRedirect(route('home'));
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/admin-cabang/laporan/laporan-wali')
            ->assertRedirect('/login');
    }

    public function test_admin_cabang_tidak_bisa_mengunduh_laporan_santri_pondok_lain(): void
    {
        // Inti pengujian: admin pondok A tidak boleh melihat data wali pondok B.
        $this->actingAs($this->buatUser('admin_cabang', $this->pondokA))
            ->get("/admin-cabang/laporan/laporan-wali/{$this->santriB->nis}/pdf")
            ->assertNotFound();
    }

    public function test_super_admin_wajib_memilih_pondok_sebelum_mengunduh(): void
    {
        $this->actingAs($this->buatUser('super_admin'))
            ->get("/super-admin/laporan/laporan-wali/{$this->santriA->nis}/pdf")
            ->assertStatus(422);
    }

    public function test_unduhan_menghasilkan_berkas_pdf(): void
    {
        $response = $this->actingAs($this->buatUser('admin_cabang', $this->pondokA))
            ->get("/admin-cabang/laporan/laporan-wali/{$this->santriA->nis}/pdf?bulan_akhir=2026-06");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        // dompdf mengembalikan response biasa (bukan streamed), jadi isinya
        // diambil lewat getContent().
        $isi = $response->getContent();
        $this->assertStringStartsWith('%PDF-', $isi);
        // Gambar latar tertanam penuh, jadi berkasnya memang besar.
        $this->assertGreaterThan(100_000, strlen($isi));
    }

    public function test_bulan_tidak_valid_tidak_menggagalkan_unduhan(): void
    {
        $this->actingAs($this->buatUser('admin_cabang', $this->pondokA))
            ->get("/admin-cabang/laporan/laporan-wali/{$this->santriA->nis}/pdf?bulan_akhir=rusak")
            ->assertOk();
    }

    public function test_nis_tidak_dikenal_menghasilkan_404(): void
    {
        $this->actingAs($this->buatUser('admin_cabang', $this->pondokA))
            ->get('/admin-cabang/laporan/laporan-wali/TIDAK-ADA/pdf')
            ->assertNotFound();
    }
}
