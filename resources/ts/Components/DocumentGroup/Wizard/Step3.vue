<script setup lang="ts">
import { useWizardStore } from "@/Stores/wizard";
import { faCircleCheck } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from "axios";
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

const addQR = () => {
    axios.post(
        route("documentGroup.addQR", {
            documentGroup: wizard.documentGroup!.id,
        })
    );
};

defineExpose({ save });
</script>
<template>
    <div class="flex flex-col bg-white p-4 m-4 rounded items-center">
        <div class="font-bold">Ανεβασμένα αρχεία</div>
        <div
            class="flex flex-row h-72 w-full bg-gray-100 rounded shadow-inner items-center justify-center m-3"
        >
            <div class="" v-if="!wizard.documents?.length">
                Δεν έχουν ανέβει αρχεία ακόμη!
            </div>
            <div v-else class="flex flex-col h-72 w-full overflow-y-auto">
                <div
                    v-for="(document, index) in wizard.documents"
                    class="bg-white rounded m-2 p-2 shadow flex flex-row items-center"
                >
                    <div class="flex-grow text-ellipsis">
                        {{ index + 1 }}. {{ document.filename }}
                    </div>
                    <span class="bg-orange-300 p-2 ml-2 rounded shadow">{{
                        document.id
                    }}</span>
                    <FontAwesomeIcon
                        :icon="faCircleCheck"
                        v-if="document.state > 0"
                        class="px-2 text-green-600"
                        size="2x"
                    />
                </div>
            </div>
        </div>
        <button
            type="button"
            @click="addQR"
            class="p-2 bg-blue-500 text-white w-full rounded shadow-lg"
        >
            Προσθήκη QR Code
        </button>
        <button
            type="button"
            class="mt-2 p-2 bg-blue-500 w-full rounded shadow-lg"
        >
            Λήψη αρχείων με QR Code
        </button>
    </div>
</template>
