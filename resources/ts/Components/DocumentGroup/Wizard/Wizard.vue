<script setup lang="ts">
import { ref, markRaw, type Component } from "vue";
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

library.add(faChevronLeft, faChevronRight);

const props = withDefaults(
    defineProps<{
        documentGroup?: App.Models.DocumentGroup;
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
            };
        },
    }
);

const wizard = useWizardStore();
wizard.documentGroup = props.documentGroup;
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
</script>

<template>
    <div
        v-if="errorMessage.length"
        class="p-4 border border-2 border-red-300 bg-red-200 rounded"
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
    <component
        :is="currentComponent"
        :ref="(el: Component) => { componentRef = el }"
    ></component>
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
