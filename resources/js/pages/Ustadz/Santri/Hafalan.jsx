import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const SantriHafalan = ({ santri, hafalans = [] }) => {
    return (
        <Layout title={`Progress Hafalan - ${santri?.nama}`}>
            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800">Progress Hafalan</h1>
                        <p className="text-gray-600">
                            {santri?.nama} ({santri?.nis}) - {santri?.kelas}
                        </p>
                    </div>
                    <div className="flex space-x-3">
                        <a
                            href={route('ustadz.santri.hafalan.pdf', santri?.nis)}
                            className="flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </a>
                        <Link
                            href={route('ustadz.santri.index')}
                            className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Kembali
                        </Link>
                    </div>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div className="text-2xl font-bold text-blue-700">{hafalans.length}</div>
                        <div className="text-sm text-blue-600">Total Setoran</div>
                    </div>
                    <div className="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div className="text-2xl font-bold text-green-700">
                            {hafalans.filter(h => h.nilai === 'A' || h.nilai === 'B' || h.nilai?.toLowerCase() === 'baik').length}
                        </div>
                        <div className="text-sm text-green-600">Nilai Baik</div>
                    </div>
                    <div className="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div className="text-2xl font-bold text-purple-700">
                            {[...new Set(hafalans.map(h => h.juz))].length}
                        </div>
                        <div className="text-sm text-purple-600">Juz Disetor</div>
                    </div>
                </div>

                {/* Hafalan Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Juz</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dari Surat</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sampai Surat</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ustadz</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {hafalans.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="px-4 py-8 text-center text-gray-500">
                                            Belum ada data setoran hafalan
                                        </td>
                                    </tr>
                                ) : (
                                    hafalans.map((h, idx) => (
                                        <tr key={h.id} className={idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                            <td className="px-4 py-3 text-sm text-gray-900">{idx + 1}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{h.tanggal_setor}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{h.juz}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{h.dari_surat} : {h.dari_ayat}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{h.sampai_surat} : {h.sampai_ayat}</td>
                                            <td className="px-4 py-3 text-sm">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                                    h.kategori === 'Ziyadah' ? 'bg-blue-100 text-blue-800' :
                                                    h.kategori === 'murojaah' ? 'bg-purple-100 text-purple-800' :
                                                    'bg-gray-100 text-gray-800'
                                                }`}>
                                                    {h.kategori}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-sm">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                                    (h.nilai === 'A' || h.nilai === 'B' || h.nilai?.toLowerCase() === 'baik') ? 'bg-green-100 text-green-800' :
                                                    (h.nilai === 'C' || h.nilai?.toLowerCase() === 'cukup') ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-red-100 text-red-800'
                                                }`}>
                                                    {h.nilai}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{h.ustadz}</td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default SantriHafalan;
