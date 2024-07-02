<script setup lang="ts">
import { ref } from "vue";

const props = withDefaults(
    defineProps<{
        active?: boolean;
    }>(),
    {
        active: false,
    }
);

const toggleActive = ref(false);

const emit = defineEmits<{
    click: [];
}>();
</script>

<template>
    <div class="flex justify-between items-center" @click="emit('click')">
        <slot></slot>
        <div
            class="w-10 h-6 flex items-center bg-gray-300 rounded-full p-1 duration-200 ease-in-out"
            :class="{ 'bg-green-400': toggleActive }"
        >
            <div
                class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200 ease-in-out"
                :class="{ 'translate-x-4': toggleActive }"
            ></div>
        </div>
    </div>
</template>
