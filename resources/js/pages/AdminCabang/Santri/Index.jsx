import { Link, router, usePage } from '@inertiajs/react';
import { useMemo, useState, useEffect } from 'react';
import { route } from 'ziggy-js';
import FormInput from '../../SuperAdmin/components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const SantriIndex = () => {
    const { santris } = usePage().props;
    const [keyword, setKeyword] = useState('');
    const [rows, setRows] = useState(santris.data || []);

    useEffect(() => {
        setRows(santris.data || []);
    }, [santris]);

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return rows.filter((d) =>
            (d.nama || '').toLowerCase().includes(q) ||
            (d.kelas && d.kelas.nama ? d.kelas.nama.toLowerCase().includes(q) : '')
        );
    }, [rows, keyword]);

    const handleDelete = (id) => {
        if (!confirm('Yakin ingin menghapus santri ini?')) return;
        try {
            const url = route ? route('admin-cabang.santri.destroy', id) : null;
            if (url) {
                router.delete(url, {
                    preserveScroll: true,
                    onSuccess: () => setRows((prev) => prev.filter((r) => r.id !== id)),
                    onError: () => setRows((prev) => prev.filter((r) => r.id !== id)),
                });
                return;
            }
        } catch (e) {}
        setRows((prev) => prev.filter((r) => r.id !== id));
    };

    const columns = [
        { header: 'ID', accessor: 'id' },
        {
            header: 'Foto',
            accessor: 'foto',
            render: (row) =>
                row.foto ? (
                    <img
                        src={`/storage/${row.foto}`}
                        alt={row.nama}
                        className="h-10 w-10 object-cover rounded-full border"
                        style={{ minWidth: 40, minHeight: 40 }}
                        onError={e => { e.target.style.display = 'none'; }}
                    />
                ) : (
                    <span className="inline-block h-10 w-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center">-</span>
                ),
        },
        { header: 'Nama', accessor: 'nama' },
        {
            header: 'Kelas',
            accessor: 'kelas',
            render: (row) => row.kelas && row.kelas.nama ? row.kelas.nama : '-',
        },
        {
            header: 'Total Juz Sah',
            accessor: 'totalJuz',
            render: (row) => Array.isArray(row.jus) ? row.jus.length : 0,
        },
        {
            header: 'Aksi',
            accessor: 'aksi',
            render: (row) => (
                <div className="space-x-2 whitespace-nowrap">
                    <Link href={route('admin-cabang.santri.show', row.id)} className="text-blue-600 hover:text-blue-900">
                        Detail
                    </Link>
                    <Link href={route('admin-cabang.santri.edit', row.id)} className="text-yellow-600 hover:text-yellow-900">
                        Edit
                    </Link>
                    <button onClick={() => handleDelete(row.id)} className="text-red-600 hover:text-red-900">
                        Hapus
                    </button>
                </div>
            ),
        },
    ];

    return (
        <Layout title="Data Santri">
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput
                            label="Pencarian"
                            name="keyword"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Cari nama atau kelas..."
                        />
                    </div>
                </div>

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-gray-700">Daftar Santri</h2>
                        <Link
                            href={route('admin-cabang.santri.create')}
                            className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                        >
                            Tambah Santri
                        </Link>
                    </div>
                    <Table columns={columns} data={filtered} actions={false} />

                    {/* Inline Pagination */}
                    {/* {santris && santris.meta && santris.links && santris.links.length > 1 && ( */}
                        <div className="mt-4 flex justify-between items-center flex-wrap gap-2">
                            <div className="text-sm text-gray-500">
                                Menampilkan {santris.from ?? 0} sampai {santris.to ?? 0} dari {santris.total ?? 0} santri
                            </div>
                            <div className="flex space-x-2">
                                {santris.links.map((link, idx) => {
                                    // Bersihkan label dari html entities
                                    const label = link.label.replace(/&laquo;|&raquo;|<[^>]+>/g, match => {
                                        if (match === "&laquo;") return "«";
                                        if (match === "&raquo;") return "»";
                                        return "";
                                    }).trim() || link.label;

                                    return link.url ? (
                                        <Link
                                            key={idx}
                                            href={link.url}
                                            preserveScroll
                                            className={`px-3 py-1 border rounded-md text-sm font-medium ${link.active
                                                    ? "bg-indigo-50 border-indigo-500 text-indigo-600"
                                                    : "bg-white border-gray-300 text-gray-700 hover:bg-gray-50"
                                                }`}
                                            disabled={link.url === null}
                                        >
                                            {label}
                                        </Link>
                                    ) : (
                                        <span
                                            key={idx}
                                            className="px-3 py-1 border border-gray-200 rounded-md text-sm font-medium text-gray-400 cursor-not-allowed"
                                        >
                                            {label}
                                        </span>
                                    );
                                })}
                            </div>
                        </div>
                    {/* )} */}
                </div>
            </div>
        </Layout>
    );
};

export default SantriIndex;
