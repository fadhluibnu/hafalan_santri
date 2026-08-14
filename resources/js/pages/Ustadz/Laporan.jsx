import { useEffect, useMemo, useState } from 'react';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import Layout from './components/Layout';

const bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

export default function Laporan(props) {
    const pondok = props.pondok ?? null;
    const classes = props.classes || [];
    const ustadzs = props.ustadzs || [];
    const santrisServer = props.santris || [];
    const results = props.results || [];
    const initial = props.initialFilters || {};

    const [filters, setFilters] = useState({
        kelas_id: initial.kelas_id ?? '',
        ustadz_id: initial.ustadz_id ?? '',
        bulan: initial.bulan ?? String(new Date().getMonth() + 1).padStart(2, '0'),
        tahun: initial.tahun ?? String(new Date().getFullYear()),
        hariAktif: initial.hariAktif ?? 24,
    });

    // sync when initialFilters from server change (e.g. navigation)
    useEffect(() => {
        setFilters({
            kelas_id: initial.kelas_id ?? '',
            ustadz_id: initial.ustadz_id ?? '',
            bulan: initial.bulan ?? String(new Date().getMonth() + 1).padStart(2, '0'),
            tahun: initial.tahun ?? String(new Date().getFullYear()),
            hariAktif: initial.hariAktif ?? 24,
        });
    }, [props.initialFilters]);

    const handleFilterChange = (name, value) => {
        const next = { ...filters, [name]: value };
        setFilters(next);
        // Fetch server-rendered results; replace URL, preserve pagination state
        router.get(route('ustadz.laporan'), next, { preserveState: true, replace: true });
    };

    const bulanLabel = useMemo(() => {
        const idx = Math.max(1, Math.min(12, parseInt(filters.bulan, 10))) - 1;
        return bulanNames[idx] ?? '-';
    }, [filters.bulan]);

    const totalJN = (bl, bs) => {
        const n = (Number(bs) || 0) - (Number(bl) || 0);
        return n < 0 ? 0 : n;
    };

    // Function to export PDF directly
    const exportToPDF = () => {
        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4'
        });

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        
        // Header
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.text('LAPORAN SETORAN HAFALAN SANTRI', pageWidth / 2, 15, { align: 'center' });
        
        // Sub-header: Nama Pondok
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text(pondok?.nama ?? '-', pageWidth / 2, 22, { align: 'center' });
        
        // Info line
        const ustadzNama = ustadzs.find(g => g.id == filters.ustadz_id)?.nama ?? '-';
        const kelasNama = classes.find(c => c.id == filters.kelas_id)?.nama ?? '-';
        const infoText = `Penguji: ${ustadzNama} | Kelas: ${kelasNama} | Bulan: ${bulanLabel} ${filters.tahun}`;
        doc.setFontSize(10);
        doc.text(infoText, pageWidth / 2, 29, { align: 'center' });

        // Table data
        const tableData = results.map((r, idx) => [
            idx + 1,
            r.nama,
            r.nis ?? '-',
            kelasNama,
            r.bl,
            r.bs,
            r.jn
        ]);

        // Generate table
        autoTable(doc, {
            startY: 35,
            head: [['NO', 'NAMA SANTRI', 'NIS', 'KELAS', 'BL', 'BS', 'Tambahan Juz']],
            body: tableData.length > 0 ? tableData : [['', 'Tidak ada data', '', '', '', '', '']],
            theme: 'grid',
            headStyles: {
                fillColor: [34, 197, 94], // green-500
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 15 },
                1: { halign: 'left', cellWidth: 70 },
                2: { halign: 'left', cellWidth: 40 },
                3: { halign: 'left', cellWidth: 40 },
                4: { halign: 'center', cellWidth: 25 },
                5: { halign: 'center', cellWidth: 25 },
                6: { halign: 'center', cellWidth: 25 }
            },
            styles: {
                font: 'helvetica',
                fontSize: 10,
                cellPadding: 3
            },
            alternateRowStyles: {
                fillColor: [240, 253, 244] // green-50
            }
        });

        // Footer note
        const finalY = doc.lastAutoTable.finalY || 35;
        doc.setFontSize(8);
        doc.setFont('helvetica', 'italic');
        doc.text(
            'Catatan: BL = jumlah juz pada bulan sebelumnya, BS = jumlah juz pada bulan terpilih, Tambahan Juz = peningkatan juz (BS - BL).',
            14,
            finalY + 10
        );

        // Keterangan singkatan di footer
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text('Keterangan:', 14, finalY + 18);
        doc.text('BL: Bulan Lalu | BS: Bulan Sekarang | Tambahan Juz: Peningkatan Juz', 14, finalY + 23);

        // Generate filename
        const fileName = `Laporan_Hafalan_${kelasNama}_${bulanLabel}_${filters.tahun}.pdf`;
        
        // Download the PDF
        doc.save(fileName);
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
                        <label className="text-sm text-gray-600">Penguji (ustadz)</label>
                        <select className="select select-bordered h-9" value={filters.ustadz_id} onChange={(e) => handleFilterChange('ustadz_id', e.target.value)}>
                            <option value="">-- Semua ustadz --</option>
                            {ustadzs.map((g) => (<option key={g.id} value={g.id}>{g.nama}</option>))}
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

                    {/* Export PDF Button - More visible */}
                    <button 
                        onClick={exportToPDF} 
                        className="ml-auto flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-white font-semibold shadow-lg hover:bg-green-700 hover:shadow-xl transition-all duration-200 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </button>
                </div>

                {/* Printable canvas */}
                <div id="report-printable" className="bg-white text-black">
                    <div className="flex">
                        {/* Left legend column */}
                        <div className="w-[120px] border-r border-gray-300 pr-4">
                            <div className="text-xs leading-tight">
                                <div className="mb-2 font-semibold">Keterangan:</div>
                                <div>BL: Bulan Lalu</div>
                                <div>BS: Bulan Sekarang</div>
                                <div>Tambahan Juz</div>
                            </div>
                        </div>

                        {/* Main content */}
                        <div className="flex-1 pl-4">
                            {/* Header */}
                            <div className="mb-2 text-center">
                                <div className="text-base font-bold">LAPORAN SETORAN HAFALAN SANTRI</div>
                                <div className="text-sm">{pondok?.nama ?? '-'}</div>
                                <div className="flex justify-center gap-3 mt-1">
                                    Penguji: {ustadzs.find(g => g.id == filters.ustadz_id)?.nama ?? '-'} | Kelas: {classes.find(c => c.id == filters.kelas_id)?.nama ?? '-'} | Bulan: {bulanLabel} {filters.tahun}
                                </div>
                            </div>

                            {/* Table */}
                            <div className="overflow-x-auto">
                                <table className="w-full border border-gray-300 text-xs">
                                    <thead>
                                        <tr className="bg-green-500 text-white">
                                            <th className="w-8 border border-gray-300 py-2">NO</th>
                                            <th className="border border-gray-300 px-2 py-2 text-left">NAMA SANTRI</th>
                                            <th className="border border-gray-300 px-2 py-2 text-left">NIS</th>
                                            <th className="border border-gray-300 px-2 py-2 text-left">KELAS</th>
                                            <th className="w-12 border border-gray-300 py-2">BL</th>
                                            <th className="w-12 border border-gray-300 py-2">BS</th>
                                            <th className="w-12 border border-gray-300 py-2">Tambahan Juz</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {results.length === 0 && (
                                            <tr>
                                                <td className="border border-gray-300 py-3 text-center" colSpan={7}>Tidak ada data</td>
                                            </tr>
                                        )}
                                        {results.map((r, idx) => (
                                            <tr key={r.santri_id} className={idx % 2 === 0 ? 'bg-white' : 'bg-green-50'}>
                                                <td className="border border-gray-300 text-center py-1">{idx + 1}</td>
                                                <td className="border border-gray-300 px-2 py-1">{r.nama}</td>
                                                <td className="border border-gray-300 px-2 py-1">{r.nis ?? '-'}</td>
                                                <td className="border border-gray-300 px-2 py-1">{classes.find(c => c.id == filters.kelas_id)?.nama ?? '-'}</td>
                                                <td className="border border-gray-300 text-center py-1">{r.bl}</td>
                                                <td className="border border-gray-300 text-center py-1">{r.bs}</td>
                                                <td className="border border-gray-300 text-center py-1">{r.jn}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Footer note */}
                            <div className="mt-2 text-[10px] italic text-gray-600">
                                Catatan: BL = jumlah juz pada bulan sebelumnya, BS = jumlah juz pada bulan terpilih, Tambahan Juz = peningkatan juz (BS - BL). Perhitungan menggunakan mapping halaman/juz dari alquran.json.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}


