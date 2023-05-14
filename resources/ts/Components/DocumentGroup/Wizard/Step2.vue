<script setup lang="ts">
import axios from "axios";
import { isLaravelValidationError } from "@/laravel-validation-error";
import { useWizardStore } from "@/Stores/wizard";
import FileDropZone from "@/Components/FileDropZone.vue";
import route from "ziggy-js";
import { onMounted, ref } from "vue";

const wizard = useWizardStore();

const getDocuments = async () => {
    return await axios
        .get(route("documentGroup.show", wizard.documentGroup?.id))
        .then((response) => {
            if (response.status === 200) {
                console.log(response.data);
                return response.data;
            }
        })
        .catch((error) => {
            //console.log(error);
            return error;
        });
};

const updateDocumentView = async () => {
    const d = await getDocuments();

    if (Array.isArray(d.documents)) {
        wizard.documents = d.documents;
    }
};

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

onMounted(async () => {
    await updateDocumentView();
});
</script>
<template>
    <div class="flex flex-col bg-white p-4 m-4 rounded items-center">
        <div class="font-bold">Ήδη ανεβασμένα αρχεία</div>
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
                </div>
            </div>
        </div>
        <div class="font-bold">Προσθήκη αρχείων</div>
        <FileDropZone
            :url="route('document.storeMany')"
            @uploaded="updateDocumentView"
        />
    </div>
</template>
