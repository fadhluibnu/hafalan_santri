import { Link } from '@inertiajs/react';

/**
 * Navigasi antar halaman laporan.
 *
 * `authRole` menentukan menu yang tampil: "Laporan Wali" hanya untuk admin,
 * sejalan dengan route-nya yang tidak didaftarkan pada grup ustadz. Tanpa ini,
 * ustadz akan melihat tautan yang pasti menghasilkan 404.
 */
export default function LaporanNav({ baseUrl, current, pondokId = '', authRole = null }) {
    const suffix = pondokId ? `?pondok_id=${pondokId}` : '';
    const menus = [
        { key: 'daftar-santri', label: 'Daftar Santri', href: `${baseUrl}/laporan/daftar-santri${suffix}` },
        { key: 'cetak-santri', label: 'Cetak Santri', href: `${baseUrl}/laporan/cetak-santri${suffix}` },
        { key: 'ujian', label: 'Laporan Ujian', href: `${baseUrl}/laporan/ujian${suffix}` },
        { key: 'raport', label: 'Cetak Raport', href: `${baseUrl}/laporan/raport${suffix}` },
    ];

    if (authRole === null || authRole === 'super_admin' || authRole === 'admin_cabang') {
        menus.push({
            key: 'laporan-wali',
            label: 'Laporan Wali',
            href: `${baseUrl}/laporan/laporan-wali${suffix}`,
        });
    }

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
