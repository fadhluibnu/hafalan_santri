import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '../components/Layout';

const SantriShow = ({ santri }) => {
    const data = santri || {};

    const labelJK = (jk) => (jk === 'L' ? 'Laki-laki' : jk === 'P' ? 'Perempuan' : '-');

    // Helper untuk orang tua
    const renderOrtu = (label, ortu) => (
        <div className="border rounded-lg p-4 bg-gray-50 mb-4">
            <h4 className="font-semibold mb-2">{label}</h4>
            <dl className="divide-y divide-gray-100">
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Nama</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.nama || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Status</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.status || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Status Hubungan</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.status_hubungan || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Tempat, Tanggal Lahir</dt>
                    <dd className="col-span-2 text-sm text-gray-900">
                        {(ortu?.tempat_lahir || '-') + ', ' + (ortu?.tanggal_lahir || '-')}
                    </dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Pendidikan</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.pendidikan || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Pekerjaan</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.pekerjaan || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Penghasilan</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.penghasilan || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Email</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.email || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Handphone</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.handphone || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Alamat</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{ortu?.alamat || '-'}</dd>
                </div>
            </dl>
        </div>
    );

    // Helper untuk kesehatan
    const renderKesehatan = (kesehatan) => (
        <div className="border rounded-lg p-4 bg-gray-50 mb-4">
            <h4 className="font-semibold mb-2">Kesehatan</h4>
            <dl className="divide-y divide-gray-100">
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Golongan Darah</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{kesehatan?.golongan_darah || '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Berat Badan (kg)</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{kesehatan?.berat_badan ?? '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Tinggi Badan (cm)</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{kesehatan?.tinggi_badan ?? '-'}</dd>
                </div>
                <div className="grid grid-cols-3 gap-4 py-2">
                    <dt className="text-sm text-gray-500">Riwayat Penyakit</dt>
                    <dd className="col-span-2 text-sm text-gray-900">{kesehatan?.riwayat_penyakit || '-'}</dd>
                </div>
            </dl>
        </div>
    );

    return (
        <Layout title={`Detail Santri - ${data.nama}`}>
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-6 shadow-md">
                    <dl className="divide-y divide-gray-200">
                        {[
                            ['Nama Lengkap', data.nama],
                            ['Nama Panggilan', data.panggilan || '-'],
                            ['Jenis Kelamin', labelJK(data.jenis_kelamin)],
                            ['Tempat, Tanggal Lahir', `${data.tempat_lahir || '-'}, ${data.tanggal_lahir || '-'}`],
                            ['Status Mukim', data.status_mukim || '-'],
                            ['Kondisi', data.kondisi || '-'],
                            ['Warga Negara', data.warga_negara || '-'],
                            ['Kode Pos', data.kode_pos || '-'],
                            ['Alamat', data.alamat || '-'],
                            ['Anak Ke', data.anak_ke ?? '-'],
                            ['Jumlah Saudara', data.jumlah_saudara ?? '-'],
                            ['Status Anak', data.status_anak || '-'],
                            ['Saudara Kandung', data.saudara_kandung ?? '-'],
                            ['Saudara Tiri', data.saudara_tiri ?? '-'],
                            ['Jarak ke Pondok (km)', data.jarak_pondok ?? '-'],
                            ['Telepon', data.telpon || '-'],
                            ['Handphone', data.handphone || '-'],
                            ['Email', data.email || '-'],
                            ['Hobi', data.hobi || '-'],
                            ['Pondok', data.pondok_nama || data.pondok_id || '-'],
                            ['Kelas', data.kelas_nama || data.kelas_id || '-'],
                        ].map(([label, value]) => (
                            <div key={label} className="grid grid-cols-3 gap-4 py-3">
                                <dt className="text-sm font-medium text-gray-500">{label}</dt>
                                <dd className="col-span-2 text-sm text-gray-900">{value}</dd>
                            </div>
                        ))}
                    </dl>
                </div>

                {/* Orang Tua & Wali */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {renderOrtu('Ayah', data.ayah)}
                    {renderOrtu('Ibu', data.ibu)}
                    {data.wali && data.wali.nama ? renderOrtu('Wali', data.wali) : null}
                </div>

                {/* Kesehatan */}
                {data.kesehatan && renderKesehatan(data.kesehatan)}

                <div className="flex items-center justify-end space-x-3">
                    <Link
                        href={route('admin-cabang.santri.edit', data.id)}
                        className="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-yellow-600"
                    >
                        Edit
                    </Link>
                    <Link
                        href={route('admin-cabang.santri.index')}
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
