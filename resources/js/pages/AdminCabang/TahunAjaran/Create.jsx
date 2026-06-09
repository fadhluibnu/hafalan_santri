import { Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const TahunAjaranCreate = () => {
    const { data, setData, post, processing, errors } = useForm({
        nama: '',
        semester: 'ganjil',
        tanggal_mulai: '',
        tanggal_selesai: '',
        is_active: false,
        keterangan: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('admin-cabang.tahun-ajaran.store'));
    };

    // Generate nama otomatis dari tanggal
    const generateNama = () => {
        if (data.tanggal_mulai && data.tanggal_selesai) {
            const startYear = new Date(data.tanggal_mulai).getFullYear();
            const endYear = new Date(data.tanggal_selesai).getFullYear();
            setData('nama', `${startYear}/${endYear}`);
        }
    };

    return (
        <Layout title="Buat Tahun Ajaran">
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
                            <div className="flex gap-2">
                                <input
                                    type="text"
                                    value={data.nama}
                                    onChange={(e) => setData('nama', e.target.value)}
                                    className="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                    placeholder="2024/2025"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={generateNama}
                                    className="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm"
                                >
                                    Auto
                                </button>
                            </div>
                            {errors.nama && <p className="mt-1 text-sm text-red-500">{errors.nama}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Semester <span className="text-red-500">*</span>
                            </label>
                            <select
                                value={data.semester}
                                onChange={(e) => setData('semester', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                required
                            >
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                            {errors.semester && <p className="mt-1 text-sm text-red-500">{errors.semester}</p>}
                        </div>

                        <div className="flex items-center">
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    checked={data.is_active}
                                    onChange={(e) => setData('is_active', e.target.checked)}
                                    className="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                />
                                <span className="ml-2 text-sm text-gray-700">Aktifkan langsung</span>
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
};

export default TahunAjaranCreate;
