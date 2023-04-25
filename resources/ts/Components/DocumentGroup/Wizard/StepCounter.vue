<script setup lang="ts">
import { ref } from "vue";

const props = defineProps<{
    totalSteps: number;
    currentStep: number;
}>();

const emit = defineEmits(["click-set-step"]);

const currentStep = ref(props.currentStep);

function setStep(step: number) {
    currentStep.value = step;
}

function clickSetStep(step: number) {
    emit("click-set-step", step);
}

defineExpose({ setStep });
</script>

<template>
    <div class="flex flex-row px-3">
        <div
            :class="i !== props.totalSteps ? 'grow' : ''"
            class="flex"
            v-for="i in props.totalSteps"
        >
            <button
                :class="currentStep >= i ? 'bg-blue-500' : 'bg-gray-500'"
                class="mx-2 rounded-full bg-blue-500 px-4 py-2 transition ease-in-out duration-700"
                type="button"
                @click="clickSetStep(i)"
            >
                {{ i }}
            </button>
            <div
                :class="i !== props.totalSteps ? 'grow' : ''"
                class="flex flex-col"
            >
                <div class="grow"></div>
                <div
                    :class="
                        i !== props.totalSteps
                            ? 'border-top-2 border-gray-500 border-solid border'
                            : ''
                    "
                ></div>
                <div class="grow"></div>
            </div>
        </div>
    </div>
</template>

<style></style>
