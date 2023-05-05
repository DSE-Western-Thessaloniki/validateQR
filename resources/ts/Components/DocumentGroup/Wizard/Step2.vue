<script setup lang="ts">
import axios from "axios";
import { isLaravelValidationError } from "@/laravel-validation-error";
import { useWizardStore } from "@/Stores/wizard";
import route from "ziggy-js";

const wizard = useWizardStore();

const save = () => {
    return new Promise(async (resolve, reject) => {
        resolve("OK");

        // Έλεγξε αν πρόκειται για δημιουργία νέου ή ενημέρωση ήδη υπάρχοντος
        // const url =
        //     wizard.documentGroup!.id === -1
        //         ? route("documentGroup.store")
        //         : route("documentGroup.update", {
        //               id: wizard.documentGroup!.id,
        //           });

        // const method = wizard.documentGroup!.id === -1 ? "post" : "put";

        // axios({
        //     url: url,
        //     method: method,
        //     data: wizard.documentGroup,
        // })
        //     .then((response) => {
        //         if (response.status === 200) {
        //             wizard.documentGroup = response.data;
        //         }
        //         console.log(response);
        //         resolve("OK");
        //     })
        //     .catch((error: unknown) => {
        //         let errors: Array<String> = [];

        //         if (isLaravelValidationError(error)) {
        //             wizard.validationErrors = error.response.data.errors;
        //             errors.push(error.response.data.message);
        //         } else if (error instanceof Error) {
        //             errors.push(error.message);
        //         } else {
        //             errors.push("Γενικό σφάλμα αποθήκευσης!");
        //         }

        //         reject(errors);
        //     });
    });
};

defineExpose({ save });
</script>
<template>
    <div class="flex flex-col bg-white p-4 m-4 rounded items-center">
        <div>Προσθήκη αρχείων</div>
        <div
            class="cursor-pointer m-3 w-full h-64 bg-indigo-500 flex flex-col items-center justify-center rounded border-2 border-indigo-700 shadow shadow-2xl"
        >
            <span class="text-white">
                Σύρετε τα αρχεία εδώ μέσα ή πατήστε για να εμφανιστεί το
                παράθυρο διαλόγου</span
            >
        </div>
    </div>
</template>
