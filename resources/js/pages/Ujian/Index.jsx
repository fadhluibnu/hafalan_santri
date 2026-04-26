import { Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import RoleLayout from '../shared/RoleLayout';

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

export default function UjianIndex(props) {
    const {
        authRole,
        isReadOnly = true,
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
        search: filters?.search ?? '',
        tahun_ajaran_id: filters?.tahun_ajaran_id ? String(filters.tahun_ajaran_id) : '',
        kelas_id: filters?.kelas_id ? String(filters.kelas_id) : '',
    });

    const canLoadData = useMemo(() => {
        if (!requiresPondokSelection) return true;
        return !!localFilters.pondok_id;
    }, [requiresPondokSelection, localFilters.pondok_id]);

    const applyFilters = (e) => {
        e.preventDefault();
        router.get(`${baseUrl}/ujian`, cleanParams(localFilters), {
            preserveState: true,
            replace: true,
        });
    };

    const detailLink = (ujianId) => {
        const query = requiresPondokSelection && localFilters.pondok_id
            ? `?pondok_id=${localFilters.pondok_id}`
            : '';
        return `${baseUrl}/ujian/${ujianId}${query}`;
    };

    return (
        <RoleLayout authRole={authRole} title="Ujian">
            <div className="space-y-4">
                <form onSubmit={applyFilters} className="rounded-lg border bg-white p-4">
                    <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
                        {requiresPondokSelection && (
                            <div>
                                <label className="mb-1 block text-sm text-gray-600">Pondok</label>
                                <select
                                    value={localFilters.pondok_id}
                                    onChange={(e) => setLocalFilters((prev) => ({ ...prev, pondok_id: e.target.value }))}
                                    className="w-full rounded-md border px-3 py-2 text-sm"
                                >
                                    <option value="">-- Pilih Pondok --</option>
                                    {pondokOptions.map((pondok) => (
                                        <option key={pondok.id} value={pondok.id}>
                                            {pondok.nama}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}

                        <div>
                            <label className="mb-1 block text-sm text-gray-600">Tahun Ajaran</label>
                            <select
                                value={localFilters.tahun_ajaran_id}
                                onChange={(e) => setLocalFilters((prev) => ({ ...prev, tahun_ajaran_id: e.target.value }))}
                                className="w-full rounded-md border px-3 py-2 text-sm"
                            >
                                <option value="">-- Semua Tahun --</option>
                                {tahunAjarans.map((ta) => (
                                    <option key={ta.id} value={ta.id}>
                                        {ta.nama}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="mb-1 block text-sm text-gray-600">Kelas</label>
                            <select
                                value={localFilters.kelas_id}
                                onChange={(e) => setLocalFilters((prev) => ({ ...prev, kelas_id: e.target.value }))}
                                className="w-full rounded-md border px-3 py-2 text-sm"
                            >
                                <option value="">-- Semua Kelas --</option>
                                {kelasOptions.map((kelas) => (
                                    <option key={kelas.id} value={kelas.id}>
                                        {kelas.nama}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="mb-1 block text-sm text-gray-600">Search</label>
                            <input
                                type="text"
                                value={localFilters.search}
                                onChange={(e) => setLocalFilters((prev) => ({ ...prev, search: e.target.value }))}
                                placeholder="Nama ujian/kelas"
                                className="w-full rounded-md border px-3 py-2 text-sm"
                            />
                        </div>
                    </div>

                    <div className="mt-3 flex items-center justify-between">
                        <button type="submit" className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Terapkan Filter
                        </button>

                        {!isReadOnly && (
                            <Link href={`${baseUrl}/ujian/create`} className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Tambah Ujian
                            </Link>
                        )}
                    </div>
                </form>

                {!canLoadData && (
                    <div className="rounded-md border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-700">
                        Pilih pondok terlebih dahulu untuk melihat data ujian.
                    </div>
                )}

                {canLoadData && (
                    <div className="rounded-lg border bg-white p-4">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[760px]">
                                <thead className="border-b bg-gray-50 text-left text-sm text-gray-600">
                                    <tr>
                                        <th className="px-3 py-2">Nama Ujian</th>
                                        <th className="px-3 py-2">Tanggal</th>
                                        <th className="px-3 py-2">Kelas</th>
                                        <th className="px-3 py-2">Tahun Ajaran</th>
                                        <th className="px-3 py-2">Penguji</th>
                                        <th className="px-3 py-2">Peserta</th>
                                        <th className="px-3 py-2">Status</th>
                                        <th className="px-3 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="text-sm">
                                    {(ujians?.data || []).length === 0 && (
                                        <tr>
                                            <td className="px-3 py-4 text-center text-gray-500" colSpan={8}>
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
                                                <span className={`rounded-full px-2 py-1 text-xs ${
                                                    row.status === 'selesai'
                                                        ? 'bg-green-100 text-green-700'
                                                        : 'bg-yellow-100 text-yellow-700'
                                                }`}>
                                                    {row.status}
                                                </span>
                                            </td>
                                            <td className="px-3 py-2">
                                                <Link href={detailLink(row.id)} className="text-blue-600 hover:text-blue-800">
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
