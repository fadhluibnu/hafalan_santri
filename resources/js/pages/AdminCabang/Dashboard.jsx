import SummaryCard from '../SuperAdmin/components/SummaryCard';
import Layout from './components/Layout';
import RekapTable from './components/RekapTable';
import { usePage } from '@inertiajs/react';

// Ikon sederhana berbasis emoji/SVG inline agar tanpa dependensi tambahan
const IconCircle = ({ bg = 'bg-blue-100 text-blue-800', children }) => <div className={`rounded-full p-3 ${bg}`}>{children}</div>;

const Dashboard = () => {
    const { jumlahSantri = 0, jumlahGuru = 0, totalJuzSah = 0, rekapData = [] } = usePage().props;

    return (
        <Layout title="Dashboard Admin Cabang">
            <div className="space-y-6">
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <SummaryCard title="Jumlah Santri" count={jumlahSantri} color="blue" icon={<IconCircle>👨‍🎓</IconCircle>} />
                    <SummaryCard
                        title="Jumlah Guru"
                        count={jumlahGuru}
                        color="green"
                        icon={<IconCircle bg="bg-green-100 text-green-800">👩‍🏫</IconCircle>}
                    />
                    <SummaryCard
                        title="Total Juz Sah"
                        count={totalJuzSah}
                        color="purple"
                        icon={<IconCircle bg="bg-purple-100 text-purple-800">📖</IconCircle>}
                    />
                </div>

                <div className="rounded-lg bg-white p-5 shadow-md">
                    <div className="mb-4 flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-gray-700">Rekap Hafalan Terbaru</h2>
                    </div>
                    <RekapTable data={rekapData} onSend={(row) => { alert(`Mengirim data ${row.nama} (Juz ${row.juz}) ke SuperAdmin...`); }} />
                </div>
            </div>
        </Layout>
    );
};

export default Dashboard;
//                 </div>

//                 <div className="rounded-lg bg-white p-5 shadow-md">
//                     <div className="mb-4 flex items-center justify-between">
//                         <h2 className="text-lg font-semibold text-gray-700">Rekap Hafalan Terbaru</h2>
//                     </div>
//                     <RekapTable data={rekapData} onSend={handleSend} />
//                 </div>
//             </div>
//         </Layout>
//     );
// };

// export default Dashboard;
