<script setup lang="ts">
import { useWizardStore } from "@/Stores/wizard";
import FileDropZone from "@/Components/FileDropZone.vue";
import route from "ziggy-js";
import { faKey, faUnlockKeyhole } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { getDocuments } from "./utilities";

const wizard = useWizardStore();

const documentsWithoutSignature = () => {
    if (!wizard.documents) {
        // Κανονικά δεν πρέπει να φτάσουμε ποτέ εδώ
        // Ωστόσο αν φτάσουμε ας μην σκάσει τουλάχιστον
        return 1;
    }

    return wizard.documents.filter((document) => !(document.state === 2))
        .length;
};

// Κάνε έλεγχο μήπως έχουμε ήδη ολοκληρώσει το βήμα
wizard.stepCompleted =
    documentsWithoutSignature() === 0 &&
    wizard.documents?.length !== 0 &&
    wizard.documentGroup?.job_status === 2;

const updateDocumentView = async () => {
    const d = await getDocuments(wizard.documentGroup!.id);

    if (Array.isArray(d.documents)) {
        wizard.documents = d.documents;
        if (wizard.documents!.length > 0) {
            wizard.stepCompleted = true;
        }
    }
};

const save = () => {
    return new Promise(async (resolve, reject) => {
        resolve("OK");
    });
};

defineExpose({ save });
</script>
<template>
    <div class="flex flex-col bg-white p-4 m-4 rounded items-center">
        <div class="font-bold pb-4 text-xl">
            Βήμα 4ο: Ανέβασμα ψηφιακά υπογεγραμμένων αρχείων
        </div>

        <div class="font-bold">Ήδη ανεβασμένα αρχεία με σφραγίδα</div>
        <div
            class="flex flex-row h-72 w-full bg-gray-100 rounded shadow-inner items-center justify-center m-3"
        >
            <div class="" v-if="!wizard.documents?.length">
                Δεν έχουν ανέβει αρχεία ακόμη! Δεν θα έπρεπε να δείτε αυτό το
                μήνυμα...
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
                        :icon="faKey"
                        v-if="document.state === 2"
                        class="px-2 text-green-600"
                        size="2x"
                    />
                </div>
            </div>
        </div>
        <div class="font-bold">Προσθήκη αρχείων με υπογραφή</div>
        <FileDropZone
            :url="route('document.storeManySigned')"
            @uploaded="updateDocumentView"
        />
    </div>
</template>
