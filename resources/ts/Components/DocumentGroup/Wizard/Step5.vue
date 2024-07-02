<script setup lang="ts">
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import { useWizardStore } from "@/Stores/wizard";
import { Link } from "@inertiajs/vue3";

const wizard = useWizardStore();

const onChangePublishedState = () => {};

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
        <div class="font-bold pb-4 text-xl">
            Βήμα 5ο: Δημοσίευση ομάδας αρχείων
        </div>

        <div class="flex flex-col items-center border-black border p-4 w-6/12">
            <ToggleSwitch
                class="w-6/12"
                :active="wizard.documentGroup?.published"
            >
                <div>Ενεργοποίηση δημοσίευσης</div>
            </ToggleSwitch>
        </div>
    </div>
</template>
