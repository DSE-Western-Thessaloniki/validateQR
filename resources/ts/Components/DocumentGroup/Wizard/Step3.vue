<script setup lang="ts">
import { useWizardStore } from "@/Stores/wizard";
import { faCircleCheck } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from "axios";
import route from "ziggy-js";

const wizard = useWizardStore();

const documentsWithoutQR = () => {
    if (!wizard.documents) {
        // Κανονικά δεν πρέπει να φτάσουμε ποτέ εδώ
        // Ωστόσο αν φτάσουμε ας μην σκάσει τουλάχιστον
        return 1;
    }

    return wizard.documents.filter((document) => !(document.state > 0)).length;
};

// Κάνε έλεγχο μήπως έχουμε ήδη ολοκληρώσει το βήμα
wizard.stepCompleted =
    documentsWithoutQR() === 0 &&
    wizard.documents?.length !== 0 &&
    wizard.documentGroup?.job_status === 2;

// Δεν έχουμε κάτι να αποθηκεύσουμε από την φόρμα
const save = () => {
    return new Promise(async (resolve, reject) => {
        resolve("OK");
    });
};

const addQR = () => {
    wizard.processingDocuments = 1;
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
        <div class="font-bold pb-4 text-xl">Βήμα 3ο: Δημιουργία σφραγίδας</div>

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
        <a
            type="button"
            class="mt-2 p-2 bg-blue-500 w-full rounded shadow-lg text-center"
            :class="
                wizard.stepCompleted
                    ? 'bg-green-500'
                    : 'bg-gray-500 pointer-events-none'
            "
            :href="route('documentGroup.getQR', {
                        documentGroup: wizard.documentGroup!.id,
                    })"
        >
            Λήψη αρχείων με QR Code
        </a>
    </div>
</template>
