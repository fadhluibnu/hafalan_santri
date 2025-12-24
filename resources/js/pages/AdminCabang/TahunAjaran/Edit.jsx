import { Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const TahunAjaranEdit = ({ tahunAjaran }) => {
    const { data, setData, put, processing, errors } = useForm({
        nama: tahunAjaran?.nama || '',
        tanggal_mulai: tahunAjaran?.tanggal_mulai || '',
        tanggal_selesai: tahunAjaran?.tanggal_selesai || '',
        is_active: tahunAjaran?.is_active || false,
        keterangan: tahunAjaran?.keterangan || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('admin-cabang.tahun-ajaran.update', tahunAjaran.id));
    };

    return (
        <Layout title={`Edit Tahun Ajaran - ${tahunAjaran?.nama}`}>
            <div className="rounded-lg bg-white p-6 shadow-md">
                {errors.error && (
                    <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {errors.error}
                    </div>
                )}
                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Nama Tahun Ajaran <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={data.nama}
                                onChange={(e) => setData('nama', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                placeholder="2024/2025"
                                required
                            />
                            {errors.nama && <p className="mt-1 text-sm text-red-500">{errors.nama}</p>}
                        </div>

                        <div className="flex items-center">
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    checked={data.is_active}
                                    onChange={(e) => setData('is_active', e.target.checked)}
                                    className="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                />
                                <span className="ml-2 text-sm text-gray-700">Tahun Ajaran Aktif</span>
                            </label>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Mulai <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                value={data.tanggal_mulai}
                                onChange={(e) => setData('tanggal_mulai', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                required
                            />
                            {errors.tanggal_mulai && <p className="mt-1 text-sm text-red-500">{errors.tanggal_mulai}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Selesai <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                value={data.tanggal_selesai}
                                onChange={(e) => setData('tanggal_selesai', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                required
                            />
                            {errors.tanggal_selesai && <p className="mt-1 text-sm text-red-500">{errors.tanggal_selesai}</p>}
                        </div>

                        <div className="md:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan
                            </label>
                            <textarea
                                value={data.keterangan}
                                onChange={(e) => setData('keterangan', e.target.value)}
                                rows={3}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                placeholder="Catatan tambahan..."
                            />
                        </div>
                    </div>

                    <div className="mt-6 flex items-center justify-end space-x-3 border-t pt-6">
                        <Link
                            href={route('admin-cabang.tahun-ajaran.index')}
                            className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 disabled:opacity-50"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
};

export default TahunAjaranEdit;
