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
            batas_bawah: item.batas_bawah ?? '',
            batas_atas: item.batas_atas ?? '',
            urutan: item.urutan ?? index + 1,
        }))
        : [];

    const { data, setData, put, processing, errors } = useForm({
        tipe: skema?.tipe ?? 'label',
        items: initialItems,
        keterangan: skema?.keterangan ?? '',
    });

    const [editor, setEditor] = useState({
        mode: null,
        index: -1,
        nama: '',
        singkatan: '',
        batas_bawah: '',
        batas_atas: '',
    });
    const [localError, setLocalError] = useState('');

    const openAddEditor = () => {
        setLocalError('');
        setEditor({
            mode: 'add',
            index: -1,
            nama: '',
            singkatan: '',
            batas_bawah: '',
            batas_atas: '',
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
            batas_bawah: current.batas_bawah ?? '',
            batas_atas: current.batas_atas ?? '',
        });
    };

    const closeEditor = () => {
        setEditor({
            mode: null,
            index: -1,
            nama: '',
            singkatan: '',
            batas_bawah: '',
            batas_atas: '',
        });
    };

    const saveEditor = () => {
        const nama = String(editor.nama || '').trim();
        const singkatan = normalizeSingkatan(editor.singkatan);
        const batas_bawah = editor.batas_bawah;
        const batas_atas = editor.batas_atas;

        if (!nama || !singkatan) {
            setLocalError('Nama dan singkatan wajib diisi.');
            return;
        }

        if (batas_bawah !== '' && batas_atas !== '' && Number(batas_bawah) >= Number(batas_atas)) {
            setLocalError('Batas Bawah tidak boleh lebih besar atau sama dengan Batas Atas.');
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
                batas_bawah,
                batas_atas,
            };
            setData('items', updated);
        } else {
            setData('items', [
                ...data.items,
                {
                    id: null,
                    nama,
                    singkatan,
                    batas_bawah,
                    batas_atas,
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

        if (data.tipe === 'label' && data.items.length === 0) {
            setLocalError('Minimal satu parameter penilaian harus diisi untuk tipe kategori.');
            return;
        }

        put('/admin-cabang/skema-penilaian', {
            data: {
                tipe: data.tipe,
                items: data.tipe === 'label' ? data.items.map((item, index) => ({
                    nama: String(item.nama || '').trim(),
                    singkatan: normalizeSingkatan(item.singkatan),
                    batas_bawah: item.batas_bawah !== '' ? item.batas_bawah : null,
                    batas_atas: item.batas_atas !== '' ? item.batas_atas : null,
                    urutan: index + 1,
                })) : [],
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
                        <h2 className="text-lg font-semibold text-gray-800">Setup Skema Penilaian</h2>
                        <p className="text-sm text-gray-500">
                            Atur tipe skema penilaian dan parameter (jika kategori).
                        </p>
                    </div>
                    <Link href="/admin-cabang" className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>

                <form onSubmit={submit} className="space-y-6">
                    <div>
                        <label className="mb-2 block text-sm font-semibold text-gray-800">Tipe Skema Penilaian</label>
                        <div className="flex items-center gap-4">
                            <label className="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="radio"
                                    name="tipe"
                                    value="label"
                                    checked={data.tipe === 'label'}
                                    onChange={(e) => setData('tipe', e.target.value)}
                                    className="text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                                />
                                <span className="text-sm text-gray-700">Kategori (A, B, C / Sangat Baik)</span>
                            </label>
                            <label className="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="radio"
                                    name="tipe"
                                    value="numeric"
                                    checked={data.tipe === 'numeric'}
                                    onChange={(e) => setData('tipe', e.target.value)}
                                    className="text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                                />
                                <span className="text-sm text-gray-700">Angka Murni (0 - 100)</span>
                            </label>
                        </div>
                    </div>

                    {data.tipe === 'label' && (
                        <div className="rounded-lg border">
                            <div className="flex items-center justify-between border-b px-4 py-3 bg-gray-50">
                                <div>
                                    <h3 className="text-sm font-semibold text-gray-800">Daftar Parameter Kategori</h3>
                                    <p className="text-xs text-gray-500">Isi kategori beserta rentang nilainya (opsional).</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={openAddEditor}
                                    className="rounded-md border border-indigo-500 bg-white px-3 py-1.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
                                >
                                    + Tambah Kategori
                                </button>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full min-w-[720px]">
                                    <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                        <tr>
                                            <th className="px-4 py-2">No.</th>
                                            <th className="px-4 py-2">Nama</th>
                                            <th className="px-4 py-2">Singkatan</th>
                                            <th className="px-4 py-2 text-center">Batas Bawah</th>
                                            <th className="px-4 py-2 text-center">Batas Atas</th>
                                            <th className="px-4 py-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="text-sm">
                                        {data.items.length === 0 && (
                                            <tr>
                                                <td className="px-4 py-10 text-center text-gray-400" colSpan={6}>
                                                    Belum ada kategori yang ditambahkan
                                                </td>
                                            </tr>
                                        )}

                                        {data.items.map((item, index) => (
                                            <tr key={`${item.id ?? 'new'}-${index}`} className="border-b">
                                                <td className="px-4 py-2">{index + 1}</td>
                                                <td className="px-4 py-2">{item.nama}</td>
                                                <td className="px-4 py-2">{normalizeSingkatan(item.singkatan)}</td>
                                                <td className="px-4 py-2 text-center">{item.batas_bawah !== '' && item.batas_bawah !== null ? item.batas_bawah : '-'}</td>
                                                <td className="px-4 py-2 text-center">{item.batas_atas !== '' && item.batas_atas !== null ? item.batas_atas : '-'}</td>
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
                    )}

                    {editor.mode && (
                        <div className="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                            <h4 className="mb-3 text-sm font-semibold text-indigo-900">
                                {editor.mode === 'edit' ? 'Edit Parameter' : 'Tambah Parameter'}
                            </h4>

                            <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
                                <div className="md:col-span-2">
                                    <label className="mb-1 block text-sm text-gray-700">Nama</label>
                                    <input
                                        type="text"
                                        value={editor.nama}
                                        onChange={(e) => setEditor((prev) => ({ ...prev, nama: e.target.value }))}
                                        className="w-full rounded-md border px-3 py-2 text-sm"
                                        placeholder="Contoh: Mumtaz"
                                    />
                                </div>

                                <div className="md:col-span-2">
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

                                <div className="md:col-span-2">
                                    <label className="mb-1 block text-sm text-gray-700">Batas Bawah Nilai (Opsional)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={editor.batas_bawah}
                                        onChange={(e) => setEditor((prev) => ({ ...prev, batas_bawah: e.target.value }))}
                                        className="w-full rounded-md border px-3 py-2 text-sm"
                                        placeholder="Contoh: 90"
                                    />
                                </div>

                                <div className="md:col-span-2">
                                    <label className="mb-1 block text-sm text-gray-700">Batas Atas Nilai (Opsional)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={editor.batas_atas}
                                        onChange={(e) => setEditor((prev) => ({ ...prev, batas_atas: e.target.value }))}
                                        className="w-full rounded-md border px-3 py-2 text-sm"
                                        placeholder="Contoh: 100"
                                    />
                                </div>
                            </div>

                            <div className="mt-4 flex gap-2">
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
                                    className="rounded-md border bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    )}

                    <div>
                        <label className="mb-1 block text-sm font-semibold text-gray-800">Keterangan Umum</label>
                        <textarea
                            value={data.keterangan}
                            onChange={(e) => setData('keterangan', e.target.value)}
                            className="w-full rounded-md border px-3 py-2 text-sm"
                            rows={3}
                            placeholder="Catatan tambahan mengenai skema penilaian ini..."
                        />
                    </div>

                    {(localError || errors.items || errors['tipe'] || errors['items.0.nama'] || errors['items.0.singkatan'] || errors['items.0.batas_bawah'] || errors['items.0.batas_atas']) && (
                        <div className="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {localError || errors.items || errors.tipe || errors['items.0.nama'] || errors['items.0.singkatan'] || errors['items.0.batas_bawah'] || errors['items.0.batas_atas']}
                        </div>
                    )}

                    <div className="flex justify-end pt-4 border-t">
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-60"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Skema Penilaian'}
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
}
