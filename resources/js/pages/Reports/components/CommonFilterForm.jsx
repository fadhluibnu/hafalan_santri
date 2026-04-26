export default function CommonFilterForm({
    filters,
    setFilters,
    onSubmit,
    requiresPondokSelection = false,
    pondokOptions = [],
    tahunAjarans = [],
    kelasOptions = [],
    searchPlaceholder = 'Cari nama / NIS',
}) {
    return (
        <form onSubmit={onSubmit} className="rounded-lg border bg-white p-4">
            <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
                {requiresPondokSelection && (
                    <div>
                        <label className="mb-1 block text-sm text-gray-600">Pondok</label>
                        <select
                            value={filters.pondok_id || ''}
                            onChange={(e) => setFilters((prev) => ({ ...prev, pondok_id: e.target.value }))}
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
                        value={filters.tahun_ajaran_id || ''}
                        onChange={(e) => setFilters((prev) => ({ ...prev, tahun_ajaran_id: e.target.value }))}
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
                        value={filters.kelas_id || ''}
                        onChange={(e) => setFilters((prev) => ({ ...prev, kelas_id: e.target.value }))}
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
                        value={filters.search || ''}
                        onChange={(e) => setFilters((prev) => ({ ...prev, search: e.target.value }))}
                        className="w-full rounded-md border px-3 py-2 text-sm"
                        placeholder={searchPlaceholder}
                    />
                </div>
            </div>

            <div className="mt-3">
                <button type="submit" className="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Terapkan Filter
                </button>
            </div>
        </form>
    );
}
