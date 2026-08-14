import { Link } from '@inertiajs/react';
import RoleLayout from '../shared/RoleLayout';

function cleanParams(params) {
    const output = {};
    Object.keys(params || {}).forEach((key) => {
        const value = params[key];
        if (value !== null && value !== undefined && value !== '') {
            output[key] = value;
        }
    });
    return output;
}

export default function LaporanUjianDetailPage({ authRole, baseUrl, query = {}, ujian }) {
    const queryString = new URLSearchParams(cleanParams(query)).toString();
    const backUrl = queryString
        ? `${baseUrl}/laporan/ujian?${queryString}`
        : `${baseUrl}/laporan/ujian`;
    const pdfUrl = queryString
        ? `${baseUrl}/laporan/ujian/${ujian.id}/pdf?${queryString}`
        : `${baseUrl}/laporan/ujian/${ujian.id}/pdf`;

    const rawItems = Array.isArray(ujian?.skema_snapshot?.items) && ujian.skema_snapshot.items.length > 0
        ? ujian.skema_snapshot.items
        : Array.isArray(ujian?.skema_snapshot?.labels)
            ? ujian.skema_snapshot.labels.map((label) => ({
                nama: label,
                singkatan: label,
            }))
            : [];

    const nilaiOptions = rawItems
        .map((item) => ({
            nama: String(item?.nama || '').trim(),
            singkatan: String(item?.singkatan || '').trim().toUpperCase(),
        }))
        .filter((item) => item.nama && item.singkatan);

    const formatNilai = (nilaiLabel, nilaiAngka = null) => {
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

    return (
        <RoleLayout authRole={authRole} title="Detail Laporan Ujian">
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
                            <Link href={backUrl} className="rounded-md border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Kembali
                            </Link>
                            <a href={pdfUrl} className="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                Export PDF
                            </a>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-2 text-sm md:grid-cols-2">
                        <div>Tanggal Ujian: <span className="font-medium">{ujian.tanggal_ujian}</span></div>
                        <div>Penguji: <span className="font-medium">{ujian.ustadz}</span></div>
                        <div>Skema Nilai: <span className="font-medium">{ujian?.skema_snapshot?.nama || '-'}</span></div>
                        <div>Status: <span className="font-medium">{ujian.status}</span></div>
                    </div>
                </div>

                <div className="rounded-lg border bg-white p-4">
                    <div className="overflow-x-auto">
                        <table className="w-full min-w-[760px]">
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
                                            Belum ada peserta ujian.
                                        </td>
                                    </tr>
                                )}
                                {ujian.nilai.map((row) => (
                                    <tr key={row.id} className="border-b">
                                        <td className="px-3 py-2">{row.nis || '-'}</td>
                                        <td className="px-3 py-2">{row.nama}</td>
                                        <td className="px-3 py-2">{formatNilai(row.nilai_label, row.nilai_angka)}</td>
                                        <td className="px-3 py-2">{row.catatan || '-'}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </RoleLayout>
    );
}
