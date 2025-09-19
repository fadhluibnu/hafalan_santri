import { Link } from '@inertiajs/react';
import Layout from '../../components/Layout';

const KelasShow = ({ kelas, santri: santriProp = [] }) => {
    // Dummy detail
    const data = kelas || {
        id: 1,
        nama: 'Kelas Tahfidz A',
        tingkat: 'Juz 30',
        waliKelas: 'Ustadz Rahman',
        kapasitas: 25,
        keterangan: 'Keterangan kelas...',
    };

    // Dummy santri list if none provided via props
    const santriList =
        Array.isArray(santriProp) && santriProp.length > 0
            ? santriProp
            : [
                  { id: 1, nis: 'S001', nama: 'Ahmad Zaki', jenis_kelamin: 'L', juzTerakhir: 'Juz 30' },
                  { id: 2, nis: 'S002', nama: 'Budi Santoso', jenis_kelamin: 'L', juzTerakhir: 'Juz 29' },
                  { id: 3, nis: 'S003', nama: 'Citra Ayu', jenis_kelamin: 'P', juzTerakhir: 'Juz 28' },
              ];

    const labelJK = (v) => (v === 'L' ? 'Laki-laki' : v === 'P' ? 'Perempuan' : '-');

    return (
        <Layout title={`Detail Kelas - ${data.nama}`}>
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-6 shadow-md">
                    <dl className="divide-y divide-gray-200">
                        <div className="grid grid-cols-3 gap-4 py-3">
                            <dt className="text-sm font-medium text-gray-500">Nama Kelas</dt>
                            <dd className="col-span-2 text-sm text-gray-900">{data.nama}</dd>
                        </div>
                        <div className="grid grid-cols-3 gap-4 py-3">
                            <dt className="text-sm font-medium text-gray-500">Tingkat</dt>
                            <dd className="col-span-2 text-sm text-gray-900">{data.tingkat}</dd>
                        </div>
                        <div className="grid grid-cols-3 gap-4 py-3">
                            <dt className="text-sm font-medium text-gray-500">Wali Kelas</dt>
                            <dd className="col-span-2 text-sm text-gray-900">{data.waliKelas}</dd>
                        </div>
                        <div className="grid grid-cols-3 gap-4 py-3">
                            <dt className="text-sm font-medium text-gray-500">Kapasitas</dt>
                            <dd className="col-span-2 text-sm text-gray-900">{data.kapasitas} santri</dd>
                        </div>
                        <div className="grid grid-cols-3 gap-4 py-3">
                            <dt className="text-sm font-medium text-gray-500">Keterangan</dt>
                            <dd className="col-span-2 text-sm text-gray-900">{data.keterangan || '-'}</dd>
                        </div>
                    </dl>
                </div>

                {/* Santri list in this class */}
                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="mb-3 flex items-center justify-between">
                        <h3 className="text-base font-semibold text-gray-700">Santri di Kelas ini</h3>
                        <span className="text-sm text-gray-500">Total: {santriList.length}</span>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm text-gray-700">
                            <thead className="bg-gray-50 text-xs text-gray-600 uppercase">
                                <tr>
                                    <th className="px-4 py-3 text-left">No</th>
                                    <th className="px-4 py-3 text-left">NIS</th>
                                    <th className="px-4 py-3 text-left">Nama</th>
                                    <th className="px-4 py-3 text-left">Jenis Kelamin</th>
                                    <th className="px-4 py-3 text-left">Juz Terakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                {santriList.map((s, idx) => (
                                    <tr key={s.id} className="border-b">
                                        <td className="px-4 py-2">{idx + 1}</td>
                                        <td className="px-4 py-2">{s.nis || '-'}</td>
                                        <td className="px-4 py-2">{s.nama || '-'}</td>
                                        <td className="px-4 py-2">{labelJK(s.jenis_kelamin)}</td>
                                        <td className="px-4 py-2">{s.juzTerakhir || '-'}</td>
                                    </tr>
                                ))}
                                {santriList.length === 0 && (
                                    <tr>
                                        <td className="px-4 py-6 text-center text-gray-500" colSpan={5}>
                                            Belum ada santri dalam kelas ini
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="flex items-center justify-end space-x-3">
                    <Link
                        href={`/admin-cabang/struktur/kelas/${data.id}/santri`}
                        className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700"
                    >
                        Tempatkan Santri
                    </Link>
                    <Link
                        href={`/admin-cabang/struktur/kelas/${data.id}/edit`}
                        className="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-yellow-600"
                    >
                        Edit
                    </Link>
                    <Link
                        href="/admin-cabang/struktur/kelas"
                        className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </Layout>
    );
};

export default KelasShow;
