<script setup lang="ts">
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import { useWizardStore } from "@/Stores/wizard";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";
import { route } from "ziggy-js";

const wizard = useWizardStore();

const onPublishedStateToggle = () => {
    axios
        .post(route("documentGroup.togglePublished", wizard.documentGroup))
        .then((response) => {
            if (response.status === 200) {
                wizard!.documentGroup!.published = response.data.published;
            }
        })
        .catch((error: unknown) => {
            let errors: Array<String> = [];

            if (error instanceof Error) {
                errors.push(error.message);
            } else {
                errors.push("Γενικό σφάλμα αποθήκευσης!");
            }
        });
};

const aboutToCancel = ref(false);

const cancelText = ref("");

const cancelDocuments = async () => {
    axios
        .post(route("documentGroup.cancelDocuments", wizard.documentGroup), {
            cancelText: cancelText.value,
        })
        .then((response) => {
            if (response.data.result === "OK") {
                axios
                    .get(route("documentGroup.show", wizard.documentGroup!.id))
                    .then((response) => {
                        if (response.status === 200) {
                            wizard.documentGroup = response.data;
                            wizard.documents = response.data.documents;
                            aboutToCancel.value = false;
                        }
                    })
                    .catch((error: unknown) => {
                        let errors: Array<String> = [];

                        if (error instanceof Error) {
                            errors.push(error.message);
                        } else {
                            errors.push("Γενικό σφάλμα αποθήκευσης!");
                        }

                        console.log(errors);
                    });
            }
        })
        .catch((error: unknown) => {
            let errors: Array<String> = [];

            if (error instanceof Error) {
                errors.push(error.message);
            } else {
                errors.push("Γενικό σφάλμα αποθήκευσης!");
            }

            console.log(errors);
        });
};

const cancelledDocuments = computed(() => {
    return wizard.documents?.filter(
        (doc) => doc.extra_state?.extra_state === 1
    );
});

const replacedDocuments = computed(() => {
    return wizard.documents?.filter(
        (doc) => doc.extra_state?.extra_state === 2
    );
});

const documentRestoreState = (id: string) => {
    axios
        .post(route("document.restoreState", id))
        .then((response) => {
            if (response.data.result === "OK") {
                axios
                    .get(route("documentGroup.show", wizard.documentGroup!.id))
                    .then((response) => {
                        wizard.documentGroup = response.data;
                        wizard.documents = response.data.documents;
                    });
            }
        })
        .catch((error: unknown) => {
            console.log(error);
        });
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
            Βήμα 5ο: Δημοσίευση ομάδας αρχείων
        </div>

        <div class="flex flex-col items-center border-black border p-4 w-6/12">
            <ToggleSwitch
                class="w-6/12"
                :active="wizard.documentGroup?.published ? true : false"
                @click="onPublishedStateToggle"
            >
                <div>Ενεργοποίηση δημοσίευσης</div>
            </ToggleSwitch>
        </div>
        <div class="flex flex-col mt-4 w-full">
            <button
                type="button"
                class="p-2 rounded-md transition ease-in-out duration-300 bg-red-300 hover:shadow-xl hover:-translate-y-0.5 mx-auto"
                v-if="!aboutToCancel"
                @click="aboutToCancel = true"
            >
                Ακύρωση εγγράφων
            </button>
            <div v-if="aboutToCancel" class="flex flex-col bg-red-200 p-2">
                <form @submit.prevent="cancelDocuments">
                    <input
                        type="text"
                        class="w-full"
                        placeholder="Αιτία ακύρωσης εγγράφων"
                        v-model="cancelText"
                    />
                    <div class="flex place-content-between mt-2">
                        <button
                            type="button"
                            class="p-2 rounded-md transition ease-in-out duration-300 bg-blue-300 hover:shadow-xl hover:-translate-y-0.5"
                            @click="aboutToCancel = false"
                        >
                            Κλείσιμο
                        </button>
                        <button
                            type="submit"
                            class="p-2 rounded-md transition ease-in-out duration-300 bg-green-300 hover:shadow-xl hover:-translate-y-0.5"
                        >
                            Αποθήκευση αιτίας ακύρωσης εγγράφων
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div
            class="flex flex-col mt-4 w-full"
            v-if="cancelledDocuments?.length"
        >
            Ακυρωμένα έγγραφα:
            <div
                class="mt-2 max-h-96 overflow-y-auto bg-indigo-50 flex mx-auto"
            >
                <table class="table-auto border border-collapse border-black">
                    <thead>
                        <tr>
                            <th class="border border-black">A/A</th>
                            <th class="border border-black">Όνομα αρχείου</th>
                            <th class="border border-black">Αιτία ακύρωσης</th>
                            <th class="border border-black">Ενέργειες</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(doc, index) in cancelledDocuments"
                            :key="doc.id"
                        >
                            <td class="border border-black text-center">
                                {{ index + 1 }}
                            </td>
                            <td class="border border-black">
                                <div
                                    class="flex justify-between mx-2 items-center"
                                >
                                    <div class="pr-2">
                                        {{ doc.filename }}
                                    </div>
                                    <div class="rounded-md bg-orange-300 p-1">
                                        {{ doc.id }}
                                    </div>
                                </div>
                            </td>
                            <td class="border border-black">
                                <div class="p-2">
                                    {{ doc.extra_state?.extra_state_text }}
                                </div>
                            </td>
                            <td class="border border-black text-center">
                                <button
                                    type="button"
                                    class="p-1 m-1 rounded-md transition ease-in-out duration-300 bg-yellow-500 hover:bg-yellow-300 hover:shadow-xl hover:-translate-y-0.5"
                                    @click="documentRestoreState(doc.id)"
                                >
                                    Αναίρεση
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex flex-col mt-4 w-full" v-if="replacedDocuments?.length">
            Έγγραφα που αντικαταστάθηκαν:
            <div
                class="mt-2 max-h-96 overflow-y-auto bg-indigo-50 flex mx-auto"
            >
                <table class="table-auto border border-collapse border-black">
                    <thead>
                        <tr>
                            <th class="border border-black">A/A</th>
                            <th class="border border-black">Όνομα αρχείου</th>
                            <th class="border border-black">
                                Έγγραφο αντικατάστασης
                            </th>
                            <th class="border border-black">Ενέργειες</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(doc, index) in replacedDocuments"
                            :key="doc.id"
                        >
                            <td class="border border-black text-center">
                                {{ index + 1 }}
                            </td>
                            <td class="border border-black">
                                <div
                                    class="flex justify-between mx-2 items-center"
                                >
                                    <div class="pr-2">
                                        {{ doc.filename }}
                                    </div>
                                    <div class="rounded-md bg-orange-300 p-1">
                                        {{ doc.id }}
                                    </div>
                                </div>
                            </td>
                            <td class="border border-black">
                                <div class="p-2">
                                    <Link
                                        :href="
                                            route(
                                                'document.show',
                                                doc.extra_state
                                                    ?.extra_state_text
                                            )
                                        "
                                        class="text-blue-500 underline"
                                        >{{
                                            doc.extra_state?.extra_state_text
                                        }}</Link
                                    >
                                </div>
                            </td>
                            <td class="border border-black text-center">
                                <button
                                    type="button"
                                    class="p-1 m-1 rounded-md transition ease-in-out duration-300 bg-yellow-500 hover:bg-yellow-300 hover:shadow-xl hover:-translate-y-0.5"
                                    @click="documentRestoreState(doc.id)"
                                >
                                    Αναίρεση
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
