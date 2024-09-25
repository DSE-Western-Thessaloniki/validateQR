<script setup lang="ts">
import { ref, markRaw, type Component, onBeforeUnmount } from "vue";
import Step1 from "./Step1.vue";
import Step2 from "./Step2.vue";
import Step3 from "./Step3.vue";
import Step4 from "./Step4.vue";
import Step5 from "./Step5.vue";
import StepCounter from "./StepCounter.vue";
import type { DocumentGroupFormStep } from "./DocumentGroupFormData";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronLeft,
    faChevronRight,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { useWizardStore } from "@/Stores/wizard";
import axios from "axios";
import { intervalCollection } from "time-events-manager";
import { route } from "ziggy-js";

library.add(faChevronLeft, faChevronRight);

const props = withDefaults(
    defineProps<{
        documentGroup?: App.Models.DocumentGroup & {
            documents?: Array<App.Models.Document>;
        };
    }>(),
    {
        documentGroup: function () {
            return {
                id: -1,
                name: "",
                step: 0,
                published: false,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
                job_status: 0,
                job_status_text: "",
                job_date: new Date().toISOString(),
            };
        },
    }
);

const wizard = useWizardStore();
wizard.documentGroup = props.documentGroup;
wizard.documents = props.documentGroup.documents;
wizard.totalStepsCompleted = props.documentGroup.step;

const totalSteps = 5;

const formSteps = [Step1, Step2, Step3, Step4, Step5];
const currentStep = ref(Math.min(props.documentGroup.step + 1, totalSteps));

const errorMessage = ref<string[]>([]);

const currentComponent = ref(markRaw(formSteps[currentStep.value - 1]));

const stepCounterRef = ref<InstanceType<typeof StepCounter>>();
const componentRef = ref<Component>();

function setStep(step: number) {
    // Αποθήκευσε τα δεδομένα της σελίδας και έλεγξε το αποτέλεσμα
    (componentRef.value as DocumentGroupFormStep)
        ?.save()
        .then(() => {
            currentStep.value = step;

            // Άλλαξε το τρέχον component για τα περιεχόμενα της σελίδας
            currentComponent.value = markRaw(formSteps[step - 1]);

            // Πέρασε το τρέχον βήμα στον StepCounter
            stepCounterRef.value?.setStep(step);

            errorMessage.value = [];
        })
        .catch((error: string[]) => {
            errorMessage.value = error;
        });
}

const prevStep = () => {
    setStep(currentStep.value - 1);
};

const nextStep = () => {
    setStep(currentStep.value + 1);
};

const refreshDocumentGroup = () => {
    if (currentStep.value > 1) {
        axios
            .get(route("documentGroup.show", wizard.documentGroup!.id))
            .then((response) => {
                wizard.documentGroup = response.data;
            });
    }
};

// Αφαίρεσε τυχόν υπολειπόμενα setInterval (Χρήσιμο ειδικά στο development)
intervalCollection.removeAll();
intervalCollection.add(refreshDocumentGroup, 2000);

let lastRunResult: number | undefined;
let lastRunResultText: string | undefined;

wizard.$subscribe((mutation, state) => {
    wizard.processingDocuments = Number(state.documentGroup?.job_status);
    if (wizard.processingDocuments === 1) {
        wizard.processingDocumentsProgress = String(
            state.documentGroup?.job_status_text
        );
    }

    if (lastRunResult !== state.documentGroup?.job_status) {
        // Αν άλλαξε κατάσταση η εργασία και πλέον δεν είναι αποτυχημένη
        // και ταυτόχρονα έχουμε παλιό μήνυμα αποτυχίας
        if (state.documentGroup?.job_status !== 3 && lastRunResultText) {
            errorMessage.value.splice(
                errorMessage.value.indexOf(lastRunResultText),
                1
            );
        }

        // Αν έσκασε εμφάνισε το σφάλμα
        if (
            wizard.processingDocuments === 3 &&
            state.documentGroup?.job_status_text &&
            (errorMessage.value.length === 0 ||
                (errorMessage.value.length !== 0 &&
                    state.documentGroup?.job_status_text &&
                    !errorMessage.value.includes(
                        state.documentGroup?.job_status_text
                    )))
        ) {
            errorMessage.value.push(state.documentGroup?.job_status_text);
        }

        lastRunResult = state.documentGroup?.job_status;
        lastRunResultText = state.documentGroup?.job_status_text;
    }
});

onBeforeUnmount(() => {
    intervalCollection.removeAll();
});
</script>

<template>
    <div
        v-if="errorMessage.length"
        class="p-4 border-2 border-red-300 bg-red-200 rounded"
    >
        <ul class="px-4">
            <li v-for="error in errorMessage" class="list-disc">{{ error }}</li>
        </ul>
    </div>
    <div class="m-4 font-bold underline">
        Οδηγός δημιουργίας ομάδας εγγράφων
    </div>
    <StepCounter
        :totalSteps="5"
        :currentStep="currentStep"
        ref="stepCounterRef"
        @click-set-step="setStep"
    ></StepCounter>
    <div class="relative">
        <component
            :class="wizard.processingDocuments == 1 ? 'blur-sm' : ''"
            :is="currentComponent"
            :ref="(el: Component) => { componentRef = el }"
        ></component>
        <div
            class="absolute top-0 left-0 w-full h-full bg-gray-700 opacity-70 rounded-xl"
            v-if="wizard.processingDocuments == 1"
        >
            <div class="flex justify-center items-center h-full">
                <div class="text-white font-bold text-lg">
                    Γίνεται επεξεργασία των εγγράφων
                </div>
                <div class="text-white font-bold text-lg pl-1">
                    ({{ wizard.processingDocumentsProgress }})
                </div>
                <!-- spinner -->
                <svg class="h-1/6" viewBox="0 0 50 50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="50"
                        height="50"
                        viewBox="0 0 100 100"
                        overflow="visible"
                        fill="#ffffff"
                        stroke="none"
                    >
                        <defs>
                            <circle id="loader" cx="20" cy="50" r="6" />
                        </defs>
                        <use xlink:href="#loader" x="34">
                            <animate
                                attributeName="opacity"
                                values="0;1;0"
                                dur="1s"
                                begin="0.33s"
                                repeatCount="indefinite"
                            ></animate>
                        </use>
                        <use xlink:href="#loader" x="50">
                            <animate
                                attributeName="opacity"
                                values="0;1;0"
                                dur="1s"
                                begin="0.67s"
                                repeatCount="indefinite"
                            ></animate>
                        </use>
                        <use xlink:href="#loader" x="66">
                            <animate
                                attributeName="opacity"
                                values="0;1;0"
                                dur="1s"
                                begin="1.00s"
                                repeatCount="indefinite"
                            ></animate>
                        </use>
                    </svg>
                </svg>
            </div>
        </div>
    </div>
    <div class="flex flex-row m-4">
        <button
            class="p-2 rounded-md"
            :class="
                wizard.backStepAllowed
                    ? 'transition ease-in-out duration-300 bg-sky-300 hover:shadow-xl hover:-translate-y-0.5'
                    : 'bg-gray-400'
            "
            :disabled="!wizard.backStepAllowed"
            v-if="currentStep !== 1"
            @click="prevStep"
        >
            <FontAwesomeIcon :icon="faChevronLeft" />
            Προηγούμενο βήμα
        </button>
        <div class="grow"></div>
        <button
            class="p-2 rounded-md"
            :class="
                wizard.stepCompleted
                    ? 'transition ease-in-out duration-300 bg-sky-300 hover:shadow-xl hover:-translate-y-0.5'
                    : 'bg-gray-400'
            "
            :disabled="!wizard.stepCompleted"
            v-if="currentStep !== 5"
            @click="nextStep"
        >
            Επόμενο βήμα
            <FontAwesomeIcon :icon="faChevronRight" />
        </button>
    </div>
</template>
