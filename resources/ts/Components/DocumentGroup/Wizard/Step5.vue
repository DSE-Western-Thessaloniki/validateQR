<script setup lang="ts">
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import { useWizardStore } from "@/Stores/wizard";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

const wizard = useWizardStore();

const onPublishedStateToggle = () => {
    axios
        .post(route("documentGroup.togglePublished", wizard.documentGroup))
        .catch((error: unknown) => {
            let errors: Array<String> = [];

            if (error instanceof Error) {
                errors.push(error.message);
            } else {
                errors.push("Γενικό σφάλμα αποθήκευσης!");
            }
        });
};

const save = () => {
    return new Promise(async (resolve, reject) => {
        resolve("OK");
    });
};

defineExpose({ save });
</script>
<template>
    <div class="flex flex-col bg-white p-4 m-4 rounded items-center">
        <div class="font-bold pb-4 text-xl">
            Βήμα 5ο: Δημοσίευση ομάδας αρχείων
        </div>

        <div class="flex flex-col items-center border-black border p-4 w-6/12">
            <ToggleSwitch
                class="w-6/12"
                :active="wizard.documentGroup?.published ? true : false"
                @click="onPublishedStateToggle"
            >
                <div>Ενεργοποίηση δημοσίευσης</div>
            </ToggleSwitch>
        </div>
    </div>
</template>
