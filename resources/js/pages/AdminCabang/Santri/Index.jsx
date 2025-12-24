import { Link, router, usePage, useForm } from '@inertiajs/react';
import { useMemo, useState, useEffect } from 'react';
import { route } from 'ziggy-js';
import FormInput from '../../SuperAdmin/components/FormInput';
import Table from '../../SuperAdmin/components/Table';
import Layout from '../components/Layout';

const SantriIndex = () => {
    const { santris, flash } = usePage().props;
    const [keyword, setKeyword] = useState('');
    const [rows, setRows] = useState(santris.data || []);
    const [showImportModal, setShowImportModal] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        file: null,
    });

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

    const handleImport = (e) => {
        e.preventDefault();
        post(route('admin-cabang.santri.import'), {
            onSuccess: () => {
                setShowImportModal(false);
                reset();
            },
        });
    };

    const columns = [
        { header: 'NIS', accessor: 'nis' },
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
                    <Link href={route('admin-cabang.santri.show', row.nis)} className="text-blue-600 hover:text-blue-900">
                        Detail
                    </Link>
                    <Link href={route('admin-cabang.santri.hafalan', row.nis)} className="text-green-600 hover:text-green-900">
                        Hafalan
                    </Link>
                    <Link href={route('admin-cabang.santri.edit', row.nis)} className="text-yellow-600 hover:text-yellow-900">
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
                {/* Flash messages */}
                {flash?.success && (
                    <div className="p-4 bg-green-100 text-green-700 rounded-lg">{flash.success}</div>
                )}
                {errors.error && (
                    <div className="p-4 bg-red-100 text-red-700 rounded-lg">{errors.error}</div>
                )}

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
                    <div className="mb-3 flex items-center justify-between flex-wrap gap-2">
                        <h2 className="text-lg font-semibold text-gray-700">Daftar Santri</h2>
                        <div className="flex items-center gap-2 flex-wrap">
                            {/* Export Button */}
                            <a
                                href={route('admin-cabang.santri.export')}
                                className="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 flex items-center gap-1"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </a>
                            {/* Template Button */}
                            <a
                                href={route('admin-cabang.santri.template')}
                                className="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-gray-700 flex items-center gap-1"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Template
                            </a>
                            {/* Import Button */}
                            <button
                                onClick={() => setShowImportModal(true)}
                                className="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 flex items-center gap-1"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import Excel
                            </button>
                            {/* Tambah Santri Button */}
                            <Link
                                href={route('admin-cabang.santri.create')}
                                className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                            >
                                Tambah Santri
                            </Link>
                        </div>
                    </div>
                    <Table columns={columns} data={filtered} actions={false} />

                    {/* Inline Pagination */}
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
                </div>
            </div>

            {/* Import Modal */}
            {showImportModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                        <h3 className="text-lg font-semibold mb-4">Import Data Santri</h3>
                        <form onSubmit={handleImport}>
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih File Excel (.xlsx, .xls)
                                </label>
                                <input
                                    type="file"
                                    accept=".xlsx,.xls"
                                    onChange={(e) => setData('file', e.target.files[0])}
                                    className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                {errors.file && <p className="mt-1 text-sm text-red-600">{errors.file}</p>}
                            </div>
                            <p className="text-sm text-gray-500 mb-4">
                                Pastikan format file sesuai dengan template. 
                                <a href={route('admin-cabang.santri.template')} className="text-indigo-600 hover:underline ml-1">
                                    Download template
                                </a>
                            </p>
                            <div className="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => { setShowImportModal(false); reset(); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing || !data.file}
                                    className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Mengimport...' : 'Import'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </Layout>
    );
};

export default SantriIndex;

