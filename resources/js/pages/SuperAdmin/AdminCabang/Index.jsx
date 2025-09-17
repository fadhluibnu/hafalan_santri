import React from "react";
import { Head, Link } from "@inertiajs/react";
import Layout from "../components/layouts";
import Table from "../components/Table";

const AdminCabangIndex = ({ adminCabangs }) => {
    // Static data for display purposes
    const adminCabangsData = adminCabangs || [
        {
            id: 1,
            user_id: 2,
            pondok_id: 1,
            name: "Ahmad Farhan",
            phone: "081234567890",
            jabatan: "Kepala Cabang",
            pondok: {
                id: 1,
                nama: "Pondok Tahfidz Al-Quran Baitul Hikmah"
            },
            user: {
                id: 2,
                email: "farhan@example.com"
            }
        },
        {
            id: 2,
            user_id: 3,
            pondok_id: 2,
            name: "Budi Santoso",
            phone: "081234567891",
            jabatan: "Kepala Cabang",
            pondok: {
                id: 2,
                nama: "Pondok Tahfidz Al-Furqon"
            },
            user: {
                id: 3,
                email: "budi@example.com"
            }
        },
        {
            id: 3,
            user_id: 4,
            pondok_id: 3,
            name: "Citra Dewi",
            phone: "081234567892",
            jabatan: "Kepala Cabang",
            pondok: {
                id: 3,
                nama: "Pondok Tahfidz Darul Quran"
            },
            user: {
                id: 4,
                email: "citra@example.com"
            }
        },
        {
            id: 4,
            user_id: 5,
            pondok_id: 1,
            name: "Dedi Purnama",
            phone: "081234567893",
            jabatan: "Wakil Kepala Cabang",
            pondok: {
                id: 1,
                nama: "Pondok Tahfidz Al-Quran Baitul Hikmah"
            },
            user: {
                id: 5,
                email: "dedi@example.com"
            }
        },
        {
            id: 5,
            user_id: 6,
            pondok_id: 2,
            name: "Eva Mulyana",
            phone: "081234567894",
            jabatan: "Wakil Kepala Cabang",
            pondok: {
                id: 2,
                nama: "Pondok Tahfidz Al-Furqon"
            },
            user: {
                id: 6,
                email: "eva@example.com"
            }
        }
    ];

    const columns = [
        {
            header: "Nama",
            accessor: "name",
        },
        {
            header: "Jabatan",
            accessor: "jabatan",
        },
        {
            header: "Kontak",
            accessor: "phone",
            render: (row) => (
                <div>
                    <div>{row.phone}</div>
                    <div className="text-gray-500 text-sm">{row.user.email}</div>
                </div>
            ),
        },
        {
            header: "Pondok",
            accessor: "pondok.nama",
            render: (row) => (
                <Link 
                    href={`/super-admin/pondok/${row.pondok_id}`}
                    className="text-indigo-600 hover:text-indigo-900"
                >
                    {row.pondok.nama}
                </Link>
            ),
        },
    ];

    return (
        <Layout title="Daftar Admin Cabang">
            <Head title="Daftar Admin Cabang" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold text-gray-900">Daftar Admin Cabang</h1>
                        <Link
                            href="/super-admin/admin-cabang/create"
                            className="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Tambah Admin Cabang
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex justify-between mb-4">
                                <div className="w-full sm:w-1/3">
                                    <input
                                        type="text"
                                        placeholder="Cari admin cabang..."
                                        className="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                                <div>
                                    <select
                                        className="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                    >
                                        <option value="">Semua Pondok</option>
                                        <option value="1">Pondok Tahfidz Al-Quran Baitul Hikmah</option>
                                        <option value="2">Pondok Tahfidz Al-Furqon</option>
                                        <option value="3">Pondok Tahfidz Darul Quran</option>
                                    </select>
                                </div>
                            </div>
                            
                            <Table
                                columns={columns}
                                data={adminCabangsData}
                                baseRoute="/super-admin/admin-cabang"
                            />

                            <div className="mt-4 flex justify-between">
                                <div className="text-sm text-gray-500">
                                    Menampilkan {adminCabangsData.length} dari {adminCabangsData.length} admin cabang
                                </div>
                                <div className="flex space-x-2">
                                    <button className="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Sebelumnya
                                    </button>
                                    <button className="px-3 py-1 bg-indigo-50 border border-indigo-500 rounded-md text-sm font-medium text-indigo-600 hover:bg-indigo-100">
                                        1
                                    </button>
                                    <button className="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Berikutnya
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default AdminCabangIndex;
