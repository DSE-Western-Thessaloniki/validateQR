<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { faArrowLeft } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps<{
    document: App.Models.Document;
    replacement: string;
}>();
</script>

<template>
    <GuestLayout>
        <div
            class="relative flex flex-col sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
        >
            <div class="font-bold text-2xl text-gray-200 text-center">
                <div>Το έγγραφο με αναγνωριστικό</div>
                <div class="text-orange-500">{{ document.id }}</div>
                <div>έχει αντικατασταθεί από το έγγραφο</div>
                <a
                    :href="route('document.show', replacement)"
                    class="text-blue-500 underline"
                    >{{ replacement }}</a
                >
            </div>
            <Link
                :href="route('home')"
                class="rounded-md bg-blue-400 hover:bg-blue-500 transition-colors duration-500 border border-black p-2 block mt-5"
            >
                <FontAwesomeIcon :icon="faArrowLeft" />
                Επιστροφή
            </Link>
        </div>
    </GuestLayout>
</template>
