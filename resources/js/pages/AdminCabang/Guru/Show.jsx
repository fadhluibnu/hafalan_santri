import { Link } from '@inertiajs/react';
import Layout from '../components/Layout';

const GuruShow = ({ guru }) => {
    const data = guru || {
        id: 1,
        nip: '1234567890',
        nama: 'Ustadz Rahman',
        gelar_awal: 'S.Pd',
        gelar_akhir: '',
        tempat_lahir: 'Bandung',
        tanggal_lahir: '1988-02-01',
        jenis_kelamin: 'L',
        status_menikah: 'Menikah',
        alamat: 'Jl. Contoh No.1',
        no_identitas: '3210...',
        no_telpon: '022-123456',
        no_handphone: '08123456789',
        email: 'rahman@example.com',
        tanggal_kerja: '2015-07-01',
        non_aktif: false,
        keterangan: 'Guru senior',
        pondok_id: 1,
        user_id: 1,
    };

    const labelJK = (v) => (v === 'L' ? 'Laki-laki' : v === 'P' ? 'Perempuan' : '-');

    return (
        <Layout title={`Detail Guru - ${data.nama}`}>
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-6 shadow-md">
                    <dl className="divide-y divide-gray-200">
                        {[
                            ['NIP', data.nip || '-'],
                            ['Nama', data.nama || '-'],
                            ['Gelar Awal', data.gelar_awal || '-'],
                            ['Gelar Akhir', data.gelar_akhir || '-'],
                            ['Tempat, Tanggal Lahir', `${data.tempat_lahir || '-'}, ${data.tanggal_lahir || '-'}`],
                            ['Jenis Kelamin', labelJK(data.jenis_kelamin)],
                            ['Status Menikah', data.status_menikah || '-'],
                            ['Alamat', data.alamat || '-'],
                            ['No Identitas', data.no_identitas || '-'],
                            ['No Telepon', data.no_telpon || '-'],
                            ['No Handphone', data.no_handphone || '-'],
                            ['Email', data.email || '-'],
                            ['Tanggal Mulai Kerja', data.tanggal_kerja || '-'],
                            ['Non Aktif', data.non_aktif ? 'Ya' : 'Tidak'],
                            ['Keterangan', data.keterangan || '-'],
                            ['Pondok ID', data.pondok_id ?? '-'],
                            ['User ID', data.user_id ?? '-'],
                        ].map(([label, value]) => (
                            <div key={label} className="grid grid-cols-3 gap-4 py-3">
                                <dt className="text-sm font-medium text-gray-500">{label}</dt>
                                <dd className="col-span-2 text-sm text-gray-900">{value}</dd>
                            </div>
                        ))}
                    </dl>
                </div>

                <div className="flex items-center justify-end space-x-3">
                    <Link
                        href={`/admin-cabang/guru/${data.id}/edit`}
                        className="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-yellow-600"
                    >
                        Edit
                    </Link>
                    <Link
                        href="/admin-cabang/guru"
                        className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </Layout>
    );
};

export default GuruShow;
