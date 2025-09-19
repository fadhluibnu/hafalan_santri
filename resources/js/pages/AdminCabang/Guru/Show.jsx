import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import Layout from '../components/Layout';

const GuruShow = ({ guru }) => {
    const [deleting, setDeleting] = useState(false);
    
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(date);
    };
    
    const handleDelete = () => {
        if (!confirm('Yakin ingin menghapus guru ini?')) return;
        
        setDeleting(true);
        router.delete(`/admin-cabang/guru/${guru.id}`, {
            onFinish: () => setDeleting(false)
        });
    };

    const labelJK = (v) => (v === 'L' ? 'Laki-laki' : v === 'P' ? 'Perempuan' : '-');

    return (
        <Layout title={`Detail Guru - ${guru.nama}`}>
            <div className="space-y-4">
                <div className="rounded-lg bg-white p-6 shadow-md">
                    <h3 className="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Data Pribadi</h3>
                    <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd className="mt-1 text-sm text-gray-900">
                                {(guru.gelar_awal ? guru.gelar_awal + ' ' : '') + 
                                 guru.nama + 
                                 (guru.gelar_akhir ? ', ' + guru.gelar_akhir : '')}
                            </dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">NIP</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.nip || '-'}</dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Tempat, Tanggal Lahir</dt>
                            <dd className="mt-1 text-sm text-gray-900">
                                {guru.tempat_lahir || '-'}, {formatDate(guru.tanggal_lahir)}
                            </dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                            <dd className="mt-1 text-sm text-gray-900">{labelJK(guru.jenis_kelamin)}</dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Status Menikah</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.status_menikah ? 'Sudah Menikah' : 'Belum Menikah'}</dd>
                        </div>
                        
                        <div className="md:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.alamat || '-'}</dd>
                        </div>
                    </dl>
                    
                    <h3 className="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 mt-6">Data Kontak</h3>
                    <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                        <div>
                            <dt className="text-sm font-medium text-gray-500">No. Identitas</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.no_identitas || '-'}</dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">No. Telepon</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.no_telpon || '-'}</dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">No. Handphone</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.no_handphone || '-'}</dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Email</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.email || '-'}</dd>
                        </div>
                    </dl>
                    
                    <h3 className="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 mt-6">Data Pekerjaan</h3>
                    <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Tanggal Mulai Kerja</dt>
                            <dd className="mt-1 text-sm text-gray-900">{formatDate(guru.tanggal_kerja)}</dd>
                        </div>
                        
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Status</dt>
                            <dd className="mt-1 text-sm text-gray-900">
                                <span className={`px-2 py-1 rounded-full text-xs ${guru.non_aktif ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`}>
                                    {guru.non_aktif ? 'Non-Aktif' : 'Aktif'}
                                </span>
                            </dd>
                        </div>
                        
                        <div className="md:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Keterangan</dt>
                            <dd className="mt-1 text-sm text-gray-900">{guru.keterangan || '-'}</dd>
                        </div>
                    </dl>
                </div>

                <div className="flex items-center justify-end space-x-3">
                    <button
                        onClick={handleDelete}
                        disabled={deleting}
                        className={`rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 ${
                            deleting ? 'opacity-50 cursor-not-allowed' : ''
                        }`}
                    >
                        {deleting ? 'Menghapus...' : 'Hapus'}
                    </button>
                    
                    <Link
                        href={`/admin-cabang/guru/${guru.id}/edit`}
                        className="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-yellow-600"
                    >
                        Edit
                    </Link>
                    
                    <Link
                        href="/admin-cabang/guru"
                        className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </Layout>
    );
};

export default GuruShow;
