import { Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import HafalanForm from '../components/HafalanForm';
import Layout from '../components/Layout';

const HafalanEdit = ({ hafalan, classes, santrisByClass, ustadzs, surahs, skemaPenilaian = { tipe: 'label', items: [] } }) => {
    const { data, setData, put, processing, errors } = useForm({
        kelas_id: hafalan?.kelas_id || '',
        santri_id: hafalan?.santri_id || '',
        ustadz_id: hafalan?.ustadz_id || '',
        tanggal_setor: hafalan?.tanggal_setor || '',
        juz: hafalan?.juz || '',
        dari_surat: hafalan?.dari_surat || '',
        dari_ayat: hafalan?.dari_ayat || '',
        sampai_surat: hafalan?.sampai_surat || '',
        sampai_ayat: hafalan?.sampai_ayat || '',
        kategori: hafalan?.kategori || '',
        nilai: hafalan?.nilai || '',
        catatan: hafalan?.catatan || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('ustadz.hafalan.update', hafalan.id), {
            onSuccess: () => {
                // Redirect handled by controller
            },
            onError: (errors) => {
                console.error('Submission errors:', errors);
            },
        });
    };

    return (
        <Layout title="Edit Setoran Hafalan">
            <div className="space-y-4">
                {errors.error && (
                    <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {errors.error}
                    </div>
                )}
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold text-gray-800">Edit Setoran Hafalan</h1>
                    <Link href={route('ustadz.hafalan.index')} className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <HafalanForm
                        formData={data}
                        setFormData={setData}
                        onSubmit={handleSubmit}
                        processing={processing}
                        classes={classes || []}
                        santrisByClass={santrisByClass || {}}
                        ustadzs={ustadzs || []}
                        surahs={surahs || []}
                        skemaPenilaian={skemaPenilaian}
                    />
                </div>
            </div>
        </Layout>
    );
};

export default HafalanEdit;
