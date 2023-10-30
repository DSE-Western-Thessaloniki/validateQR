<script setup lang="ts">
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faHouse,
    faFileArrowUp,
    faFileContract,
    faBoxArchive,
    faUsers,
    faLayerGroup,
    faGears,
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
                Καλωσορίσατε στην εφαρμογή ελέγχου εγκυρότητας ψηφιακών εγγράφων
            </h1>

            <p class="mt-6 text-gray-500 leading-relaxed text-justify indent-8">
                Η εφαρμογή επιτρέπει τη δημοσίευση εγγράφων προσθέτοντας ένα
                μοναδικό αναγνωριστικό σε κάθε ένα από αυτά (με κείμενο και QR).
                Με χρήση ενός συνδέσμου μπορεί ο τελικός αποδέκτης να κάνει
                έλεγχο του εγγράφου κάνοντας λήψη του πρωτότυπου. Ο σύνδεσμος
                εμφανίζεται στο έγγραφο και είναι και το περιεχόμενο του QR για
                ευκολία στη χρήση.
            </p>
        </div>

        <div
            class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8"
        >
            <Link :href="route('documentGroup.index')">
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
            <Link :href="route('settings.index')">
                <div
                    class="flex flex-col items-center bg-white shadow p-4 rounded-md hover:shadow-lg"
                >
                    <div>
                        <font-awesome-icon :icon="faGears" size="2xl" />
                    </div>
                    <div class="mt-2">Ρυθμίσεις</div>
                </div>
            </Link>
            <Link :href="route('user.index')">
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
