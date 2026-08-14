import { Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import RoleLayout from '../shared/RoleLayout';
import CommonFilterForm from './components/CommonFilterForm';
import LaporanNav from './components/LaporanNav';

function cleanParams(filters) {
    const params = {};
    Object.keys(filters).forEach((key) => {
        const value = filters[key];
        if (value !== null && value !== undefined && value !== '') {
            params[key] = value;
        }
    });
    return params;
}

function detailUrl(baseUrl, id, filters) {
    const query = new URLSearchParams(cleanParams(filters)).toString();
    if (!query) return `${baseUrl}/laporan/ujian/${id}`;
    return `${baseUrl}/laporan/ujian/${id}?${query}`;
}

export default function LaporanUjianPage(props) {
    const {
        authRole,
        baseUrl,
        filters,
        pondokOptions = [],
        requiresPondokSelection = false,
        tahunAjarans = [],
        kelasOptions = [],
        ujians = null,
    } = props;

    const [localFilters, setLocalFilters] = useState({
        pondok_id: filters?.pondok_id ? String(filters.pondok_id) : '',
        tahun_ajaran_id: filters?.tahun_ajaran_id ? String(filters.tahun_ajaran_id) : '',
        kelas_id: filters?.kelas_id ? String(filters.kelas_id) : '',
        search: filters?.search ?? '',
    });

    const canLoadData = useMemo(() => {
        if (!requiresPondokSelection) return true;
        return !!localFilters.pondok_id;
    }, [requiresPondokSelection, localFilters.pondok_id]);

    const submit = (e) => {
        e.preventDefault();
        router.get(`${baseUrl}/laporan/ujian`, cleanParams(localFilters), {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <RoleLayout authRole={authRole} title="Laporan Ujian">
            <div className="space-y-4">
                <LaporanNav baseUrl={baseUrl} current="ujian" pondokId={localFilters.pondok_id} authRole={authRole} />

                <CommonFilterForm
                    filters={localFilters}
                    setFilters={setLocalFilters}
                    onSubmit={submit}
                    requiresPondokSelection={requiresPondokSelection}
                    pondokOptions={pondokOptions}
                    tahunAjarans={tahunAjarans}
                    kelasOptions={kelasOptions}
                    searchPlaceholder="Cari nama ujian / kelas"
                />

                {!canLoadData && (
                    <div className="rounded-md border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-700">
                        Pilih pondok terlebih dahulu untuk menampilkan laporan ujian.
                    </div>
                )}

                {canLoadData && (
                    <div className="rounded-lg border bg-white p-4">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[820px]">
                                <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                    <tr>
                                        <th className="px-3 py-2">Nama Ujian</th>
                                        <th className="px-3 py-2">Tanggal</th>
                                        <th className="px-3 py-2">Kelas</th>
                                        <th className="px-3 py-2">Tahun Ajaran</th>
                                        <th className="px-3 py-2">Penguji</th>
                                        <th className="px-3 py-2">Peserta</th>
                                        <th className="px-3 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="text-sm">
                                    {(ujians?.data || []).length === 0 && (
                                        <tr>
                                            <td className="px-3 py-4 text-center text-gray-500" colSpan={7}>
                                                Tidak ada data ujian.
                                            </td>
                                        </tr>
                                    )}
                                    {(ujians?.data || []).map((row) => (
                                        <tr key={row.id} className="border-b">
                                            <td className="px-3 py-2">{row.nama}</td>
                                            <td className="px-3 py-2">{row.tanggal_ujian}</td>
                                            <td className="px-3 py-2">{row.kelas_nama}</td>
                                            <td className="px-3 py-2">{row.tahun_ajaran_nama}</td>
                                            <td className="px-3 py-2">{row.ustadz_nama}</td>
                                            <td className="px-3 py-2">{row.jumlah_peserta}</td>
                                            <td className="px-3 py-2">
                                                <Link href={detailUrl(baseUrl, row.id, localFilters)} className="text-blue-600 hover:text-blue-800">
                                                    Detail
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {Array.isArray(ujians?.links) && (
                            <div className="mt-4 flex flex-wrap gap-2">
                                {ujians.links.map((link, idx) => (
                                    link.url ? (
                                        <Link
                                            key={idx}
                                            href={link.url}
                                            preserveScroll
                                            className={`rounded border px-3 py-1 text-sm ${
                                                link.active
                                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span
                                            key={idx}
                                            className="rounded border border-gray-200 px-3 py-1 text-sm text-gray-400"
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    )
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </div>
        </RoleLayout>
    );
}
