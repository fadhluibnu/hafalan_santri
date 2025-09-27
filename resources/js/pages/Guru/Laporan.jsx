import { useMemo, useState } from 'react';
import Layout from './components/Layout';

// Contract
// Inputs: optional initial filters via props, and optional data (santri list + hafalan summaries)
// Output: printable monthly report per class with A4 landscape styling
// Error modes: with no data, shows an empty table; print/export still works

const bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

function classNames(...arr) {
    return arr.filter(Boolean).join(' ');
}

const defaultFilters = () => {
    const now = new Date();
    return {
        lembaga: 'PONDOK TAHFIDZ',
        penguji: '',
        kelas: '',
        bulan: String(now.getMonth() + 1).padStart(2, '0'),
        tahun: String(now.getFullYear()),
        hariAktif: 24,
    };
};

// Dummy rows for preview; replace with live data via props when backend is ready
const dummyRows = [
    {
        nama: 'Ahmad Fauzi',
        daerah: 'Cirebon',
        tingkat: 'MTs',
        kelas: '7A',
        bl: 3,
        bs: 5,
        taqo: { SB: 3, ST: 4, SD: 4, PS: 3, TQ: 4 },
        nilai: 'A',
    },
    {
        nama: 'Budi Santoso',
        daerah: 'Brebes',
        tingkat: 'SMP',
        kelas: '8B',
        bl: 2,
        bs: 3,
        taqo: { SB: 2, ST: 3, SD: 3, PS: 2, TQ: 3 },
        nilai: 'B',
    },
    {
        nama: 'Citra Ayu',
        daerah: 'Tegal',
        tingkat: 'MA',
        kelas: '10C',
        bl: 6,
        bs: 7,
        taqo: { SB: 4, ST: 4, SD: 4, PS: 4, TQ: 5 },
        nilai: 'A',
    },
];

export default function Laporan(props) {
    const [filters, setFilters] = useState({ ...defaultFilters(), ...(props.initialFilters || {}) });
    const rows = props.rows || dummyRows;

    const bulanLabel = useMemo(() => {
        const idx = Math.max(1, Math.min(12, parseInt(filters.bulan, 10))) - 1;
        return bulanNames[idx];
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
                        <label className="text-sm text-gray-600">Lembaga</label>
                        <input
                            value={filters.lembaga}
                            onChange={(e) => setFilters({ ...filters, lembaga: e.target.value })}
                            className="input input-bordered h-9"
                            placeholder="Nama Lembaga"
                        />
                    </div>
                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Penguji (Guru)</label>
                        <input
                            value={filters.penguji}
                            onChange={(e) => setFilters({ ...filters, penguji: e.target.value })}
                            className="input input-bordered h-9"
                            placeholder="Nama Guru"
                        />
                    </div>
                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Kelas</label>
                        <input
                            value={filters.kelas}
                            onChange={(e) => setFilters({ ...filters, kelas: e.target.value })}
                            className="input input-bordered h-9"
                            placeholder="Mis. 7A"
                        />
                    </div>
                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Bulan</label>
                        <select
                            value={filters.bulan}
                            onChange={(e) => setFilters({ ...filters, bulan: e.target.value })}
                            className="select select-bordered h-9"
                        >
                            {bulanNames.map((b, i) => (
                                <option key={b} value={String(i + 1).padStart(2, '0')}>
                                    {b}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Tahun</label>
                        <input
                            type="number"
                            value={filters.tahun}
                            onChange={(e) => setFilters({ ...filters, tahun: e.target.value })}
                            className="input input-bordered h-9 w-24"
                        />
                    </div>
                    <div className="flex flex-col">
                        <label className="text-sm text-gray-600">Hari Aktif</label>
                        <input
                            type="number"
                            value={filters.hariAktif}
                            onChange={(e) => setFilters({ ...filters, hariAktif: e.target.value })}
                            className="input input-bordered h-9 w-24"
                        />
                    </div>
                    <button onClick={() => window.print()} className="btn btn-primary ml-auto">
                        Export PDF
                    </button>
                </div>

                {/* Printable canvas */}
                <div className="bg-white text-black print:bg-white print:text-black">
                    <style>{`
                        @media print {
                            @page { size: A4 landscape; margin: 10mm; }
                            .no-print { display: none !important; }
                            .print\\:block { display: block !important; }
                            .print\\:text-xs { font-size: 10px; }
                            .print\\:leading-tight { line-height: 1.1; }
                            .print\\:border { border: 1px solid #000; }
                            .print\\:border-l-0 { border-left: 0; }
                            .print\\:border-r-0 { border-right: 0; }
                            .print\\:border-t-0 { border-top: 0; }
                            .print\\:border-b-0 { border-bottom: 0; }
                        }
                    `}</style>

                    <div className="flex">
                        {/* Left legend column */}
                        <div className="w-[120px] border-r border-black pr-4">
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
                                <div className="text-sm">{filters.lembaga}</div>
                                <div className="flex">
                                    Penguji: {filters.penguji || '-'} | Kelas: {filters.kelas || '-'} | Bulan: {bulanLabel} {filters.tahun} | Hari
                                    Aktif: {filters.hariAktif}
                                </div>
                            </div>

                            {/* Table */}
                            <div className="overflow-x-auto">
                                <table className="w-full border border-black text-xs">
                                    <thead>
                                        <tr>
                                            <th className="w-8 border border-black">NO</th>
                                            <th className="border border-black px-1 text-left">NAMA SANTRI</th>
                                            <th className="border border-black px-1 text-left">DAERAH</th>
                                            <th className="border border-black px-1 text-left">TINGKAT</th>
                                            <th className="border border-black px-1 text-left">KELAS</th>
                                            <th className="w-10 border border-black">BL</th>
                                            <th className="w-10 border border-black">BS</th>
                                            <th className="w-10 border border-black">JN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.length === 0 && (
                                            <tr>
                                                <td className="border border-black py-3 text-center" colSpan={8}>
                                                    Tidak ada data
                                                </td>
                                            </tr>
                                        )}
                                        {rows.map((r, idx) => {
                                            const jn = totalJN(r.bl, r.bs);
                                            return (
                                                <tr key={idx}>
                                                    <td className="border border-black text-center">{idx + 1}</td>
                                                    <td className="border border-black px-1">{r.nama}</td>
                                                    <td className="border border-black px-1">{r.daerah}</td>
                                                    <td className="border border-black px-1">{r.tingkat}</td>
                                                    <td className="border border-black px-1">{r.kelas}</td>
                                                    <td className="border border-black text-center">{r.bl}</td>
                                                    <td className="border border-black text-center">{r.bs}</td>
                                                    <td className="border border-black text-center">{jn}</td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Footer note */}
                            <div className="mt-2 text-[10px] italic">
                                Catatan: Silakan periksa kembali data sebelum dicetak. Format disesuaikan dengan contoh foto.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
