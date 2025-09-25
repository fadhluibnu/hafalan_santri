import { Link, router, usePage } from '@inertiajs/react';
import { useMemo, useState, useEffect, useRef } from 'react';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Table from '../../../SuperAdmin/components/Table';
import Layout from '../../components/Layout';

const KelasIndex = () => {
    const { kelas, filters = {} } = usePage().props;
    const [keyword, setKeyword] = useState(filters.q ?? '');
    const [rows, setRows] = useState(kelas?.data || []);
    const debounceRef = useRef(null);

    useEffect(() => {
        setRows(kelas?.data || []);
    }, [kelas]);

    useEffect(() => {
        // debounce live search
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('admin-cabang.struktur.kelas.index'), { q: keyword }, {
                preserveState: true,
                replace: true,
            });
        }, 400);
        return () => {
            if (debounceRef.current) clearTimeout(debounceRef.current);
        };
    }, [keyword]);

    const columns = [
        { header: 'ID', accessor: 'id' },
        { header: 'Nama Kelas', accessor: 'nama' },
        { header: 'Tingkat', accessor: 'tingkat' },
        { header: 'Wali Kelas', accessor: 'wali_kelas' },
        { header: 'Kapasitas', accessor: 'kapasitas' },
        {
            header: 'Terisi',
            accessor: 'terisi',
            render: (row) => (
                <span className="inline-flex min-w-[72px] items-center justify-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                    {row.terisi ?? 0}/{row.kapasitas ?? '-'}
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
                    <Table columns={columns} data={rows} actions={true} baseRoute="/admin-cabang/struktur/kelas" />

                    {/* Pagination */}
                    {kelas && kelas.links && (
                        <div className="mt-4 flex justify-between items-center flex-wrap gap-2">
                            <div className="text-sm text-gray-500">
                                Menampilkan {kelas.from ?? 0} sampai {kelas.to ?? 0} dari {kelas.total ?? 0} kelas
                            </div>
                            <div className="flex space-x-2">
                                {kelas.links.map((link, idx) => {
                                    const label = link.label.replace(/&laquo;|&raquo;|<[^>]+>/g, match => {
                                        if (match === "&laquo;") return "«";
                                        if (match === "&raquo;") return "»";
                                        return "";
                                    }).trim() || link.label;

                                    return link.url ? (
                                        <Link
                                            key={idx}
                                            href={link.url}
                                            preserveState
                                            className={`px-3 py-1 border rounded-md text-sm font-medium ${link.active
                                                    ? "bg-indigo-50 border-indigo-500 text-indigo-600"
                                                    : "bg-white border-gray-300 text-gray-700 hover:bg-gray-50"
                                                }`}
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
                    )}
                </div>
            </div>
        </Layout>
    );
};

export default KelasIndex;
