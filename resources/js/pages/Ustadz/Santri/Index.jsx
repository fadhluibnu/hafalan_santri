import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const SantriIndex = ({ santris, filters = {} }) => {
    const [search, setSearch] = useState(filters.q || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('ustadz.santri.index'), { q: search }, { preserveState: true });
    };

    return (
        <Layout title="Daftar Santri">
            <div className="space-y-4">
                {/* Search */}
                <form onSubmit={handleSearch} className="flex gap-2">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari nama atau NIS..."
                        className="flex-1 rounded-md border border-gray-300 px-4 py-2 focus:border-green-500 focus:ring-green-500"
                    />
                    <button
                        type="submit"
                        className="rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                    >
                        Cari
                    </button>
                </form>

                {/* Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {santris.data?.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data santri
                                        </td>
                                    </tr>
                                ) : (
                                    santris.data?.map((santri, idx) => (
                                        <tr key={santri.id} className={idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                            <td className="px-4 py-3 text-sm text-gray-900">{santri.nis}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{santri.nama}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{santri.kelas?.nama ?? '-'}</td>
                                            <td className="px-4 py-3 text-sm space-x-2">
                                                <Link
                                                    href={route('ustadz.santri.show', santri.nis)}
                                                    className="text-blue-600 hover:text-blue-900"
                                                >
                                                    Detail
                                                </Link>
                                                <Link
                                                    href={route('ustadz.santri.hafalan', santri.nis)}
                                                    className="text-green-600 hover:text-green-900"
                                                >
                                                    Hafalan
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {santris.links && (
                        <div className="px-4 py-3 border-t flex justify-between items-center">
                            <span className="text-sm text-gray-700">
                                Menampilkan {santris.from} - {santris.to} dari {santris.total} santri
                            </span>
                            <div className="flex gap-1">
                                {santris.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        className={`px-3 py-1 text-sm rounded ${
                                            link.active
                                                ? 'bg-green-600 text-white'
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </Layout>
    );
};

export default SantriIndex;
