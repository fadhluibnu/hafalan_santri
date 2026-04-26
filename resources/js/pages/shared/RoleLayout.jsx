import AdminCabangLayout from '../AdminCabang/components/Layout';
import SuperAdminLayout from '../SuperAdmin/components/layouts';
import UstadzLayout from '../Ustadz/components/Layout';

export default function RoleLayout({ authRole, title, children }) {
    if (authRole === 'admin_cabang') {
        return <AdminCabangLayout title={title}>{children}</AdminCabangLayout>;
    }

    if (authRole === 'super_admin') {
        return <SuperAdminLayout title={title}>{children}</SuperAdminLayout>;
    }

    return <UstadzLayout title={title}>{children}</UstadzLayout>;
}
