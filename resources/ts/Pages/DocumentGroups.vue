<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import DocumentGroupItem from "@/Components/DocumentGroup/DocumentGroupItem.vue";

const props = defineProps<{
    groups: Array<DocumentGroup>;
}>();
</script>

<template>
    <AppLayout title="Ομάδες εγγράφων">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ομάδες εγγράφων
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-for="group in groups" class="p-6">
                        <DocumentGroupItem :group="group" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
