import { Link } from '@inertiajs/react';
import { useMemo, useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Layout from '../../components/Layout';

const ManageSantri = ({ kelas = {}, santris = [], assignedIds = [] }) => {
    const dataKelas = kelas || { id: null, nama: '-' };

    const [keyword, setKeyword] = useState('');
    const { data, setData, post, processing, errors } = useForm({
        santri_ids: assignedIds ?? [],
    });

    useEffect(() => {
        // sync when assignedIds prop changes
        setData('santri_ids', assignedIds ?? []);
    }, [assignedIds]);

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return (santris || []).filter(
            (s) =>
                (s.nama || '').toLowerCase().includes(q) ||
                (s.nis || '').toLowerCase().includes(q) ||
                (s.juzTerakhir || '').toLowerCase().includes(q)
        );
    }, [santris, keyword]);

    const toggle = (id) => {
        const cur = new Set(data.santri_ids || []);
        if (cur.has(id)) cur.delete(id);
        else cur.add(id);
        setData('santri_ids', Array.from(cur));
    };

    const toggleAll = (checked) => {
        if (checked) setData('santri_ids', filtered.map((s) => s.id));
        else setData('santri_ids', []);
    };

    const handleSave = (e) => {
        e.preventDefault();
        if (!dataKelas.id) return;
        post(route('admin-cabang.struktur.kelas.manage_santri.store', dataKelas.id));
    };

    return (
        <Layout title={`Penempatan Santri - ${dataKelas.nama}`}>
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <FormInput
                            label="Cari Santri"
                            name="keyword"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Cari nama/NIS/juz"
                        />
                    </div>
                </div>

                <form onSubmit={handleSave}>
                    <div className="rounded-lg bg-white p-4 shadow-md">
                        <div className="mb-3 flex items-center justify-between">
                            <div className="text-sm text-gray-600">Terpilih: {data.santri_ids?.length ?? 0}</div>
                            <div className="space-x-2">
                                <button type="button" onClick={() => toggleAll(true)} className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                    Pilih Semua (tampilan)
                                </button>
                                <button type="button" onClick={() => toggleAll(false)} className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                    Bersihkan
                                </button>
                            </div>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="min-w-full text-sm text-gray-700">
                                <thead className="bg-gray-50 text-xs text-gray-600 uppercase">
                                    <tr>
                                        <th className="px-4 py-3">
                                            <input
                                                type="checkbox"
                                                onChange={(e) => toggleAll(e.target.checked)}
                                                checked={filtered.length > 0 && filtered.every((s) => (data.santri_ids || []).includes(s.id))}
                                            />
                                        </th>
                                        <th className="px-4 py-3 text-left">NIS</th>
                                        <th className="px-4 py-3 text-left">Nama</th>
                                        <th className="px-4 py-3 text-left">Juz Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filtered.map((s) => (
                                        <tr key={s.id} className="border-b">
                                            <td className="px-4 py-2">
                                                <input type="checkbox" checked={(data.santri_ids || []).includes(s.id)} onChange={() => toggle(s.id)} />
                                            </td>
                                            <td className="px-4 py-2">{s.nis}</td>
                                            <td className="px-4 py-2">{s.nama}</td>
                                            <td className="px-4 py-2">{s.juzTerakhir ?? '-'}</td>
                                        </tr>
                                    ))}
                                    {filtered.length === 0 && (
                                        <tr>
                                            <td className="px-4 py-6 text-center text-gray-500" colSpan={4}>
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div className="flex items-center justify-end space-x-3">
                        {errors.error && <div className="text-sm text-red-600">{errors.error}</div>}
                        <button type="submit" disabled={processing} className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                            Simpan Penempatan
                        </button>
                        <Link href={`/admin-cabang/struktur/kelas/${dataKelas.id}`} className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Kembali
                        </Link>
                    </div>
                </form>
            </div>
        </Layout>
    );
};

// export default ManageSantri;
//             </div>
//         </Layout>
//     );
// };

export default ManageSantri;
