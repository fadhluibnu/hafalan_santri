import { Link } from '@inertiajs/react';
import { useState } from 'react';

export default function Layout({ children, title }) {
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);

    const toggleSidebar = () => {
        setIsSidebarOpen(!isSidebarOpen);
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
                            href="/guru/"
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
                            href="/guru/hafalan"
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
                            href="/guru/hafalan/create"
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
                            href="/guru/laporan"
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
                <div className="p-6">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-2xl font-semibold text-gray-800">{title}</h1>
                    </div>

                    {/* Content */}
                    <div className="rounded-lg bg-white p-6 shadow-md">{children}</div>
                </div>
            </div>
        </div>
    );
}
