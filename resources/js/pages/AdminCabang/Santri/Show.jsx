import { Link } from '@inertiajs/react';
import Layout from '../components/Layout';

const SantriShow = ({ santri }) => {
    const data = santri || {
        id: 1,
        nama: 'Ahmad Fauzi',
        panggilan: 'Ahmad',
        jenis_kelamin: 'L',
        tempat_lahir: 'Bandung',
        tanggal_lahir: '2010-01-01',
        status_mukim: 'Mukim',
        kondisi: 'Sehat',
        warga_negara: 'Indonesia',
        kode_pos: '40111',
        alamat: 'Jl. Contoh No. 123',
        anak_ke: 1,
        jumlah_saudara: 3,
        status_anak: 'Kandung',
        saudara_kandung: 2,
        saudara_tiri: 1,
        jarak_pondok: 5,
        telpon: '022123456',
        handphone: '081234567890',
        email: 'ahmad@example.com',
        hobi: 'Membaca',
        pondok_id: 1,
        kelas_id: 1,
    };

    const labelJK = (jk) => (jk === 'L' ? 'Laki-laki' : jk === 'P' ? 'Perempuan' : '-');

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
                            ['Pondok ID', data.pondok_id ?? '-'],
                            ['Kelas ID', data.kelas_id ?? '-'],
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
                        href={`/admin-cabang/santri/${data.id}/edit`}
                        className="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-yellow-600"
                    >
                        Edit
                    </Link>
                    <Link
                        href="/admin-cabang/santri"
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
