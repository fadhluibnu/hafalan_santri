import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import FormInput from '../../../SuperAdmin/components/FormInput';
import Layout from '../../components/Layout';

const KelasCreate = ({ ustadzs = [] }) => {
    const { data, setData, post, processing, errors } = useForm({
        nama: '',
        tingkat: 'Juz 30',
        wali_kelas_id: '',
        kapasitas: 20,
        keterangan: '',
    });

    const tingkatOptions = [
        { value: 'Juz 30', label: 'Juz 30' },
        { value: 'Juz 29', label: 'Juz 29' },
        { value: 'Juz 28', label: 'Juz 28' },
        { value: 'Juz 1-10', label: 'Juz 1-10' },
        { value: 'Juz 11-20', label: 'Juz 11-20' },
        { value: 'Juz 21-30', label: 'Juz 21-30' },
    ];

    const ustadzOptions = [{ value: '', label: '— Pilih Wali Kelas —' }, ...ustadzs.map(u => ({ value: u.id, label: u.nama }))];

    const onChange = (e) => {
        const { name, value, type } = e.target;
        const parsed = type === 'number' ? Number(value) : value;
        setData(name, parsed);
    };

    const onSubmit = (e) => {
        e.preventDefault();
        post(route('admin-cabang.struktur.kelas.store'));
    };

    return (
        <Layout title="Buat Kelas">
            <div className="rounded-lg bg-white p-6 shadow-md">
                {errors.error && (
                    <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {errors.error}
                    </div>
                )}
                <form onSubmit={onSubmit}>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <FormInput
                            label="Nama Kelas"
                            name="nama"
                            value={data.nama}
                            onChange={onChange}
                            required
                            placeholder="Contoh: Kelas Tahfidz A"
                            error={errors.nama}
                        />
                        <FormInput
                            label="Tingkat/Juz"
                            name="tingkat"
                            type="select"
                            value={data.tingkat}
                            onChange={onChange}
                            options={tingkatOptions}
                            required
                            error={errors.tingkat}
                        />
                        <FormInput
                            label="Wali Kelas"
                            name="wali_kelas_id"
                            type="select"
                            value={data.wali_kelas_id}
                            onChange={onChange}
                            options={ustadzOptions}
                            placeholder="Pilih wali kelas"
                            error={errors.wali_kelas_id}
                        />
                        <FormInput
                            label="Kapasitas (orang)"
                            name="kapasitas"
                            type="number"
                            value={data.kapasitas}
                            onChange={onChange}
                            required
                            error={errors.kapasitas}
                        />
                        <div className="md:col-span-2">
                            <FormInput
                                label="Keterangan"
                                name="keterangan"
                                type="textarea"
                                value={data.keterangan}
                                onChange={onChange}
                                placeholder="Catatan tambahan untuk kelas ini"
                                error={errors.keterangan}
                            />
                        </div>
                    </div>
                    <div className="mt-6 flex items-center justify-end space-x-3 border-t pt-6">
                        <button
                            type="button"
                            className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            onClick={() => window.history.back()}
                            disabled={processing}
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                            disabled={processing}
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
