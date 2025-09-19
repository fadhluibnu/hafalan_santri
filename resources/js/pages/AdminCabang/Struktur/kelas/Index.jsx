import { Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Table from '../../../SuperAdmin/components/Table';
import Layout from '../../components/Layout';

const KelasIndex = () => {
    const [keyword, setKeyword] = useState('');

    const dummyData = useMemo(
        () => [
            { id: 1, nama: 'Kelas Tahfidz A', tingkat: 'Juz 30', waliKelas: 'Ustadz Rahman', kapasitas: 25, terisi: 18 },
            { id: 2, nama: 'Kelas Tahfidz B', tingkat: 'Juz 29', waliKelas: 'Ustadzah Salma', kapasitas: 20, terisi: 12 },
        ],
        [],
    );

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return dummyData.filter(
            (d) => d.nama.toLowerCase().includes(q) || d.tingkat.toLowerCase().includes(q) || d.waliKelas.toLowerCase().includes(q),
        );
    }, [dummyData, keyword]);

    const columns = [
        { header: 'ID', accessor: 'id' },
        { header: 'Nama Kelas', accessor: 'nama' },
        { header: 'Tingkat', accessor: 'tingkat' },
        { header: 'Wali Kelas', accessor: 'waliKelas' },
        { header: 'Kapasitas', accessor: 'kapasitas' },
        {
            header: 'Terisi',
            accessor: 'terisi',
            render: (row) => (
                <span className="inline-flex min-w-[72px] items-center justify-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                    {row.terisi ?? 0}/{row.kapasitas}
                </span>
            ),
        },
        {
            header: 'Penempatan',
            accessor: 'penempatan',
            render: (row) => (
                <Link
                    href={`/admin-cabang/struktur/kelas/${row.id}/santri`}
                    className="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-green-700"
                >
                    Tempatkan Santri
                </Link>
            ),
        },
    ];

    return (
        <Layout title="Struktur - Kelas">
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-700">Daftar Kelas</h2>
                    <Link
                        href="/admin-cabang/struktur/kelas/create"
                        className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                    >
                        Buat Kelas
                    </Link>
                </div>

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput
                            label="Pencarian"
                            name="keyword"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Cari nama/tingkat/wali kelas"
                        />
                    </div>
                </div>

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <Table columns={columns} data={filtered} actions={true} baseRoute="/admin-cabang/struktur/kelas" />
                </div>
            </div>
        </Layout>
    );
};

export default KelasIndex;
