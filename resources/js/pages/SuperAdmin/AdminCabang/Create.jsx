import React from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import Layout from "../components/layouts";
import FormInput from "../components/FormInput";

const AdminCabangCreate = ({ pondoks }) => {
    // Static data for pondok options (simulating data received from props)
    const pondokOptions = pondoks || [
        { value: 1, label: "Pondok Tahfidz Al-Quran Baitul Hikmah" },
        { value: 2, label: "Pondok Tahfidz Al-Furqon" },
        { value: 3, label: "Pondok Tahfidz Darul Quran" },
        { value: 4, label: "Pondok Pesantren As-Salam" },
        { value: 5, label: "Pondok Tahfidz An-Nur" }
    ];

    // Using Inertia's useForm for form handling
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        phone: "",
        jabatan: "Kepala Cabang",
        pondok_id: "",
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setData(name, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        // In a real application, this would submit to the backend
        alert("Form submitted with data: " + JSON.stringify(data));
        // post(route("super-admin.admin-cabang.store"));
    };

    return (
        <Layout title="Tambah Admin Cabang">
            <Head title="Tambah Admin Cabang" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold text-gray-900">Tambah Admin Cabang</h1>
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
                                            label="Email"
                                            name="email"
                                            type="email"
                                            value={data.email}
                                            onChange={handleChange}
                                            error={errors.email}
                                            required
                                        />
                                        
                                        <FormInput
                                            label="Password"
                                            name="password"
                                            type="password"
                                            value={data.password}
                                            onChange={handleChange}
                                            error={errors.password}
                                            required
                                        />

                                        <FormInput
                                            label="Konfirmasi Password"
                                            name="password_confirmation"
                                            type="password"
                                            value={data.password_confirmation}
                                            onChange={handleChange}
                                            error={errors.password_confirmation}
                                            required
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

                                <div className="flex items-center justify-between mt-6 border-t pt-6">
                                    <div className="text-sm text-gray-500">
                                        <span>* </span>
                                        <span>Wajib diisi</span>
                                    </div>
                                    
                                    <div className="flex items-center space-x-3">
                                        <button
                                            type="button"
                                            className="px-4 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            onClick={() => window.history.back()}
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            className="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            disabled={processing}
                                        >
                                            {processing ? "Menyimpan..." : "Simpan"}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default AdminCabangCreate;
