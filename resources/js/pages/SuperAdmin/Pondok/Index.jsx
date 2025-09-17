import React from "react";
import { Head, Link } from "@inertiajs/react";
import Layout from "../components/layouts";
import Table from "../components/Table";

const PondokIndex = ({ pondoks }) => {
    console.log(pondoks);
    // Static data for display purposes
    const pondoksData = pondoks.data

    const columns = [
        {
            header: "Nama Pondok",
            accessor: "nama",
            render: (row) => (
                <div className="flex items-center">
                    <div className="h-10 w-10 flex-shrink-0">
                        <img
                            className="h-10 w-10 rounded-full object-cover"
                            src={`/storage/${row.logo}` || "/logo.svg"}
                            alt={row.nama}
                        />
                    </div>
                    <div className="ml-4">
                        <div className="font-medium text-gray-900">{row.nama}</div>
                        <div className="text-gray-500">Est. {row.tahun_berdiri}</div>
                    </div>
                </div>
            ),
        },
        {
            header: "Alamat",
            accessor: "alamat",
        },
        {
            header: "Kontak",
            accessor: "telepon",
            render: (row) => (
                <div>
                    <div>{row.telepon}</div>
                    <div className="text-gray-500">{row.email}</div>
                </div>
            ),
        },
    ];

    return (
        <Layout title="Daftar Pondok">
            <Head title="Daftar Pondok" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold text-gray-900">Daftar Pondok</h1>
                        <Link
                            href="/super-admin/pondok/create"
                            className="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Tambah Pondok
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="mb-4">
                                <input
                                    type="text"
                                    placeholder="Cari nama pondok..."
                                    className="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                            
                            <Table
                                columns={columns}
                                data={pondoksData}
                                baseRoute="/super-admin/pondok"
                            />

                            <div className="mt-4 flex justify-between">
                                <div className="text-sm text-gray-500">
                                    Menampilkan {pondoksData.length} dari {pondoksData.length} pondok
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

export default PondokIndex;
