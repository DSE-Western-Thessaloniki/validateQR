<script setup lang="ts">
import { ref, markRaw } from "vue";
import Step1 from "./Step1.vue";
import Step2 from "./Step2.vue";
import Step3 from "./Step3.vue";
import Step4 from "./Step4.vue";
import Step5 from "./Step5.vue";
import StepCounter from "./StepCounter.vue";
import type DocumentGroupFormData from "./DocumentGroupFormData";
import type { DocumentGroup } from "@/models";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPencil, faTrash } from "@fortawesome/free-solid-svg-icons";
import {
    faChevronLeft,
    faChevronRight,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faChevronLeft, faChevronRight);

const emptyDocumentGroup: DocumentGroup = {
    id: -1,
    name: "",
    step: 1,
    published: false,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString(),
};

const props = defineProps<{
    documentGroup?: DocumentGroup;
}>();

const form_data = ref({
    steps: 5,
});

const formSteps = [Step1, Step2, Step3, Step4, Step5];
const currentStep = ref(1);

const currentComponent = ref(markRaw(formSteps[0]));

function setStep(step: number) {
    currentStep.value = step;
    currentComponent.value = markRaw(formSteps[step - 1]);
}
</script>

<template>
    <div class="m-4 px-4 font-bold underline">
        Οδηγός δημιουργίας ομάδας εγγράφων
    </div>
    <StepCounter :totalSteps="5" :currentStep="1"></StepCounter>
    <component :is="currentComponent"></component>
    <div class="flex flex-row m-4">
        <button
            class="p-2 rounded-md transition ease-in-out duration-300 bg-sky-300 hover:shadow-xl hover:-translate-y-0.5"
            v-if="currentStep !== 1"
            @click="setStep(currentStep - 1)"
        >
            <FontAwesomeIcon :icon="faChevronLeft" />
            Προηγούμενο βήμα
        </button>
        <div class="grow"></div>
        <button
            class="p-2 rounded-md transition ease-in-out duration-300 bg-sky-300 hover:shadow-xl hover:-translate-y-0.5"
            v-if="currentStep !== 5"
            @click="setStep(currentStep + 1)"
        >
            Επόμενο βήμα
            <FontAwesomeIcon :icon="faChevronRight" />
        </button>
    </div>
</template>
