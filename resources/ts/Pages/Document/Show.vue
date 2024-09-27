<script setup lang="ts">
import AppLayoutNoTransition from "@/Layouts/AppLayoutNoTransition.vue";
import { isDocumentCanceled, isDocumentReplaced } from "@/tools";
import { faCancel, faCopy } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { ref } from "vue";
import { route } from "ziggy-js";

const props = defineProps<{
    document: App.Models.Document & {
        document_group: App.Models.DocumentGroup;
        extra_state: App.Models.ExtraState;
    };
}>();

const showCancelForm = ref(false);

const showReplacementForm = ref(false);

const onCancelButtonClick = () => {
    showReplacementForm.value = false;
    showCancelForm.value = true;
};

const onReplaceButtonClick = () => {
    showCancelForm.value = false;
    showReplacementForm.value = true;
};

const cancelText = ref("");

const replacement = ref("");

const errorMessage = ref("");

const cancelDocument = () => {
    axios
        .post(route("document.cancel", props.document.id), {
            cancelText: cancelText.value,
        })
        .then((response) => {
            if (response.data.result === "OK") {
                showCancelForm.value = false;
                router.reload();
            } else {
                console.log(response.data.result);
            }
        })
        .catch((error) => {
            // Handle error
            console.error(error);
        });
};

const replaceDocument = () => {
    axios
        .post(route("document.replace", props.document.id), {
            replacement: replacement.value,
        })
        .then((response) => {
            if (response.data.result === "OK") {
                errorMessage.value = "";
                showReplacementForm.value = false;
                router.reload();
            } else if (response.data.result === "Not found") {
                errorMessage.value = response.data.error;
            } else {
                console.log(response.data.result);
            }
        })
        .catch((error) => {
            // Handle error
            console.error(error);
        });
};

const onRestoreStateButtonClick = () => {
    axios
        .post(route("document.restoreState", props.document.id), {})
        .then(() => {
            showReplacementForm.value = false;
            router.reload();
        })
        .catch((error) => {
            // Handle error
            console.error(error);
        });
};
</script>

<template>
    <AppLayoutNoTransition>
        <template #header>
            <div class="flex flex-row items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Πληροφορίες εγγράφου
                </h2>
            </div>
        </template>

        <div class="flex flex-col flex-grow w-1/3 mx-auto">
            <div class="mt-4 p-4 flex flex-col bg-slate-300 rounded-md">
                <div>Αναγνωριστικό: {{ document.id }}</div>
                <div>Όνομα αρχείου: {{ document.filename }}</div>
                <div class="mb-2">
                    Ομάδα εγγράφων: {{ document.document_group.name }}
                </div>
                <div
                    v-if="isDocumentCanceled(document)"
                    class="mb-2 p-2 bg-red-200"
                >
                    Το έγγραφο ακυρώθηκε. Αιτία: "{{
                        document.extra_state.extra_state_text
                    }}"
                </div>
                <div
                    v-if="isDocumentReplaced(document)"
                    class="mb-2 p-2 bg-green-200"
                >
                    Το έγγραφο αντικαταστάθηκε με το:
                    <Link
                        :href="
                            route(
                                'document.adminShow',
                                document.extra_state.extra_state_text
                            )
                        "
                        class="text-blue-600 underline"
                        >{{ document.extra_state.extra_state_text }}</Link
                    >
                </div>
                <hr />
                <div class="mt-2 flex justify-between">
                    <button
                        class="text-white px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600"
                        @click="onCancelButtonClick"
                    >
                        Ακύρωση εγγράφου
                    </button>
                    <button
                        @click="onReplaceButtonClick"
                        class="ml-4 text-white px-4 py-2 rounded-md bg-green-500 hover:bg-green-600"
                    >
                        Αντικατάσταση εγγράφου
                    </button>
                    <button
                        v-if="document.extra_state"
                        class="ml-4 text-white px-4 py-2 rounded-md bg-red-500 hover:bg-red-600"
                        @click="onRestoreStateButtonClick"
                    >
                        Επαναφορά κατάστασης
                    </button>
                </div>
            </div>
            <div
                class="mt-4 flex flex-col bg-slate-300 rounded-md"
                v-if="showCancelForm"
            >
                <div class="py-2 px-4 rounded-t-md bg-blue-500">
                    <FontAwesomeIcon :icon="faCancel"></FontAwesomeIcon>
                    Ακύρωση εγγράφου
                </div>
                <div class="flex p-4">
                    <input
                        type="text"
                        placeholder="Αιτία ακύρωσης εγγράφου"
                        class="flex-grow"
                        v-model="cancelText"
                    /><button
                        class="ml-4 text-white px-4 py-2 rounded-md bg-orange-500 hover:bg-orange-600"
                        @click="cancelDocument"
                    >
                        Αποθήκευση
                    </button>
                </div>
            </div>
            <div
                class="mt-4 pb-4 flex flex-col bg-slate-300 rounded-md"
                v-if="showReplacementForm"
            >
                <div class="py-2 px-4 rounded-t-md bg-green-500">
                    <FontAwesomeIcon :icon="faCopy"></FontAwesomeIcon>
                    Αντικατάσταση εγγράφου
                </div>
                <div class="flex pt-4 px-4">
                    <input
                        type="text"
                        placeholder="Αναγνωριστικό εγγράφου για αντικατάσταση"
                        class="flex-grow"
                        v-model="replacement"
                    /><button
                        class="ml-4 text-white px-4 py-2 rounded-md bg-orange-500 hover:bg-orange-600"
                        @click="replaceDocument"
                    >
                        Αποθήκευση
                    </button>
                </div>
                <div class="text-sm text-red-600 px-4" v-if="errorMessage">
                    {{ errorMessage }}
                </div>
            </div>
        </div>
    </AppLayoutNoTransition>
</template>
