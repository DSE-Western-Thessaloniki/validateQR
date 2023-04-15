<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import DocumentGroupItem from "@/Components/DocumentGroup/DocumentGroupItem.vue";
import Pagination from "@/Components/Pagination.vue";
import type { Pagination as PaginationProps } from "@/pagination.d.ts";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    groups: PaginationProps<DocumentGroup>;
}>();
</script>

<template>
    <AppLayout title="Ομάδες εγγράφων">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ομάδες εγγράφων
            </h2>
        </template>

        <div class="py-12 flex flex-col">
            <div v-for="(group, index) in groups.data">
                <DocumentGroupItem
                    :group="group"
                    :index="groups.from + index - 1"
                    :step="group.step"
                />
            </div>
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
