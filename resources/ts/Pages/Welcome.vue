<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ref } from "vue";
import { route } from "ziggy-js";

defineProps({
    canLogin: Boolean,
});

const form_document = ref("");

const onKeyDown = (event: KeyboardEvent) => {
    if (event.key === "Enter") {
        window.location.href = route("document.show", {
            document: form_document.value,
        });
    }
};
</script>

<template>
    <GuestLayout :canLogin title="Αρχική">
        <div class="flex justify-center">
            <h1 class="font-bold text-5xl text-red-600">validateQR</h1>
        </div>

        <div class="mt-16"></div>

        <div
            class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between"
        >
            <div
                class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0"
            >
                Λήψη επικυρωμένων αντιγράφων εγγράφων με χρήση QRCode
            </div>
        </div>
        <div class="flex justify-center mt-16 sm:justify-between">
            <input
                type="text"
                name="code"
                class="block w-full rounded-l-md"
                v-model="form_document"
                @keydown="onKeyDown"
                placeholder="Εισάγετε εδώ τον κωδικό του εγγράφου"
            />
            <a
                class="inline-flex items-center px-4 py-2 bg-red-800 rounded-r-md font-semibold text-xs text-white tracking-widest hover:bg-red-600 disabled:opacity-25 disabled:bg-gray-500 transition"
                :href="route('document.show', { document: form_document })"
            >
                Αναζήτηση
            </a>
        </div>
    </GuestLayout>
</template>
