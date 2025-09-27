import { useMemo, useState } from 'react';
import FormInput from '../../SuperAdmin/components/FormInput';

// This form aligns with App\Models\Hafalan::$fillable
const HafalanForm = ({ initial = {}, santriList = [], onSubmit }) => {
    const defaultSantri = useMemo(
        () => [
            { id: 1, nama: 'Ahmad Fauzi', kelas: 'Juz 30' },
            { id: 2, nama: 'Nur Aisyah', kelas: 'Juz 29' },
        ],
        [],
    );

    const santriOptions = (santriList.length ? santriList : defaultSantri).map((s) => ({ value: s.id, label: `${s.nama} (${s.kelas})` }));

    const [form, setForm] = useState({
        santri_id: initial.santri_id || '',
        guru_id: initial.guru_id || '',
        kelas_id: initial.kelas_id || '',
        tanggal_setor: initial.tanggal_setor || '',
        juz: initial.juz || '',
        dari_surat: initial.dari_surat || '',
        dari_ayat: initial.dari_ayat || '',
        sampai_surat: initial.sampai_surat || '',
        sampai_ayat: initial.sampai_ayat || '',
        kategori: initial.kategori || '',
        nilai: initial.nilai || '',
        catatan: initial.catatan || '',
    });

    const set = (name, value) => setForm((f) => ({ ...f, [name]: value }));

    const kategoriOptions = [
        { value: 'ziadah', label: 'Ziadah' },
        { value: 'murojaah', label: 'Murojaah' },
        { value: 'semaan', label: 'Sema’an' },
    ];
    const nilaiOptions = [
        { value: 'baik', label: 'Baik' },
        { value: 'cukup', label: 'Cukup' },
        { value: 'kurang', label: 'Kurang' },
    ];

    const handleSubmit = (e) => {
        e.preventDefault();
        onSubmit?.(form);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormInput
                    type="select"
                    label="Santri"
                    name="santri_id"
                    value={form.santri_id}
                    onChange={(e) => set('santri_id', e.target.value)}
                    options={santriOptions}
                    required
                />
                <FormInput
                    type="number"
                    label="Guru ID (opsional)"
                    name="guru_id"
                    value={form.guru_id}
                    onChange={(e) => set('guru_id', e.target.value)}
                    placeholder="Isi id guru atau biarkan kosong"
                />
                <FormInput
                    type="number"
                    label="Kelas ID (opsional)"
                    name="kelas_id"
                    value={form.kelas_id}
                    onChange={(e) => set('kelas_id', e.target.value)}
                    placeholder="Isi id kelas jika ada"
                />
                <FormInput
                    type="date"
                    label="Tanggal Setor"
                    name="tanggal_setor"
                    value={form.tanggal_setor}
                    onChange={(e) => set('tanggal_setor', e.target.value)}
                    required
                />
                <FormInput
                    type="number"
                    label="Juz"
                    name="juz"
                    value={form.juz}
                    onChange={(e) => set('juz', e.target.value)}
                    placeholder="Misal 30"
                    required
                />
                <FormInput
                    label="Dari Surat"
                    name="dari_surat"
                    value={form.dari_surat}
                    onChange={(e) => set('dari_surat', e.target.value)}
                    placeholder="Contoh: An-Naba"
                    required
                />
                <FormInput
                    type="number"
                    label="Dari Ayat"
                    name="dari_ayat"
                    value={form.dari_ayat}
                    onChange={(e) => set('dari_ayat', e.target.value)}
                    required
                />
                <FormInput
                    label="Sampai Surat"
                    name="sampai_surat"
                    value={form.sampai_surat}
                    onChange={(e) => set('sampai_surat', e.target.value)}
                    placeholder="Contoh: An-Naba"
                    required
                />
                <FormInput
                    type="number"
                    label="Sampai Ayat"
                    name="sampai_ayat"
                    value={form.sampai_ayat}
                    onChange={(e) => set('sampai_ayat', e.target.value)}
                    required
                />
                <FormInput
                    type="select"
                    label="Kategori"
                    name="kategori"
                    value={form.kategori}
                    onChange={(e) => set('kategori', e.target.value)}
                    options={kategoriOptions}
                    required
                />
                <FormInput
                    type="select"
                    label="Nilai"
                    name="nilai"
                    value={form.nilai}
                    onChange={(e) => set('nilai', e.target.value)}
                    options={nilaiOptions}
                    required
                />
            </div>
            <FormInput
                type="textarea"
                label="Catatan"
                name="catatan"
                value={form.catatan}
                onChange={(e) => set('catatan', e.target.value)}
                placeholder="Catatan tambahan"
            />
            <div className="flex items-center justify-end space-x-2">
                <button type="submit" className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </form>
    );
};

export default HafalanForm;
