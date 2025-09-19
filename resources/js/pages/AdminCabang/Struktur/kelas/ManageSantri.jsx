import { Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Layout from '../../components/Layout';

const ManageSantri = ({ kelas, assignedIds = [] }) => {
    const dataKelas = kelas || { id: 1, nama: 'Kelas Tahfidz A', tingkat: 'Juz 30' };

    const [keyword, setKeyword] = useState('');
    const [selected, setSelected] = useState(new Set(assignedIds));

    const allSantri = useMemo(
        () => [
            { id: 1, nis: 'S001', nama: 'Ahmad Zaki', juzTerakhir: 'Juz 30' },
            { id: 2, nis: 'S002', nama: 'Budi Santoso', juzTerakhir: 'Juz 29' },
            { id: 3, nis: 'S003', nama: 'Citra Ayu', juzTerakhir: 'Juz 28' },
            { id: 4, nis: 'S004', nama: 'Dewi Lestari', juzTerakhir: 'Juz 30' },
        ],
        [],
    );

    const filtered = useMemo(() => {
        const q = keyword.toLowerCase();
        return allSantri.filter(
            (s) => s.nama.toLowerCase().includes(q) || s.nis.toLowerCase().includes(q) || s.juzTerakhir.toLowerCase().includes(q),
        );
    }, [allSantri, keyword]);

    const toggle = (id) => {
        const s = new Set(selected);
        if (s.has(id)) s.delete(id);
        else s.add(id);
        setSelected(s);
    };

    const toggleAll = (checked) => {
        if (checked) setSelected(new Set(filtered.map((s) => s.id)));
        else setSelected(new Set());
    };

    const handleSave = () => {
        const ids = Array.from(selected);
        alert(`Berhasil menyimpan penempatan ${ids.length} santri ke ${dataKelas.nama} (dummy).\nIDs: ${ids.join(', ')}`);
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

                <div className="rounded-lg bg-white p-4 shadow-md">
                    <div className="mb-3 flex items-center justify-between">
                        <div className="text-sm text-gray-600">Terpilih: {selected.size}</div>
                        <div className="space-x-2">
                            <button
                                onClick={() => toggleAll(true)}
                                className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50"
                            >
                                Pilih Semua (tampilan)
                            </button>
                            <button
                                onClick={() => toggleAll(false)}
                                className="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50"
                            >
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
                                            checked={filtered.length > 0 && filtered.every((s) => selected.has(s.id))}
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
                                            <input type="checkbox" checked={selected.has(s.id)} onChange={() => toggle(s.id)} />
                                        </td>
                                        <td className="px-4 py-2">{s.nis}</td>
                                        <td className="px-4 py-2">{s.nama}</td>
                                        <td className="px-4 py-2">{s.juzTerakhir}</td>
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
                    <button
                        onClick={handleSave}
                        className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                    >
                        Simpan Penempatan (Dummy)
                    </button>
                    <Link
                        href={`/admin-cabang/struktur/kelas/${dataKelas.id}`}
                        className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </Layout>
    );
};

export default ManageSantri;
