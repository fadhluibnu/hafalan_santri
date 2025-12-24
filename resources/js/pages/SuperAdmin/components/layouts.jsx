import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function Layout({ children, title }) {
    const { auth } = usePage().props;
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const [isProfileDropdownOpen, setIsProfileDropdownOpen] = useState(false);

    const toggleSidebar = () => {
        setIsSidebarOpen(!isSidebarOpen);
    };

    const toggleProfileDropdown = () => {
        setIsProfileDropdownOpen(!isProfileDropdownOpen);
    };

    return (
        <div className="flex h-screen bg-gray-100">
            {/* Sidebar */}
            <div className={`${isSidebarOpen ? 'w-64' : 'w-20'} bg-white shadow-md transition-all duration-300 ease-in-out`}>
                <div className="flex items-center justify-between p-4">
                    {isSidebarOpen ? (
                        <span className="text-xl font-semibold text-gray-800">Hafalan Santri</span>
                    ) : (
                        <span className="text-xl font-semibold text-gray-800">HS</span>
                    )}
                    <button onClick={toggleSidebar} className="rounded-full p-1 hover:bg-gray-100">
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

                <nav className="mt-6">
                    <div className="px-4 py-2">
                        <Link
                            href="/super-admin/"
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
                            href="/super-admin/pondok"
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
                            {isSidebarOpen && <span>Pondok</span>}
                        </Link>
                    </div>
                    <div className="px-4 py-2">
                        <Link
                            href="/super-admin/admin-cabang"
                            className="flex items-center rounded-md px-4 py-2 text-gray-700 transition duration-150 ease-in-out hover:bg-green-100 hover:text-green-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                            {isSidebarOpen && <span>Admin Cabang</span>}
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
                <div className="border-b border-gray-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between px-6 py-4">
                        {/* Header Title */}
                        <h1 className="text-2xl font-semibold text-gray-800">{title}</h1>

                        {/* Profile Section */}
                        <div className="relative">
                            <button
                                onClick={toggleProfileDropdown}
                                className="flex items-center space-x-3 rounded-lg px-3 py-2 transition duration-150 ease-in-out hover:bg-gray-50"
                            >
                                {/* Avatar */}
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 font-semibold text-white">
                                    {auth?.user?.name?.charAt(0).toUpperCase() || 'U'}
                                </div>
                                {/* User Info */}
                                <div className="text-left">
                                    <p className="text-sm font-medium text-gray-800">{auth?.user?.name || 'User'}</p>
                                    <p className="text-xs text-gray-500">Super Admin</p>
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
                                <div className="absolute right-0 z-50 mt-2 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                                    <div className="border-b border-gray-100 px-4 py-3">
                                        <p className="text-sm font-medium text-gray-800">{auth?.user?.name || 'User'}</p>
                                        <p className="truncate text-xs text-gray-500">{auth?.user?.email || ''}</p>
                                    </div>
                                    <Link
                                        href="/logout"
                                        method="get"
                                        as="a"
                                        className="flex items-center px-4 py-2 text-sm text-red-600 transition duration-150 ease-in-out hover:bg-red-50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            className="mr-2 h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
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
    );
}
