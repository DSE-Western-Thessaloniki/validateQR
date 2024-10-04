<script setup>
import { ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

defineProps({
    title: String,
    canLogin: Boolean,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route("logout"));
};

const page = usePage();
</script>

<template>
    <div>
        <Head :title="title" />

        <div
            class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
        >
            <div
                v-if="canLogin"
                class="sm:fixed sm:top-0 sm:right-0 p-6 text-right"
            >
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Πίνακας ελέγχου</Link
                >

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        >Log in</Link
                    >

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        >Εγγραφή</Link
                    >
                </template>
            </div>

            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <slot />
                <div
                    class="footer text-black bg-gradient-to-br from-red-700 to-orange-500"
                >
                    Σχεδίαση και υλοποίηση Ιντζόγλου Θεόφιλος - Διεύθυνση Δ.Ε.
                    Δυτ. Θεσσαλονίκης
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 0.25rem;
    text-align: center;
}
</style>
