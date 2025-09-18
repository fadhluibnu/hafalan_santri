import { useState } from 'react';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Layout from '../../components/Layout';

const KelasCreate = () => {
    const [data, setData] = useState({ nama: '', tingkat: 'Juz 30', waliKelas: '', kapasitas: 20, keterangan: '' });

    const tingkatOptions = [
        { value: 'Juz 30', label: 'Juz 30' },
        { value: 'Juz 29', label: 'Juz 29' },
        { value: 'Juz 28', label: 'Juz 28' },
        { value: 'Juz 1-10', label: 'Juz 1-10' },
        { value: 'Juz 11-20', label: 'Juz 11-20' },
        { value: 'Juz 21-30', label: 'Juz 21-30' },
    ];

    const onChange = (e) => {
        const { name, value, type } = e.target;
        setData((prev) => ({ ...prev, [name]: type === 'number' ? Number(value) : value }));
    };

    const onSubmit = (e) => {
        e.preventDefault();
        alert('Create Kelas (dummy):\n' + JSON.stringify(data, null, 2));
    };

    return (
        <Layout title="Buat Kelas">
            <div className="rounded-lg bg-white p-6 shadow-md">
                <form onSubmit={onSubmit}>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <FormInput
                            label="Nama Kelas"
                            name="nama"
                            value={data.nama}
                            onChange={onChange}
                            required
                            placeholder="Contoh: Kelas Tahfidz A"
                        />
                        <FormInput
                            label="Tingkat/Juz"
                            name="tingkat"
                            type="select"
                            value={data.tingkat}
                            onChange={onChange}
                            options={tingkatOptions}
                            required
                        />
                        <FormInput label="Wali Kelas" name="waliKelas" value={data.waliKelas} onChange={onChange} placeholder="Nama wali kelas" />
                        <FormInput label="Kapasitas (orang)" name="kapasitas" type="number" value={data.kapasitas} onChange={onChange} required />
                        <div className="md:col-span-2">
                            <FormInput
                                label="Keterangan"
                                name="keterangan"
                                type="textarea"
                                value={data.keterangan}
                                onChange={onChange}
                                placeholder="Catatan tambahan untuk kelas ini"
                            />
                        </div>
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
};

export default KelasCreate;
