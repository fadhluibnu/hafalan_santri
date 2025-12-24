import { Link, usePage, router, Head } from '@inertiajs/react';
import { useState } from 'react';
import { route } from 'ziggy-js';

export default function Layout({ children, title = 'Admin Cabang' }) {
    const { auth, tahunAjaran, pageTitle } = usePage().props;
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const [isProfileDropdownOpen, setIsProfileDropdownOpen] = useState(false);

    const toggleSidebar = () => setIsSidebarOpen((v) => !v);

    const toggleProfileDropdown = () => {
        setIsProfileDropdownOpen(!isProfileDropdownOpen);
    };

    const handleTahunAjaranChange = (e) => {
        const tahunAjaranId = e.target.value;
        if (tahunAjaranId) {
            router.post(route('admin-cabang.set-tahun-ajaran'), {
                tahun_ajaran_id: tahunAjaranId
            }, {
                preserveScroll: true,
            });
        }
    };

    return (
        <>
            <Head title={title ? `${title} | ${pageTitle}` : pageTitle} />
            <div className="flex h-screen bg-gray-200">
                {/* Sidebar */}
            <div className={`${isSidebarOpen ? 'w-64' : 'w-20'} bg-white shadow-md transition-all duration-300 ease-in-out flex flex-col`}>
                <div className={`p-4 flex items-center ${isSidebarOpen ? 'justify-between' : 'justify-center'}`}>
                    {isSidebarOpen ? (
                        <img src="/img/logo-pppa.png" alt="Logo" className="h-10 w-auto mx-auto" />
                    ) : (
                        <img src="/img/logo-pppa.png" alt="Logo" className="h-10 w-auto mx-auto hidden" />
                    )}
                    <button onClick={toggleSidebar} className="p-1 rounded-full hover:bg-gray-100">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className="h-6 w-6 text-gray-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            {isSidebarOpen ? (
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            ) : (
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            )}
                        </svg>
                    </button>
                </div>

                {/* Tahun Ajaran Filter */}
                {isSidebarOpen && tahunAjaran?.list?.length > 0 && (
                    <div className="px-4 py-2 border-b border-gray-200">
                        <label className="block text-xs font-medium text-gray-500 mb-1">Tahun Ajaran</label>
                        <select
                            value={tahunAjaran.selected_id || ''}
                            onChange={handleTahunAjaranChange}
                            className="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        >
                            {tahunAjaran.list.map((ta) => (
                                <option key={ta.id} value={ta.id}>
                                    {ta.nama} {ta.is_active ? '(Aktif)' : ''}
                                </option>
                            ))}
                        </select>
                    </div>
                )}

                <nav className="mt-4 flex-1">
                    <div className="px-4 py-2">
                        <Link
                            href="/admin-cabang"
                            className={`flex items-center py-2 px-4 text-gray-700 hover:bg-green-100 hover:text-green-700 rounded-md transition duration-150 ease-in-out ${isSidebarOpen ? 'justify-start' : 'justify-center'}`}
                            title="Dashboard"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className={`h-5 w-5 ${isSidebarOpen ? 'mr-3' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                            {isSidebarOpen && <span className='font-semibold text-md'>Dashboard</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/admin-cabang/santri"
                            className={`flex items-center py-2 px-4 text-gray-700 hover:bg-green-100 hover:text-green-700 rounded-md transition duration-150 ease-in-out ${isSidebarOpen ? 'justify-start' : 'justify-center'}`}
                            title="Santri"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className={`h-5 w-5 ${isSidebarOpen ? 'mr-3' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                            {isSidebarOpen && <span className='font-semibold text-md'>Santri</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/admin-cabang/ustadz"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className={`h-5 w-5 ${isSidebarOpen ? 'mr-3' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                            {isSidebarOpen && <span>Ustadz</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/admin-cabang/tahun-ajaran"
                            className={`flex items-center py-2 px-4 text-gray-700 hover:bg-green-100 hover:text-green-700 rounded-md transition duration-150 ease-in-out ${isSidebarOpen ? 'justify-start' : 'justify-center'}`}
                            title="Tahun Ajaran"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className={`h-5 w-5 ${isSidebarOpen ? 'mr-3' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {isSidebarOpen && <span className='font-semibold text-md'>Tahun Ajaran</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/admin-cabang/struktur/kelas"
                            className={`flex items-center py-2 px-4 text-gray-700 hover:bg-green-100 hover:text-green-700 rounded-md transition duration-150 ease-in-out ${isSidebarOpen ? 'justify-start' : 'justify-center'}`}
                            title="Struktur - Kelas"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className={`h-5 w-5 ${isSidebarOpen ? 'mr-3' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            {isSidebarOpen && <span className='font-semibold text-md'>Penempatan Kelas</span>}
                        </Link>
                    </div>
                </nav>

                <div className="mt-4 border-t px-4 py-2">
                    <Link
                        href="/logout"
                        method="get"
                        as="a"
                        className={`flex items-center py-2 px-4 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-md transition duration-150 ease-in-out ${isSidebarOpen ? 'justify-start' : 'justify-center'}`}
                        title="Logout"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className={`h-5 w-5 ${isSidebarOpen ? 'mr-3' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"
                            />
                        </svg>
                        {isSidebarOpen && <span className='font-semibold text-md'>Logout</span>}
                    </Link>
                </div>
            </div>

            {/* Main Content */}
            <div className="flex-1 overflow-auto">
                {/* Top Navbar */}
                <div className="bg-white shadow-sm border-b border-gray-200">
                    <div className="flex items-center justify-between px-6 py-4">
                        {/* Header Title */}
                        <h1 className="text-2xl font-semibold text-gray-800">{title}</h1>

                        {/* Profile Section */}
                        <div className="relative">
                            <button
                                onClick={toggleProfileDropdown}
                                className="flex items-center space-x-3 hover:bg-gray-50 rounded-lg px-3 py-2 transition duration-150 ease-in-out"
                            >
                                {/* Avatar */}
                                <div className="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                    {auth?.user?.name?.charAt(0).toUpperCase() || 'U'}
                                </div>
                                {/* User Info */}
                                <div className="text-left">
                                    <p className="text-sm font-medium text-gray-800">{auth?.user?.name || 'User'}</p>
                                    <p className="text-xs text-gray-500">Admin Cabang</p>
                                </div>
                                {/* Dropdown Arrow */}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className={`h-4 w-4 text-gray-500 transition-transform duration-200 ${isProfileDropdownOpen ? 'rotate-180' : ''}`}
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {/* Dropdown Menu */}
                            {isProfileDropdownOpen && (
                                <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    <div className="px-4 py-3 border-b border-gray-100">
                                        <p className="text-sm font-medium text-gray-800">{auth?.user?.name || 'User'}</p>
                                        <p className="text-xs text-gray-500 truncate">{auth?.user?.email || ''}</p>
                                    </div>
                                    <Link
                                        href="/logout"
                                        method="get"
                                        as="a"
                                        className="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150 ease-in-out"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"
                                            />
                                        </svg>
                                        Logout
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="p-6">
                    <div className="rounded-lg bg-white p-6 shadow-md">{children}</div>
                </div>
            </div>
        </div>
        </>
    );
}

