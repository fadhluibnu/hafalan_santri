import { router } from '@inertiajs/react';
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

function buildPrintUrl(baseUrl, nis, filters) {
    const query = new URLSearchParams(cleanParams(filters)).toString();
    if (!query) return `${baseUrl}/laporan/raport/${nis}/pdf`;
    return `${baseUrl}/laporan/raport/${nis}/pdf?${query}`;
}

export default function RaportPage(props) {
    const {
        authRole,
        baseUrl,
        filters,
        rows = [],
        pondokOptions = [],
        requiresPondokSelection = false,
        tahunAjarans = [],
        kelasOptions = [],
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
        router.get(`${baseUrl}/laporan/raport`, cleanParams(localFilters), {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <RoleLayout authRole={authRole} title="Cetak Raport">
            <div className="space-y-4">
                <LaporanNav baseUrl={baseUrl} current="raport" pondokId={localFilters.pondok_id} />

                <CommonFilterForm
                    filters={localFilters}
                    setFilters={setLocalFilters}
                    onSubmit={submit}
                    requiresPondokSelection={requiresPondokSelection}
                    pondokOptions={pondokOptions}
                    tahunAjarans={tahunAjarans}
                    kelasOptions={kelasOptions}
                />

                {!canLoadData && (
                    <div className="rounded-md border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-700">
                        Pilih pondok terlebih dahulu untuk menampilkan data raport.
                    </div>
                )}

                {canLoadData && (
                    <div className="rounded-lg border bg-white p-4">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[780px]">
                                <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                    <tr>
                                        <th className="px-3 py-2">NIS</th>
                                        <th className="px-3 py-2">Nama</th>
                                        <th className="px-3 py-2">Kelas</th>
                                        <th className="px-3 py-2">Tahun Ajaran</th>
                                        <th className="px-3 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="text-sm">
                                    {rows.length === 0 && (
                                        <tr>
                                            <td className="px-3 py-4 text-center text-gray-500" colSpan={5}>
                                                Tidak ada data untuk dicetak sebagai raport.
                                            </td>
                                        </tr>
                                    )}
                                    {rows.map((row) => (
                                        <tr key={`${row.santri_id}-${row.kelas_id}-${row.tahun_ajaran_id}`} className="border-b">
                                            <td className="px-3 py-2">{row.nis || '-'}</td>
                                            <td className="px-3 py-2">{row.nama}</td>
                                            <td className="px-3 py-2">{row.kelas_nama || '-'}</td>
                                            <td className="px-3 py-2">{row.tahun_ajaran_nama || '-'}</td>
                                            <td className="px-3 py-2">
                                                <a
                                                    href={buildPrintUrl(baseUrl, row.nis, localFilters)}
                                                    className="rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                                                >
                                                    Cetak Raport
                                                </a>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </RoleLayout>
    );
}
