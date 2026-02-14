import { Check } from 'lucide-react';
import { cn } from '@/lib/utils';

export interface Step {
    id: string;
    title: string;
    description?: string;
}

interface StepperProps {
    steps: Step[];
    currentStep: number;
    completedSteps?: Set<number>;
    onStepClick?: (stepIndex: number) => void;
}

export function Stepper({
    steps,
    currentStep,
    completedSteps = new Set(),
    onStepClick,
}: StepperProps) {
    return (
        <nav aria-label="Progress">
            <ol className="flex items-center justify-between gap-2 md:gap-4">
                {steps.map((step, index) => {
                    const isCompleted = completedSteps.has(index);
                    const isCurrent = index === currentStep;
                    const isPending = index > currentStep;
                    const isClickable =
                        onStepClick && (isCompleted || isCurrent);

                    return (
                        <li
                            key={step.id}
                            className="relative flex flex-1 items-center"
                        >
                            {/* Connecting line (except for last step) */}
                            {index < steps.length - 1 && (
                                <div
                                    className={cn(
                                        'absolute left-[calc(50%+1rem)] top-4 hidden h-0.5 w-[calc(100%-2rem)] md:block',
                                        isCompleted || isCurrent
                                            ? 'bg-primary'
                                            : 'bg-muted',
                                    )}
                                    aria-hidden="true"
                                />
                            )}

                            {/* Step button/indicator */}
                            <button
                                type="button"
                                onClick={() =>
                                    isClickable && onStepClick(index)
                                }
                                disabled={!isClickable}
                                className={cn(
                                    'group relative flex flex-col items-center gap-1 text-center transition-colors',
                                    isClickable &&
                                        'cursor-pointer hover:text-primary',
                                    !isClickable && 'cursor-default',
                                )}
                                aria-current={isCurrent ? 'step' : undefined}
                            >
                                {/* Step circle */}
                                <span
                                    className={cn(
                                        'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-medium transition-colors md:h-10 md:w-10 md:text-base',
                                        isCompleted &&
                                            'border-primary bg-primary text-primary-foreground',
                                        isCurrent &&
                                            'border-primary bg-background text-primary',
                                        isPending &&
                                            'border-muted bg-muted text-muted-foreground',
                                    )}
                                >
                                    {isCompleted ? (
                                        <Check className="h-4 w-4 md:h-5 md:w-5" />
                                    ) : (
                                        <span>{index + 1}</span>
                                    )}
                                </span>

                                {/* Step title (hidden on mobile, abbreviated on tablet, full on desktop) */}
                                <span className="flex flex-col items-center gap-0.5">
                                    <span
                                        className={cn(
                                            'hidden text-xs font-medium md:block md:text-sm',
                                            isCurrent && 'text-primary',
                                            isPending && 'text-muted-foreground',
                                        )}
                                    >
                                        {step.title}
                                    </span>
                                    {step.description && (
                                        <span
                                            className={cn(
                                                'hidden text-xs text-muted-foreground lg:block',
                                            )}
                                        >
                                            {step.description}
                                        </span>
                                    )}
                                </span>
                            </button>
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}
