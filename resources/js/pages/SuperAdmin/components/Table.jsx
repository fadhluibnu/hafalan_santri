import React from "react";
import PropTypes from "prop-types";
import { Link, router } from "@inertiajs/react";
import { route } from "ziggy-js";

const Table = ({ columns, data, actions = true, baseRoute = "" }) => {
    // Pastikan data adalah array
    const safeData = Array.isArray(data) ? data : [];

    const deleteData = (id) => {
        router.delete(route(`super-admin.admin-cabang.destroy`, id), {  preserveScroll: true});
    }
    return (
        <div className="overflow-x-auto relative shadow-md sm:rounded-lg">
            <table className="w-full text-sm text-left text-gray-500">
                <thead className="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        {columns.map((column, index) => (
                            <th key={index} scope="col" className="py-3 px-6">
                                {column.header}
                            </th>
                        ))}
                        {actions && <th scope="col" className="py-3 px-6">Aksi</th>}
                    </tr>
                </thead>
                <tbody>
                    {safeData.map((row, rowIndex) => (
                        <tr key={rowIndex} className="bg-white border-b hover:bg-gray-50">
                            {columns.map((column, colIndex) => (
                                <td key={colIndex} className="py-4 px-6">
                                    {column.render ? column.render(row) : row[column.accessor]}
                                </td>
                            ))}
                            {actions && (
                                <td className="py-4 px-6 space-x-2 whitespace-nowrap">
                                    <Link
                                        href={`${baseRoute}/${row.id}`}
                                        className="text-blue-600 hover:text-blue-900"
                                    >
                                        Detail
                                    </Link>
                                    <Link
                                        href={`${baseRoute}/${row.id}/edit`}
                                        className="text-yellow-600 hover:text-yellow-900 ml-2"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        onClick={() => {
                                            if (confirm("Yakin ingin menghapus item ini?")) {
                                                // Handle delete action here
                                                deleteData(row.id);
                                            }
                                        }}
                                        className="text-red-600 hover:text-red-900 ml-2"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

Table.propTypes = {
    columns: PropTypes.arrayOf(
        PropTypes.shape({
            header: PropTypes.string.isRequired,
            accessor: PropTypes.string.isRequired,
            render: PropTypes.func,
        })
    ).isRequired,
    data: PropTypes.array.isRequired,
    actions: PropTypes.bool,
    baseRoute: PropTypes.string,
};

export default Table;