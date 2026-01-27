import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export default function AppLogo() {
    const { name } = usePage<SharedData>().props;
    return (
        <>
            <div className="flex aspect-square size-8 shrink-0 items-center justify-center overflow-hidden rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                {/* <AppLogoIcon className="size-5 fill-current text-white dark:text-black" /> */}
                <img
                    src="/logo-512-d.png"
                    alt="Green Ledger"
                    className="hidden shrink-0 dark:block"
                />
                {/* <Leaf className="size-5 text-white dark:text-black" /> */}
                <img
                    src="/logo-256-t.png"
                    alt="Green Ledger"
                    className="shrink-0 dark:hidden"
                />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    {name}
                </span>
            </div>
        </>
    );
}
