import { Link } from '@inertiajs/react';

export default function LaporanNav({ baseUrl, current, pondokId = '' }) {
    const suffix = pondokId ? `?pondok_id=${pondokId}` : '';
    const menus = [
        { key: 'daftar-santri', label: 'Daftar Santri', href: `${baseUrl}/laporan/daftar-santri${suffix}` },
        { key: 'cetak-santri', label: 'Cetak Santri', href: `${baseUrl}/laporan/cetak-santri${suffix}` },
        { key: 'ujian', label: 'Laporan Ujian', href: `${baseUrl}/laporan/ujian${suffix}` },
        { key: 'raport', label: 'Cetak Raport', href: `${baseUrl}/laporan/raport${suffix}` },
    ];

    return (
        <div className="flex flex-wrap gap-2">
            {menus.map((menu) => (
                <Link
                    key={menu.key}
                    href={menu.href}
                    className={`rounded-md px-3 py-2 text-sm font-medium ${
                        current === menu.key
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }`}
                >
                    {menu.label}
                </Link>
            ))}
        </div>
    );
}
