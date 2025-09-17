import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function Layouts({ children, title = 'Super Admin Dashboard' }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { auth } = usePage().props;

    return (
        <div className="flex h-screen bg-gray-50">
            {/* Sidebar */}
            <div 
                className={`bg-white w-64 fixed h-screen shadow transform ${
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                } md:translate-x-0 transition-transform duration-300 ease-in-out`}
            >
                <div className="flex items-center justify-center p-6 border-b border-gray-200">
                    <span className="text-2xl font-bold text-blue-600">Hafalan Santri</span>
                </div>
                <nav className="mt-6">
                    <Link 
                        href="/super-admin" 
                        className="flex items-center px-6 py-3 text-gray-500 hover:bg-blue-50 hover:text-blue-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </Link>
                    <Link 
                        href="/super-admin/pondok" 
                        className="flex items-center px-6 py-3 text-gray-500 hover:bg-blue-50 hover:text-blue-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Pondok</span>
                    </Link>
                    <Link 
                        href="/super-admin/admin-cabang" 
                        className="flex items-center px-6 py-3 text-gray-500 hover:bg-blue-50 hover:text-blue-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Admin Cabang</span>
                    </Link>
                    <Link 
                        href="/logout" 
                        method="post" 
                        as="button" 
                        className="flex items-center px-6 py-3 text-gray-500 hover:bg-red-50 hover:text-red-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </Link>
                </nav>
            </div>

            {/* Main Content */}
            <div className="flex-1 md:ml-64">
                {/* Top Navigation */}
                <div className="bg-white shadow-sm">
                    <div className="flex justify-between items-center py-4 px-6">
                        <button 
                            className="md:hidden text-gray-500"
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 className="text-xl font-semibold text-gray-800">{title}</h1>
                        <div className="flex items-center">
                            <span className="text-sm text-gray-600 mr-2">
                                {auth?.user?.name || 'Super Admin'}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {/* Page Content */}
                <div className="p-6">
                    {children}
                </div>
            </div>
        </div>
    );
}
