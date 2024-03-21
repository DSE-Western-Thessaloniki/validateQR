<script setup lang="ts">
import Message from "@/Components/Message.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import type { PageWithFlashProps } from "@/flash-message";
import type { PageWithAuthProps } from "@/page-props-auth";
import {
    faCircleCheck,
    faCircleXmark,
    faPencil,
    faPlus,
    faTrash,
    faUser,
    faUserNinja,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { TransitionGroup, useAttrs } from "vue";
import route from "ziggy-js";

const props = defineProps<{
    users: App.Models.User[];
}>();

const page = usePage<PageWithFlashProps & PageWithAuthProps>();

const toggleActive = (user: App.Models.User) => {
    axios
        .post(route("user.toggleActive", user))
        .then((response) => {
            console.log(response);
            if (response.status === 200 && response.data.success === true) {
                console.log("Success");
                router.reload({
                    only: ["users"],
                    preserveScroll: true,
                });
            } else {
                console.log("Error");
            }
        })
        .catch((reason) => {
            console.log(`Error: ${reason}`);
        });
};
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
                        <Message
                            class="mb-5"
                            type="success"
                            v-if="page.props.flash.success"
                            >{{ page.props.flash.success }}</Message
                        >
                        <Message
                            class="mb-5"
                            type="danger"
                            v-if="page.props.flash.danger"
                            >{{ page.props.flash.danger }}</Message
                        >
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
                                class="flex flex-wrap flex-row mx-4 my-4 rounded-lg p-4 shadow-lg content-center"
                                :class="
                                    user.active ? 'bg-white' : 'bg-gray-300'
                                "
                            >
                                <div class="py-2 my-auto">
                                    {{ index + 1 }}.
                                    <FontAwesomeIcon
                                        v-if="user.role === 255"
                                        :icon="faUserNinja"
                                        class="mx-1 text-red-500"
                                    />
                                    <FontAwesomeIcon
                                        v-if="user.role === 100"
                                        :icon="faUser"
                                        class="mx-1"
                                    />
                                    {{ user.name }}
                                </div>
                                <span class="text-blue-500 mx-1 py-2"
                                    >({{ user.username }})</span
                                >
                                <span
                                    v-if="!user.active"
                                    class="text-red-500 mx-1 py-2"
                                    >(Ανενεργός χρήστης)</span
                                >
                                <span class="grow my-auto"></span>
                                <Link
                                    :href="route('user.edit', [user.id])"
                                    class="transition ease-in-out duration-300 mx-1 hover:bg-sky-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                                >
                                    <FontAwesomeIcon :icon="faPencil" />
                                </Link>
                                <button
                                    v-if="
                                        user.id !== page.props.auth.user.id &&
                                        !user.active
                                    "
                                    @click="toggleActive(user)"
                                    class="transition ease-in-out duration-300 mx-1 hover:bg-green-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                                >
                                    <FontAwesomeIcon :icon="faCircleCheck" />
                                </button>
                                <button
                                    v-if="
                                        user.id !== page.props.auth.user.id &&
                                        user.active
                                    "
                                    @click="toggleActive(user)"
                                    class="transition ease-in-out duration-300 mx-1 hover:bg-orange-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                                >
                                    <FontAwesomeIcon :icon="faCircleXmark" />
                                </button>
                                <Link
                                    v-if="user.id !== page.props.auth.user.id"
                                    :href="route('user.destroy', [user.id])"
                                    method="delete"
                                    as="button"
                                    class="transition ease-in-out duration-300 mx-1 hover:bg-red-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                                >
                                    <FontAwesomeIcon :icon="faTrash" />
                                </Link>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
