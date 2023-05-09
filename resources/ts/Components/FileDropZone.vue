<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from "vue";
import { humanReadableFileSize } from "@/tools";

const onDrop = (event: DragEvent) => {
    dragged.value = false;

    if (!event.dataTransfer) {
        return;
    }

    for (const file of event.dataTransfer.files) {
        files.value.push(file);
    }
};

const removeFile = (index: number) => {
    files.value.splice(index, 1);
};

const dragged = ref(false);

const files: Ref<File[]> = ref([]);
</script>

<template>
    <div
        class="cursor-pointer m-3 p-2 w-full h-64 bg-indigo-500 flex flex-col items-center justify-center rounded border-2 border-indigo-700 shadow-2xl"
        :class="{ 'bg-indigo-100': dragged }"
        @drop.prevent="onDrop"
        @dragenter.prevent="dragged = true"
        @dragover.prevent
        @dragend="dragged = false"
        @dragleave="dragged = false"
    >
        <span
            class="text-white text-center pointer-events-none"
            v-if="!dragged"
        >
            Σύρετε τα αρχεία εδώ μέσα ή πατήστε για να εμφανιστεί το παράθυρο
            διαλόγου
        </span>
        <span class="text-black text-center pointer-events-none" v-if="dragged">
            Αφήστε τα αρχεία εδώ μέσα για να προστεθούν στην ομάδα
        </span>
    </div>
    <div
        v-if="files.length"
        class="flex flex-col h-48 overflow-y-auto w-full mt-2"
    >
        <div
            v-for="(file, index) in files"
            class="w-full rounded shadow-md bg-gray-200 p-3 my-1 flex flex-row items-center"
        >
            <div class="flex-grow flex flex-row items-center">
                <div class="text-ellipsis">
                    {{ index + 1 }}. {{ file.name }}
                </div>
                <div class="text-gray-600 text-sm pl-2">
                    ({{ humanReadableFileSize(file.size) }})
                </div>
            </div>
            <div
                class="px-3 py-2 hover:bg-red-200 hover:shadow-lg hover:duration-700 hover:ease-in-out cursor-pointer"
                @click="removeFile(index)"
            >
                X
            </div>
        </div>
    </div>
</template>
