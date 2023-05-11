<script setup lang="ts">
import route from "ziggy-js";

import { useWizardStore } from "@/Stores/wizard";
import { ref, watch } from "vue";
import axios from "axios";
import { isLaravelValidationError } from "@/laravel-validation-error";

const wizard = useWizardStore();

// Κάνε έλεγχο μήπως έχουμε ήδη ολοκληρώσει το βήμα
wizard.stepCompleted = wizard.documentGroup!.name.length > 0;

const documentGroupName = ref(wizard.documentGroup!.name);

let nameChanged = false;

watch(documentGroupName, (newName) => {
    wizard.documentGroup!.name = newName;

    // Σηματοδότησε ότι το όνομα άλλαξε για να γίνει εκ νέου αποθήκευση
    nameChanged = true;

    // Αν το όνομα είναι κενό δεν έχει ολοκληρωθεί το βήμα
    if (newName === "") {
        wizard.stepCompleted = false;
        return;
    }

    wizard.stepCompleted = true;
});

const save = () => {
    return new Promise(async (resolve, reject) => {
        // Έλεγξε αν έχεις κάτι να αποθηκεύσεις πρώτα
        if (documentGroupName.value === "") {
            reject([
                "Είναι υποχρεωτικό να δώσετε ένα όνομα στην ομάδα των εγγράφων",
            ]);
            return;
        }

        // Αν δεν άλλαξε το όνομα δεν έχει νόημα να ζητήσουμε εκ νέου αποθήκευση
        if (!nameChanged) {
            resolve("OK");
            return;
        }

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
            data: wizard.documentGroup,
        })
            .then((response) => {
                if (response.status === 200) {
                    wizard.documentGroup = response.data;
                }
                resolve("OK");
            })
            .catch((error: unknown) => {
                let errors: Array<String> = [];

                if (isLaravelValidationError(error)) {
                    wizard.validationErrors = error.response.data.errors;
                    errors.push(error.response.data.message);
                } else if (error instanceof Error) {
                    errors.push(error.message);
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
        <div v-if="wizard.validationErrors?.name">
            <div
                class="text-red-500 text-sm"
                v-for="error in wizard.validationErrors.name"
            >
                {{ error }}
            </div>
        </div>
    </div>
</template>
