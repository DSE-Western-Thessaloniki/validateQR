<script setup lang="ts">
import { faArrowLeft } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Head, Link } from "@inertiajs/vue3";
</script>
<template>
    <Head title="Το έγγραφο δε βρέθηκε" />
    <div
        class="relative flex flex-col sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div class="font-bold text-2xl text-red-600">
            Το έγγραφο που αναζητήσατε δε βρέθηκε!
        </div>
        <Link
            href="/"
            as="button"
            class="rounded-md bg-blue-400 hover:bg-blue-500 transition-colors duration-500 border border-black p-2 block mt-5"
        >
            <FontAwesomeIcon :icon="faArrowLeft" />
            Επιστροφή
        </Link>
    </div>
</template>
<style>
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
