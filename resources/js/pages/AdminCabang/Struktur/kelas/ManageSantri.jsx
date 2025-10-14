import { Link } from '@inertiajs/react';
import { useMemo, useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Layout from '../../components/Layout';

const ManageSantri = ({ kelas = {}, assignedSantris = [], availableSantris = [], assignedIds = [] }) => {
    const dataKelas = kelas || { id: null, nama: '-' };

    const [keyword, setKeyword] = useState('');
    const { data, setData, post, processing, errors } = useForm({
        santri_ids: assignedIds ?? [],
    });

    useEffect(() => {
        // sync when assignedIds prop changes
        setData('santri_ids', assignedIds ?? []);
    }, [assignedIds]);

    const filteredAssigned = useMemo(() => {
        const q = keyword.toLowerCase();
        return (assignedSantris || []).filter(
            (s) =>
                (s.nama || '').toLowerCase().includes(q) ||
                (s.nis || '').toLowerCase().includes(q) ||
                ((s.juzTerakhir || '') + '').toLowerCase().includes(q)
        );
    }, [assignedSantris, keyword]);

    const filteredAvailable = useMemo(() => {
        const q = keyword.toLowerCase();
        return (availableSantris || []).filter(
            (s) =>
                (s.nama || '').toLowerCase().includes(q) ||
                (s.nis || '').toLowerCase().includes(q) ||
                ((s.juzTerakhir || '') + '').toLowerCase().includes(q)
        );
    }, [availableSantris, keyword]);

    const kapasitas = typeof kelas.kapasitas !== 'undefined' ? kelas.kapasitas : null;
    const selectedCount = (data.santri_ids || []).length;
    const overCapacity = kapasitas !== null && selectedCount > kapasitas;

    const isSelected = (id) => (data.santri_ids || []).includes(id);

    const toggle = (id) => {
        const cur = new Set(data.santri_ids || []);
        // jika ingin menambahkan (belum terpilih) tapi sudah penuh => tolak penambahan
        if (!cur.has(id)) {
            if (kapasitas !== null && cur.size >= kapasitas) {
                // jangan tambahkan jika sudah mencapai kapasitas
                return;
            }
            cur.add(id);
        } else {
            cur.delete(id);
        }
        setData('santri_ids', Array.from(cur));
    };

    const toggleAll = (section, checked) => {
        if (section === 'available') {
            const ids = filteredAvailable.map((s) => s.id);
            // hanya ids yang belum ada di selection
            const currentSet = new Set(data.santri_ids || []);
            const toAdd = ids.filter((i) => !currentSet.has(i));
            let allowedToAdd = toAdd;
            if (kapasitas !== null) {
                const remaining = Math.max(kapasitas - currentSet.size, 0);
                allowedToAdd = toAdd.slice(0, remaining);
            }
            if (checked) {
                setData('santri_ids', Array.from(new Set([...(data.santri_ids || []), ...allowedToAdd])));
            } else {
                setData('santri_ids', (data.santri_ids || []).filter((i) => !ids.includes(i)));
            }
        } else if (section === 'assigned') {
            const ids = filteredAssigned.map((s) => s.id);
            const currentSet = new Set(data.santri_ids || []);
            const toAdd = ids.filter((i) => !currentSet.has(i));
            let allowedToAdd = toAdd;
            if (kapasitas !== null) {
                const remaining = Math.max(kapasitas - currentSet.size, 0);
                allowedToAdd = toAdd.slice(0, remaining);
            }
            if (checked) {
                setData('santri_ids', Array.from(new Set([...(data.santri_ids || []), ...allowedToAdd])));
            } else {
                setData('santri_ids', (data.santri_ids || []).filter((i) => !ids.includes(i)));
            }
        }
    };

    const handleSave = (e) => {
        e.preventDefault();
        if (!dataKelas.id) return;
        // server side juga melakukan validasi kapasitas; frontend hanya mencegah submit jika overCapacity
        if (overCapacity) return;
        post(route('admin-cabang.struktur.kelas.manage_santri.store', dataKelas.id));
    };

    return (
        <Layout title={`Penempatan Santri - ${dataKelas.nama}`}>
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="text-sm text-gray-700">
                        Kapasitas kelas: {kapasitas ?? '-'} &nbsp;|&nbsp; Terpilih: {selectedCount}
                        {kapasitas !== null && <span className="ml-3 text-sm text-gray-500">Sisa slot: {Math.max(kapasitas - selectedCount, 0)}</span>}
                    </div>
                    {overCapacity && (
                        <div className="mt-2 text-sm text-red-600">Jumlah yang dipilih melebihi kapasitas kelas ({kapasitas}). Kurangi pilihan sebelum menyimpan.</div>
                    )}
                </div>

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
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {/* Assigned Section */}
                        <div className="rounded-lg bg-white p-4 shadow-md">
                            <div className="mb-3 flex items-center justify-between">
                                <div className="text-sm text-gray-600">Sudah di kelas ini: { (data.santri_ids || []).filter(id => assignedIds.includes(id)).length }</div>
                                <div className="space-x-2">
                                    <button type="button" onClick={() => toggleAll('assigned', true)} className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                        Pilih Semua (Assigned)
                                    </button>
                                    <button type="button" onClick={() => toggleAll('assigned', false)} className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                        Bersihkan (Assigned)
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
                                                    onChange={(e) => toggleAll('assigned', e.target.checked)}
                                                    checked={filteredAssigned.length > 0 && filteredAssigned.every((s) => isSelected(s.id))}
                                                />
                                            </th>
                                            <th className="px-4 py-3 text-left">NIS</th>
                                            <th className="px-4 py-3 text-left">Nama</th>
                                            <th className="px-4 py-3 text-left">Juz Terakhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filteredAssigned.map((s) => (
                                            <tr key={s.id} className="border-b">
                                                <td className="px-4 py-2">
                                                    <input type="checkbox" checked={isSelected(s.id)} onChange={() => toggle(s.id)} />
                                                </td>
                                                <td className="px-4 py-2">{s.nis}</td>
                                                <td className="px-4 py-2">{s.nama}</td>
                                                <td className="px-4 py-2">{s.juzTerakhir ?? '-'}</td>
                                            </tr>
                                        ))}
                                        {filteredAssigned.length === 0 && (
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

                        {/* Available Section */}
                        <div className="rounded-lg bg-white p-4 shadow-md">
                            <div className="mb-3 flex items-center justify-between">
                                <div className="text-sm text-gray-600">Belum ditempatkan: {filteredAvailable.length}</div>
                                <div className="space-x-2">
                                    <button type="button" onClick={() => toggleAll('available', true)} className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                        Pilih Semua (Available)
                                    </button>
                                    <button type="button" onClick={() => toggleAll('available', false)} className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                        Bersihkan (Available)
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
                                                    onChange={(e) => toggleAll('available', e.target.checked)}
                                                    checked={filteredAvailable.length > 0 && filteredAvailable.every((s) => isSelected(s.id))}
                                                />
                                            </th>
                                            <th className="px-4 py-3 text-left">NIS</th>
                                            <th className="px-4 py-3 text-left">Nama</th>
                                            <th className="px-4 py-3 text-left">Juz Terakhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filteredAvailable.map((s) => (
                                            <tr key={s.id} className="border-b">
                                                <td className="px-4 py-2">
                                                    <input type="checkbox" checked={isSelected(s.id)} onChange={() => toggle(s.id)} />
                                                </td>
                                                <td className="px-4 py-2">{s.nis}</td>
                                                <td className="px-4 py-2">{s.nama}</td>
                                                <td className="px-4 py-2">{s.juzTerakhir ?? '-'}</td>
                                            </tr>
                                        ))}
                                        {filteredAvailable.length === 0 && (
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
                    </div>

                    <div className="flex items-center justify-end space-x-3 mt-4">
                        {errors.error && <div className="text-sm text-red-600">{errors.error}</div>}
                        <button type="submit" disabled={processing || overCapacity} className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 disabled:opacity-60">
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

export default ManageSantri;
