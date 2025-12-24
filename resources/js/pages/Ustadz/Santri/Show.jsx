import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const SantriShow = ({ santri }) => {
    const data = santri || {};

    const labelJK = (jk) => (jk === 'L' ? 'Laki-laki' : jk === 'P' ? 'Perempuan' : '-');

    const renderOrtu = (label, ortu) => (
        <div className="border rounded-lg p-4 bg-gray-50 mb-4">
            <h4 className="font-semibold mb-2">{label}</h4>
            <dl className="divide-y divide-gray-100">
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Nama</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.nama || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Pekerjaan</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.pekerjaan || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Handphone</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.handphone || '-'}</dd>
                </div>
            </dl>
        </div>
    );

    return (
        <Layout title={`Detail Santri - ${data.nama}`}>
            <div className="space-y-6">
                {/* Data Diri */}
                <div className="bg-white rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Data Diri</h3>
                    <dl className="divide-y divide-gray-100">
                        {[
                            ['NIS', data.nis],
                            ['Nama Lengkap', data.nama],
                            ['Nama Panggilan', data.panggilan],
                            ['Jenis Kelamin', labelJK(data.jenis_kelamin)],
                            ['Tempat, Tanggal Lahir', `${data.tempat_lahir || '-'}, ${data.tanggal_lahir || '-'}`],
                            ['Alamat', data.alamat],
                            ['Status Mukim', data.status_mukim],
                            ['Kelas', data.kelas],
                            ['Pondok', data.pondok],
                        ].map(([label, value]) => (
                            <div key={label} className="grid grid-cols-3 gap-4 py-3">
                                <dt className="text-sm font-medium text-gray-500">{label}</dt>
                                <dd className="col-span-2 text-sm text-gray-900">{value || '-'}</dd>
                            </div>
                        ))}
                    </dl>
                </div>

                {/* Orang Tua */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {renderOrtu('Ayah', data.ayah)}
                    {renderOrtu('Ibu', data.ibu)}
                </div>

                {/* Actions */}
                <div className="flex items-center justify-end space-x-3">
                    <Link
                        href={route('ustadz.santri.hafalan', data.nis)}
                        className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700"
                    >
                        Lihat Hafalan
                    </Link>
                    <Link
                        href={route('ustadz.santri.index')}
                        className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </Layout>
    );
};

export default SantriShow;
