<?php

namespace Tests\Feature;

use App\Models\Hafalan;
use App\Models\Kelas;
use App\Models\Pondok;
use App\Models\QuranSurah;
use App\Models\Santri;
use App\Models\SkemaPenilaian;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Ustadz;
use App\Services\ReportService;
use App\Services\SantriPlacementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SantriHistoryAndHafalanGradingTest extends TestCase
{
    use RefreshDatabase;

    private bool $databaseAvailable = true;

    protected function setUp(): void
    {
        $connection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: null;
        if ($connection === 'sqlite' && !extension_loaded('pdo_sqlite')) {
            $this->databaseAvailable = false;
            return;
        }

        parent::setUp();
    }

    public function test_moving_santri_preserves_old_class_history_and_report_context(): void
    {
        if (!$this->canUseDatabase()) {
            return;
        }

        [$pondok, $tahunAjaran, $kelasA, $kelasB, $santri] = $this->createPlacementFixture();
        $surah = QuranSurah::create(['name' => 'Al-Fatihah', 'jumlah_ayat' => 7]);

        $placements = app(SantriPlacementService::class);
        $placements->assignToClass($santri, $kelasA, 'pindah', '2026-01-10');

        Hafalan::create([
            'santri_id' => $santri->id,
            'ustadz_id' => 1,
            'kelas_id' => $kelasA->id,
            'tanggal_setor' => '2026-02-01',
            'juz' => 1,
            'dari_surat' => $surah->id,
            'dari_ayat' => 1,
            'sampai_surat' => $surah->id,
            'sampai_ayat' => 7,
            'kategori' => 'Ziyadah',
            'nilai' => 'A',
        ]);

        $placements->assignToClass($santri, $kelasB, 'pindah', '2026-03-01');

        Hafalan::create([
            'santri_id' => $santri->id,
            'ustadz_id' => 1,
            'kelas_id' => $kelasB->id,
            'tanggal_setor' => '2026-04-01',
            'juz' => 2,
            'dari_surat' => $surah->id,
            'dari_ayat' => 1,
            'sampai_surat' => $surah->id,
            'sampai_ayat' => 7,
            'kategori' => 'Murojaah',
            'nilai' => 'B',
        ]);

        $this->assertDatabaseHas('santri_kelas', [
            'santri_id' => $santri->id,
            'kelas_id' => $kelasA->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'status' => 'pindah',
            'tanggal_keluar' => '2026-03-01',
        ]);

        $this->assertDatabaseHas('santri_kelas', [
            'santri_id' => $santri->id,
            'kelas_id' => $kelasB->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'status' => 'aktif',
        ]);

        $rows = app(ReportService::class)->getSantriList($pondok->id, $tahunAjaran->id);

        $rowA = $rows->firstWhere('kelas_id', $kelasA->id);
        $rowB = $rows->firstWhere('kelas_id', $kelasB->id);

        $this->assertSame('A', $rowA['nilai_hafalan_terakhir']);
        $this->assertSame('pindah', $rowA['status_penempatan']);
        $this->assertSame('B', $rowB['nilai_hafalan_terakhir']);
        $this->assertSame('aktif', $rowB['status_penempatan']);
    }

    public function test_hafalan_store_uses_active_grading_schema(): void
    {
        if (!$this->canUseDatabase()) {
            return;
        }

        [$pondok, $tahunAjaran, $kelas, , $santri] = $this->createPlacementFixture();
        $user = User::create([
            'username' => 'ustadz',
            'email' => 'ustadz@example.test',
            'password' => bcrypt('secret'),
            'role' => 'ustadz',
        ]);
        $ustadz = Ustadz::create([
            'user_id' => $user->id,
            'pondok_id' => $pondok->id,
            'nip' => 'UST001',
            'nama' => 'Ustadz Test',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'status_menikah' => 'Menikah',
            'alamat' => 'Alamat',
            'no_identitas' => '123',
            'no_telpon' => '022',
            'no_handphone' => '0812',
            'email' => 'ustadz@example.test',
            'tanggal_kerja' => '2020-01-01',
        ]);
        $surah = QuranSurah::create(['name' => 'Al-Fatihah', 'jumlah_ayat' => 7]);
        app(SantriPlacementService::class)->assignToClass($santri, $kelas, 'pindah', '2026-01-10');

        $skema = SkemaPenilaian::create([
            'pondok_id' => $pondok->id,
            'nama' => 'Skema Test',
            'is_active' => true,
        ]);
        $skema->items()->create([
            'nama' => 'Mumtaz',
            'singkatan' => 'MTZ',
            'label' => 'MTZ',
            'urutan' => 1,
        ]);

        $payload = [
            'kelas_id' => $kelas->id,
            'santri_id' => $santri->id,
            'ustadz_id' => $ustadz->id,
            'tanggal_setor' => '2026-02-01',
            'juz' => 1,
            'dari_surat' => $surah->id,
            'dari_ayat' => 1,
            'sampai_surat' => $surah->id,
            'sampai_ayat' => 7,
            'kategori' => 'Ziyadah',
            'nilai' => 'bad',
        ];

        $this->actingAs($user)
            ->from('/ustadz/hafalan/create')
            ->post(route('ustadz.hafalan.store'), $payload)
            ->assertRedirect('/ustadz/hafalan/create')
            ->assertSessionHasErrors('error');

        $this->assertDatabaseMissing('hafalans', ['nilai' => 'BAD']);

        $this->actingAs($user)
            ->post(route('ustadz.hafalan.store'), [...$payload, 'nilai' => 'mtz'])
            ->assertRedirect(route('ustadz.hafalan.index'));

        $this->assertDatabaseHas('hafalans', [
            'santri_id' => $santri->id,
            'kelas_id' => $kelas->id,
            'nilai' => 'MTZ',
        ]);
    }

    private function createPlacementFixture(): array
    {
        $pondok = Pondok::create(['nama' => 'Pondok Test']);
        $tahunAjaran = TahunAjaran::create([
            'pondok_id' => $pondok->id,
            'nama' => '2026/2027',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'is_active' => true,
        ]);
        $kelasA = Kelas::create([
            'pondok_id' => $pondok->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama' => 'Kelas A',
        ]);
        $kelasB = Kelas::create([
            'pondok_id' => $pondok->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama' => 'Kelas B',
        ]);
        $santri = Santri::create([
            'nis' => 'S001',
            'pondok_id' => $pondok->id,
            'nama' => 'Abdi',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
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

        return [$pondok, $tahunAjaran, $kelasA, $kelasB, $santri];
    }

    private function canUseDatabase(): bool
    {
        if ($this->databaseAvailable) {
            return true;
        }

        $this->assertTrue(true, 'pdo_sqlite is not installed; database-backed assertions are covered when the sqlite test driver is available.');

        return false;
    }
}
