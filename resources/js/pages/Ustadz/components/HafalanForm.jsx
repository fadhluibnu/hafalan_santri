import { useMemo } from 'react';
import FormInput from '../../SuperAdmin/components/FormInput';

// This form aligns with App\Models\Hafalan::$fillable
const HafalanForm = ({ formData, setFormData, onSubmit, processing, classes = [], santrisByClass = {}, ustadzs = [], surahs = [], nilaiOptions = [] }) => {
	const kelasOptions = classes.map((c) => ({ value: c.id, label: c.nama }));
	const ustadzOptions = ustadzs.map((g) => ({ value: g.id, label: g.nama }));
	const surahOptions = surahs.map((s) => ({ value: s.id, label: s.name }));

	const santriOptions = useMemo(() => {
		const kelasId = formData.kelas_id;
		if (!kelasId || !santrisByClass[kelasId]) return [];
		return santrisByClass[kelasId].map((s) => ({ value: s.id, label: `${s.nama} (${s.nis ?? 'NIS'})` }));
	}, [formData.kelas_id, santrisByClass]);

	const dariAyatOptions = useMemo(() => {
		const surahId = formData.dari_surat;
		if (!surahId) return [];
		const surah = surahs.find((s) => s.id == surahId);
		if (!surah) return [];
		return Array.from({ length: surah.jumlah_ayat }, (_, i) => ({ value: i + 1, label: (i + 1).toString() }));
	}, [formData.dari_surat, surahs]);

	const sampaiAyatOptions = useMemo(() => {
		const surahId = formData.sampai_surat;
		if (!surahId) return [];
		const surah = surahs.find((s) => s.id == surahId);
		if (!surah) return [];
		return Array.from({ length: surah.jumlah_ayat }, (_, i) => ({ value: i + 1, label: (i + 1).toString() }));
	}, [formData.sampai_surat, surahs]);

	const set = (name, value) => {
		setFormData(name, value);
		if (name === 'kelas_id') {
			// reset santri ketika kelas berubah
			setFormData('santri_id', '');
		}
		if (name === 'dari_surat') {
			// reset dari_ayat ketika dari_surat berubah
			setFormData('dari_ayat', '');
		}
		if (name === 'sampai_surat') {
			// reset sampai_ayat ketika sampai_surat berubah
			setFormData('sampai_ayat', '');
		}
	};

	const kategoriOptions = [
		{ value: 'Ziyadah', label: 'Ziyadah' },
		{ value: 'murojaah', label: 'Murojaah' },
		{ value: 'semaan', label: 'Sema’an' },
	];
	const hasNilaiOptions = nilaiOptions.length > 0;

	return (
		<form onSubmit={onSubmit} className="space-y-4">
			{!hasNilaiOptions && (
				<div className="rounded-md border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
					Skema penilaian aktif belum tersedia.
				</div>
			)}
			<div className="grid grid-cols-1 gap-4 md:grid-cols-2">
				<FormInput
					type="select"
					label="Kelas"
					name="kelas_id"
					value={formData.kelas_id}
					onChange={(e) => set('kelas_id', e.target.value)}
					options={kelasOptions}
					required
				/>
				<FormInput
					type="select"
					label="Santri"
					name="santri_id"
					value={formData.santri_id}
					onChange={(e) => set('santri_id', e.target.value)}
					options={santriOptions}
					required
					disabled={!formData.kelas_id}
				/>
				<FormInput
					type="select"
					label="ustadz"
					name="ustadz_id"
					value={formData.ustadz_id}
					onChange={(e) => set('ustadz_id', e.target.value)}
					options={ustadzOptions}
					required
				/>
				<FormInput
					type="date"
					label="Tanggal Setor"
					name="tanggal_setor"
					value={formData.tanggal_setor}
					onChange={(e) => set('tanggal_setor', e.target.value)}
					required
				/>
				<FormInput
					type="number"
					label="Juz"
					name="juz"
					value={formData.juz}
					onChange={(e) => set('juz', e.target.value)}
					placeholder="Misal 30"
					required
				/>
				<FormInput
					type="select"
					label="Dari Surat"
					name="dari_surat"
					value={formData.dari_surat}
					onChange={(e) => set('dari_surat', e.target.value)}
					options={surahOptions}
					required
				/>
				<FormInput
					type="select"
					label="Dari Ayat"
					name="dari_ayat"
					value={formData.dari_ayat}
					onChange={(e) => set('dari_ayat', e.target.value)}
					options={dariAyatOptions}
					required
					disabled={!formData.dari_surat}
				/>
				<FormInput
					type="select"
					label="Sampai Surat"
					name="sampai_surat"
					value={formData.sampai_surat}
					onChange={(e) => set('sampai_surat', e.target.value)}
					options={surahOptions}
					required
				/>
				<FormInput
					type="select"
					label="Sampai Ayat"
					name="sampai_ayat"
					value={formData.sampai_ayat}
					onChange={(e) => set('sampai_ayat', e.target.value)}
					options={sampaiAyatOptions}
					required
					disabled={!formData.sampai_surat}
				/>
				<FormInput
					type="select"
					label="Kategori"
					name="kategori"
					value={formData.kategori}
					onChange={(e) => set('kategori', e.target.value)}
					options={kategoriOptions}
					required
				/>
				<FormInput
					type="select"
					label="Nilai"
					name="nilai"
					value={formData.nilai}
					onChange={(e) => set('nilai', e.target.value)}
					options={nilaiOptions}
					required
					disabled={!hasNilaiOptions}
				/>
			</div>
			<FormInput
				type="textarea"
				label="Catatan"
				name="catatan"
				value={formData.catatan}
				onChange={(e) => set('catatan', e.target.value)}
				placeholder="Catatan tambahan"
			/>
			<div className="flex items-center justify-end space-x-2">
				<button
					type="submit"
					disabled={processing || !hasNilaiOptions}
					className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
				>
					{processing ? 'Menyimpan...' : 'Simpan'}
				</button>
			</div>
		</form>
	);
};

export default HafalanForm;
