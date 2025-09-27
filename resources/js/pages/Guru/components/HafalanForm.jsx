import { useMemo } from 'react';
import { useForm } from '@inertiajs/react';
import FormInput from '../../SuperAdmin/components/FormInput';

// This form aligns with App\Models\Hafalan::$fillable
const HafalanForm = ({ formData, setFormData, onSubmit, processing, classes = [], santrisByClass = {}, gurus = [], currentGuruId }) => {
	const kelasOptions = classes.map((c) => ({ value: c.id, label: c.nama }));
	const guruOptions = gurus.map((g) => ({ value: g.id, label: g.nama }));

	const santriOptions = useMemo(() => {
		const kelasId = formData.kelas_id;
		if (!kelasId || !santrisByClass[kelasId]) return [];
		return santrisByClass[kelasId].map((s) => ({ value: s.id, label: `${s.nama} (${s.nis ?? 'NIS'})` }));
	}, [formData.kelas_id, santrisByClass]);

	const set = (name, value) => {
		setFormData(name, value);
		if (name === 'kelas_id') {
			// reset santri ketika kelas berubah
			setFormData('santri_id', '');
		}
	};

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

	return (
		<form onSubmit={onSubmit} className="space-y-4">
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
					label="Guru"
					name="guru_id"
					value={formData.guru_id}
					onChange={(e) => set('guru_id', e.target.value)}
					options={guruOptions}
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
					label="Dari Surat"
					name="dari_surat"
					value={formData.dari_surat}
					onChange={(e) => set('dari_surat', e.target.value)}
					placeholder="Contoh: An-Naba"
					required
				/>
				<FormInput
					type="number"
					label="Dari Ayat"
					name="dari_ayat"
					value={formData.dari_ayat}
					onChange={(e) => set('dari_ayat', e.target.value)}
					required
				/>
				<FormInput
					label="Sampai Surat"
					name="sampai_surat"
					value={formData.sampai_surat}
					onChange={(e) => set('sampai_surat', e.target.value)}
					placeholder="Contoh: An-Naba"
					required
				/>
				<FormInput
					type="number"
					label="Sampai Ayat"
					name="sampai_ayat"
					value={formData.sampai_ayat}
					onChange={(e) => set('sampai_ayat', e.target.value)}
					required
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
					disabled={processing}
					className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
				>
					{processing ? 'Menyimpan...' : 'Simpan'}
				</button>
			</div>
		</form>
	);
};

// export default HafalanForm;
// 					name="nilai"
// 					value={form.data.nilai}
// 					onChange={(e) => set('nilai', e.target.value)}
// 					options={nilaiOptions}
// 					required
// 				/>
// 			</div>
// 			<FormInput
// 				type="textarea"
// 				label="Catatan"
// 				name="catatan"
// 				value={form.data.catatan}
// 				onChange={(e) => set('catatan', e.target.value)}
// 				placeholder="Catatan tambahan"
// 			/>
// 			<div className="flex items-center justify-end space-x-2">
// 				<button
// 					type="submit"
// 					disabled={form.processing}
// 					className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
// 				>
// 					{form.processing ? 'Menyimpan...' : 'Simpan'}
// 				</button>
// 			</div>
// 		</form>
// 	);
// };

export default HafalanForm;
