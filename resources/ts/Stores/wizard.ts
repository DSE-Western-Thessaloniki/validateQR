import type { LaravelValidationError } from "@/laravel-validation-error";
import { defineStore } from "pinia";

export const useWizardStore = defineStore("wizard", {
    state: (): {
        documentGroup: App.Models.DocumentGroup | undefined;
        documents:
            | Array<
                  App.Models.Document & {
                      extra_state: App.Models.ExtraState | undefined;
                  }
              >
            | undefined;
        validationErrors: Record<string, Array<string>> | undefined;
        backStepAllowed: boolean;
        stepCompleted: boolean;
        totalStepsCompleted: number;
        processingDocuments: number;
        processingDocumentsProgress: string;
        confirmationModal: {
            show: boolean;
            title: string;
            content: string;
        };
    } => {
        return {
            documentGroup: undefined,
            documents: undefined,
            validationErrors: undefined,
            backStepAllowed: true,
            stepCompleted: false,
            totalStepsCompleted: 0,
            processingDocuments: 0,
            processingDocumentsProgress: "",
            confirmationModal: {
                show: false,
                title: "Test",
                content: "This is a test confirmation modal",
            },
        };
    },
});
