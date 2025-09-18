import PropTypes from 'prop-types';
import Table from '../../SuperAdmin/components/Table';

// Komponen RekapTable untuk menampilkan rekap hafalan santri
// Menggunakan Table dari SuperAdmin agar konsisten style & struktur
const RekapTable = ({ data = [], onSend }) => {
    const safeData =
        Array.isArray(data) && data.length > 0
            ? data
            : [
                  { id: 1, nama: 'Ahmad Fauzi', juz: 5, status: 'Sah' },
                  { id: 2, nama: 'Nur Aisyah', juz: 3, status: 'Proses' },
              ];

    // Tambahkan nomor urut agar kolom "No" tidak bergantung pada index dari Table
    const numberedData = safeData.map((row, idx) => ({ ...row, no: idx + 1 }));

    const statusBadge = (status) => {
        const base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
        const color =
            status === 'Sah' ? 'bg-green-100 text-green-800' : status === 'Proses' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800';
        return <span className={`${base} ${color}`}>{status}</span>;
    };

    const columns = [
        {
            header: 'No',
            accessor: 'no',
            // Tidak perlu render khusus; gunakan field no yang sudah dihitung
        },
        { header: 'Nama Santri', accessor: 'nama' },
        {
            header: 'Juz Sah',
            accessor: 'juz',
        },
        {
            header: 'Status Hafalan',
            accessor: 'status',
            render: (row) => statusBadge(row.status),
        },
        {
            header: 'Aksi',
            accessor: 'aksi',
            render: (row) => (
                <button
                    type="button"
                    onClick={() => (onSend ? onSend(row) : alert(`Data ${row.nama} dikirim ke SuperAdmin (dummy)`))}
                    className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-sm leading-4 font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none"
                >
                    Kirim ke SuperAdmin
                </button>
            ),
        },
    ];

    return (
        <div className="space-y-3">
            <Table columns={columns} data={numberedData} actions={false} />
        </div>
    );
};

RekapTable.propTypes = {
    data: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            nama: PropTypes.string.isRequired,
            juz: PropTypes.number.isRequired,
            status: PropTypes.string.isRequired,
        }),
    ),
    onSend: PropTypes.func,
};

export default RekapTable;
