import { Link, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import FormInput from '../../SuperAdmin/components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const HafalanIndex = () => {
    const { flash, hafalan } = usePage().props;
    const [keyword, setKeyword] = useState('');

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return hafalan.filter((h) =>
            h.santri.toLowerCase().includes(q) ||
            h.kelas.toLowerCase().includes(q) ||
            h.ustadz.toLowerCase().includes(q) ||
            h.juz.toString().includes(q) ||
            h.kategori.toLowerCase().includes(q) ||
            h.nilai.toLowerCase().includes(q)
        );
    }, [hafalan, keyword]);

    const columns = [
        { header: 'Santri', accessor: 'santri' },
        { header: 'Kelas', accessor: 'kelas' },
        { header: 'ustadz', accessor: 'ustadz' },
        { header: 'Tanggal Setor', accessor: 'tanggal_setor' },
        { header: 'Juz', accessor: 'juz' },
        { header: 'Kategori', accessor: 'kategori' },
        { header: 'Nilai', accessor: 'nilai' },
        {
            header: 'Aksi',
            accessor: 'aksi',
            render: (row) => (
                <Link
                    href={`/ustadz/hafalan/${row.id}/edit`}
                    className="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                >
                    Edit
                </Link>
            ),
        },
    ];

    return (
        <Layout title="Daftar Hafalan">
            <div className="space-y-4">
                {flash.success && (
                    <div className="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {flash.success}
                    </div>
                )}
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-700">Daftar Hafalan</h2>
                    <Link
                        href="/ustadz/hafalan/create"
                        className="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700"
                    >
                        Tambah Hafalan
                    </Link>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput
                            label="Pencarian"
                            name="keyword"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                        />
                    </div>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <Table columns={columns} data={filtered} actions={false} />
                </div>
            </div>
        </Layout>
    );
};

export default HafalanIndex;

