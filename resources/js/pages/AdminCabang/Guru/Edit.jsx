import { Link } from '@inertiajs/react';
import { useState } from 'react';
import FormInput from '../../SuperAdmin/components/FormInput';
import Layout from '../components/Layout';

const GuruEdit = ({ guru }) => {
    const initial = guru || {
        id: 1,
        user_id: 1,
        pondok_id: 1,
        nip: '1234567890',
        nama: 'Ustadz Rahman',
        gelar_awal: 'S.Pd',
        gelar_akhir: '',
        tempat_lahir: 'Bandung',
        tanggal_lahir: '1988-02-01',
        jenis_kelamin: 'L',
        status_menikah: 'Menikah',
        alamat: 'Jl. Contoh No.1',
        no_identitas: '3210...',
        no_telpon: '022-123456',
        no_handphone: '08123456789',
        email: 'rahman@example.com',
        tanggal_kerja: '2015-07-01',
        non_aktif: false,
        keterangan: 'Guru senior',
    };

    const [data, setData] = useState(initial);

    const onChange = (e) => {
        const { name, value, type, checked } = e.target;
        setData((prev) => ({ ...prev, [name]: type === 'checkbox' ? checked : value }));
    };

    const onSubmit = (e) => {
        e.preventDefault();
        alert('Edit Guru (dummy):\n' + JSON.stringify(data, null, 2));
    };

    const jkOptions = [
        { value: 'L', label: 'Laki-laki' },
        { value: 'P', label: 'Perempuan' },
    ];

    const statusMenikahOptions = [
        { value: 'Belum Menikah', label: 'Belum Menikah' },
        { value: 'Menikah', label: 'Menikah' },
        { value: 'Cerai', label: 'Cerai' },
    ];

    return (
        <Layout title={`Edit Guru - ${data.nama}`}>
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-700">Form Edit Guru</h2>
                    <Link
                        href={`/admin-cabang/guru/${data.id}`}
                        className="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>

                <div className="rounded-lg bg-white p-6 shadow-md">
                    <form onSubmit={onSubmit}>
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <FormInput label="NIP" name="nip" value={data.nip} onChange={onChange} />
                            <FormInput label="Nama Lengkap" name="nama" value={data.nama} onChange={onChange} required />

                            <FormInput label="Gelar Awal" name="gelar_awal" value={data.gelar_awal} onChange={onChange} />
                            <FormInput label="Gelar Akhir" name="gelar_akhir" value={data.gelar_akhir} onChange={onChange} />

                            <FormInput
                                label="Jenis Kelamin"
                                name="jenis_kelamin"
                                type="select"
                                value={data.jenis_kelamin}
                                onChange={onChange}
                                options={jkOptions}
                            />
                            <FormInput label="Tempat Lahir" name="tempat_lahir" value={data.tempat_lahir} onChange={onChange} />
                            <FormInput label="Tanggal Lahir" name="tanggal_lahir" type="date" value={data.tanggal_lahir} onChange={onChange} />

                            <FormInput
                                label="Status Menikah"
                                name="status_menikah"
                                type="select"
                                value={data.status_menikah}
                                onChange={onChange}
                                options={statusMenikahOptions}
                            />
                            <div className="md:col-span-2">
                                <FormInput label="Alamat" name="alamat" type="textarea" value={data.alamat} onChange={onChange} />
                            </div>

                            <FormInput label="No Identitas" name="no_identitas" value={data.no_identitas} onChange={onChange} />
                            <FormInput label="No Telepon" name="no_telpon" value={data.no_telpon} onChange={onChange} />
                            <FormInput label="No Handphone" name="no_handphone" value={data.no_handphone} onChange={onChange} />
                            <FormInput label="Email" name="email" type="email" value={data.email} onChange={onChange} />

                            <FormInput label="Tanggal Mulai Kerja" name="tanggal_kerja" type="date" value={data.tanggal_kerja} onChange={onChange} />
                            <FormInput label="Non Aktif" name="non_aktif" type="checkbox" value={data.non_aktif} onChange={onChange} />

                            <div className="md:col-span-2">
                                <FormInput label="Keterangan" name="keterangan" type="textarea" value={data.keterangan} onChange={onChange} />
                            </div>

                            <FormInput label="Pondok ID" name="pondok_id" type="number" value={data.pondok_id} onChange={onChange} />
                            <FormInput label="User ID" name="user_id" type="number" value={data.user_id} onChange={onChange} />
                        </div>

                        <div className="mt-6 flex items-center justify-end space-x-3 border-t pt-6">
                            <button
                                type="button"
                                className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                onClick={() => window.history.back()}
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                            >
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Layout>
    );
};

export default GuruEdit;
