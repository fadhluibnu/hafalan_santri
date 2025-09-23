import { Link, router, usePage } from '@inertiajs/react';
import { useMemo, useState, useEffect } from 'react';
import { route } from 'ziggy-js';
import FormInput from '../../SuperAdmin/components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const SantriIndex = () => {
    const { santris = [] } = usePage().props;
    const [keyword, setKeyword] = useState('');
    const [rows, setRows] = useState(santris);

    // Sync rows jika santris dari backend berubah
    useEffect(() => {
        setRows(santris);
    }, [santris]);

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return rows.filter((d) => d.nama.toLowerCase().includes(q) || d.kelas.toLowerCase().includes(q));
    }, [rows, keyword]);

    const handleDelete = (id) => {
        if (!confirm('Yakin ingin menghapus santri ini?')) return;
        try {
            const url = route ? route('admin-cabang.santri.destroy', id) : null;
            if (url) {
                router.delete(url, {
                    preserveScroll: true,
                    onSuccess: () => setRows((prev) => prev.filter((r) => r.id !== id)),
                    onError: () => setRows((prev) => prev.filter((r) => r.id !== id)), // fallback
                });
                return;
            }
        } catch (e) {
            // route() mungkin tidak tersedia; lanjut ke fallback
        }
        // Fallback hapus lokal (dummy)
        setRows((prev) => prev.filter((r) => r.id !== id));
    };

    const columns = [
        { header: 'ID', accessor: 'id' },
        { header: 'Nama', accessor: 'nama' },
        { header: 'Kelas', accessor: 'kelas' },
        { header: 'Total Juz Sah', accessor: 'totalJuz' },
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
                </div>
            </div>
        </Layout>
    );
};

export default SantriIndex;
