import MainLayoutTemplate from '@/layouts/main/main-sidebar-layout';
import type { AppLayoutProps } from '@/types';

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => (
    <MainLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
        {children}
    </MainLayoutTemplate>
);
