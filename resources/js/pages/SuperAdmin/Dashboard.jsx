import React from "react";
import { Head, Link } from "@inertiajs/react";
import Layout from "./components/layouts";
import SummaryCard from "./components/SummaryCard";

// Icons for summary cards
const BuildingIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
    </svg>
);

const UserIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
);

const TeacherIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292V15M4 10v2a2 2 0 002 2h12a2 2 0 002-2v-2M6 20h12a2 2 0 002-2v-1M6 20h12a2 2 0 01-2-2v-1" />
    </svg>
);

const StudentIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    </svg>
);

const Dashboard = ({ summary }) => {
    // Static data for display purposes
    console.log(summary);
    const stats = summary 
    // || {
    //     pondokCount: 8,
    //     adminCabangCount: 15,
    //     guruCount: 42,
    //     santriCount: 560
    // };

    // Recent activity data (static)
    const recentActivities = [
        {
            id: 1,
            title: "Pondok Baru Ditambahkan",
            description: "Pondok Tahfidz Al-Quran Baitul Hikmah",
            time: "1 jam yang lalu"
        },
        {
            id: 2,
            title: "Admin Cabang Baru",
            description: "Ahmad Farhan - Pondok Tahfidz Al-Quran",
            time: "3 jam yang lalu"
        },
        {
            id: 3,
            title: "Guru Baru Ditambahkan",
            description: "Ustadz Muhammad Ridwan - Pondok Tahfidz Al-Furqon",
            time: "5 jam yang lalu"
        },
        {
            id: 4,
            title: "15 Santri Baru Terdaftar",
            description: "Pondok Tahfidz Darul Quran",
            time: "1 hari yang lalu"
        }
    ];

    return (
        <Layout title="Dashboard Super Admin">
            <Head title="Dashboard Super Admin" />
            
            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    
                    <div className="mt-6">
                        <div className="text-gray-500 mb-4">
                            <p className="text-lg">Selamat datang di Panel Super Admin Sistem Hafalan Santri.</p>
                            <p className="text-sm">Tanggal: {new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        
                        {/* Summary Cards */}
                        <div className="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <Link href="/super-admin/pondok">
                                <SummaryCard 
                                    title="Total Pondok" 
                                    count={stats.pondokCount} 
                                    icon={<BuildingIcon />}
                                    color="blue" 
                                />
                            </Link>
                            
                            <Link href="/super-admin/admin-cabang">
                                <SummaryCard 
                                    title="Admin Cabang" 
                                    count={stats.adminCabangCount} 
                                    icon={<UserIcon />}
                                    color="green" 
                                />
                            </Link>
                            
                            <SummaryCard 
                                title="Guru" 
                                count={stats.guruCount} 
                                icon={<TeacherIcon />}
                                color="orange" 
                            />
                            
                            <SummaryCard 
                                title="Santri" 
                                count={stats.santriCount} 
                                icon={<StudentIcon />}
                                color="purple" 
                            />
                        </div>
                        
                        {/* Recent Activity Section */}
                        {/* <div className="mt-8">
                            <div className="flex justify-between items-center">
                                <h2 className="text-lg font-medium text-gray-900">Aktivitas Terkini</h2>
                                <button className="text-sm text-indigo-600 hover:text-indigo-900">
                                    Lihat Semua
                                </button>
                            </div>
                            <div className="mt-4 bg-white shadow overflow-hidden sm:rounded-lg">
                                <div className="divide-y divide-gray-200">
                                    {recentActivities.map((activity) => (
                                        <div key={activity.id} className="px-4 py-4 sm:px-6">
                                            <div className="flex justify-between">
                                                <div>
                                                    <p className="font-medium text-gray-800">{activity.title}</p>
                                                    <p className="text-sm text-gray-500">{activity.description}</p>
                                                </div>
                                                <span className="text-sm text-gray-500">{activity.time}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                         */}
                        {/* Quick Stats */}
                        {/* <div className="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div className="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div className="px-4 py-5 sm:p-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Statistik Hafalan</h3>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Santri dengan hafalan lengkap</span>
                                            <span className="font-medium">23</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Santri dengan hafalan 20+ juz</span>
                                            <span className="font-medium">58</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Santri dengan hafalan 10+ juz</span>
                                            <span className="font-medium">127</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Rata-rata juz per santri</span>
                                            <span className="font-medium">7.5</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div className="px-4 py-5 sm:p-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Perkembangan Pondok</h3>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Pondok didirikan tahun ini</span>
                                            <span className="font-medium">3</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Total guru baru bulan ini</span>
                                            <span className="font-medium">12</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Peningkatan santri (YoY)</span>
                                            <span className="font-medium text-green-600">+15%</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-gray-500">Pondok dengan pertumbuhan tertinggi</span>
                                            <span className="font-medium">Darul Quran</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> */}
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Dashboard;
