<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { faArrowLeft } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
</script>
<template>
    <GuestLayout title="Το έγγραφο δε βρέθηκε">
        <div
            class="relative flex flex-col sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
        >
            <div class="font-bold text-2xl text-red-600">
                Το έγγραφο που αναζητήσατε δε βρέθηκε!
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
