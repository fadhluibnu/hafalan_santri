import SummaryCard from '../SuperAdmin/components/SummaryCard';
import Layout from './components/Layout';
import { usePage, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';

// Ikon sederhana berbasis emoji/SVG inline agar tanpa dependensi tambahan
const IconCircle = ({ bg = 'bg-red-100 text-red-800', children }) => <div className={`rounded-full p-3 ${bg}`}>{children}</div>;

const Dashboard = () => {
    const { 
        jumlahSantri = 0, 
        jumlahUstadz = 0, 
        jumlahKelas = 0,
        jumlahTahunAjaran = 0,
        santriSudahDitempatkan = 0,
        santriBelumDitempatkan = 0,
        tahunAjaranAktif = '-',
        rekapHafalan = [], 
        statistikKelas = []
    } = usePage().props;

    return (
        <Layout title="Dashboard">
            <div className="space-y-6">
                {/* Info Tahun Ajaran Aktif - Red to Orange Gradient */}
                <div className="bg-gradient-to-r from-red-600 to-orange-500 rounded-lg p-4 text-white shadow-lg">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm opacity-90">Tahun Ajaran Aktif</p>
                            <h2 className="text-2xl font-bold">{tahunAjaranAktif}</h2>
                        </div>
                        <Link 
                            href={route('admin-cabang.tahun-ajaran.index')}
                            className="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition"
                        >
                            Kelola Tahun Ajaran
                        </Link>
                    </div>
                </div>

                {/* Summary Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <SummaryCard 
                        title="Jumlah Santri" 
                        count={jumlahSantri} 
                        color="red" 
                        icon={<IconCircle bg="bg-red-100 text-red-800"><span className="text-xl">👨‍🎓</span></IconCircle>} 
                    />
                    <SummaryCard
                        title="Jumlah Ustadz"
                        count={jumlahUstadz}
                        color="orange"
                        icon={<IconCircle bg="bg-orange-100 text-orange-800"><span className="text-xl">👩‍🏫</span></IconCircle>}
                    />
                    <SummaryCard
                        title="Jumlah Kelas"
                        count={jumlahKelas}
                        color="red"
                        icon={<IconCircle bg="bg-red-100 text-red-800"><span className="text-xl">🏫</span></IconCircle>}
                    />
                    <SummaryCard
                        title="Tahun Ajaran"
                        count={jumlahTahunAjaran}
                        color="orange"
                        icon={<IconCircle bg="bg-orange-100 text-orange-800"><span className="text-xl">📅</span></IconCircle>}
                    />
                </div>

                {/* Penempatan Santri */}
                <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div className="bg-white rounded-lg shadow-md p-5">
                        <h3 className="text-lg font-semibold text-gray-700 mb-4">Status Penempatan Santri</h3>
                        <div className="space-y-4">
                            <div className="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div className="flex items-center gap-3">
                                    <div className="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <span className="text-gray-700">Sudah Ditempatkan</span>
                                </div>
                                <span className="text-2xl font-bold text-red-600">{santriSudahDitempatkan}</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <div className="flex items-center gap-3">
                                    <div className="w-3 h-3 bg-orange-500 rounded-full"></div>
                                    <span className="text-gray-700">Belum Ditempatkan</span>
                                </div>
                                <span className="text-2xl font-bold text-orange-600">{santriBelumDitempatkan}</span>
                            </div>
                            <div className="mt-4 pt-4 border-t">
                                <div className="flex justify-between items-center">
                                    <span className="text-gray-500">Total Santri</span>
                                    <span className="text-xl font-bold text-gray-800">{jumlahSantri}</span>
                                </div>
                                <div className="mt-2 w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        className="bg-gradient-to-r from-red-500 to-orange-500 h-2 rounded-full transition-all duration-300" 
                                        style={{ width: `${jumlahSantri > 0 ? (santriSudahDitempatkan / jumlahSantri) * 100 : 0}%` }}
                                    ></div>
                                </div>
                                <p className="text-xs text-gray-500 mt-1">
                                    {jumlahSantri > 0 ? Math.round((santriSudahDitempatkan / jumlahSantri) * 100) : 0}% santri sudah ditempatkan
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Statistik Kelas */}
                    <div className="bg-white rounded-lg shadow-md p-5">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold text-gray-700">Statistik Kelas</h3>
                            <Link 
                                href={route('admin-cabang.struktur.kelas.index')} 
                                className="text-sm text-red-600 hover:text-red-700"
                            >
                                Lihat Semua →
                            </Link>
                        </div>
                        {statistikKelas.length === 0 ? (
                            <div className="text-center py-8 text-gray-500">
                                <p>Belum ada kelas di tahun ajaran ini</p>
                                <Link 
                                    href={route('admin-cabang.struktur.kelas.create')}
                                    className="mt-2 inline-block text-red-600 hover:text-red-700"
                                >
                                    + Buat Kelas Baru
                                </Link>
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {statistikKelas.map((kelas) => (
                                    <div key={kelas.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-red-50 transition">
                                        <div>
                                            <p className="font-medium text-gray-800">{kelas.nama}</p>
                                            <p className="text-xs text-gray-500">{kelas.tingkat}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-bold text-red-600">{kelas.terisi}/{kelas.kapasitas}</p>
                                            <p className="text-xs text-gray-500">santri</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Rekap Hafalan Terbaru */}
                <div className="bg-white rounded-lg shadow-md p-5">
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-lg font-semibold text-gray-700">Riwayat Hafalan Terbaru</h2>
                    </div>
                    {rekapHafalan.length === 0 ? (
                        <div className="text-center py-8 text-gray-500">
                            <p>Belum ada data hafalan</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-red-100">
                                        <th className="text-left py-3 px-2 text-sm font-medium text-gray-500">Santri</th>
                                        <th className="text-left py-3 px-2 text-sm font-medium text-gray-500">Jenis</th>
                                        <th className="text-left py-3 px-2 text-sm font-medium text-gray-500">Surah/Ayat</th>
                                        <th className="text-left py-3 px-2 text-sm font-medium text-gray-500">Nilai</th>
                                        <th className="text-left py-3 px-2 text-sm font-medium text-gray-500">Tanggal</th>
                                        <th className="text-left py-3 px-2 text-sm font-medium text-gray-500">Ustadz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rekapHafalan.map((item) => (
                                        <tr key={item.id} className="border-b hover:bg-red-50 transition">
                                            <td className="py-3 px-2">
                                                <div>
                                                    <p className="font-medium text-gray-800">{item.santri_nama}</p>
                                                    <p className="text-xs text-gray-500">{item.santri_nis}</p>
                                                </div>
                                            </td>
                                            <td className="py-3 px-2">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                                    item.jenis === 'Sabaq' ? 'bg-red-100 text-red-800' :
                                                    item.jenis === 'Sabqi' ? 'bg-orange-100 text-orange-800' :
                                                    'bg-yellow-100 text-yellow-800'
                                                }`}>
                                                    {item.jenis}
                                                </span>
                                            </td>
                                            <td className="py-3 px-2 text-sm text-gray-600">
                                                {item.dari_surah} ({item.dari_ayat}) - {item.sampai_surah} ({item.sampai_ayat})
                                            </td>
                                            <td className="py-3 px-2">
                                                <span className={`font-bold ${
                                                    item.nilai >= 80 ? 'text-red-600' :
                                                    item.nilai >= 60 ? 'text-orange-600' :
                                                    'text-gray-600'
                                                }`}>
                                                    {item.nilai}
                                                </span>
                                            </td>
                                            <td className="py-3 px-2 text-sm text-gray-600">{item.tanggal}</td>
                                            <td className="py-3 px-2 text-sm text-gray-600">{item.ustadz}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </Layout>
    );
};

export default Dashboard;
