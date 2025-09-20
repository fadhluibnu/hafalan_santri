import { Link, useForm } from '@inertiajs/react';
import FormInput from '../../AdminCabang/components/FormInput';
import Layout from '../components/Layout';

const SantriCreate = () => {
    const { data, setData, post, errors } = useForm({
        // User
        username: '',
        email: '',
        password: '',

        // Santri
        user_id: '',
        kelas_id: null,
        nama: '',
        panggilan: '',
        jenis_kelamin: '',
        tempat_lahir: '',
        tanggal_lahir: '',
        status_mukim: '',
        kondisi: '',
        warga_negara: '',
        kode_pos: '',
        alamat: '',
        anak_ke: '',
        jumlah_saudara: '',
        status_anak: '',
        saudara_kandung: '',
        saudara_tiri: '',
        jarak_pondok: '',
        telpon: '',
        handphone: '',
        hobi: '',
        foto: null,

        // Orang Tua Ayah
        ayah_nama: '',
        ayah_status: '',
        ayah_status_hubungan: '',
        ayah_tempat_lahir: '',
        ayah_tanggal_lahir: '',
        ayah_pendidikan: '',
        ayah_pekerjaan: '',
        ayah_penghasilan: '',
        ayah_email: '',
        ayah_handphone: '',
        ayah_alamat: '',

        // Orang Tua Ibu
        ibu_nama: '',
        ibu_status: '',
        ibu_status_hubungan: '',
        ibu_tempat_lahir: '',
        ibu_tanggal_lahir: '',
        ibu_pendidikan: '',
        ibu_pekerjaan: '',
        ibu_penghasilan: '',
        ibu_email: '',
        ibu_handphone: '',
        ibu_alamat: '',

        // Wali (opsional)
        wali_nama: '',
        wali_status: '',
        wali_status_hubungan: '',
        wali_tempat_lahir: '',
        wali_tanggal_lahir: '',
        wali_pendidikan: '',
        wali_pekerjaan: '',
        wali_penghasilan: '',
        wali_email: '',
        wali_handphone: '',
        wali_alamat: '',

        // Kesehatan
        golongan_darah: '',
        berat_badan: '',
        tinggi_badan: '',
        riwayat_penyakit: '',
    });

    const onChange = (e) => {
        const { name, value, type, files } = e.target;
        setData(name, type === 'file' ? (files?.[0] ?? null) : value);
    };

    const onSubmit = (e) => {
        e.preventDefault();
        post(route('admin-cabang.santri.store'));
    };

    const jkOptions = [
        { value: 'L', label: 'Laki-laki' },
        { value: 'P', label: 'Perempuan' },
    ];

    const yaTidak = [
        { value: 'Mukim', label: 'Mukim' },
        { value: 'Non-mukim', label: 'Non-mukim' },
    ];

    // Untuk select orang tua
    const statusOrtu = [
        { value: 'Hidup', label: 'Hidup' },
        { value: 'Almarhum', label: 'Almarhum' },
    ];
    const statusHubungan = [
        { value: 'Kandung', label: 'Kandung' },
        { value: 'Tiri', label: 'Tiri' },
        { value: 'Angkat', label: 'Angkat' },
    ];

    const golDarahOptions = [
        { value: 'A', label: 'A' },
        { value: 'B', label: 'B' },
        { value: 'AB', label: 'AB' },
        { value: 'O', label: 'O' },
    ];

    return (
        <Layout title="Tambah Santri">
            <div className="space-y-4">
                {errors.error && (
                    <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {errors.error}
                    </div>
                )}
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-700">Form Tambah Santri</h2>
                    <Link
                        href={route('admin-cabang.santri.index')}
                        className="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>

                <div className="rounded-lg bg-white p-6 shadow-md">
                    <form onSubmit={onSubmit}>
                        {/* User Info */}
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 mb-6">
                            <FormInput label="Username" name="username" value={data.username} onChange={onChange} required />
                            <FormInput label="Email" name="email" type="email" value={data.email} onChange={onChange} />
                            <FormInput label="Password" name="password" type="password" value={data.password} onChange={onChange} required />
                        </div>
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
                            <FormInput label="Tempat Lahir" name="tempat_lahir" value={data.tempat_lahir} onChange={onChange} required />
                            <FormInput label="Tanggal Lahir" name="tanggal_lahir" type="date" value={data.tanggal_lahir} onChange={onChange} required />

                            <FormInput
                                label="Status Mukim"
                                name="status_mukim"
                                type="select"
                                value={data.status_mukim}
                                onChange={onChange}
                                options={yaTidak}
                                required
                            />
                            <FormInput label="Kondisi" name="kondisi" value={data.kondisi} onChange={onChange} placeholder="Sehat / dll" required />

                            <FormInput
                                label="Warga Negara"
                                name="warga_negara"
                                value={data.warga_negara}
                                onChange={onChange}
                                placeholder="Indonesia"
                                required
                            />
                            <FormInput label="Kode Pos" name="kode_pos" value={data.kode_pos} onChange={onChange} />
                            <div className="md:col-span-2">
                                <FormInput label="Alamat" name="alamat" type="textarea" value={data.alamat} onChange={onChange} required />
                            </div>

                            <FormInput label="Anak Ke" name="anak_ke" type="number" value={data.anak_ke} onChange={onChange} required />
                            <FormInput label="Jumlah Saudara" name="jumlah_saudara" type="number" value={data.jumlah_saudara} onChange={onChange} required />
                            <FormInput
                                label="Status Anak"
                                name="status_anak"
                                value={data.status_anak}
                                onChange={onChange}
                                placeholder="Kandung / Yatim / Piatu / dll"
                                required
                            />
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
                            {/* Email sudah di atas */}
                            <FormInput label="Hobi" name="hobi" value={data.hobi} onChange={onChange} />

                            {/* Relasional - placeholder input id */}
                            {/* <FormInput label="Kelas ID" name="kelas_id" type="number" value={data.kelas_id} onChange={onChange} /> */}
                            {/* Pondok ID tidak perlu diinput manual, diambil dari backend */}

                            {/* Foto */}
                            <div className="md:col-span-2">
                                <FormInput label="Foto" name="foto" type="file" onChange={onChange} />
                            </div>
                        </div>

                        {/* Orang Tua */}
                        <div className="mt-8">
                            <h3 className="text-md font-semibold mb-2">Data Orang Tua/Wali</h3>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {/* Ayah */}
                                <div className="border rounded-lg p-4 bg-gray-50">
                                    <h4 className="font-semibold mb-2">Ayah</h4>
                                    <FormInput label="Nama" name="ayah_nama" value={data.ayah_nama} onChange={onChange} required />
                                    <FormInput label="Status" name="ayah_status" type="select" value={data.ayah_status} onChange={onChange} options={statusOrtu} required />
                                    <FormInput label="Status Hubungan" name="ayah_status_hubungan" type="select" value={data.ayah_status_hubungan} onChange={onChange} options={statusHubungan} required />
                                    <FormInput label="Tempat Lahir" name="ayah_tempat_lahir" value={data.ayah_tempat_lahir} onChange={onChange} />
                                    <FormInput label="Tanggal Lahir" name="ayah_tanggal_lahir" type="date" value={data.ayah_tanggal_lahir} onChange={onChange} />
                                    <FormInput label="Pendidikan" name="ayah_pendidikan" value={data.ayah_pendidikan} onChange={onChange} />
                                    <FormInput label="Pekerjaan" name="ayah_pekerjaan" value={data.ayah_pekerjaan} onChange={onChange} />
                                    <FormInput label="Penghasilan" name="ayah_penghasilan" value={data.ayah_penghasilan} onChange={onChange} />
                                    <FormInput label="Email" name="ayah_email" type="email" value={data.ayah_email} onChange={onChange} />
                                    <FormInput label="Handphone" name="ayah_handphone" value={data.ayah_handphone} onChange={onChange} required/>
                                    <FormInput label="Alamat" name="ayah_alamat" type="textarea" value={data.ayah_alamat} onChange={onChange} />
                                </div>
                                {/* Ibu */}
                                <div className="border rounded-lg p-4 bg-gray-50">
                                    <h4 className="font-semibold mb-2">Ibu</h4>
                                    <FormInput label="Nama" name="ibu_nama" value={data.ibu_nama} onChange={onChange} required />
                                    <FormInput label="Status" name="ibu_status" type="select" value={data.ibu_status} onChange={onChange} options={statusOrtu} required />
                                    <FormInput label="Status Hubungan" name="ibu_status_hubungan" type="select" value={data.ibu_status_hubungan} onChange={onChange} options={statusHubungan} required />
                                    <FormInput label="Tempat Lahir" name="ibu_tempat_lahir" value={data.ibu_tempat_lahir} onChange={onChange} />
                                    <FormInput label="Tanggal Lahir" name="ibu_tanggal_lahir" type="date" value={data.ibu_tanggal_lahir} onChange={onChange} />
                                    <FormInput label="Pendidikan" name="ibu_pendidikan" value={data.ibu_pendidikan} onChange={onChange} />
                                    <FormInput label="Pekerjaan" name="ibu_pekerjaan" value={data.ibu_pekerjaan} onChange={onChange} />
                                    <FormInput label="Penghasilan" name="ibu_penghasilan" value={data.ibu_penghasilan} onChange={onChange} />
                                    <FormInput label="Email" name="ibu_email" type="email" value={data.ibu_email} onChange={onChange} />
                                    <FormInput label="Handphone" name="ibu_handphone" value={data.ibu_handphone} onChange={onChange} required/>
                                    <FormInput label="Alamat" name="ibu_alamat" type="textarea" value={data.ibu_alamat} onChange={onChange} />
                                </div>
                                {/* Wali */}
                                <div className="border rounded-lg p-4 bg-gray-50">
                                    <h4 className="font-semibold mb-2">Wali (Opsional)</h4>
                                    <FormInput label="Nama" name="wali_nama" value={data.wali_nama} onChange={onChange}/>
                                    <FormInput label="Status" name="wali_status" type="select" value={data.wali_status} onChange={onChange} options={statusOrtu}/>
                                    <FormInput label="Status Hubungan" name="wali_status_hubungan" type="select" value={data.wali_status_hubungan} onChange={onChange} options={statusHubungan}/>
                                    <FormInput label="Tempat Lahir" name="wali_tempat_lahir" value={data.wali_tempat_lahir} onChange={onChange}/>
                                    <FormInput label="Tanggal Lahir" name="wali_tanggal_lahir" type="date" value={data.wali_tanggal_lahir} onChange={onChange} />
                                    <FormInput label="Pendidikan" name="wali_pendidikan" value={data.wali_pendidikan} onChange={onChange} />
                                    <FormInput label="Pekerjaan" name="wali_pekerjaan" value={data.wali_pekerjaan} onChange={onChange} />
                                    <FormInput label="Penghasilan" name="wali_penghasilan" value={data.wali_penghasilan} onChange={onChange} />
                                    <FormInput label="Email" name="wali_email" type="email" value={data.wali_email} onChange={onChange} />
                                    <FormInput label="Handphone" name="wali_handphone" value={data.wali_handphone} onChange={onChange}/>
                                    <FormInput label="Alamat" name="wali_alamat" type="textarea" value={data.wali_alamat} onChange={onChange} />
                                </div>
                            </div>
                        </div>

                        {/* Kesehatan */}
                        <div className="mt-8">
                            <h3 className="text-md font-semibold mb-2">Data Kesehatan Santri</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput label="Golongan Darah" name="golongan_darah" type="select" value={data.golongan_darah} onChange={onChange} options={golDarahOptions} required />
                                <FormInput label="Berat Badan (kg)" name="berat_badan" type="number" value={data.berat_badan} onChange={onChange} step="0.1" />
                                <FormInput label="Tinggi Badan (cm)" name="tinggi_badan" type="number" value={data.tinggi_badan} onChange={onChange} step="0.1" />
                                <FormInput label="Riwayat Penyakit" name="riwayat_penyakit" type="textarea" value={data.riwayat_penyakit} onChange={onChange} />
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
            </div>
        </Layout>
    );
};

// export default SantriCreate;
//                 </div>
//             </div>
//         </Layout>
//     );
// };

export default SantriCreate;
