import { Link, useForm } from '@inertiajs/react';
import HafalanForm from '../components/HafalanForm';
import Layout from '../components/Layout';
import { route } from 'ziggy-js';

const HafalanCreate = ({ classes, santrisByClass, ustadzs, surahs, currentUstadzId, skemaPenilaian = { tipe: 'label', items: [] } }) => {
    const { data, setData, post, processing, errors } = useForm({
        kelas_id: '',
        santri_id: '',
        ustadz_id: currentUstadzId || '',
        tanggal_setor: '',
        juz: '',
        dari_surat: '',
        dari_ayat: '',
        sampai_surat: '',
        sampai_ayat: '',
        kategori: '',
        nilai: '',
        catatan: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('ustadz.hafalan.store'), {
            onSuccess: () => {
                // Redirect handled by controller
            },
            onError: (errors) => {
                console.error('Submission errors:', errors);
            },
        });
    };

    return (
        <Layout title="Input Setoran Hafalan">
            <div className="space-y-4">
                {errors.error && (
                    <div className="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {errors.error}
                    </div>
                )}
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold text-gray-800">Input Setoran Hafalan</h1>
                    <Link href="/ustadz/hafalan" className="text-sm text-indigo-600 hover:underline">
                        Kembali
                    </Link>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <HafalanForm
                        formData={data}
                        setFormData={setData}
                        onSubmit={handleSubmit}
                        processing={processing}
                        classes={classes}
                        santrisByClass={santrisByClass}
                        ustadzs={ustadzs}
                        surahs={surahs}
                        skemaPenilaian={skemaPenilaian}
                    />
                </div>
            </div>
        </Layout>
    );
};

export default HafalanCreate;
