<script setup lang="ts">
import FileDropZone from "@/Components/FileDropZone.vue";
import AppLayoutNoTransition from "@/Layouts/AppLayoutNoTransition.vue";
import { isDocumentCanceled, isDocumentReplaced } from "@/tools";
import { faCancel, faCopy } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { Form } from "@inertiajs/vue3";
import { ref } from "vue";
import { route } from "ziggy-js";
import type { PageWithFlashProps } from "@/flash-message";
import type { PageWithAuthProps } from "@/page-props-auth";
import Message from "@/Components/Message.vue";

const props = defineProps<{
    document: App.Models.Document & {
        document_group: App.Models.DocumentGroup;
        extra_state: App.Models.ExtraState;
    };
    newDocument?: App.Models.Document & {
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
    replaceAlt.value = false;
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
        .post(route("document.replaceWithId", props.document.id), {
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

const page = usePage<PageWithFlashProps & PageWithAuthProps>();

console.log("newDocument prop:", props.newDocument);

const replaceAlt = ref(false);

console.log("replaceAlt initial value:", replaceAlt.value);
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
            <div
                class="m-4 py-2"
                v-if="page.props.flash.success || page.props.flash.danger"
            >
                <Message
                    class="mb-5"
                    type="success"
                    v-if="page.props.flash.success"
                    >{{ page.props.flash.success }}</Message
                >
                <Message
                    class="mb-5"
                    type="danger"
                    v-if="page.props.flash.danger"
                    >{{ page.props.flash.danger }}</Message
                >
            </div>
            <div class="mt-4 p-4 flex flex-col bg-white rounded-md shadow-xl">
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
                class="mt-4 flex flex-col bg-white rounded-md shadow-xl"
                v-if="showCancelForm"
            >
                <div class="py-2 px-4 rounded-t-md bg-blue-500 text-white">
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
                class="mt-4 pb-4 flex flex-col bg-white rounded-md shadow-xl"
                v-if="showReplacementForm"
            >
                <div class="py-2 px-4 rounded-t-md bg-green-500 text-white">
                    <FontAwesomeIcon :icon="faCopy"></FontAwesomeIcon>
                    Αντικατάσταση εγγράφου
                </div>
                <div v-if="!replaceAlt">
                    <div class="p-4">
                        Αν έχετε ήδη προσθέσει σε μια ομάδα το νέο έγγραφο που
                        έρχεται σε αντικατάσταση του παραπάνω εγγράφου και έχετε
                        ήδη το αναγνωριστικό του, πληκτρολογήστε το στο παρακάτω
                        πλαίσιο:
                    </div>
                    <div class="flex px-4">
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
                    <div class="p-4">
                        Διαφορετικά πατήστε
                        <span
                            class="text-blue-500 cursor-pointer"
                            @click="replaceAlt = true"
                            >εδώ</span
                        >
                        για να το προσθέσετε τώρα.
                    </div>
                </div>
                <div class="p-4" v-if="replaceAlt && !newDocument">
                    <div>
                        Εισάγετε το έγγραφο παρακάτω για να προστεθεί στην ίδια
                        ομάδα με το αρχικό και να προετοιμαστεί για
                        αντικατάσταση.
                    </div>
                    <Form
                        class="pt-4"
                        :action="route('document.replaceWithFile', document)"
                        method="post"
                        #default="{ errors }"
                        :options="{
                            preserveUrl: true,
                        }"
                        disable-while-processing
                    >
                        <input
                            type="file"
                            accept=".pdf,application/pdf"
                            name="file"
                            required
                        />
                        <div v-if="errors.file" class="text-red-500 text-sm">
                            {{ errors.file }}
                        </div>
                        <div class="flex justify-end pt-4">
                            <button
                                class="text-white px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600"
                                type="submit"
                            >
                                Προετοιμασία εγγράφου
                            </button>
                        </div>
                    </Form>
                </div>
                <div class="p-4" v-if="replaceAlt && newDocument">
                    <div>
                        Έγινε αποθήκευση του αρχείου με αναγνωριστικό
                        <span>{{ newDocument.id }}</span> και μπορείτε να κάνετε
                        λήψη του αρχείου με το QR κάνοντας κλικ
                        <a
                            :href="
                                route('document.downloadWithQR', newDocument)
                            "
                            class="text-blue-500"
                            >εδώ</a
                        >.
                    </div>
                    <div class="pt-4">
                        Παρακαλούμε μετά τη λήψη να γίνει υπογραφή του αρχείου
                        και έπειτα ανέβασμα εκ νέου για να ολοκληρωθεί η
                        διαδικασία.
                    </div>
                    <Form
                        class="pt-4"
                        :action="route('document.storeSigned', newDocument)"
                        method="post"
                        :transform="
                            (data) => ({ ...data, replaces: document.id })
                        "
                        disable-while-processing
                        #default="{ errors }"
                    >
                        <input
                            type="file"
                            name="signedFile"
                            accept=".pdf,application/pdf"
                            required
                        />
                        <div
                            v-if="errors.signedFile"
                            class="text-red-500 text-sm"
                        >
                            {{ errors.signedFile }}
                        </div>
                        <div class="flex justify-end pt-4">
                            <button
                                class="text-white px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600"
                                type="submit"
                            >
                                Αποθήκευση υπογεγραμμένου εγγράφου
                            </button>
                        </div>
                    </Form>
                </div>
                <div class="text-sm text-red-600 px-4" v-if="errorMessage">
                    {{ errorMessage }}
                </div>
            </div>
        </div>
    </AppLayoutNoTransition>
</template>
