import { useMemo } from 'react';
import SummaryCard from '../SuperAdmin/components/SummaryCard';
import Layout from './components/Layout';

const Icon = ({ children, className = '' }) => <div className={`flex h-8 w-8 items-center justify-center ${className}`}>{children}</div>;

const GuruDashboard = () => {
    const santri = useMemo(
        () => [
            { id: 1, nama: 'Ahmad Fauzi', kelas: 'Juz 30' },
            { id: 2, nama: 'Nur Aisyah', kelas: 'Juz 29' },
        ],
        [],
    );

    const today = new Date().toISOString().slice(0, 10);
    const hafalan = useMemo(
        () => [
            {
                id: 1,
                santriId: 1,
                juz: 30,
                surah: 'An-Naba',
                ayatAwal: 1,
                ayatAkhir: 10,
                kategori: 'ziadah',
                penilaian: 'baik',
                catatan: 'lancar',
                tanggal: today,
            },
            {
                id: 2,
                santriId: 2,
                juz: 29,
                surah: 'Al-Mulk',
                ayatAwal: 1,
                ayatAkhir: 15,
                kategori: 'murojaah',
                penilaian: 'cukup',
                catatan: 'ada pengulangan',
                tanggal: today,
            },
        ],
        [today],
    );

    const jumlahSantri = santri.length;
    const setoranHariIni = hafalan.filter((h) => h.tanggal === today).length;
    const totalJuzSah = hafalan.filter((h) => h.penilaian === 'baik').length; // dummy aggregate

    return (
        <Layout title="Dashboard Guru">
            <div className="space-y-6">
                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <SummaryCard title="Santri Binaan" count={jumlahSantri} color="blue" icon={<Icon className="text-blue-700">👥</Icon>} />
                    <SummaryCard title="Setoran Hari Ini" count={setoranHariIni} color="green" icon={<Icon className="text-green-700">📝</Icon>} />
                    <SummaryCard title="Total Juz Sah" count={totalJuzSah} color="orange" icon={<Icon className="text-orange-700">✅</Icon>} />
                </div>
            </div>
        </Layout>
    );
};

export default GuruDashboard;
