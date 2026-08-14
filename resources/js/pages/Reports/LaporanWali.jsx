import { router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import RoleLayout from '../shared/RoleLayout';
import CommonFilterForm from './components/CommonFilterForm';
import LaporanNav from './components/LaporanNav';

const NAMA_BULAN = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

function cleanParams(filters) {
    const params = {};
    Object.keys(filters).forEach((key) => {
        const value = filters[key];
        if (value !== null && value !== undefined && value !== '') {
            params[key] = value;
        }
    });
    return params;
}

function bulanIni() {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
}

/**
 * Label periode laporan, misal "Desember 2025 - Juni 2026".
 * Periode selalu sepanjang `jumlahBulan` dan berakhir di bulan yang dipilih.
 */
function labelPeriode(bulanAkhir, jumlahBulan) {
    const cocok = /^(\d{4})-(\d{2})$/.exec(bulanAkhir || '');
    if (!cocok) return '-';

    const akhir = new Date(Number(cocok[1]), Number(cocok[2]) - 1, 1);
    const awal = new Date(akhir.getFullYear(), akhir.getMonth() - (jumlahBulan - 1), 1);

    const format = (d) => `${NAMA_BULAN[d.getMonth()]} ${d.getFullYear()}`;
    return `${format(awal)} - ${format(akhir)}`;
}

export default function LaporanWaliPage(props) {
    const {
        authRole,
        baseUrl,
        filters,
        rows = [],
        pondokOptions = [],
        requiresPondokSelection = false,
        tahunAjarans = [],
        kelasOptions = [],
        jumlahBulan = 7,
    } = props;

    const [localFilters, setLocalFilters] = useState({
        pondok_id: filters?.pondok_id ? String(filters.pondok_id) : '',
        tahun_ajaran_id: filters?.tahun_ajaran_id ? String(filters.tahun_ajaran_id) : '',
        kelas_id: filters?.kelas_id ? String(filters.kelas_id) : '',
        search: filters?.search ?? '',
    });

    const [bulanAkhir, setBulanAkhir] = useState(filters?.bulan_akhir || bulanIni());

    const canLoadData = useMemo(() => {
        if (!requiresPondokSelection) return true;
        return !!localFilters.pondok_id;
    }, [requiresPondokSelection, localFilters.pondok_id]);

    const periode = useMemo(() => labelPeriode(bulanAkhir, jumlahBulan), [bulanAkhir, jumlahBulan]);

    const submit = (e) => {
        e.preventDefault();
        router.get(`${baseUrl}/laporan/laporan-wali`, cleanParams({ ...localFilters, bulan_akhir: bulanAkhir }), {
            preserveState: true,
            replace: true,
        });
    };

    const buildPdfUrl = (nis) => {
        const query = new URLSearchParams(
            cleanParams({ pondok_id: localFilters.pondok_id, bulan_akhir: bulanAkhir })
        ).toString();
        const base = `${baseUrl}/laporan/laporan-wali/${nis}/pdf`;
        return query ? `${base}?${query}` : base;
    };

    return (
        <RoleLayout authRole={authRole} title="Laporan Perkembangan Santri">
            <div className="space-y-4">
                <LaporanNav baseUrl={baseUrl} current="laporan-wali" pondokId={localFilters.pondok_id} authRole={authRole} />

                <CommonFilterForm
                    filters={localFilters}
                    setFilters={setLocalFilters}
                    onSubmit={submit}
                    requiresPondokSelection={requiresPondokSelection}
                    pondokOptions={pondokOptions}
                    tahunAjarans={tahunAjarans}
                    kelasOptions={kelasOptions}
                />

                <div className="rounded-lg border bg-white p-4">
                    <div className="flex flex-wrap items-end gap-4">
                        <div>
                            <label htmlFor="bulan-akhir" className="mb-1 block text-sm text-gray-600">
                                Bulan terakhir periode
                            </label>
                            <input
                                id="bulan-akhir"
                                type="month"
                                value={bulanAkhir}
                                onChange={(e) => setBulanAkhir(e.target.value)}
                                className="rounded-md border px-3 py-2 text-sm"
                            />
                        </div>

                        <div className="text-sm text-gray-600">
                            <span className="block text-xs uppercase tracking-wide text-gray-400">
                                Periode laporan ({jumlahBulan} bulan)
                            </span>
                            <span className="font-semibold text-gray-800">{periode}</span>
                        </div>
                    </div>

                    <p className="mt-3 text-xs text-gray-500">
                        Template laporan memuat tepat {jumlahBulan} baris bulanan. Bulan tanpa setoran tetap
                        ditampilkan sebagai baris kosong.
                    </p>
                </div>

                {!canLoadData && (
                    <div className="rounded-md border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-700">
                        Pilih pondok terlebih dahulu untuk menampilkan data santri.
                    </div>
                )}

                {canLoadData && (
                    <div className="rounded-lg border bg-white p-4">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[880px]">
                                <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                    <tr>
                                        <th className="px-3 py-2">NIS</th>
                                        <th className="px-3 py-2">Nama</th>
                                        <th className="px-3 py-2">Kelas</th>
                                        <th className="px-3 py-2">Tahun Ajaran</th>
                                        <th className="px-3 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="text-sm">
                                    {rows.length === 0 && (
                                        <tr>
                                            <td className="px-3 py-4 text-center text-gray-500" colSpan={5}>
                                                Tidak ada santri untuk filter yang dipilih.
                                            </td>
                                        </tr>
                                    )}
                                    {rows.map((row) => (
                                        <tr
                                            key={
                                                row.placement_id ||
                                                `${row.santri_id}-${row.kelas_id}-${row.tahun_ajaran_id}`
                                            }
                                            className="border-b"
                                        >
                                            <td className="px-3 py-2">{row.nis || '-'}</td>
                                            <td className="px-3 py-2">{row.nama}</td>
                                            <td className="px-3 py-2">{row.kelas_nama || '-'}</td>
                                            <td className="px-3 py-2">{row.tahun_ajaran_nama || '-'}</td>
                                            <td className="px-3 py-2">
                                                {/* Unduhan berkas, jadi memakai <a> biasa, bukan Inertia <Link>. */}
                                                <a
                                                    id={`unduh-laporan-wali-${row.nis}`}
                                                    href={buildPdfUrl(row.nis)}
                                                    className="inline-block rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                                                >
                                                    Unduh PDF
                                                </a>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </RoleLayout>
    );
}
