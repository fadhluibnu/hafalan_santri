import SummaryCard from '../SuperAdmin/components/SummaryCard';
import Layout from './components/Layout';

const Icon = ({ children, className = '' }) => <div className={`flex h-8 w-8 items-center justify-center ${className}`}>{children}</div>;

const UstadzDashboard = ({
    ustadz = {},
    jumlahSantri = 0,
    setoranHariIni = 0,
    setoranBulanIni = 0,
    totalSetoran = 0,
    statistikNilai = {},
    kelasWali = [],
    rekapSetoran = [],
    chartData = [],
    tahunAjaranAktif = '-',
}) => {
    return (
        <Layout title="Dashboard Ustadz">
            <div className="space-y-6">
                {/* Welcome Section */}
                <div className="rounded-lg bg-gradient-to-r from-red-600 to-orange-500 p-6 text-white shadow-lg">
                    <h1 className="text-2xl font-bold">Assalamu'alaikum, {ustadz.nama}</h1>
                    <p className="mt-1 text-orange-100">Tahun Ajaran: {tahunAjaranAktif}</p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <SummaryCard title="Total Santri" count={jumlahSantri} color="red" icon={<Icon className="text-red-700">👥</Icon>} />
                    <SummaryCard title="Setoran Hari Ini" count={setoranHariIni} color="orange" icon={<Icon className="text-orange-700">📝</Icon>} />
                    <SummaryCard title="Setoran Bulan Ini" count={setoranBulanIni} color="amber" icon={<Icon className="text-amber-700">📅</Icon>} />
                    <SummaryCard title="Total Setoran" count={totalSetoran} color="rose" icon={<Icon className="text-rose-700">📊</Icon>} />
                </div>

                {/* Charts and Stats Row */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Grafik Setoran 7 Hari Terakhir */}
                    <div className="rounded-lg bg-white p-6 shadow-md">
                        <h2 className="mb-4 text-lg font-semibold text-gray-700">Setoran 7 Hari Terakhir</h2>
                        <div className="flex items-end justify-between space-x-2" style={{ height: '150px' }}>
                            {chartData.map((item, idx) => {
                                const maxValue = Math.max(...chartData.map(d => d.jumlah), 1);
                                const height = (item.jumlah / maxValue) * 100;
                                return (
                                    <div key={idx} className="flex flex-col items-center flex-1">
                                        <span className="mb-1 text-xs font-semibold text-gray-600">{item.jumlah}</span>
                                        <div
                                            className="w-full rounded-t bg-gradient-to-t from-red-500 to-orange-400 transition-all duration-300"
                                            style={{ height: `${Math.max(height, 5)}%`, minHeight: '4px' }}
                                        />
                                        <span className="mt-2 text-xs text-gray-500">{item.tanggal}</span>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Statistik Nilai */}
                    <div className="rounded-lg bg-white p-6 shadow-md">
                        <h2 className="mb-4 text-lg font-semibold text-gray-700">Statistik Nilai Setoran</h2>
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center space-x-3">
                                    <div className="h-4 w-4 rounded-full bg-green-500" />
                                    <span className="text-gray-600">Nilai A/B (Baik)</span>
                                </div>
                                <span className="text-xl font-bold text-green-600">{statistikNilai.baik || 0}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center space-x-3">
                                    <div className="h-4 w-4 rounded-full bg-yellow-500" />
                                    <span className="text-gray-600">Nilai C (Cukup)</span>
                                </div>
                                <span className="text-xl font-bold text-yellow-600">{statistikNilai.cukup || 0}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center space-x-3">
                                    <div className="h-4 w-4 rounded-full bg-red-500" />
                                    <span className="text-gray-600">Nilai D (Kurang)</span>
                                </div>
                                <span className="text-xl font-bold text-red-600">{statistikNilai.kurang || 0}</span>
                            </div>
                            {/* Progress Bar */}
                            <div className="mt-4">
                                <div className="flex h-4 overflow-hidden rounded-full bg-gray-200">
                                    {(() => {
                                        const total = (statistikNilai.baik || 0) + (statistikNilai.cukup || 0) + (statistikNilai.kurang || 0);
                                        if (total === 0) return <div className="w-full bg-gray-300" />;
                                        return (
                                            <>
                                                <div className="bg-green-500" style={{ width: `${((statistikNilai.baik || 0) / total) * 100}%` }} />
                                                <div className="bg-yellow-500" style={{ width: `${((statistikNilai.cukup || 0) / total) * 100}%` }} />
                                                <div className="bg-red-500" style={{ width: `${((statistikNilai.kurang || 0) / total) * 100}%` }} />
                                            </>
                                        );
                                    })()}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Kelas Wali Section */}
                {kelasWali.length > 0 && (
                    <div className="rounded-lg bg-white p-6 shadow-md">
                        <h2 className="mb-4 text-lg font-semibold text-gray-700">Kelas yang Anda Walikan</h2>
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {kelasWali.map((kelas) => (
                                <div key={kelas.id} className="rounded-lg border border-gray-200 bg-gradient-to-br from-orange-50 to-red-50 p-4">
                                    <h3 className="text-lg font-bold text-red-700">{kelas.nama}</h3>
                                    <p className="text-sm text-gray-600">Tingkat: {kelas.tingkat}</p>
                                    <div className="mt-3 flex items-center justify-between">
                                        <span className="text-sm text-gray-500">Santri</span>
                                        <span className="text-lg font-bold text-orange-600">
                                            {kelas.jumlah_santri}/{kelas.kapasitas}
                                        </span>
                                    </div>
                                    <div className="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            className="h-full rounded-full bg-gradient-to-r from-red-500 to-orange-500"
                                            style={{ width: `${Math.min((kelas.jumlah_santri / (kelas.kapasitas || 1)) * 100, 100)}%` }}
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Rekap Setoran Terbaru */}
                <div className="rounded-lg bg-white p-6 shadow-md">
                    <h2 className="mb-4 text-lg font-semibold text-gray-700">Setoran Terbaru yang Anda Terima</h2>
                    {rekapSetoran.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gradient-to-r from-red-50 to-orange-50">
                                    <tr>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-red-700">Santri</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-red-700">Juz</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-red-700">Surat</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-red-700">Kategori</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-red-700">Nilai</th>
                                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-red-700">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 bg-white">
                                    {rekapSetoran.map((setoran, idx) => (
                                        <tr key={setoran.id} className={idx % 2 === 0 ? 'bg-white' : 'bg-orange-50/30'}>
                                            <td className="whitespace-nowrap px-4 py-3">
                                                <div className="text-sm font-medium text-gray-900">{setoran.santri_nama}</div>
                                                <div className="text-xs text-gray-500">{setoran.santri_nis}</div>
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-700">Juz {setoran.juz}</td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {setoran.dari_surah} - {setoran.sampai_surah}
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3">
                                                <span className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                                                    setoran.kategori === 'Sabaq' ? 'bg-red-100 text-red-800' :
                                                    setoran.kategori === 'Sabqi' ? 'bg-orange-100 text-orange-800' :
                                                    'bg-amber-100 text-amber-800'
                                                }`}>
                                                    {setoran.kategori}
                                                </span>
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3">
                                                <span className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                                                    setoran.nilai === 'A' || setoran.nilai === 'B' ? 'bg-green-100 text-green-800' :
                                                    setoran.nilai === 'C' ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-red-100 text-red-800'
                                                }`}>
                                                    {setoran.nilai}
                                                </span>
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{setoran.tanggal}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="text-center text-gray-500 py-8">Belum ada data setoran</p>
                    )}
                </div>
            </div>
        </Layout>
    );
};

export default UstadzDashboard;
