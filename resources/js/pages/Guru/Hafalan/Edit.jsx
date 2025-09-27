import { Link, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import HafalanForm from '../components/HafalanForm';
import Layout from '../components/Layout';

const HafalanEdit = ({ id }) => {
    const params = usePage().props || {};
    const recordId = id || params.id || 1;

    const santri = useMemo(
        () => [
            { id: 1, nama: 'Ahmad Fauzi', kelas: 'Juz 30' },
            { id: 2, nama: 'Nur Aisyah', kelas: 'Juz 29' },
        ],
        [],
    );

    const data = useMemo(
        () => ({
            1: {
                id: 1,
                santri_id: 1,
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

    const initial = data[recordId] || data[1];

    const onSubmit = (payload) => {
        alert(`Update setoran #${recordId} (dummy):\n${JSON.stringify(payload, null, 2)}`);
    };

    return (
        <Layout title="Edit Setoran Hafalan">
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold text-gray-800">Edit Setoran Hafalan</h1>
                    <Link href="/guru/hafalan" className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <HafalanForm initial={initial} santriList={santri} onSubmit={onSubmit} />
                </div>
            </div>
        </Layout>
    );
};

export default HafalanEdit;
