<script setup lang="ts">
import { useWizardStore } from "@/Stores/wizard";

const wizard = useWizardStore();

const save = () => {
    return new Promise((resolve, reject) => {
        console.log(wizard.documentGroup);
        // resolve(true);
        reject(["Σφάλμα αποθήκευσης"]);
    });
};

defineExpose({ save });
</script>
<template>
    <div class="flex flex-row bg-white p-4 m-4 rounded items-center">
        <label for="group-name" class="px-4">Όνομα ομάδας εγγράφων:</label>
        <input
            class="transition ease-in-out hover:border-blue-500 ring-blue-500 hover:inset-ring hover:ring-1 focus:border-blue-500 placeholder:italic placeholder:text-slate-400"
            type="text"
            name="group-name"
            placeholder="Όνομα"
        />
    </div>
</template>
