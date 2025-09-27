import { Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import FormInput from '../../SuperAdmin/components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const HafalanIndex = () => {
    const [keyword, setKeyword] = useState('');

    const santri = useMemo(
        () => [
            { id: 1, nama: 'Ahmad Fauzi', kelas: 'Juz 30' },
            { id: 2, nama: 'Nur Aisyah', kelas: 'Juz 29' },
        ],
        [],
    );

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return santri.filter((s) => s.nama.toLowerCase().includes(q) || s.kelas.toLowerCase().includes(q));
    }, [santri, keyword]);

    const columns = [
        { header: 'ID', accessor: 'id' },
        { header: 'Nama', accessor: 'nama' },
        { header: 'Kelas', accessor: 'kelas' },
        {
            header: 'Aksi',
            accessor: 'aksi',
            render: (row) => (
                <Link
                    href={`/guru/hafalan/create?santriId=${row.id}`}
                    className="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                >
                    Isi Setoran
                </Link>
            ),
        },
    ];

    return (
        <Layout title="Daftar Santri Binaan">
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-700">Daftar Santri Binaan</h2>
                    <Link
                        href="/guru/hafalan/create"
                        className="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700"
                    >
                        Tambah Setoran
                    </Link>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput label="Pencarian" name="keyword" value={keyword} onChange={(e) => setKeyword(e.target.value)} />
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
