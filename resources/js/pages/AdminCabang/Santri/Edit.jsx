import { Link } from '@inertiajs/react';
import { useState } from 'react';
import FormInput from '../../SuperAdmin/components/FormInput';
import Layout from '../components/Layout';

const SantriEdit = ({ santri }) => {
    const initial = santri || {
        id: 1,
        user_id: 1,
        pondok_id: 1,
        kelas_id: 1,
        nama: 'Ahmad Fauzi',
        panggilan: 'Ahmad',
        jenis_kelamin: 'L',
        tempat_lahir: 'Bandung',
        tanggal_lahir: '2010-01-01',
        status_mukim: 'Mukim',
        kondisi: 'Sehat',
        warga_negara: 'Indonesia',
        kode_pos: '40111',
        alamat: 'Jl. Contoh No. 123',
        anak_ke: 1,
        jumlah_saudara: 3,
        status_anak: 'Kandung',
        saudara_kandung: 2,
        saudara_tiri: 1,
        jarak_pondok: 5,
        telpon: '022123456',
        handphone: '081234567890',
        email: 'ahmad@example.com',
        hobi: 'Membaca',
        foto: null,
    };

    const [data, setData] = useState(initial);

    const onChange = (e) => {
        const { name, value, type, files } = e.target;
        setData((prev) => ({ ...prev, [name]: type === 'file' ? (files?.[0] ?? null) : value }));
    };

    const onSubmit = (e) => {
        e.preventDefault();
        alert('Edit Santri (dummy):\n' + JSON.stringify({ ...data, foto: data.foto ? data.foto.name : null }, null, 2));
    };

    const jkOptions = [
        { value: 'L', label: 'Laki-laki' },
        { value: 'P', label: 'Perempuan' },
    ];

    const yaTidak = [
        { value: 'Mukim', label: 'Mukim' },
        { value: 'Non-mukim', label: 'Non-mukim' },
    ];

    return (
        <Layout title={`Edit Santri - ${data.nama}`}>
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-700">Form Edit Santri</h2>
                    <Link
                        href={`/admin-cabang/santri/${data.id}`}
                        className="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>

                <div className="rounded-lg bg-white p-6 shadow-md">
                    <form onSubmit={onSubmit}>
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <FormInput label="Nama Lengkap" name="nama" value={data.nama} onChange={onChange} required />
                            <FormInput label="Nama Panggilan" name="panggilan" value={data.panggilan} onChange={onChange} />

                            <FormInput
                                label="Jenis Kelamin"
                                name="jenis_kelamin"
                                type="select"
                                value={data.jenis_kelamin}
                                onChange={onChange}
                                options={jkOptions}
                                required
                            />
                            <FormInput label="Tempat Lahir" name="tempat_lahir" value={data.tempat_lahir} onChange={onChange} />
                            <FormInput label="Tanggal Lahir" name="tanggal_lahir" type="date" value={data.tanggal_lahir} onChange={onChange} />

                            <FormInput
                                label="Status Mukim"
                                name="status_mukim"
                                type="select"
                                value={data.status_mukim}
                                onChange={onChange}
                                options={yaTidak}
                            />
                            <FormInput label="Kondisi" name="kondisi" value={data.kondisi} onChange={onChange} placeholder="Sehat / dll" />

                            <FormInput
                                label="Warga Negara"
                                name="warga_negara"
                                value={data.warga_negara}
                                onChange={onChange}
                                placeholder="Indonesia"
                            />
                            <FormInput label="Kode Pos" name="kode_pos" value={data.kode_pos} onChange={onChange} />
                            <div className="md:col-span-2">
                                <FormInput label="Alamat" name="alamat" type="textarea" value={data.alamat} onChange={onChange} />
                            </div>

                            <FormInput label="Anak Ke" name="anak_ke" type="number" value={data.anak_ke} onChange={onChange} />
                            <FormInput label="Jumlah Saudara" name="jumlah_saudara" type="number" value={data.jumlah_saudara} onChange={onChange} />
                            <FormInput label="Status Anak" name="status_anak" value={data.status_anak} onChange={onChange} />
                            <FormInput
                                label="Saudara Kandung"
                                name="saudara_kandung"
                                type="number"
                                value={data.saudara_kandung}
                                onChange={onChange}
                            />
                            <FormInput label="Saudara Tiri" name="saudara_tiri" type="number" value={data.saudara_tiri} onChange={onChange} />
                            <FormInput label="Jarak ke Pondok (km)" name="jarak_pondok" type="number" value={data.jarak_pondok} onChange={onChange} />

                            <FormInput label="Telepon" name="telpon" value={data.telpon} onChange={onChange} />
                            <FormInput label="Handphone" name="handphone" value={data.handphone} onChange={onChange} />
                            <FormInput label="Email" name="email" type="email" value={data.email} onChange={onChange} />
                            <FormInput label="Hobi" name="hobi" value={data.hobi} onChange={onChange} />

                            <FormInput label="Pondok ID" name="pondok_id" type="number" value={data.pondok_id} onChange={onChange} />
                            <FormInput label="Kelas ID" name="kelas_id" type="number" value={data.kelas_id} onChange={onChange} />

                            <div className="md:col-span-2">
                                <FormInput label="Foto" name="foto" type="file" onChange={onChange} />
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
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Layout>
    );
};

export default SantriEdit;
