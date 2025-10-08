<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from "vue";
import { humanReadableFileSize } from "@/tools";
import axios from "axios";
import { isLaravelValidationError } from "@/laravel-validation-error";
import { useWizardStore } from "@/Stores/wizard";

const wizard = useWizardStore();

const props = withDefaults(
    defineProps<{
        url: string;
    }>(),
    {
        url: "",
    }
);

const emit = defineEmits(["uploaded"]);

const onDrop = (event: DragEvent) => {
    dragged.value = false;

    if (!event.dataTransfer) {
        return;
    }

    for (const file of event.dataTransfer.files) {
        files.value.push(file);
    }
};

const onClick = (event: Event) => {
    let input = document.createElement("input");
    input.type = "file";
    input.multiple = true;

    input.onchange = (e) => {
        if (
            e &&
            e.target !== null &&
            (e.target as HTMLInputElement).files !== null
        ) {
            for (const file of (e.target as HTMLInputElement).files) {
                files.value.push(file);
            }
        }
    };

    input.click();
};

const removeFile = (index: number) => {
    files.value.splice(index, 1);
};

const upload = () => {
    if (props.url === "") {
        console.log("FileDropZone: Path is empty. Upload aborted.");
        return;
    }

    const formData = new FormData();
    files.value.forEach((file) => {
        formData.append("documents[]", file);
    });
    formData.append("document_group_id", `${wizard?.documentGroup?.id}`);

    uploadButtonDisabled.value = true;

    axios
        .post(props.url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
            onUploadProgress(progressEvent) {
                uploadButtonRef.value!.innerHTML = `${Math.round(
                    progressEvent.percentage ?? 0
                )}% ETA: ${
                    progressEvent.estimated?.toLocaleString() ?? "άγνωστο"
                }`;
            },
        })
        .then((response) => {
            // Έλεγχος για απάντηση 210 (όταν υπήρχαν ήδη αρχεία)
            if (response.status === 210) {
                uploadButtonDisabled.value = false;
                uploadButtonRef.value!.innerHTML = "Ανέβασμα αρχείων";
                files.value = [];
                emit("uploaded");
                wizard.confirmationModal.show = true;
                wizard.confirmationModal.title = "Αρχεία ήδη ανεβασμένα";
                wizard.confirmationModal.content = `<p>Τα αρχεία με τα ονόματα:</p><br/><ul class="list-disc"><li>${response.data.existing.join(
                    "</li><li>"
                )}</li></ul><br/><p>είχαν ήδη ανέβει στην ομάδα εγγράφων και αγνοήθηκαν. Τα υπόλοιπα αρχεία έχουν ανέβει επιτυχώς.</p>`;
                return;
            }

            // Έλεγχος για απάντηση 211 (όταν υπογεγραμμένα έγγραφα δεν ταίριαξαν με κάποιο έγγραφο της ομάδας)
            if (response.status === 211) {
                uploadButtonDisabled.value = false;
                uploadButtonRef.value!.innerHTML = "Ανέβασμα αρχείων";
                files.value = [];
                emit("uploaded");
                wizard.confirmationModal.show = true;
                wizard.confirmationModal.title =
                    "Αρχεία που δεν ταίριαξαν με κάποιο από τα έγγραφα της ομάδας";
                wizard.confirmationModal.content = `<p>Τα αρχεία με τα ονόματα:</p><br/><ul class="list-disc"><li>${response.data.not_matching.join(
                    "</li><li>"
                )}</li></ul><br/><p>δεν ταίριαξαν με κάποια από τα έγγραφα της ομάδας και αγνοήθηκαν. Τα υπόλοιπα αρχεία έχουν ανέβει επιτυχώς.</p>`;
                console.log(response.data);
                return;
            }

            // Έλεγχος για απάντηση 200 (όταν όλα πήγαν καλά)
            if (response.status === 200) {
                uploadButtonDisabled.value = false;
                uploadButtonRef.value!.innerHTML = "Ανέβασμα αρχείων";
                files.value = [];
                emit("uploaded");
            }
        })
        .catch((error: unknown) => {
            let errors: Array<String> = [];

            if (isLaravelValidationError(error)) {
                wizard.validationErrors = error.response.data.errors;
                errors.push(error.response.data.message);
            } else if (error instanceof Error) {
                errors.push(error.message);
            } else {
                errors.push("Γενικό σφάλμα αποθήκευσης!");
            }
        });
};

const dragged = ref(false);

const files: Ref<File[]> = ref([]);

const uploadButtonRef: Ref<HTMLButtonElement | undefined> = ref();

const uploadButtonDisabled: Ref<boolean> = ref(false);
</script>

<template>
    <div
        class="cursor-pointer m-3 p-2 w-full h-64 bg-indigo-500 flex flex-col items-center justify-center rounded border-2 border-indigo-700 shadow-2xl"
        :class="{ 'bg-indigo-100': dragged }"
        @drop.prevent="onDrop"
        @dragenter.prevent="dragged = true"
        @dragover.prevent
        @dragend="dragged = false"
        @dragleave="dragged = false"
        @click="onClick"
    >
        <span
            class="text-white text-center pointer-events-none"
            v-if="!dragged"
        >
            Σύρετε τα αρχεία εδώ μέσα ή πατήστε για να εμφανιστεί το παράθυρο
            διαλόγου
        </span>
        <span class="text-black text-center pointer-events-none" v-if="dragged">
            Αφήστε τα αρχεία εδώ μέσα για να προστεθούν στην ομάδα
        </span>
    </div>
    <div v-if="files.length" class="flex flex-col w-full mt-2">
        <div class="h-48 overflow-y-auto">
            <div
                v-for="(file, index) in files"
                class="w-full rounded shadow-md bg-gray-200 p-3 my-1 flex flex-row items-center"
            >
                <div class="flex-grow flex flex-row items-center">
                    <div class="text-ellipsis">
                        {{ index + 1 }}. {{ file.name }}
                    </div>
                    <div class="text-gray-600 text-sm pl-2">
                        ({{ humanReadableFileSize(file.size) }})
                    </div>
                </div>
                <div
                    class="px-3 py-2 hover:bg-red-200 hover:shadow-lg hover:duration-700 hover:ease-in-out cursor-pointer"
                    @click="removeFile(index)"
                >
                    X
                </div>
            </div>
        </div>
        <button
            type="button"
            class="rounded p-2"
            :class="uploadButtonDisabled ? 'bg-gray-400' : 'bg-green-400 '"
            @click="upload"
            ref="uploadButtonRef"
            :disabled="uploadButtonDisabled"
        >
            Ανέβασμα αρχείων
        </button>
    </div>
</template>
