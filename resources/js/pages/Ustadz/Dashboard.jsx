import SummaryCard from '../SuperAdmin/components/SummaryCard';
import Layout from './components/Layout';

const Icon = ({ children, className = '' }) => <div className={`flex h-8 w-8 items-center justify-center ${className}`}>{children}</div>;

const UstadzDashboard = ({ jumlahSantri = 0, setoranHariIni = 0 }) => {
    return (
        <Layout title="Dashboard Ustadz">
            <div className="space-y-6">
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SummaryCard title="Santri Binaan" count={jumlahSantri} color="blue" icon={<Icon className="text-blue-700">👥</Icon>} />
                    <SummaryCard title="Setoran Hari Ini" count={setoranHariIni} color="green" icon={<Icon className="text-green-700">📝</Icon>} />
                </div>
            </div>
        </Layout>
    );
};

export default UstadzDashboard;

