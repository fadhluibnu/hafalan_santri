import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import RoleLayout from '../shared/RoleLayout';

export default function RaportPreview({ authRole, baseUrl, santri, placement, hafalans, ujianNilais, pondokNama, filters }) {
    const [keterangan, setKeterangan] = useState('');

    const formatNilaiUjian = (nilaiLabel, snapshot, nilaiAngka) => {
        const label = String(nilaiLabel || '').trim().toUpperCase();
        if (label) {
            const items = snapshot?.items || snapshot?.labels?.map(l => ({ singkatan: l, nama: l })) || [];
            const found = items.find(i => String(i.singkatan || '').trim().toUpperCase() === label);
            return found && found.nama ? `${label} - ${found.nama}` : label;
        }
        if (nilaiAngka !== null && nilaiAngka !== undefined && nilaiAngka !== '') {
            return String(nilaiAngka);
        }
        return '-';
    };

    /**
     * Query string bersama untuk tautan yang keluar dari halaman ini.
     *
     * `pondok_id` wajib ikut: untuk super admin, konteks pondok hanya dibawa
     * lewat query string, sehingga tanpa itu endpoint PDF menolak dengan 422.
     */
    const buildFilterQuery = () => {
        const query = new URLSearchParams();
        if (filters?.pondok_id) query.append('pondok_id', filters.pondok_id);
        if (filters?.tahun_ajaran_id) query.append('tahun_ajaran_id', filters.tahun_ajaran_id);
        if (filters?.kelas_id) query.append('kelas_id', filters.kelas_id);
        return query;
    };

    const buildPdfUrl = () => {
        const query = buildFilterQuery();
        if (keterangan) query.append('keterangan', keterangan);

        const q = query.toString();
        return `${baseUrl}/laporan/raport/${santri.nis}/pdf${q ? `?${q}` : ''}`;
    };

    // Membawa filter saat kembali, supaya super admin tidak mendarat di daftar
    // kosong dan harus memilih pondok dari awal.
    const buildBackUrl = () => {
        const q = buildFilterQuery().toString();
        return `${baseUrl}/laporan/raport${q ? `?${q}` : ''}`;
    };

    return (
        <RoleLayout authRole={authRole} title="Preview Raport">
            <Head title={`Preview Raport - ${santri.nama}`} />

            <div className="mx-auto max-w-4xl space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800">Preview Raport</h1>
                        <p className="text-sm text-gray-500">Pratinjau sebelum dicetak menjadi PDF.</p>
                    </div>
                    <div className="flex gap-3">
                        <Link
                            href={buildBackUrl()}
                            className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Kembali
                        </Link>
                        <a
                            href={buildPdfUrl()}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
                        >
                            <svg className="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clipRule="evenodd" />
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </div>

                {/* Form Input Keterangan */}
                <div className="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 className="mb-4 text-lg font-semibold text-gray-800">Keterangan Tambahan</h2>
                    <p className="mb-2 text-sm text-gray-600">Catatan dari Wali Kelas atau Mudir yang akan ditampilkan di bagian bawah raport.</p>
                    <textarea
                        value={keterangan}
                        onChange={(e) => setKeterangan(e.target.value)}
                        placeholder="Contoh: Terus tingkatkan hafalan dan semangat muraja'ah..."
                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        rows={3}
                    />
                </div>

                {/* Paper Preview */}
                <div className="overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-200">
                    <div className="p-10">
                        {/* Header */}
                        <div className="mb-6 border-b-2 border-green-600 pb-3 text-center">
                            <h2 className="text-xl font-bold text-green-600 uppercase">Raport Santri</h2>
                            <p className="mt-1 text-sm text-gray-600">{pondokNama}</p>
                        </div>

                        {/* Identitas */}
                        <div className="mb-2 mt-4 bg-green-600 px-2 py-1 text-xs font-bold text-white">Identitas</div>
                        <table className="w-full text-xs">
                            <tbody>
                                <tr><td className="w-1/3 py-1 font-semibold text-gray-700">NIS</td><td>{santri.nis}</td></tr>
                                <tr><td className="py-1 font-semibold text-gray-700">Nama</td><td>{santri.nama}</td></tr>
                                <tr><td className="py-1 font-semibold text-gray-700">Kelas</td><td>{placement?.kelas?.nama || '-'}</td></tr>
                                <tr><td className="py-1 font-semibold text-gray-700">Tahun Ajaran</td><td>{placement?.tahun_ajaran?.nama || '-'}</td></tr>
                            </tbody>
                        </table>

                        {/* Hafalan */}
                        <div className="mb-2 mt-6 bg-green-600 px-2 py-1 text-xs font-bold text-white">Ringkasan Hafalan</div>
                        <table className="w-full border-collapse border border-gray-200 text-[10px]">
                            <thead>
                                <tr className="bg-green-50 text-gray-800">
                                    <th className="border border-gray-200 p-1 text-left font-bold">No</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Tanggal</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Range Ayat</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Kategori</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                {hafalans?.length > 0 ? hafalans.map((h, i) => (
                                    <tr key={h.id || i} className="even:bg-gray-50 text-gray-700">
                                        <td className="border border-gray-200 p-1">{i + 1}</td>
                                        <td className="border border-gray-200 p-1">{h.tanggal_setor ? new Date(h.tanggal_setor).toISOString().split('T')[0] : '-'}</td>
                                        <td className="border border-gray-200 p-1">
                                            {h.dari_surah?.name || '-'}:{h.dari_ayat} - {h.sampai_surah?.name || '-'}:{h.sampai_ayat}
                                        </td>
                                        <td className="border border-gray-200 p-1">{h.kategori}</td>
                                        <td className="border border-gray-200 p-1">{h.nilai}</td>
                                    </tr>
                                )) : (
                                    <tr><td colSpan={5} className="border border-gray-200 p-2 text-center text-gray-500">Belum ada data hafalan pada periode ini.</td></tr>
                                )}
                            </tbody>
                        </table>

                        {/* Ujian */}
                        <div className="mb-2 mt-6 bg-green-600 px-2 py-1 text-xs font-bold text-white">Nilai Ujian</div>
                        <table className="w-full border-collapse border border-gray-200 text-[10px]">
                            <thead>
                                <tr className="bg-green-50 text-gray-800">
                                    <th className="border border-gray-200 p-1 text-left font-bold">No</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Nama Ujian</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Tanggal</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Nilai</th>
                                    <th className="border border-gray-200 p-1 text-left font-bold">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {ujianNilais?.length > 0 ? ujianNilais.map((u, i) => (
                                    <tr key={u.id || i} className="even:bg-gray-50 text-gray-700">
                                        <td className="border border-gray-200 p-1">{i + 1}</td>
                                        <td className="border border-gray-200 p-1">{u.ujian?.nama || '-'}</td>
                                        <td className="border border-gray-200 p-1">{u.ujian?.tanggal_ujian ? new Date(u.ujian.tanggal_ujian).toISOString().split('T')[0] : '-'}</td>
                                        <td className="border border-gray-200 p-1">{formatNilaiUjian(u.nilai_label, u.ujian?.skema_snapshot, u.nilai_angka)}</td>
                                        <td className="border border-gray-200 p-1">{u.catatan || '-'}</td>
                                    </tr>
                                )) : (
                                    <tr><td colSpan={5} className="border border-gray-200 p-2 text-center text-gray-500">Belum ada data ujian pada periode ini.</td></tr>
                                )}
                            </tbody>
                        </table>

                        {/* Keterangan Preview */}
                        {keterangan && (
                            <div className="mt-6">
                                <div className="mb-2 mt-6 bg-green-600 px-2 py-1 text-xs font-bold text-white">Keterangan Tambahan</div>
                                <p className="text-xs text-gray-800 whitespace-pre-wrap rounded border border-gray-200 bg-gray-50 p-3">{keterangan}</p>
                            </div>
                        )}
                        
                        <div className="mt-10 border-t pt-4 text-center text-[10px] text-gray-400">
                            Dicetak pada {new Date().toISOString().replace('T', ' ').substring(0, 19)}
                        </div>
                    </div>
                </div>
            </div>
        </RoleLayout>
    );
}
