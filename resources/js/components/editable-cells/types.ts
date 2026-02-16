/**
 * Shared types for editable table cells
 */

export interface TableMeta {
    dataKey: string;
    errors: Record<string, string>;
    updateData: (rowIndex: number, columnId: string, value: any) => void;
    addRow?: () => void;
    removeRow?: (rowIndex: number) => void;
}
