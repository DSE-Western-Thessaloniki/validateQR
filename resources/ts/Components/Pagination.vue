<script setup lang="ts">
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronLeft,
    faChevronRight,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import type { PaginationLink } from "@/pagination.d.ts";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    from: number;
    to: number;
    total: number;
    links: Array<PaginationLink>;
}>();

library.add(faChevronLeft, faChevronRight);
</script>

<template>
    <div
        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
    >
        <div
            class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
        >
            <div>
                <p class="text-sm text-gray-700">
                    Εμφάνιση
                    {{ " " }}
                    <span class="font-medium">{{ from }}</span>
                    {{ " " }}
                    έως
                    {{ " " }}
                    <span class="font-medium">{{ to }}</span>
                    {{ " " }}
                    από
                    {{ " " }}
                    <span class="font-medium">{{ total }}</span>
                    {{ " " }}
                    αποτελέσματα
                </p>
            </div>
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <div v-for="(link, index) in links">
                        <!-- Αριστερό βέλος -->
                        <span
                            v-if="index == 0 && link.url == null"
                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 bg-gray-200 cursor-default"
                        >
                            <font-awesome-icon
                                :icon="faChevronLeft"
                                size="2x"
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </span>
                        <Link
                            :href="link.url"
                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            v-if="index == 0 && link.url != null"
                        >
                            <span class="sr-only">Προηγούμενη</span>
                            <font-awesome-icon
                                :icon="faChevronLeft"
                                size="2x"
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </Link>

                        <!-- Δεξί βέλος -->
                        <span
                            v-if="index == links.length - 1 && link.url == null"
                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 bg-gray-200 cursor-default"
                        >
                            <font-awesome-icon
                                :icon="faChevronRight"
                                size="2x"
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </span>
                        <Link
                            :href="link.url"
                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            v-if="index == links.length - 1 && link.url != null"
                        >
                            <span class="sr-only">Προηγούμενη</span>
                            <font-awesome-icon
                                :icon="faChevronRight"
                                size="2x"
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </Link>

                        <!-- Υπόλοιποι σύνδεσμοι -->
                        <span
                            aria-current="page"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 cursor-default"
                            v-if="
                                index != 0 &&
                                index != links.length - 1 &&
                                link.url == null
                            "
                            >{{ link.label }}</span
                        >
                        <span
                            :href="link.url"
                            aria-current="page"
                            class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-default"
                            v-if="
                                index != 0 &&
                                index != links.length - 1 &&
                                link.url != null &&
                                link.active
                            "
                            >{{ link.label }}</span
                        >
                        <Link
                            :href="link.url"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            v-if="
                                index != 0 &&
                                index != links.length - 1 &&
                                link.url != null &&
                                link.active == false
                            "
                            >{{ link.label }}</Link
                        >
                    </div>
                </nav>
            </div>
        </div>
    </div>
</template>
