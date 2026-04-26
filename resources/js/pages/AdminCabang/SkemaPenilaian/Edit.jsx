import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import Layout from '../components/Layout';

const normalizeSingkatan = (value) => String(value || '').trim().toUpperCase();

export default function EditSkemaPenilaian({ skema }) {
    const initialItems = Array.isArray(skema?.items)
        ? skema.items.map((item, index) => ({
            id: item.id ?? null,
            nama: String(item.nama || '').trim(),
            singkatan: normalizeSingkatan(item.singkatan),
            urutan: item.urutan ?? index + 1,
        }))
        : [];

    const { data, setData, put, processing, errors } = useForm({
        items: initialItems,
        keterangan: skema?.keterangan ?? '',
    });

    const [editor, setEditor] = useState({
        mode: null,
        index: -1,
        nama: '',
        singkatan: '',
    });
    const [localError, setLocalError] = useState('');

    const openAddEditor = () => {
        setLocalError('');
        setEditor({
            mode: 'add',
            index: -1,
            nama: '',
            singkatan: '',
        });
    };

    const openEditEditor = (index) => {
        const current = data.items[index];
        if (!current) return;

        setLocalError('');
        setEditor({
            mode: 'edit',
            index,
            nama: current.nama || '',
            singkatan: normalizeSingkatan(current.singkatan),
        });
    };

    const closeEditor = () => {
        setEditor({
            mode: null,
            index: -1,
            nama: '',
            singkatan: '',
        });
    };

    const saveEditor = () => {
        const nama = String(editor.nama || '').trim();
        const singkatan = normalizeSingkatan(editor.singkatan);

        if (!nama || !singkatan) {
            setLocalError('Nama dan singkatan wajib diisi.');
            return;
        }

        const duplicate = data.items.some((item, index) => {
            if (editor.mode === 'edit' && index === editor.index) {
                return false;
            }
            return normalizeSingkatan(item.singkatan) === singkatan;
        });

        if (duplicate) {
            setLocalError(`Singkatan ${singkatan} sudah digunakan.`);
            return;
        }

        if (editor.mode === 'edit' && editor.index >= 0) {
            const updated = [...data.items];
            updated[editor.index] = {
                ...updated[editor.index],
                nama,
                singkatan,
            };
            setData('items', updated);
        } else {
            setData('items', [
                ...data.items,
                {
                    id: null,
                    nama,
                    singkatan,
                    urutan: data.items.length + 1,
                },
            ]);
        }

        setLocalError('');
        closeEditor();
    };

    const removeItem = (index) => {
        const current = data.items[index];
        if (!current) return;

        if (!confirm(`Hapus parameter ${current.nama}?`)) return;

        const filtered = data.items
            .filter((_, rowIndex) => rowIndex !== index)
            .map((item, rowIndex) => ({
                ...item,
                urutan: rowIndex + 1,
            }));

        setData('items', filtered);
        setLocalError('');
    };

    const submit = (e) => {
        e.preventDefault();
        setLocalError('');

        if (data.items.length === 0) {
            setLocalError('Minimal satu parameter penilaian harus diisi.');
            return;
        }

        put('/admin-cabang/skema-penilaian', {
            data: {
                items: data.items.map((item, index) => ({
                    nama: String(item.nama || '').trim(),
                    singkatan: normalizeSingkatan(item.singkatan),
                    urutan: index + 1,
                })),
                keterangan: data.keterangan,
            },
            preserveScroll: true,
        });
    };

    return (
        <Layout title="Skema Penilaian">
            <div className="mx-auto max-w-5xl rounded-lg border bg-white p-6">
                <div className="mb-5 flex items-center justify-between">
                    <div>
                        <h2 className="text-lg font-semibold text-gray-800">Parameter Penilaian</h2>
                        <p className="text-sm text-gray-500">
                            Silakan tambahkan parameter penilaian satu per satu.
                        </p>
                    </div>
                    <Link href="/admin-cabang" className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div className="rounded-lg border">
                        <div className="flex items-center justify-between border-b px-4 py-3">
                            <div>
                                <h3 className="text-sm font-semibold text-gray-800">Daftar Parameter</h3>
                                <p className="text-xs text-gray-500">Input nilai kategori untuk modul ujian.</p>
                            </div>
                            <button
                                type="button"
                                onClick={openAddEditor}
                                className="rounded-md border border-indigo-500 px-3 py-1.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
                            >
                                + Tambah
                            </button>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[720px]">
                                <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                    <tr>
                                        <th className="px-4 py-2">No.</th>
                                        <th className="px-4 py-2">Nama</th>
                                        <th className="px-4 py-2">Singkatan</th>
                                        <th className="px-4 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="text-sm">
                                    {data.items.length === 0 && (
                                        <tr>
                                            <td className="px-4 py-10 text-center text-gray-400" colSpan={4}>
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    )}

                                    {data.items.map((item, index) => (
                                        <tr key={`${item.id ?? 'new'}-${index}`} className="border-b">
                                            <td className="px-4 py-2">{index + 1}</td>
                                            <td className="px-4 py-2">{item.nama}</td>
                                            <td className="px-4 py-2">{normalizeSingkatan(item.singkatan)}</td>
                                            <td className="px-4 py-2">
                                                <div className="flex gap-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => openEditEditor(index)}
                                                        className="rounded-md border px-2 py-1 text-xs text-gray-700 hover:bg-gray-50"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => removeItem(index)}
                                                        className="rounded-md border border-red-300 px-2 py-1 text-xs text-red-600 hover:bg-red-50"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {editor.mode && (
                        <div className="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                            <h4 className="mb-3 text-sm font-semibold text-indigo-900">
                                {editor.mode === 'edit' ? 'Edit Parameter' : 'Tambah Parameter'}
                            </h4>

                            <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-sm text-gray-700">Nama</label>
                                    <input
                                        type="text"
                                        value={editor.nama}
                                        onChange={(e) => setEditor((prev) => ({ ...prev, nama: e.target.value }))}
                                        className="w-full rounded-md border px-3 py-2 text-sm"
                                        placeholder="Contoh: Mumtaz"
                                    />
                                </div>

                                <div>
                                    <label className="mb-1 block text-sm text-gray-700">Singkatan</label>
                                    <input
                                        type="text"
                                        value={editor.singkatan}
                                        onChange={(e) => setEditor((prev) => ({ ...prev, singkatan: e.target.value.toUpperCase() }))}
                                        className="w-full rounded-md border px-3 py-2 text-sm"
                                        placeholder="Contoh: M"
                                        maxLength={20}
                                    />
                                </div>
                            </div>

                            <div className="mt-3 flex gap-2">
                                <button
                                    type="button"
                                    onClick={saveEditor}
                                    className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                >
                                    Simpan Item
                                </button>
                                <button
                                    type="button"
                                    onClick={closeEditor}
                                    className="rounded-md border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    )}

                    <div>
                        <label className="mb-1 block text-sm text-gray-600">Keterangan</label>
                        <textarea
                            value={data.keterangan}
                            onChange={(e) => setData('keterangan', e.target.value)}
                            className="w-full rounded-md border px-3 py-2 text-sm"
                            rows={3}
                        />
                    </div>

                    {(localError || errors.items || errors['items.0.nama'] || errors['items.0.singkatan']) && (
                        <div className="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {localError || errors.items || errors['items.0.nama'] || errors['items.0.singkatan']}
                        </div>
                    )}

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-60"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
}
