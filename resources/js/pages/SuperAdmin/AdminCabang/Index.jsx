import React from "react";
import { Head, Link, usePage } from "@inertiajs/react";
import Layout from "../components/layouts";
import Table from "../components/Table";

const AdminCabangIndex = ({ adminCabangs }) => {
    const { flash } = usePage().props;

    const adminCabangsData = adminCabangs.data;

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

    // Pagination component
    const Pagination = ({ links }) => (
        <nav className="flex space-x-2">
            {links.map((link, idx) => {
                // Remove html tags from label
                const label = link.label.replace(/(<([^>]+)>)/gi, "");
                if (!link.url) {
                    return (
                        <span
                            key={idx}
                            className="px-3 py-1 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-400 cursor-not-allowed"
                            dangerouslySetInnerHTML={{ __html: label }}
                        />
                    );
                }
                return (
                    <Link
                        key={idx}
                        href={link.url}
                        preserveScroll
                        className={`px-3 py-1 border rounded-md text-sm font-medium ${
                            link.active
                                ? "bg-indigo-50 border-indigo-500 text-indigo-600"
                                : "bg-white border-gray-300 text-gray-700 hover:bg-gray-50"
                        }`}
                        dangerouslySetInnerHTML={{ __html: label }}
                    />
                );
            })}
        </nav>
    );

    return (
        <Layout title="Daftar Admin Cabang">
            <Head title="Daftar Admin Cabang" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {flash.success && (
                        <div className="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {flash.success}
                        </div>
                    )}
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

                            <div className="mt-4 flex justify-between items-center">
                                <div className="text-sm text-gray-500">
                                    Menampilkan {adminCabangs.from} - {adminCabangs.to} dari {adminCabangs.total} admin cabang
                                </div>
                                <Pagination links={adminCabangs.links} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default AdminCabangIndex;
