<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Wizard from "@/Components/DocumentGroup/Wizard/Wizard.vue";
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
        <div class="py-6 flex flex-col">
            <Wizard></Wizard>
        </div>
    </AppLayout>
</template>
