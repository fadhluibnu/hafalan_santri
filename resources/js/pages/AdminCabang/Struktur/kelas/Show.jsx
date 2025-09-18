import { Link } from '@inertiajs/react';
import Layout from '../../components/Layout';

const KelasShow = ({ kelas }) => {
    // Dummy detail
    const data = kelas || {
        id: 1,
        nama: 'Kelas Tahfidz A',
        tingkat: 'Juz 30',
        waliKelas: 'Ustadz Rahman',
        kapasitas: 25,
        keterangan: 'Keterangan kelas...',
    };

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

                <div className="flex items-center justify-end space-x-3">
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
