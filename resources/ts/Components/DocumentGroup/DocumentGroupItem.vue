<script setup lang="ts">
import DocumentGroupStatus from "@/Components/DocumentGroup/DocumentGroupStatus.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPencil, faTrash } from "@fortawesome/free-solid-svg-icons";
import {
    faCircleCheck,
    faCircleXmark,
} from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";

library.add(faPencil, faTrash, faCircleCheck, faCircleXmark);

const props = defineProps<{
    group: App.Models.DocumentGroup & { documents_count: number };
    index: number;
    step: number;
}>();

function format_date(dateString: string) {
    const date = new Date(dateString);
    return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
}

const published = props.group.published;

const publishedBackground = published ? "bg-green-300" : "bg-orange-300";
const publishedBorder = published ? "border-green-500" : "border-orange-500";
const publishedText = published ? "text-green-700" : "text-orange-700";
</script>

<template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-3">
        <div
            class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col"
        >
            <div class="flex flex-row pt-3 px-6">
                <div class="py-2">{{ index + 1 }}. {{ group.name }}</div>
                <div class="text-gray-500 px-1 grow py-2">
                    ({{ group.documents_count }} έγγραφα)
                </div>
                <DocumentGroupStatus
                    :step="step"
                    totalSteps="5"
                    :colors="[
                        'LightGray',
                        'Silver',
                        'DarkGray',
                        'Gray',
                        'DimGray',
                    ]"
                    class="py-2 px-5"
                ></DocumentGroupStatus>
                <Link
                    :href="route('documentGroup.edit', { id: group.id })"
                    class="transition ease-in-out duration-300 px-3 py-2 mx-2 rounded-md hover:shadow-xl hover:bg-sky-300 hover:-translate-y-0.5"
                >
                    <FontAwesomeIcon :icon="faPencil" size="1x" />
                </Link>
                <div
                    class="transition ease-in-out duration-300 px-3 py-2 mx-2 rounded-md hover:shadow-xl hover:bg-red-500 hover:-translate-y-0.5"
                >
                    <FontAwesomeIcon :icon="faTrash" size="1x" />
                </div>
                <div
                    class="transition ease-in-out duration-300 px-3 py-2 mx-2 rounded-md hover:shadow-xl hover:-translate-y-0.5"
                    :class="
                        published ? 'hover:bg-orange-300' : 'hover:bg-green-300'
                    "
                >
                    <FontAwesomeIcon
                        :icon="published ? faCircleXmark : faCircleCheck"
                        size="1x"
                    />
                </div>
            </div>
            <div
                class="flex flex-row px-6 mt-2 py-1 bg-gray-100 border-t-2"
                :class="[publishedBackground, publishedBorder]"
            >
                <div class="text-xs text-gray-700">
                    Δημιουργήθηκε: {{ format_date(group.created_at) }}
                </div>
                <div
                    class="text-xs text-gray-700"
                    v-if="group.created_at !== group.updated_at"
                >
                    Τροποποιήθηκε: {{ format_date(group.updated_at) }}
                </div>
                <div
                    class="grow text-xs text-bold text-right"
                    :class="[publishedText]"
                >
                    {{ published ? "Δημοσιευμένο" : "Αδημοσίευτο" }}
                </div>
            </div>
        </div>
    </div>
</template>
