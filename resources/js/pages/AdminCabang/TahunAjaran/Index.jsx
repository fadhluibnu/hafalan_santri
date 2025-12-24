import { Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const TahunAjaranIndex = ({ tahunAjarans = [] }) => {
    const handleDelete = (id) => {
        if (confirm('Yakin ingin menghapus tahun ajaran ini?')) {
            router.delete(route('admin-cabang.tahun-ajaran.destroy', id));
        }
    };

    const handleSetActive = (id) => {
        router.post(route('admin-cabang.tahun-ajaran.set-active', id));
    };

    const handleClose = (id) => {
        if (confirm('Yakin ingin menutup tahun ajaran ini? Santri yang belum diproses akan tetap di kelas saat ini.')) {
            router.post(route('admin-cabang.tahun-ajaran.close', id));
        }
    };

    return (
        <Layout title="Tahun Ajaran">
            <div className="space-y-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold text-gray-800">Daftar Tahun Ajaran</h1>
                    <Link
                        href={route('admin-cabang.tahun-ajaran.create')}
                        className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700"
                    >
                        + Tahun Ajaran Baru
                    </Link>
                </div>

                {/* Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {tahunAjarans.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-8 text-center text-gray-500">
                                            Belum ada tahun ajaran. Silakan buat tahun ajaran baru.
                                        </td>
                                    </tr>
                                ) : (
                                    tahunAjarans.map((ta, idx) => (
                                        <tr key={ta.id} className={idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                            <td className="px-4 py-3 text-sm text-gray-900">
                                                {ta.nama}
                                                {ta.is_active && (
                                                    <span className="ml-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        Aktif
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{ta.tanggal_mulai}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{ta.tanggal_selesai}</td>
                                            <td className="px-4 py-3 text-sm">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                                    ta.status === 'aktif' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                                                }`}>
                                                    {ta.status === 'aktif' ? 'Aktif' : 'Selesai'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-sm space-x-2 whitespace-nowrap">
                                                {!ta.is_active && ta.status === 'aktif' && (
                                                    <button
                                                        onClick={() => handleSetActive(ta.id)}
                                                        className="text-green-600 hover:text-green-900"
                                                    >
                                                        Aktifkan
                                                    </button>
                                                )}
                                                {ta.is_active && (
                                                    <button
                                                        onClick={() => handleClose(ta.id)}
                                                        className="text-orange-600 hover:text-orange-900"
                                                    >
                                                        Tutup
                                                    </button>
                                                )}
                                                <Link
                                                    href={route('admin-cabang.tahun-ajaran.edit', ta.id)}
                                                    className="text-yellow-600 hover:text-yellow-900"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(ta.id)}
                                                    className="text-red-600 hover:text-red-900"
                                                >
                                                    Hapus
                                                </button>
                                            </td>
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

export default TahunAjaranIndex;
