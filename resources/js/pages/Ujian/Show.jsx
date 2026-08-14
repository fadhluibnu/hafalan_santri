import { Link, router, useForm } from '@inertiajs/react';
import { useMemo } from 'react';
import RoleLayout from '../shared/RoleLayout';

function buildBackLink(baseUrl, queryPondokId) {
    if (queryPondokId) {
        return `${baseUrl}/ujian?pondok_id=${queryPondokId}`;
    }
    return `${baseUrl}/ujian`;
}

export default function UjianShow({
    authRole,
    isReadOnly = true,
    baseUrl,
    ujian,
    queryPondokId = null,
}) {
    const snapshot = ujian?.skema_snapshot || {};
    const isNumeric = snapshot?.tipe === 'numeric';
    const items = Array.isArray(snapshot?.items) && snapshot.items.length > 0
        ? snapshot.items
        : Array.isArray(snapshot?.labels)
            ? snapshot.labels.map((label) => ({
                nama: label,
                singkatan: label,
            }))
            : [];
    const nilaiOptions = items
        .map((item) => ({
            nama: String(item?.nama || '').trim(),
            singkatan: String(item?.singkatan || '').trim().toUpperCase(),
        }))
        .filter((item) => item.nama && item.singkatan);

    const getNilaiDisplay = (nilaiLabel, nilaiAngka = null) => {
        const key = String(nilaiLabel || '').trim().toUpperCase();
        if (key) {
            const found = nilaiOptions.find((item) => item.singkatan === key);
            return found ? `${key} - ${found.nama}` : key;
        }

        if (nilaiAngka !== null && nilaiAngka !== undefined && nilaiAngka !== '') {
            return String(nilaiAngka);
        }

        return '-';
    };

    const nilaiRows = useMemo(
        () =>
            (ujian?.nilai || []).map((row) => ({
                id: row.id,
                nilai_label: row.nilai_label ? String(row.nilai_label).toUpperCase() : '',
                nilai_angka: row.nilai_angka !== null ? row.nilai_angka : '',
                catatan: row.catatan ?? '',
            })),
        [ujian]
    );

    const { data, setData, put, processing, errors } = useForm({
        nilai: nilaiRows,
    });

    const updateNilai = (index, key, value) => {
        const copy = [...data.nilai];
        copy[index] = { ...copy[index], [key]: value };
        setData('nilai', copy);
    };

    const submitNilai = (e) => {
        e.preventDefault();
        put(`${baseUrl}/ujian/${ujian.id}/nilai`, {
            preserveScroll: true,
        });
    };

    const changeStatus = (status) => {
        router.patch(
            `${baseUrl}/ujian/${ujian.id}/status`,
            { status },
            { preserveScroll: true }
        );
    };

    const removeUjian = () => {
        if (!confirm('Yakin ingin menghapus ujian ini?')) return;
        router.delete(`${baseUrl}/ujian/${ujian.id}`);
    };

    const reportDetailLink = queryPondokId
        ? `${baseUrl}/laporan/ujian/${ujian.id}?pondok_id=${queryPondokId}`
        : `${baseUrl}/laporan/ujian/${ujian.id}`;

    const reportPdfLink = queryPondokId
        ? `${baseUrl}/laporan/ujian/${ujian.id}/pdf?pondok_id=${queryPondokId}`
        : `${baseUrl}/laporan/ujian/${ujian.id}/pdf`;

    return (
        <RoleLayout authRole={authRole} title="Detail Ujian">
            <div className="space-y-4">
                <div className="rounded-lg border bg-white p-4">
                    <div className="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 className="text-lg font-semibold text-gray-800">{ujian.nama}</h2>
                            <p className="text-sm text-gray-600">
                                Kelas: {ujian.kelas} | Tahun Ajaran: {ujian.tahun_ajaran}
                            </p>
                        </div>
                        <div className="flex gap-2">
                            <Link href={buildBackLink(baseUrl, queryPondokId)} className="rounded-md border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Kembali
                            </Link>
                            <a href={reportPdfLink} className="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                Export PDF
                            </a>
                            <Link href={reportDetailLink} className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Lihat Halaman Laporan
                            </Link>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                        <div>Tanggal Ujian: <span className="font-medium">{ujian.tanggal_ujian}</span></div>
                        <div>Penguji: <span className="font-medium">{ujian.ustadz}</span></div>
                        <div>
                            Status:
                            <span className={`ml-2 rounded-full px-2 py-1 text-xs ${
                                ujian.status === 'selesai'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-yellow-100 text-yellow-700'
                            }`}>
                                {ujian.status}
                            </span>
                        </div>
                        <div>Skema Nilai: <span className="font-medium">{snapshot?.nama || '-'}</span></div>
                    </div>

                    {ujian.keterangan && (
                        <div className="mt-3 rounded-md bg-gray-50 p-3 text-sm text-gray-700">
                            {ujian.keterangan}
                        </div>
                    )}

                    {!isReadOnly && (
                        <div className="mt-3 flex flex-wrap gap-2">
                            <button
                                type="button"
                                onClick={() => changeStatus('draft')}
                                className="rounded-md border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                Set Draft
                            </button>
                            <button
                                type="button"
                                onClick={() => changeStatus('selesai')}
                                className="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                            >
                                Tandai Selesai
                            </button>
                            <button
                                type="button"
                                onClick={removeUjian}
                                className="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700"
                            >
                                Hapus Ujian
                            </button>
                        </div>
                    )}
                </div>

                <div className="rounded-lg border bg-white p-4">
                    <h3 className="mb-3 text-base font-semibold text-gray-800">Daftar Nilai Santri</h3>

                    {!isReadOnly && !isNumeric && nilaiOptions.length === 0 && (
                        <div className="mb-3 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                            Parameter penilaian belum tersedia pada snapshot ujian ini.
                        </div>
                    )}

                    {errors.nilai && (
                        <div className="mb-3 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {errors.nilai}
                        </div>
                    )}

                    <form onSubmit={submitNilai}>
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[780px]">
                                <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                    <tr>
                                        <th className="px-3 py-2">NIS</th>
                                        <th className="px-3 py-2">Nama</th>
                                        <th className="px-3 py-2">Nilai</th>
                                        <th className="px-3 py-2">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody className="text-sm">
                                    {ujian.nilai.length === 0 && (
                                        <tr>
                                            <td className="px-3 py-4 text-center text-gray-500" colSpan={4}>
                                                Tidak ada peserta pada ujian ini.
                                            </td>
                                        </tr>
                                    )}

                                    {ujian.nilai.map((row, index) => (
                                        <tr key={row.id} className="border-b">
                                            <td className="px-3 py-2">{row.nis || '-'}</td>
                                            <td className="px-3 py-2">{row.nama}</td>
                                            <td className="px-3 py-2">
                                                {isReadOnly ? (
                                                    <span>{getNilaiDisplay(row.nilai_label, row.nilai_angka)}</span>
                                                ) : isNumeric ? (
                                                    <input
                                                        type="number"
                                                        value={data.nilai[index]?.nilai_angka ?? ''}
                                                        onChange={(e) => updateNilai(index, 'nilai_angka', e.target.value)}
                                                        className="w-40 rounded-md border px-2 py-1"
                                                        step="0.01"
                                                        min="0"
                                                        max="100"
                                                        placeholder="0 - 100"
                                                    />
                                                ) : (
                                                    <select
                                                        value={data.nilai[index]?.nilai_label ?? ''}
                                                        onChange={(e) => updateNilai(index, 'nilai_label', e.target.value)}
                                                        className="w-40 rounded-md border px-2 py-1"
                                                    >
                                                        <option value="">-- Pilih --</option>
                                                        {nilaiOptions.map((item) => (
                                                            <option key={item.singkatan} value={item.singkatan}>
                                                                {item.singkatan} - {item.nama}
                                                            </option>
                                                        ))}
                                                    </select>
                                                )}
                                            </td>
                                            <td className="px-3 py-2">
                                                {isReadOnly ? (
                                                    <span>{row.catatan || '-'}</span>
                                                ) : (
                                                    <input
                                                        type="text"
                                                        value={data.nilai[index]?.catatan ?? ''}
                                                        onChange={(e) => updateNilai(index, 'catatan', e.target.value)}
                                                        className="w-full rounded-md border px-2 py-1"
                                                        placeholder="Catatan"
                                                    />
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {!isReadOnly && (
                            <div className="mt-4 flex justify-end">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                                >
                                    {processing ? 'Menyimpan...' : 'Simpan Nilai'}
                                </button>
                            </div>
                        )}
                    </form>
                </div>
            </div>
        </RoleLayout>
    );
}
