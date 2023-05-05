<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Wizard from "@/Components/DocumentGroup/Wizard/Wizard.vue";
import type { DocumentGroup } from "@/models";

const props = defineProps<{
    group: DocumentGroup;
}>();
</script>

<template>
    <AppLayout title="Δημιουργία νέας ομάδας εγγράφων">
        <template #header>
            <div class="flex flex-row items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Δημιουργία νέας ομάδας εγγράφων
                </h2>
            </div>
        </template>
        <div class="p-3 max-w-7xl mx-auto sm:px-6 lg:px-8 my-3">
            <Wizard :documentGroup="props.group" />
        </div>
    </AppLayout>
</template>
