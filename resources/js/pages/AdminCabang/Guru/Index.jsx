import { Link, router, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { route } from 'ziggy-js';
import FormInput from '../components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const GuruIndex = () => {
    const { gurus, flash } = usePage().props;
    const [keyword, setKeyword] = useState('');
    const [alertVisible, setAlertVisible] = useState(!!flash?.success || !!flash?.error);
    
    // Format tanggal untuk tampilan
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(date);
    };

    const filtered = useMemo(() => {
        if (!gurus) return [];
        const q = keyword.toLowerCase();
        return gurus.filter((d) => 
            d.nama?.toLowerCase().includes(q) || 
            d.nip?.toLowerCase().includes(q) || 
            d.email?.toLowerCase().includes(q)
        );
    }, [gurus, keyword]);

    const handleDelete = (id) => {
        if (!confirm('Yakin ingin menghapus guru ini?')) return;
        router.delete(route('admin-cabang.guru.destroy', id), {
            preserveScroll: true
        });
    };

    const columns = [
        { header: 'NIP', accessor: 'nip', render: (row) => row.nip || '-' },
        { header: 'Nama', accessor: 'nama' },
        { 
            header: 'Jenis Kelamin', 
            accessor: 'jenis_kelamin', 
            render: (row) => row.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' 
        },
        { header: 'No. HP', accessor: 'no_handphone', render: (row) => row.no_handphone || '-' },
        { header: 'Email', accessor: 'email', render: (row) => row.email || '-' },
        { 
            header: 'Tanggal Kerja', 
            accessor: 'tanggal_kerja', 
            render: (row) => formatDate(row.tanggal_kerja) 
        },
        { 
            header: 'Status', 
            accessor: 'non_aktif', 
            render: (row) => (
                <span className={`px-2 py-1 rounded-full text-xs ${row.non_aktif ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`}>
                    {row.non_aktif == 1 ? 'Non-Aktif' : 'Aktif'}
                </span>
            )
        },
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
                {alertVisible && flash?.success && (
                    <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span className="block sm:inline">{flash.success}</span>
                        <button
                            className="absolute top-0 right-0 px-4 py-3"
                            onClick={() => setAlertVisible(false)}
                        >
                            <span className="text-green-500">×</span>
                        </button>
                    </div>
                )}

                {alertVisible && flash?.error && (
                    <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span className="block sm:inline">{flash.error}</span>
                        <button
                            className="absolute top-0 right-0 px-4 py-3"
                            onClick={() => setAlertVisible(false)}
                        >
                            <span className="text-red-500">×</span>
                        </button>
                    </div>
                )}

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput
                            label="Pencarian"
                            name="keyword"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Cari nama, NIP, atau email..."
                        />
                    </div>
                </div>

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-gray-700">Daftar Guru</h2>
                        <Link
                            href="/admin-cabang/guru/create"
                            className="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-green-700"
                        >
                            Tambah Guru
                        </Link>
                    </div>
                    
                    {filtered && filtered.length > 0 ? (
                        <Table columns={columns} data={filtered} actions={false} />
                    ) : (
                        <div className="text-center py-10">
                            <p className="text-gray-500">
                                {keyword ? 'Tidak ada guru yang sesuai dengan pencarian.' : 'Belum ada data guru. Silakan tambahkan guru baru.'}
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </Layout>
    );
};

export default GuruIndex;
