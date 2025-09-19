import { Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import FormInput from '../components/FormInput';
import Layout from '../components/Layout';
import { route } from 'ziggy-js';

const GuruCreate = () => {
    const [loading, setLoading] = useState(false);
    
    const { data, setData, errors, post, processing } = useForm({
        nip: '',
        nama: '',
        gelar_awal: '',
        gelar_akhir: '',
        tempat_lahir: '',
        tanggal_lahir: '',
        jenis_kelamin: '',
        status_menikah: false,
        alamat: '',
        no_identitas: '',
        no_telpon: '',
        no_handphone: '',
        email: '',
        tanggal_kerja: '',
        non_aktif: false,
        keterangan: '',
        username: '',
        password: ''
    });

    const onChange = (e) => {
        const { name, value, type, checked } = e.target;
        setData(name, type === 'checkbox' ? checked : value);
    };

    const onSubmit = (e) => {
        e.preventDefault();
        setLoading(true);
        
        post(route('admin-cabang.guru.store'), {
            onSuccess: () => {
                setLoading(false);
            },
            onError: () => {
                setLoading(false);
            }
        });
    };

    const jkOptions = [
        { value: 'L', label: 'Laki-laki' },
        { value: 'P', label: 'Perempuan' },
    ];

    return (
        <Layout title="Tambah Guru">
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    {errors.error && (
                        <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {errors.error}
                        </div>
                    )}
                    
                    <h2 className="text-lg font-semibold text-gray-700">Form Tambah Guru</h2>
                    <Link
                        href="/admin-cabang/guru"
                        className="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>

                <div className="rounded-lg bg-white p-6 shadow-md">
                    <form onSubmit={onSubmit}>
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {/* Data Pribadi */}
                            <div className="md:col-span-2">
                                <h3 className="text-md font-semibold text-gray-700 border-b pb-2 mb-2">Data Pribadi</h3>
                            </div>
                            
                            <FormInput 
                                label="NIP" 
                                name="nip" 
                                value={data.nip} 
                                onChange={onChange} 
                                required
                                error={errors.nip}
                            />
                            
                            <FormInput 
                                label="Nama Lengkap" 
                                name="nama" 
                                value={data.nama} 
                                onChange={onChange} 
                                required 
                                error={errors.nama}
                            />

                            <FormInput 
                                label="Gelar Awal" 
                                name="gelar_awal" 
                                value={data.gelar_awal} 
                                onChange={onChange} 
                                error={errors.gelar_awal}
                            />
                            
                            <FormInput 
                                label="Gelar Akhir" 
                                name="gelar_akhir" 
                                value={data.gelar_akhir} 
                                onChange={onChange} 
                                error={errors.gelar_akhir}
                            />

                            <FormInput
                                label="Jenis Kelamin"
                                name="jenis_kelamin"
                                type="select"
                                value={data.jenis_kelamin}
                                onChange={onChange}
                                options={jkOptions}
                                required
                                error={errors.jenis_kelamin}
                            />
                            
                            <FormInput 
                                label="Tempat Lahir" 
                                name="tempat_lahir" 
                                value={data.tempat_lahir} 
                                onChange={onChange} 
                                required
                                error={errors.tempat_lahir}
                            />
                            
                            <FormInput 
                                label="Tanggal Lahir" 
                                name="tanggal_lahir" 
                                type="date" 
                                value={data.tanggal_lahir} 
                                onChange={onChange} 
                                required
                                error={errors.tanggal_lahir}
                            />

                            <div className="flex items-center mt-8">
                                <input
                                    id="status_menikah"
                                    name="status_menikah"
                                    type="checkbox"
                                    checked={data.status_menikah}
                                    onChange={onChange}
                                    className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label htmlFor="status_menikah" className="ml-2 block text-sm text-gray-700">
                                    Sudah Menikah
                                </label>
                                {errors.status_menikah && (
                                    <p className="mt-1 text-sm text-red-600">{errors.status_menikah}</p>
                                )}
                            </div>
                            
                            <div className="md:col-span-2">
                                <FormInput 
                                    label="Alamat" 
                                    name="alamat" 
                                    type="textarea" 
                                    value={data.alamat} 
                                    onChange={onChange} 
                                    required
                                    error={errors.alamat}
                                />
                            </div>

                            {/* Data Kontak */}
                            <div className="md:col-span-2">
                                <h3 className="text-md font-semibold text-gray-700 border-b pb-2 mb-2 mt-4">Data Kontak</h3>
                            </div>

                            <FormInput 
                                label="No Identitas" 
                                name="no_identitas" 
                                value={data.no_identitas} 
                                onChange={onChange} 
                                required
                                error={errors.no_identitas}
                            />
                            
                            <FormInput 
                                label="No Telepon" 
                                name="no_telpon" 
                                value={data.no_telpon} 
                                onChange={onChange} 
                                required
                                error={errors.no_telpon}
                            />
                            
                            <FormInput 
                                label="No Handphone" 
                                name="no_handphone" 
                                value={data.no_handphone} 
                                onChange={onChange} 
                                required
                                error={errors.no_handphone}
                            />

                            {/* Data Akun */}
                            <div className="md:col-span-2">
                                <h3 className="text-md font-semibold text-gray-700 border-b pb-2 mb-2 mt-4">Data Akun</h3>
                            </div>
                            <FormInput
                                label="Username"
                                name="username"
                                value={data.username}
                                onChange={onChange}
                                required
                                error={errors.username}
                            />
                            <FormInput 
                                label="Email" 
                                name="email" 
                                type="email" 
                                value={data.email} 
                                onChange={onChange} 
                                required
                                error={errors.email}
                            />
                            <FormInput
                                label="Password"
                                name="password"
                                type="password"
                                value={data.password}
                                onChange={onChange}
                                required
                                error={errors.password}
                            />

                            {/* Data Pekerjaan */}
                            <div className="md:col-span-2">
                                <h3 className="text-md font-semibold text-gray-700 border-b pb-2 mb-2 mt-4">Data Pekerjaan</h3>
                            </div>

                            <FormInput 
                                label="Tanggal Mulai Kerja" 
                                name="tanggal_kerja" 
                                type="date" 
                                value={data.tanggal_kerja} 
                                onChange={onChange} 
                                required
                                error={errors.tanggal_kerja}
                            />
                            
                            <div className="flex items-center mt-8">
                                <input
                                    id="non_aktif"
                                    name="non_aktif"
                                    type="checkbox"
                                    checked={data.non_aktif}
                                    onChange={onChange}
                                    className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label htmlFor="non_aktif" className="ml-2 block text-sm text-gray-700">
                                    Non-Aktif
                                </label>
                                {errors.non_aktif && (
                                    <p className="mt-1 text-sm text-red-600">{errors.non_aktif}</p>
                                )}
                            </div>
                            <div className="md:col-span-2">
                                <FormInput 
                                    label="Keterangan" 
                                    name="keterangan" 
                                    type="textarea" 
                                    value={data.keterangan} 
                                    onChange={onChange} 
                                    error={errors.keterangan}
                                />
                            </div>
                        </div>

                        <div className="mt-6 flex items-center justify-end space-x-3 border-t pt-6">
                            <Link
                                href="/admin-cabang/guru"
                                className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing || loading}
                                className={`rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 ${
                                    (processing || loading) ? 'opacity-50 cursor-not-allowed' : ''
                                }`}
                            >
                                {processing || loading ? 'Menyimpan...' : 'Simpan'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Layout>
    );
};

export default GuruCreate;
