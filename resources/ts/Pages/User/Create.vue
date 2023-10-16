<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import DangerButton from "@/Components/DangerButton.vue";
import route from "ziggy-js";
import DropdownList from "@/Components/DropdownList.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const submit = () => {
    form.post(route("user.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AppLayout title="Δημιουργία νέου χρήστη">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Χρήστες
            </h2>
        </template>

        <div class="py-4">
            <AuthenticationCard class="min-h-full">
                <div
                    class="text-3xl text-bold mt-2 mb-4 p-5 bg-slate-300 rounded-lg shadow-md"
                >
                    Δημιουργία νέου χρήστη
                </div>
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="Όνομα" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Κωδικός" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="password_confirmation"
                            value="Επιβεβαίωση κωδικού"
                        />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="user_type" value="Είδος" />
                        <DropdownList id="user_type" class="w-full">
                            <option value="255">Διαχειριστής</option>
                            <option value="100">Συγγραφέας</option>
                        </DropdownList>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <DangerButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="router.get(route('user.index'))"
                        >
                            ΑΚΥΡΩΣΗ
                        </DangerButton>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            ΑΠΟΘΗΚΕΥΣΗ
                        </PrimaryButton>
                    </div>
                </form>
            </AuthenticationCard>
        </div>
    </AppLayout>
</template>
