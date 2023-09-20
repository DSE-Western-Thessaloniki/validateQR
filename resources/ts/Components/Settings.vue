<script setup lang="ts">
import { useForm, type InertiaForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import route from "ziggy-js";

const props = withDefaults(
    defineProps<{
        image?: string;
        settings: App.Models.Settings;
    }>(),
    {
        image: "",
    }
);

const form: InertiaForm<{
    image: File | null;
    qr_side: number;
    qr_top_margin: number;
    qr_side_margin: number;
    qr_scale: number;
    img_side: number;
    img_top_margin: number;
    img_side_margin: number;
    img_scale: number;
    remove_image: number;
}> = useForm({
    image: null,
    qr_side: 0,
    qr_top_margin: 0,
    qr_side_margin: 0,
    qr_scale: 1,
    img_side: 0,
    img_top_margin: 0,
    img_side_margin: 0,
    img_scale: 1,
    remove_image: 0,
});

let image = ref(props.image);

// Κάνουμε έλεγχο για ασφάλεια. Πρέπει πάντα να έχουμε μόνο ένα set ρυθμίσεων
if (props.settings instanceof Array && props.settings.length >= 1) {
    // Πέρασε τις ρυθμίσεις στην φόρμα
    Object.assign(form, props.settings[0]);
}

const changeImage = (e: Event) => {
    e.preventDefault();
    form.image = (e.target as HTMLInputElement).files![0];
};

const submit = () => {
    form.post(route("settings.store"));
};

const removeImage = (e: Event) => {
    e.preventDefault();
    image.value = "";
    form.remove_image = 1;
};
</script>

<template>
    <div class="p-4">
        <form @submit.prevent="submit">
            <!-- QR -->
            <fieldset class="border-black border-2 p-2 mb-2">
                <legend>Ρυθμίσεις QR</legend>

                <div class="mb-2 flex-row">
                    <label for="qr_side" class="w-1/3 inline-block"
                        >Θέση εμφάνισης QR:</label
                    >
                    <select
                        id="qr_side"
                        v-model="form.qr_side"
                        class="inline-block w-auto"
                    >
                        <option value="0">Στο αριστερό άκρο της σελίδας</option>
                        <option value="1">Στο κέντρο της σελίδας</option>
                        <option value="2">Στο δεξί άκρο της σελίδας</option>
                    </select>
                </div>
                <div v-if="form.errors?.qr_side" class="flex-row text-red-600">
                    {{ form.errors?.qr_side }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="qr_scale" class="w-1/3 inline-block"
                        >Κλίμακα:
                    </label>
                    <input
                        type="number"
                        v-model="form.qr_scale"
                        step="0.1"
                        min="0"
                        class="inline-block"
                    />
                </div>
                <div v-if="form.errors?.qr_scale" class="flex-row text-red-600">
                    {{ form.errors?.qr_scale }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="qr_top_margin" class="w-1/3 inline-block"
                        >Περιθώριο από την κορυφή:
                    </label>
                    <input
                        type="number"
                        v-model="form.qr_top_margin"
                        step="0.1"
                        min="0"
                        class="inline-block w-max"
                    />
                </div>
                <div
                    v-if="form.errors?.qr_top_margin"
                    class="flex-row text-red-600"
                >
                    {{ form.errors?.qr_top_margin }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="qr_side_margin" class="w-1/3 inline-block"
                        >Περιθώρια αριστερά και δεξιά του εγγράφου:
                    </label>
                    <input
                        type="number"
                        v-model="form.qr_side_margin"
                        step="0.1"
                        min="0"
                        class="inline-block w-max"
                    />
                </div>
                <div
                    v-if="form.errors?.qr_side_margin"
                    class="flex-row text-red-600"
                >
                    {{ form.errors?.qr_side_margin }}
                </div>
            </fieldset>

            <!-- image -->
            <fieldset class="border-black border-2 p-2 mb-2">
                <legend>Ρυθμίσεις Εικόνας</legend>

                <div class="mb-2 flex">
                    <label for="current_image" class="w-1/3 inline-block"
                        >Τρέχουσα εικόνα:</label
                    >
                    <div v-if="image" class="inline-block w-2/3 relative">
                        <img :src="image" />
                        <button
                            @click="removeImage"
                            type="button"
                            class="absolute top-0 right-0 px-3 py-2 bg-slate-500"
                        >
                            X
                        </button>
                    </div>
                    <div
                        v-else
                        class="flex h-24 w-24 text-center border-gray-300 border-2 p-2 items-center justify-center"
                    >
                        Καμία
                    </div>
                </div>

                <div class="mb-2 flex-row">
                    <label for="image" class="w-1/3 inline-block"
                        >Νέα εικόνα:</label
                    >
                    <input
                        type="file"
                        id="image"
                        @change="changeImage"
                        class="inline-block"
                        accept="image/*"
                    />
                </div>
                <div v-if="form.errors?.image" class="flex-row text-red-600">
                    {{ form.errors?.image }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="img_side" class="w-1/3 inline-block"
                        >Θέση εμφάνισης εικόνας:</label
                    >
                    <select
                        id="img_side"
                        v-model="form.img_side"
                        class="inline-block w-auto"
                    >
                        <option value="0">Στο αριστερό άκρο της σελίδας</option>
                        <option value="1">Στο κέντρο της σελίδας</option>
                        <option value="2">Στο δεξί άκρο της σελίδας</option>
                    </select>
                </div>
                <div v-if="form.errors?.img_side" class="flex-row text-red-600">
                    {{ form.errors?.img_side }}
                </div>

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
                        >Περιθώριο από την κορυφή:
                    </label>
                    <input
                        type="number"
                        v-model="form.img_top_margin"
                        step="0.1"
                        min="0"
                        class="inline-block w-max"
                    />
                </div>
                <div
                    v-if="form.errors?.img_top_margin"
                    class="flex-row text-red-600"
                >
                    {{ form.errors?.img_top_margin }}
                </div>

                <div class="mb-2 flex-row">
                    <label for="img_side_margin" class="w-1/3 inline-block"
                        >Περιθώρια αριστερά και δεξιά του εγγράφου:
                    </label>
                    <input
                        type="number"
                        v-model="form.img_side_margin"
                        step="0.1"
                        min="0"
                        class="inline-block w-max"
                    />
                </div>
                <div
                    v-if="form.errors?.img_side_margin"
                    class="flex-row text-red-600"
                >
                    {{ form.errors?.img_side_margin }}
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
