import { Link, usePage, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function Layout({ children, title }) {
    const { auth, pageTitle } = usePage().props;
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const [isProfileDropdownOpen, setIsProfileDropdownOpen] = useState(false);

    const toggleSidebar = () => {
        setIsSidebarOpen(!isSidebarOpen);
    };

    const toggleProfileDropdown = () => {
        setIsProfileDropdownOpen(!isProfileDropdownOpen);
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

                <nav className="mt-4 flex-1">
                    <div className="px-4 py-2">
                        <Link
                            href="/ustadz/"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                            {isSidebarOpen && <span>Dashboard</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/ustadz/hafalan"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                            {isSidebarOpen && <span>Daftar Hafalan</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/ustadz/santri"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            {isSidebarOpen && <span>Daftar Santri</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/ustadz/hafalan/create"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                            </svg>
                            {isSidebarOpen && <span>Input Setoran</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/ustadz/laporan"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M9 17v-2h6v2m-7 4h8a2 2 0 002-2v-8a2 2 0 00-2-2h-1V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2H6a2 2 0 00-2 2v8a2 2 0 002 2z"
                                />
                            </svg>
                            {isSidebarOpen && <span>Laporan</span>}
                        </Link>
                    </div>
                </nav>

                {/* Tambahkan menu logout */}
                <div className="mt-4 border-t px-4 py-2">
                    <Link
                        href="/logout"
                        method="get"
                        as="a"
                        className="flex items-center rounded-md px-4 py-2 text-red-600 transition duration-150 ease-in-out hover:bg-red-100 hover:text-red-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"
                            />
                        </svg>
                        {isSidebarOpen && <span>Logout</span>}
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
                                    <p className="text-xs text-gray-500">ustadz</p>
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
                    {/* Content */}
                    <div className="rounded-lg bg-white p-6 shadow-md">{children}</div>
                </div>
            </div>
        </div>
        </>
    );
}
