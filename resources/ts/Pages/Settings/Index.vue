<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Settings from "@/Components/Settings.vue";

const props = defineProps<{
    image?: string;
    settings: App.Models.Settings;
}>();
</script>

<template>
    <AppLayout title="Ρυθμίσεις">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ρυθμίσεις
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Settings :image="props.image" :settings="settings" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
