import React from "react";
import { Head, Link } from "@inertiajs/react";
import Layout from "../components/layouts";
const PondokShow = ({ pondok }) => {
    console.log(pondok);
    // Static data for display (simulating a pondok received from props)
    const pondokData = pondok
    // || {
    //     id: 2,
    //     nama: "Pondok Tahfidz Al-Furqon",
    //     alamat: "Jl. Pahlawan No. 45, Bandung",
    //     telepon: "022-5551234",
    //     email: "alfurqon@example.com",
    //     website: "www.alfurqon.or.id",
    //     logo: "/storage/logos/alfurqon.png",
    //     deskripsi: "Pondok pesantren modern dengan fokus tahfidz Al-Quran yang didirikan pada tahun 2015. Memiliki program unggulan tahfidz 30 juz dalam 3 tahun dengan metode pembelajaran inovatif dan terintegrasi.",
    //     tahun_berdiri: 2015,
    //     // Additional data for display purposes
    //     stats: {
    //         adminCabang: 3,
    //         guru: 12,
    //         santri: 145,
    //         kelas: 8,
    //     }
    // };

    // Stats cards for this pondok
    const statsCards = [
        // { name: "Admin Cabang", value: pondokData.stats.adminCabang },
        { name: "Guru", value: pondokData.gurus.length },
        { name: "Santri", value: pondokData.santris.length },
        { name: "Kelas", value: pondokData.kelas.length },
    ];

    return (
        <Layout title={`Detail Pondok: ${pondokData.nama}`}>
            <Head title={`Pondok ${pondokData.nama}`} />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <Link
                            href="/super-admin/pondok"
                            className="text-indigo-600 hover:text-indigo-900 flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Daftar Pondok
                        </Link>
                    </div>

                    {/* Header with logo and basic info */}
                    <div className="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                            <div className="flex items-center">
                                <div className="flex-shrink-0 h-20 w-20">
                                    <img
                                        className="h-20 w-20 rounded-full object-cover border-2 border-gray-200"
                                        src={`/storage/${pondokData.logo}` || "/logo.svg"}
                                        alt={pondokData.nama}
                                    />
                                </div>
                                <div className="ml-4">
                                    <h1 className="text-2xl font-bold text-gray-900">{pondokData.nama}</h1>
                                    <p className="text-sm text-gray-500">Didirikan tahun {pondokData.tahun_berdiri}</p>
                                </div>
                            </div>
                            <div>
                                <Link
                                    href={`/super-admin/pondok/${pondokData.id}/edit`}
                                    className="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    Edit Pondok
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Stats Overview */}
                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                        {statsCards.map((stat) => (
                            <div key={stat.name} className="bg-white overflow-hidden shadow rounded-lg">
                                <div className="px-4 py-5 sm:p-6">
                                    <dt className="text-sm font-medium text-gray-500 truncate">{stat.name}</dt>
                                    <dd className="mt-1 text-3xl font-semibold text-gray-900">{stat.value}</dd>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Detailed Information */}
                    <div className="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div className="px-4 py-5 sm:px-6">
                            <h3 className="text-lg leading-6 font-medium text-gray-900">Informasi Detail Pondok</h3>
                            <p className="mt-1 max-w-2xl text-sm text-gray-500">Data lengkap tentang pondok.</p>
                        </div>
                        <div className="border-t border-gray-200">
                            <dl>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{pondokData.nama}</dd>
                                </div>
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Alamat</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{pondokData.alamat}</dd>
                                </div>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Email</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{pondokData.email}</dd>
                                </div>
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Telepon</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{pondokData.telepon}</dd>
                                </div>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Website</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <a href={`${pondokData.website}`} target="_blank" rel="noopener noreferrer" className="text-indigo-600 hover:text-indigo-900">
                                            {pondokData.website}
                                        </a>
                                    </dd>
                                </div>
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Deskripsi</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{pondokData.deskripsi}</dd>
                                </div>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Tahun Berdiri</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{pondokData.tahun_berdiri}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default PondokShow;
