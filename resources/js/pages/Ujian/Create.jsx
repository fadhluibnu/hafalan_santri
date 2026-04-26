import { Link, useForm } from '@inertiajs/react';
import RoleLayout from '../shared/RoleLayout';

export default function UjianCreate({ baseUrl, classes = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        nama: '',
        kelas_id: '',
        tanggal_ujian: '',
        keterangan: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(`${baseUrl}/ujian`);
    };

    return (
        <RoleLayout authRole="ustadz" title="Tambah Ujian">
            <div className="mx-auto max-w-3xl rounded-lg border bg-white p-6">
                <div className="mb-4 flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-800">Form Tambah Ujian</h2>
                    <Link href={`${baseUrl}/ujian`} className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>

                {errors.skema && (
                    <div className="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {errors.skema}
                    </div>
                )}

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label className="mb-1 block text-sm text-gray-600">Nama Ujian</label>
                        <input
                            type="text"
                            value={data.nama}
                            onChange={(e) => setData('nama', e.target.value)}
                            className="w-full rounded-md border px-3 py-2 text-sm"
                            placeholder="Contoh: Ujian Tengah Semester"
                            required
                        />
                        {errors.nama && <p className="mt-1 text-xs text-red-600">{errors.nama}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block text-sm text-gray-600">Kelas</label>
                        <select
                            value={data.kelas_id}
                            onChange={(e) => setData('kelas_id', e.target.value)}
                            className="w-full rounded-md border px-3 py-2 text-sm"
                            required
                        >
                            <option value="">-- Pilih Kelas --</option>
                            {classes.map((kelas) => (
                                <option key={kelas.id} value={kelas.id}>
                                    {kelas.nama}
                                </option>
                            ))}
                        </select>
                        {errors.kelas_id && <p className="mt-1 text-xs text-red-600">{errors.kelas_id}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block text-sm text-gray-600">Tanggal Ujian</label>
                        <input
                            type="date"
                            value={data.tanggal_ujian}
                            onChange={(e) => setData('tanggal_ujian', e.target.value)}
                            className="w-full rounded-md border px-3 py-2 text-sm"
                            required
                        />
                        {errors.tanggal_ujian && <p className="mt-1 text-xs text-red-600">{errors.tanggal_ujian}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block text-sm text-gray-600">Keterangan (opsional)</label>
                        <textarea
                            value={data.keterangan}
                            onChange={(e) => setData('keterangan', e.target.value)}
                            className="w-full rounded-md border px-3 py-2 text-sm"
                            rows={3}
                        />
                        {errors.keterangan && <p className="mt-1 text-xs text-red-600">{errors.keterangan}</p>}
                    </div>

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-60"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Ujian'}
                        </button>
                    </div>
                </form>
            </div>
        </RoleLayout>
    );
}
