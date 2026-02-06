import type { ComponentType } from 'react';
import { GenericDetails } from './details/GenericDetails';

/**
 * Base props that all detail components must accept.
 * Each detail component can extend this with specific types.
 */
export interface DetailComponentProps {
    approvable: any;
}

/**
 * Type for the component registry mapping.
 */
export type DetailComponent = ComponentType<DetailComponentProps>;

/**
 * Registry mapping backend model class names to detail components.
 * Add new mappings here as approval types are added.
 * If no mapping is found, GenericDetails will be used.
 */
const componentRegistry: Record<string, DetailComponent> = {
    // Example: 'App\\Models\\Finance\\PaymentRequest': PaymentRequestDetails,
    // Add more mappings as needed
};

export function ContentResolver({
    approvableType,
    approvable,
}: {
    approvableType: string;
    approvable: any;
}) {
    // Resolve component from registry, fallback to GenericDetails
    const DetailComponent = componentRegistry[approvableType] || GenericDetails;

    return <DetailComponent approvable={approvable} />;
}
