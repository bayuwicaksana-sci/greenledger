export default function PageHeader({
    pageTitle,
    pageSubtitle,
    children,
}: {
    pageTitle: string;
    pageSubtitle: string;
    children?: React.ReactNode;
}) {
    return (
        <div className="flex items-center justify-between">
            <div>
                <h1 className="text-3xl font-bold">{pageTitle}</h1>
                <p className="text-muted-foreground">{pageSubtitle}</p>
            </div>
            {children}
        </div>
    );
}
