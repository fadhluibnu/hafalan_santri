import { Head, Link, usePage } from "@inertiajs/react";
import Layout from "../components/layouts";
import Table from "../components/Table";

const PondokIndex = ({ pondoks }) => {

    const { flash } = usePage().props;

    const pondoksData = pondoks.data;

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
                    {flash.success && (
                        <div className="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {flash.success}
                        </div>
                    )}
                    
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

                            <div className="mt-4 flex justify-between items-center">
                                <div className="text-sm text-gray-500">
                                    Menampilkan {pondoks.from} sampai {pondoks.to} dari {pondoks.total} pondok
                                </div>
                                <div className="flex space-x-2">
                                    {pondoks.links.map((link, idx) => {
                                        // Remove html entities from label for Previous/Next
                                        const label = link.label.replace(/&laquo;|&raquo;|<[^>]+>/g, match => {
                                            if (match === "&laquo;") return "«";
                                            if (match === "&raquo;") return "»";
                                            return "";
                                        }).trim() || link.label;

                                        return link.url ? (
                                            <Link
                                                key={idx}
                                                href={link.url}
                                                preserveScroll
                                                className={`px-3 py-1 border rounded-md text-sm font-medium ${link.active
                                                        ? "bg-indigo-50 border-indigo-500 text-indigo-600"
                                                        : "bg-white border-gray-300 text-gray-700 hover:bg-gray-50"
                                                    }`}
                                                disabled={link.url === null}
                                            >
                                                {label}
                                            </Link>
                                        ) : (
                                            <span
                                                key={idx}
                                                className="px-3 py-1 border border-gray-200 rounded-md text-sm font-medium text-gray-400 cursor-not-allowed"
                                            >
                                                {label}
                                            </span>
                                        );
                                    })}
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