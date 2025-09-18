import { Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { route } from 'ziggy-js';
import FormInput from '../../SuperAdmin/components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const GuruIndex = () => {
    const [keyword, setKeyword] = useState('');
    const [rows, setRows] = useState([
        { id: 1, nama: 'Ustadz Rahman', mapel: 'Tahfidz', jumlahSantri: 15 },
        { id: 2, nama: 'Ustadzah Salma', mapel: 'Tahfidz', jumlahSantri: 12 },
        { id: 3, nama: 'Ustadz Fikri', mapel: 'Tahsin', jumlahSantri: 10 },
    ]);

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return rows.filter((d) => d.nama.toLowerCase().includes(q) || d.mapel.toLowerCase().includes(q));
    }, [rows, keyword]);

    const handleDelete = (id) => {
        if (!confirm('Yakin ingin menghapus guru ini?')) return;
        try {
            const url = route ? route('admin-cabang.guru.destroy', id) : null;
            if (url) {
                router.delete(url, {
                    preserveScroll: true,
                    onSuccess: () => setRows((prev) => prev.filter((r) => r.id !== id)),
                    onError: () => setRows((prev) => prev.filter((r) => r.id !== id)),
                });
                return;
            }
        } catch (e) {
            // route() mungkin tidak tersedia; fallback lokal
        }
        setRows((prev) => prev.filter((r) => r.id !== id));
    };

    const columns = [
        { header: 'ID', accessor: 'id' },
        { header: 'Nama', accessor: 'nama' },
        { header: 'Mapel', accessor: 'mapel' },
        { header: 'Jumlah Santri', accessor: 'jumlahSantri' },
        {
            header: 'Aksi',
            accessor: 'aksi',
            render: (row) => (
                <div className="space-x-2 whitespace-nowrap">
                    <Link href={`/admin-cabang/guru/${row.id}`} className="text-blue-600 hover:text-blue-900">
                        Detail
                    </Link>
                    <Link href={`/admin-cabang/guru/${row.id}/edit`} className="text-yellow-600 hover:text-yellow-900">
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
        <Layout title="Data Guru">
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput
                            label="Pencarian"
                            name="keyword"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Cari nama atau mapel..."
                        />
                    </div>
                </div>

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-gray-700">Daftar Guru</h2>
                        <Link
                            href="/admin-cabang/guru/create"
                            className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                        >
                            Tambah Guru
                        </Link>
                    </div>
                    <Table columns={columns} data={filtered} actions={false} />
                </div>
            </div>
        </Layout>
    );
};

export default GuruIndex;
