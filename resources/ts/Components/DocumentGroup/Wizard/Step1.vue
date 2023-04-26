<script setup lang="ts">
import route from "ziggy-js";

import { useWizardStore } from "@/Stores/wizard";
import { router } from "@inertiajs/core";
import { useForm } from "@inertiajs/vue3";

const wizard = useWizardStore();

const form = useForm({
    ...wizard.documentGroup,
});

const save = () => {
    return new Promise(async (resolve, reject) => {
        // Έλεγξε αν πρόκειται για δημιουργία νέου ή ενημέρωση ήδη υπάρχοντος
        const url =
            wizard.documentGroup!.id === -1
                ? route("documentGroup.store")
                : route("documentGroup.update", {
                      id: wizard.documentGroup!.id,
                  });

        const method = wizard.documentGroup!.id === -1 ? "post" : "put";

        form.submit(method, url, {
            preserveState: true,
            onSuccess: () => resolve("OK"),
        });
        // router.visit(url, {
        //     method: method,
        //     // @ts-expect-error
        //     data: wizard.documentGroup,
        //     onError: (error) => {
        //         console.log(error);
        //         reject(["Σφάλμα αποθήκευσης ομάδας αρχείων"]);
        //     },
        //     preserveState: true,
        // });

        resolve("OK");
    });
};

defineExpose({ save });
</script>
<template>
    <div class="flex flex-row bg-white p-4 m-4 rounded items-center">
        <label for="group-name" class="px-4">Όνομα ομάδας εγγράφων:</label>
        <input
            class="transition ease-in-out hover:border-blue-500 ring-blue-500 hover:inset-ring hover:ring-1 focus:border-blue-500 placeholder:italic placeholder:text-slate-400"
            type="text"
            name="group-name"
            placeholder="Όνομα"
            v-bind="form.name"
        />
        <div v-if="form.errors.name">{{ form.errors.name }}</div>
    </div>
</template>
