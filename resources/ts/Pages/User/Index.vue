<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import { faPencil, faPlus } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import { TransitionGroup } from "vue";
import route from "ziggy-js";

const props = defineProps<{
    users: App.Models.User[];
}>();
</script>
<template>
    <AppLayout title="Χρήστες">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Χρήστες
            </h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden sm:rounded-lg">
                    <div class="m-4 py-2">
                        <Link
                            :href="route('user.create')"
                            class="bg-blue-500 px-4 py-3 rounded-md shadow-lg"
                            ><FontAwesomeIcon :icon="faPlus" /> Δημιουργία νέου
                            χρήστη</Link
                        >
                    </div>
                    <TransitionGroup name="list" tag="div">
                        <div v-for="(user, index) in users" :key="user.id">
                            <div
                                class="flex flex-wrap flex-row bg-white mx-4 my-4 rounded-lg p-4 shadow-lg content-center"
                            >
                                <div class="py-2 my-auto">
                                    {{ index + 1 }}. {{ user.name }}
                                </div>
                                <span
                                    class="text-blue-500 mx-1 py-2 grow my-auto"
                                    >({{ user.username }})</span
                                >
                                <Link
                                    href=""
                                    class="transition ease-in-out duration-300 mx-1 hover:bg-sky-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                                    ><FontAwesomeIcon :icon="faPencil"
                                /></Link>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
