import React from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import Layout from "../components/layouts";
import FormInput from "../components/FormInput";

const PondokEdit = ({ pondok }) => {
    // Static data for the form (simulating a pondok received from props)
    const pondokData = pondok || {
        id: 2,
        nama: "Pondok Tahfidz Al-Furqon",
        alamat: "Jl. Pahlawan No. 45, Bandung",
        telepon: "022-5551234",
        email: "alfurqon@example.com",
        website: "www.alfurqon.or.id",
        logo: "/storage/logos/alfurqon.png",
        deskripsi: "Pondok pesantren modern dengan fokus tahfidz Al-Quran",
        tahun_berdiri: 2015
    };

    // Using Inertia's useForm for form handling
    const { data, setData, post, processing, errors } = useForm({
        nama: pondokData.nama,
        alamat: pondokData.alamat,
        telepon: pondokData.telepon,
        email: pondokData.email,
        website: pondokData.website,
        logo: null,
        deskripsi: pondokData.deskripsi,
        tahun_berdiri: pondokData.tahun_berdiri,
        _method: "PUT",
    });

    const handleChange = (e) => {
        const { name, value, type, files } = e.target;
        setData(
            name,
            type === "file" ? files[0] : value
        );
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        // In a real application, this would submit to the backend
        // alert("Form updated with data: " + JSON.stringify(data));
        post(route("super-admin.pondok.update", pondokData.id));
    };

    return (
        <Layout title="Edit Pondok">
            <Head title="Edit Pondok" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold text-gray-900">Edit Pondok</h1>
                        <Link
                            href="/super-admin/pondok"
                            className="px-4 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Kembali
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <form onSubmit={handleSubmit} encType="multipart/form-data">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <FormInput
                                            label="Nama Pondok"
                                            name="nama"
                                            value={data.nama}
                                            onChange={handleChange}
                                            error={errors.nama}
                                            required
                                        />
                                        
                                        <FormInput
                                            label="Alamat"
                                            name="alamat"
                                            type="textarea"
                                            value={data.alamat}
                                            onChange={handleChange}
                                            error={errors.alamat}
                                            required
                                        />
                                        
                                        <FormInput
                                            label="Telepon"
                                            name="telepon"
                                            value={data.telepon}
                                            onChange={handleChange}
                                            error={errors.telepon}
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
                                    </div>
                                    
                                    <div>
                                        <FormInput
                                            label="Website"
                                            name="website"
                                            value={data.website}
                                            onChange={handleChange}
                                            error={errors.website}
                                        />
                                        
                                        <FormInput
                                            label="Logo Pondok"
                                            name="logo"
                                            type="file"
                                            onChange={handleChange}
                                            error={errors.logo}
                                            value={pondokData.logo}
                                        />
                                        
                                        <FormInput
                                            label="Deskripsi"
                                            name="deskripsi"
                                            type="textarea"
                                            value={data.deskripsi}
                                            onChange={handleChange}
                                            error={errors.deskripsi}
                                        />
                                        
                                        <FormInput
                                            label="Tahun Berdiri"
                                            name="tahun_berdiri"
                                            type="number"
                                            value={data.tahun_berdiri}
                                            onChange={handleChange}
                                            error={errors.tahun_berdiri}
                                        />
                                    </div>
                                </div>
                                
                                <div className="flex justify-end mt-6 space-x-3">
                                    <Link
                                        href={`/super-admin/pondok/${pondokData.id}`}
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

export default PondokEdit;
