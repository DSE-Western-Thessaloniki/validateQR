import { defineStore } from "pinia";

export const useWizardStore = defineStore("wizard", {
    state: () => {
        return { documentGroup: {} };
    },
});
