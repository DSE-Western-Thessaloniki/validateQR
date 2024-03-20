<script setup lang="ts">
import { useForm, type InertiaForm, Link } from "@inertiajs/vue3";
import route from "ziggy-js";

const props = defineProps<{
    settings: App.Models.Settings;
}>();

const form: InertiaForm<{
    img_x: number;
    img_y: number;
    img_scale: number;
}> = useForm({
    img_x: 0,
    img_y: 0,
    img_scale: 1,
});

// Κάνουμε έλεγχο για ασφάλεια. Πρέπει πάντα να έχουμε μόνο ένα set ρυθμίσεων
if (props.settings instanceof Array && props.settings.length >= 1) {
    // Πέρασε τις ρυθμίσεις στην φόρμα
    Object.assign(form, props.settings[0]);
}

const submit = () => {
    form.post(route("settings.store"));
};
</script>

<template>
    <div class="p-4">
        <form @submit.prevent="submit">
            <!-- image -->
            <fieldset class="border-black border-2 p-2 mb-2">
                <legend>Ρυθμίσεις Εικόνας QR</legend>

                <div class="mb-2 flex-row">
                    <label for="img_scale" class="w-1/3 inline-block"
                        >Κλίμακα:
                    </label>
                    <input
                        type="number"
                        v-model="form.img_scale"
                        step="0.1"
                        min="0"
                        class="inline-block"
                    />
                </div>
                <div
                    v-if="form.errors?.img_scale"
                    class="flex-row text-red-600"
                >
                    {{ form.errors?.img_scale }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="img_top_margin" class="w-1/3 inline-block"
                        >Απόσταση από το αριστερό άκρο:
                    </label>
                    <input
                        type="number"
                        v-model="form.img_x"
                        step="0.1"
                        min="0"
                        class="inline-block w-max"
                    />
                </div>
                <div v-if="form.errors?.img_x" class="flex-row text-red-600">
                    {{ form.errors?.img_x }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="img_side_margin" class="w-1/3 inline-block"
                        >Απόσταση από το κάτω μέρος της σελίδας:
                    </label>
                    <input
                        type="number"
                        v-model="form.img_y"
                        step="0.1"
                        min="0"
                        class="inline-block w-max"
                    />
                </div>
                <div v-if="form.errors?.img_y" class="flex-row text-red-600">
                    {{ form.errors?.img_y }}
                </div>
            </fieldset>

            <div class="flex">
                <Link
                    :href="route('dashboard')"
                    class="p-3 bg-red-500 rounded-md"
                    >Ακύρωση</Link
                >
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="p-3 bg-blue-500 rounded-md ml-auto"
                >
                    Αποθήκευση
                </button>
            </div>
        </form>
    </div>
</template>
