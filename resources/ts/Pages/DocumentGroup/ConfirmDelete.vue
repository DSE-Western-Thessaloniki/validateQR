<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import { faWarning } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps<{
    group: App.Models.DocumentGroup;
}>();
</script>

<template>
    <AppLayout title="Ομάδες εγγράφων">
        <template #header>
            <div class="flex flex-row items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ομάδες εγγράφων
                </h2>
            </div>
        </template>
        <div class="mt-5 mx-auto w-1/2">
            <div
                class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col"
            >
                <div>
                    Είστε σίγουροι ότι θέλετε να διαγράψετε την ομάδα εγγράφων
                    '{{ group.name }}';
                </div>
                <div class="mt-2">
                    <FontAwesomeIcon
                        :icon="faWarning"
                        class="text-red-600 text-3xl mr-2"
                    ></FontAwesomeIcon
                    ><b>Προσοχή! Δεν θα είναι δυνατή η επαναφορά της.</b>
                </div>
                <div class="flex justify-between mt-8">
                    <Link
                        :href="route('documentGroup.index')"
                        as="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Ακύρωση
                    </Link>
                    <Link
                        :href="route('documentGroup.destroy', group.id)"
                        method="delete"
                        as="button"
                        class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Διαγραφή
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
