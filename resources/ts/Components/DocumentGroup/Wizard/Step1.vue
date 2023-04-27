<script setup lang="ts">
import route from "ziggy-js";

import { useWizardStore } from "@/Stores/wizard";
import { ref, watch } from "vue";
import axios from "axios";
import type { AxiosError } from "axios";

const wizard = useWizardStore();

const documentGroupName = ref("");

watch(documentGroupName, (value) => {
    // wizard.documentGroup!.name = documentGroupName.value;
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

        axios({
            url: url,
            method: method,
            // @ts-expect-error
            data: wizard.documentGroup,
        })
            .then((response) => {
                if (response.status === 200) {
                    wizard.documentGroup = response.data;
                }
                console.log(response);
                resolve("OK");
            })
            .catch((error: AxiosError) => {
                let errors: Array<String> = [];

                if (error.response) {
                    error.response.data.errors.forEach((error: AxiosError) => {
                        errors.push(error.message);
                    });
                } else {
                    errors.push("Γενικό σφάλμα αποθήκευσης!");
                }
                reject(errors);
            });
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
            placeholder="Όνομα"
            v-model="documentGroupName"
        />
        <!-- <div v-if="form.errors.name">{{ form.errors.name }}</div> -->
    </div>
</template>
