import { Link, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import Layout from '../components/Layout';

const HafalanShow = ({ id }) => {
    const params = usePage().props || {};
    const recordId = id || params.id || 1;

    const data = useMemo(
        () => ({
            1: {
                id: 1,
                santri_id: 1,
                santriNama: 'Ahmad Fauzi',
                guru_id: 10,
                kelas_id: 3,
                tanggal_setor: '2025-09-20',
                juz: 30,
                dari_surat: 'An-Naba',
                dari_ayat: 1,
                sampai_surat: 'An-Naba',
                sampai_ayat: 10,
                kategori: 'ziadah',
                nilai: 'baik',
                catatan: 'lancar',
            },
            2: {
                id: 2,
                santri_id: 2,
                santriNama: 'Nur Aisyah',
                guru_id: 10,
                kelas_id: 2,
                tanggal_setor: '2025-09-20',
                juz: 29,
                dari_surat: 'Al-Mulk',
                dari_ayat: 1,
                sampai_surat: 'Al-Mulk',
                sampai_ayat: 15,
                kategori: 'murojaah',
                nilai: 'cukup',
                catatan: 'ada pengulangan',
            },
        }),
        [],
    );

    const d = data[recordId] || data[1];

    return (
        <Layout title="Detail Setoran Hafalan">
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold text-gray-800">Detail Setoran Hafalan</h1>
                    <div className="space-x-3">
                        <Link
                            href={`/guru/hafalan/${d.id}/edit`}
                            className="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-semibold text-white hover:bg-yellow-600"
                        >
                            Edit
                        </Link>
                        <Link href="/guru/hafalan" className="text-sm text-indigo-600 hover:underline">
                            Kembali
                        </Link>
                    </div>
                </div>
                <div className="rounded-lg bg-white p-6 shadow">
                    <dl className="divide-y divide-gray-200">
                        {[
                            ['Santri', d.santriNama],
                            ['Guru ID', d.guru_id],
                            ['Kelas ID', d.kelas_id || '-'],
                            ['Tanggal Setor', d.tanggal_setor],
                            ['Juz', d.juz],
                            ['Dari', `${d.dari_surat} ${d.dari_ayat}`],
                            ['Sampai', `${d.sampai_surat} ${d.sampai_ayat}`],
                            ['Kategori', d.kategori],
                            ['Nilai', d.nilai],
                            ['Catatan', d.catatan || '-'],
                        ].map(([label, value]) => (
                            <div key={label} className="grid grid-cols-3 gap-4 py-3">
                                <dt className="text-sm font-medium text-gray-500">{label}</dt>
                                <dd className="col-span-2 text-sm text-gray-900">{value}</dd>
                            </div>
                        ))}
                    </dl>
                </div>
            </div>
        </Layout>
    );
};

export default HafalanShow;
