import type { DocumentGroup } from "@/models";
import type { LaravelValidationError } from "@/laravel-validation-error";
import { defineStore } from "pinia";

export const useWizardStore = defineStore("wizard", {
    state: (): {
        documentGroup: DocumentGroup | undefined;
        validationErrors: Record<string, Array<string>> | undefined;
        backStepAllowed: boolean;
        stepCompleted: boolean;
        totalStepsCompleted: number;
    } => {
        return {
            documentGroup: undefined,
            validationErrors: undefined,
            backStepAllowed: true,
            stepCompleted: false,
            totalStepsCompleted: 0,
        };
    },
});
