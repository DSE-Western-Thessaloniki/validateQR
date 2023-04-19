<script setup lang="ts">
import DocumentGroupStatus from "@/Components/DocumentGroup/DocumentGroupStatus.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPencil, faTrash } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faPencil, faTrash);

const props = defineProps<{
    group: DocumentGroup;
    index: number;
    step: number;
}>();
</script>

<template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-2">
        <div
            class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-row"
        >
            <div class="py-2">{{ index + 1 }}. {{ group.name }}</div>
            <div class="text-gray-500 px-1 grow py-2">
                ({{ group.documents_count }} έγγραφα)
            </div>
            <DocumentGroupStatus
                :step="step"
                totalSteps="5"
                :colors="['red', 'orange', 'green']"
                class="py-2 px-5"
            ></DocumentGroupStatus>
            <div
                class="transition ease-in-out duration-300 bg-amber-400 px-3 py-2 mx-2 rounded-md shadow-sm hover:shadow-xl hover:bg-amber-300 hover:-translate-y-0.5"
            >
                <font-awesome-icon :icon="faPencil" size="1x" />
            </div>
            <div
                class="transition ease-in-out duration-300 bg-red-500 px-3 py-2 mx-2 rounded-md shadow-sm hover:shadow-xl hover:bg-red-400 hover:-translate-y-0.5"
            >
                <font-awesome-icon :icon="faTrash" size="1x" />
            </div>
        </div>
    </div>
</template>
