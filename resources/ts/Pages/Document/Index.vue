<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import type { Pagination as PaginationProps } from "@/pagination.d.ts";
import Pagination from "@/Components/Pagination.vue";
import { debounce } from "lodash";
import { ref, watch } from "vue";
import AppLayoutNoTransition from "@/Layouts/AppLayoutNoTransition.vue";

const props = defineProps<{
    documents: PaginationProps<
        App.Models.Document & {
            document_group: App.Models.DocumentGroup;
            extra_state: App.Models.ExtraState;
        }
    >;
    filters: {
        filter?: string;
    };
}>();

const filter = ref(props.filters?.filter ?? "");

const findDocuments = () => {
    router.get(
        route("document.index"),
        { filter: filter.value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["documents"],
        }
    );
};

watch(
    filter,

    debounce(
        //     () => {
        //     console.log("debounce");
        //     console.log(window.history.state);
        //     findDocuments();
        // }
        findDocuments,
        300
    )
);
</script>

<template>
    <AppLayoutNoTransition title="Αναζήτηση εγγράφων">
        <template #header>
            <div class="flex flex-row items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Αναζήτηση εγγράφων
                </h2>
            </div>
        </template>
        <div class="mt-2 w-[70%] mx-auto">
            <div class="flex grow items-center">
                <label class="px-2" for="filter"> Φίλτρο: </label>
                <input
                    class="grow hover:border-blue-500 ring-blue-500 hover:inset-ring hover:ring-1 focus:border-blue-500 placeholder:italic placeholder:text-slate-400"
                    :class="filter ? 'bg-sky-200' : ''"
                    type="search"
                    name="filter"
                    v-model="filter"
                    placeholder="Όνομα αρχείου/αναγνωριστικό"
                />
            </div>

            <div class="mt-4 bg-white rounded-md p-2">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-x border-black"
                            >
                                Όνομα αρχείου
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-x border-black"
                            >
                                Αναγνωριστικό
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-x border-black"
                            >
                                Ομάδα
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(document, index) in documents.data"
                            :key="document.id"
                            class="border-b border-gray-200 hover:bg-gray-200"
                            :class="index % 2 ? 'bg-gray-100' : 'bg-white'"
                        >
                            <td
                                class="px-4 py-2 text-sm font-medium text-gray-700 border-x border-black"
                            >
                                {{ document.filename }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm font-medium text-gray-700 border-x border-black"
                            >
                                {{ document.id }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm font-medium text-gray-700 border-x border-black"
                            >
                                {{ document.document_group.name }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination
                    :from="documents.from"
                    :to="documents.to"
                    :total="documents.total"
                    :links="documents.links"
                    v-if="documents.next_page_url !== documents.prev_page_url"
                    class="mt-3"
                ></Pagination>
            </div>
        </div>
    </AppLayoutNoTransition>
</template>
