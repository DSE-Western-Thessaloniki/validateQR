<script setup lang="ts">
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faHouse,
    faFileArrowUp,
    faFileContract,
    faBoxArchive,
    faUsers,
    faLayerGroup,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import route from "ziggy-js";

library.add(
    faHouse,
    faFileArrowUp,
    faFileContract,
    faBoxArchive,
    faUsers,
    faLayerGroup
);
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Welcome to your Jetstream application!
            </h1>

            <p class="mt-6 text-gray-500 leading-relaxed">
                Laravel Jetstream provides a beautiful, robust starting point
                for your next Laravel application. Laravel is designed to help
                you build your application using a development environment that
                is simple, powerful, and enjoyable. We believe you should love
                expressing your creativity through programming, so we have spent
                time carefully crafting the Laravel ecosystem to be a breath of
                fresh air. We hope you love it.
            </p>
        </div>

        <div
            class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8"
        >
            <Link :href="route('documentGroups')">
                <div
                    class="flex flex-col items-center bg-white shadow p-4 rounded-md hover:shadow-lg"
                >
                    <div>
                        <font-awesome-icon :icon="faLayerGroup" size="2xl" />
                    </div>
                    <div class="mt-2">Ομάδες εγγράφων</div>
                </div>
            </Link>
            <!-- <Link href="">
                <div
                    class="flex flex-col items-center bg-white shadow p-4 rounded-md hover:shadow-lg"
                >
                    <div>
                        <font-awesome-icon :icon="faBoxArchive" size="2xl" />
                    </div>
                    <div class="mt-2">Αρχείο ολοκληρωμένων ενεργειών</div>
                </div>
            </Link> -->
            <!-- <Link href="">
                <div
                    class="flex flex-col items-center bg-white shadow p-4 rounded-md hover:shadow-lg"
                >
                    <div>
                        <font-awesome-icon :icon="faFileArrowUp" size="2xl" />
                    </div>
                    <div class="mt-2">Ανέβασμα εγγράφων για παραγωγή QR</div>
                </div>
            </Link>
            <Link href="">
                <div
                    class="flex flex-col items-center bg-white shadow p-4 rounded-md hover:shadow-lg"
                >
                    <div>
                        <font-awesome-icon :icon="faFileContract" size="2xl" />
                    </div>
                    <div class="mt-2">Ανέβασμα υπογεγραμμένων εγγράφων</div>
                </div>
            </Link> -->
            <Link href="">
                <div
                    class="flex flex-col items-center bg-white shadow p-4 rounded-md hover:shadow-lg"
                >
                    <div>
                        <font-awesome-icon :icon="faUsers" size="2xl" />
                    </div>
                    <div class="mt-2">Διαχείριση χρηστών</div>
                </div>
            </Link>
        </div>
    </div>
</template>
