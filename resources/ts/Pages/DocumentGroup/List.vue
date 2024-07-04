<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import DocumentGroupItem from "@/Components/DocumentGroup/DocumentGroupItem.vue";
import Pagination from "@/Components/Pagination.vue";
import type { Pagination as PaginationProps } from "@/pagination.d.ts";
import { router, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { route } from "ziggy-js";
import debounce from "lodash/debounce";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faPlus);

const props = defineProps<{
    groups: PaginationProps<
        App.Models.DocumentGroup & { documents_count: number }
    >;
    filters: { filter: string };
}>();

const filter = ref(props.filters.filter);

watch(
    filter,
    debounce((value) => {
        router.get(
            route("documentGroups"),
            { filter: filter.value },
            { preserveState: true, replace: true }
        );
    }, 500)
);
</script>

<template>
    <AppLayout title="Ομάδες εγγράφων">
        <template #header>
            <div class="flex flex-row items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ομάδες εγγράφων
                </h2>
                <div class="flex grow items-center">
                    <label class="grow text-right px-2" for="filter">
                        Φίλτρο:
                    </label>
                    <input
                        class="transition ease-in-out hover:border-blue-500 ring-blue-500 hover:inset-ring hover:ring-1 focus:border-blue-500 placeholder:italic placeholder:text-slate-400"
                        :class="filter ? 'bg-sky-200' : ''"
                        type="search"
                        name="filter"
                        v-model="filter"
                        placeholder="Όνομα ομάδας"
                    />
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="p-3 max-w-7xl mx-auto sm:px-6 lg:px-8 my-3">
                <Link
                    :href="route('documentGroup.create')"
                    class="p-3 bg-blue-500 rounded"
                >
                    <FontAwesomeIcon :icon="faPlus" />
                    Δημιουργία νέας ομάδας
                </Link>
            </div>
            <TransitionGroup name="list" tag="div">
                <div v-for="(group, index) in groups.data" :key="group.id">
                    <DocumentGroupItem
                        :group="group"
                        :index="groups.from + index - 1"
                        :step="group.step"
                    />
                </div>
            </TransitionGroup>
            <Pagination
                :from="groups.from"
                :to="groups.to"
                :total="groups.total"
                :links="groups.links"
                v-if="groups.next_page_url !== groups.prev_page_url"
                class="mt-3"
            ></Pagination>
        </div>
    </AppLayout>
</template>

<style>
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
