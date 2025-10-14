import { useEffect, useMemo, useState } from 'react';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from './components/Layout';

const bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

export default function Laporan(props) {
    const pondok = props.pondok ?? null;
    const classes = props.classes || [];
    const gurus = props.gurus || [];
    const santrisServer = props.santris || [];
    const results = props.results || [];
    const initial = props.initialFilters || {};

    const [filters, setFilters] = useState({
        kelas_id: initial.kelas_id ?? '',
        guru_id: initial.guru_id ?? '',
        bulan: initial.bulan ?? String(new Date().getMonth() + 1).padStart(2, '0'),
        tahun: initial.tahun ?? String(new Date().getFullYear()),
        hariAktif: initial.hariAktif ?? 24,
    });

    // sync when initialFilters from server change (e.g. navigation)
    useEffect(() => {
        setFilters({
            kelas_id: initial.kelas_id ?? '',
            guru_id: initial.guru_id ?? '',
            bulan: initial.bulan ?? String(new Date().getMonth() + 1).padStart(2, '0'),
            tahun: initial.tahun ?? String(new Date().getFullYear()),
            hariAktif: initial.hariAktif ?? 24,
        });
    }, [props.initialFilters]);

    const handleFilterChange = (name, value) => {
        const next = { ...filters, [name]: value };
        setFilters(next);
        // Fetch server-rendered results; replace URL, preserve pagination state
        router.get(route('guru.laporan'), next, { preserveState: true, replace: true });
    };

    const bulanLabel = useMemo(() => {
        const idx = Math.max(1, Math.min(12, parseInt(filters.bulan, 10))) - 1;
        return bulanNames[idx] ?? '-';
    }, [filters.bulan]);

    const totalJN = (bl, bs) => {
        const n = (Number(bs) || 0) - (Number(bl) || 0);
        return n < 0 ? 0 : n;
    };

    return (
        <Layout title="Laporan Hafalan">
            <div className="p-4 md:p-6 lg:p-8">
                {/* Controls (hidden when printing) */}
                <div className="mb-4 flex flex-wrap items-end gap-3 print:hidden">
                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Pondok</label>
                        <div className="input input-bordered h-9">{pondok?.nama ?? '-'}</div>
                    </div>

                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Penguji (Guru)</label>
                        <select className="select select-bordered h-9" value={filters.guru_id} onChange={(e) => handleFilterChange('guru_id', e.target.value)}>
                            <option value="">-- Semua Guru --</option>
                            {gurus.map((g) => (<option key={g.id} value={g.id}>{g.nama}</option>))}
                        </select>
                    </div>

                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Kelas</label>
                        <select className="select select-bordered h-9" value={filters.kelas_id} onChange={(e) => handleFilterChange('kelas_id', e.target.value)}>
                            <option value="">-- Pilih Kelas --</option>
                            {classes.map((c) => (<option key={c.id} value={c.id}>{c.nama}</option>))}
                        </select>
                    </div>

                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Bulan</label>
                        <select value={filters.bulan} onChange={(e) => handleFilterChange('bulan', e.target.value)} className="select select-bordered h-9">
                            {bulanNames.map((b, i) => (<option key={b} value={String(i + 1).padStart(2, '0')}>{b}</option>))}
                        </select>
                    </div>

                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Tahun</label>
                        <input type="number" value={filters.tahun} onChange={(e) => handleFilterChange('tahun', e.target.value)} className="input input-bordered h-9 w-24" />
                    </div>

                    <button onClick={() => window.print()} className="btn btn-primary ml-auto">Export PDF</button>
                </div>

                {/* Printable canvas */}
                <div id="report-printable" className="bg-white text-black">
                    <style>{`
                            /* print: only the #report-printable visible, hide everything else.
                               additionally hide any element marked .no-print inside the report. */
                            @media print {
                                @page { size: A4 landscape; margin: 10mm; }
                                /* hide everything */
                                body * { visibility: hidden !important; }
                                /* show the report container and its children */
                                #report-printable, #report-printable * { visibility: visible !important; }
                                /* explicitly hide no-print elements inside the report */
                                #report-printable .no-print, #report-printable .no-print * { display: none !important; visibility: hidden !important; }
                                /* position the report at the top-left when printing */
                                #report-printable { position: absolute; left: 0; top: 0; width: 100%; }
                            }
                        `}</style>

                    <div className="flex">
                        {/* Left legend column */}
                        <div className="w-[120px] border-r border-black pr-4 no-print">
                            <div className="text-xs leading-tight">
                                <div className="mb-2 font-semibold">Keterangan:</div>
                                <div>BL: Bulan Lalu</div>
                                <div>BS: Bulan Sekarang</div>
                                <div>JN: Juz Nambah</div>
                                {/* TAQO/MLH/Nilai dihapus */}
                            </div>
                        </div>

                        {/* Main content */}
                        <div className="flex-1 pl-4">
                            {/* Header */}
                            <div className="mb-2 text-center">
                                <div className="text-base font-bold">LAPORAN SETORAN HAFALAN SANTRI</div>
                                <div className="text-sm">{pondok?.nama ?? '-'}</div>
                                <div className="flex justify-center gap-3 mt-1">
                                    Penguji: {gurus.find(g => g.id == filters.guru_id)?.nama ?? '-'} | Kelas: {classes.find(c => c.id == filters.kelas_id)?.nama ?? '-'} | Bulan: {bulanLabel} {filters.tahun}
                                </div>
                            </div>

                            {/* Table */}
                            <div className="overflow-x-auto">
                                <table className="w-full border border-black text-xs">
                                    <thead>
                                        <tr>
                                            <th className="w-8 border border-black">NO</th>
                                            <th className="border border-black px-1 text-left">NAMA SANTRI</th>
                                            <th className="border border-black px-1 text-left">NIS</th>
                                            <th className="border border-black px-1 text-left">KELAS</th>
                                            <th className="w-12 border border-black">BL</th>
                                            <th className="w-12 border border-black">BS</th>
                                            <th className="w-12 border border-black">JN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {results.length === 0 && (
                                            <tr>
                                                <td className="border border-black py-3 text-center" colSpan={7}>Tidak ada data</td>
                                            </tr>
                                        )}
                                        {results.map((r, idx) => (
                                            <tr key={r.santri_id}>
                                                <td className="border border-black text-center">{idx + 1}</td>
                                                <td className="border border-black px-1">{r.nama}</td>
                                                <td className="border border-black px-1">{r.nis ?? '-'}</td>
                                                <td className="border border-black px-1">{classes.find(c => c.id == filters.kelas_id)?.nama ?? '-'}</td>
                                                <td className="border border-black text-center">{r.bl}</td>
                                                <td className="border border-black text-center">{r.bs}</td>
                                                <td className="border border-black text-center">{r.jn}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Footer note */}
                            <div className="mt-2 text-[10px] italic">
                                Catatan: BL = jumlah juz pada bulan sebelumnya, BS = jumlah juz pada bulan terpilih, JN = peningkatan juz (BS - BL). Perhitungan menggunakan mapping halaman/juz dari alquran.json.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
//     );
// }
