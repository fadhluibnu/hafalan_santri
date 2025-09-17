import React from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import Layout from "../components/layouts";
import FormInput from "../components/FormInput";

const AdminCabangEdit = ({ adminCabang, pondoks }) => {
    // Static data for the form (simulating an admin cabang received from props)
    const adminCabangData = adminCabang
    // Static data for pondok options (simulating data received from props)
    const pondokOptions = pondoks.map(p => ({
        value: p.id,
        label: p.nama
    }));

    // Using Inertia's useForm for form handling
    const { data, setData, post, processing, errors } = useForm({
        name: adminCabangData.name,
        email: adminCabangData.user.email,
        username: adminCabangData.user.username,
        phone: adminCabangData.phone,
        jabatan: adminCabangData.jabatan,
        pondok_id: adminCabangData.pondok_id,
        status: adminCabangData.user.status,
        password: "",
        _method: "PUT",
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setData(name, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        // In a real application, this would submit to the backend
        // alert("Form updated with data: " + JSON.stringify(data));
        post(route("super-admin.admin-cabang.update", adminCabangData.id));
    };

    return (
        <Layout title="Edit Admin Cabang">
            <Head title="Edit Admin Cabang" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {errors.error && (
                        <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {errors.error}
                        </div>
                    )}

                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold text-gray-900">Edit Admin Cabang</h1>
                        <Link
                            href="/super-admin/admin-cabang"
                            className="px-4 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Kembali
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <form onSubmit={handleSubmit}>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <FormInput
                                            label="Nama Lengkap"
                                            name="name"
                                            value={data.name}
                                            onChange={handleChange}
                                            error={errors.name}
                                            required
                                        />

                                        <FormInput
                                            label="Username"
                                            name="username"
                                            value={data.username}
                                            onChange={handleChange}
                                            error={errors.username}
                                            required
                                        />

                                        <FormInput
                                            label="Email"
                                            name="email"
                                            type="email"
                                            value={data.email}
                                            onChange={handleChange}
                                            error={errors.email}
                                            required
                                        />

                                        <div className="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                            <div className="flex">
                                                <div className="flex-shrink-0">
                                                    <svg className="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div className="ml-3">
                                                    <p className="text-sm text-yellow-700">
                                                        Kosongkan password jika tidak ingin mengubahnya
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <FormInput
                                            label="Password Baru"
                                            name="password"
                                            type="password"
                                            value={data.password}
                                            onChange={handleChange}
                                            error={errors.password}
                                        />
                                    </div>

                                    <div>
                                        <FormInput
                                            label="Nomor Telepon"
                                            name="phone"
                                            value={data.phone}
                                            onChange={handleChange}
                                            error={errors.phone}
                                            required
                                        />

                                        <FormInput
                                            label="Jabatan"
                                            name="jabatan"
                                            value={data.jabatan}
                                            onChange={handleChange}
                                            error={errors.jabatan}
                                            required
                                        />

                                        <FormInput
                                            label="Pondok"
                                            name="pondok_id"
                                            type="select"
                                            value={data.pondok_id}
                                            onChange={handleChange}
                                            error={errors.pondok_id}
                                            options={pondokOptions}
                                            required
                                        />
                                    </div>
                                </div>

                                <div className="flex justify-end mt-6 space-x-3">
                                    <Link
                                        href={`/super-admin/admin-cabang/${adminCabangData.id}`}
                                        className="px-4 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Batalkan
                                    </Link>
                                    <button
                                        type="submit"
                                        className="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        disabled={processing}
                                    >
                                        {processing ? "Menyimpan..." : "Simpan Perubahan"}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default AdminCabangEdit;
