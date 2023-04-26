import type { DocumentGroup } from "@/models";
import { defineStore } from "pinia";

export const useWizardStore = defineStore("wizard", {
    state: (): {
        documentGroup: DocumentGroup | null;
    } => {
        return { documentGroup: null };
    },
});
