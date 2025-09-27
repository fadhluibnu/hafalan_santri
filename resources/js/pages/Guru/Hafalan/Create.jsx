import { Link } from '@inertiajs/react';
import { useMemo } from 'react';
import HafalanForm from '../components/HafalanForm';
import Layout from '../components/Layout';

const HafalanCreate = () => {
    const santri = useMemo(
        () => [
            { id: 1, nama: 'Ahmad Fauzi', kelas: 'Juz 30' },
            { id: 2, nama: 'Nur Aisyah', kelas: 'Juz 29' },
        ],
        [],
    );

    const onSubmit = (payload) => {
        alert(`Simpan setoran (dummy):\n${JSON.stringify(payload, null, 2)}`);
    };

    return (
        <Layout title="Input Setoran Hafalan">
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold text-gray-800">Input Setoran Hafalan</h1>
                    <Link href="/guru/hafalan" className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <HafalanForm santriList={santri} onSubmit={onSubmit} />
                </div>
            </div>
        </Layout>
    );
};

export default HafalanCreate;
