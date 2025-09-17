import React from "react";
import { Head, Link, useForm, router } from "@inertiajs/react";
import Layout from "../components/layouts";

const AdminCabangShow = ({ adminCabang }) => {
    // Static data for display (simulating an admin cabang received from props)
    console.log(adminCabang);
    const adminCabangData = adminCabang

    // Format dates for display
    const formatDate = (dateString) => {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };

    const { data, setData, post, processing, errors } = useForm({
        name: adminCabangData.name,
        email: adminCabangData.user.email,
        username: adminCabangData.user.username,
        phone: adminCabangData.phone,
        jabatan: adminCabangData.jabatan,
        pondok_id: adminCabangData.pondok_id,
        password: "",
        _method: "PUT",
    });

    const handleStatus = (param) => {
        console.log(param);
        router.post(route("super-admin.admin-cabang.update", adminCabangData.id), {
            ...data,
            status: param
        });
    };

    return (
        <Layout title={`Detail Admin Cabang: ${adminCabangData.name}`}>
            <Head title={`Admin Cabang: ${adminCabangData.name}`} />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {errors.error && (
                        <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {errors.error}
                        </div>
                    )}
                    <div className="mb-6">
                        <Link
                            href="/super-admin/admin-cabang"
                            className="text-indigo-600 hover:text-indigo-900 flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Daftar Admin Cabang
                        </Link>
                    </div>

                    {/* Header with admin info */}
                    <div className="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                            <div>
                                <h1 className="text-2xl font-bold text-gray-900">{adminCabangData.name}</h1>
                                <p className="text-sm text-gray-500">{adminCabangData.jabatan}</p>
                            </div>
                            <div>
                                <Link
                                    href={`/super-admin/admin-cabang/${adminCabangData.id}/edit`}
                                    className="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    Edit Admin
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Main Information */}
                    <div className="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div className="px-4 py-5 sm:px-6">
                            <h3 className="text-lg leading-6 font-medium text-gray-900">Informasi Admin Cabang</h3>
                            <p className="mt-1 max-w-2xl text-sm text-gray-500">Detail personal dan kontak admin.</p>
                        </div>
                        <div className="border-t border-gray-200">
                            <dl>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.name}</dd>
                                </div>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Username</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.user.username}</dd>
                                </div>
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Email</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.user.email}</dd>
                                </div>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.phone}</dd>
                                </div>
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Jabatan</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.jabatan}</dd>
                                </div>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Bergabung Sejak</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{formatDate(adminCabangData.created_at)}</dd>
                                </div>

                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Status Akun</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.user.status == 1 ? 'Aktif' : 'Tidak Aktif'}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {/* Pondok Information */}
                    <div className="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div className="px-4 py-5 sm:px-6">
                            <h3 className="text-lg leading-6 font-medium text-gray-900">Informasi Pondok</h3>
                            <p className="mt-1 max-w-2xl text-sm text-gray-500">Pondok yang dikelola admin.</p>
                        </div>
                        <div className="border-t border-gray-200">
                            <dl>
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Nama Pondok</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <Link
                                            href={`/super-admin/pondok/${adminCabangData.pondok.id}`}
                                            className="text-indigo-600 hover:text-indigo-900"
                                        >
                                            {adminCabangData.pondok.nama}
                                        </Link>
                                    </dd>
                                </div>
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Alamat Pondok</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{adminCabangData.pondok.alamat}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end space-x-4">
                        {
                            adminCabangData.user.status == 1 ? (
                                <button
                                    type="button"
                                    className="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                                    onClick={() => {
                                        if (confirm("Apakah Anda yakin ingin menonaktifkan admin ini?")) {
                                            handleStatus(0)
                                        }
                                    }}
                                >
                                    Nonaktifkan Admin
                                </button>
                            ) : (
                                <button
                                    type="button"
                                    className="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                    onClick={() => {
                                        if (confirm("Apakah Anda yakin ingin Mengaktifkan admin ini?")) {
                                            handleStatus(1)
                                        }
                                    }}
                                >
                                    Aktifkan Admin
                                </button>
                            )
                        }
                        {/* <Link
                            href={`/super-admin/admin-cabang/${adminCabangData.id}/reset-password`}
                            className="px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700"
                        >
                            Reset Password
                        </Link> */}
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default AdminCabangShow;
