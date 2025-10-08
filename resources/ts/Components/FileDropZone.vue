<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from "vue";
import { humanReadableFileSize } from "@/tools";
import axios, { type AxiosResponse } from "axios";

type FormDataObjectValueType = string | Blob | [Blob, string];

type FormDataObjectType = Record<string, FormDataObjectValueType>;

const props = withDefaults(
    defineProps<{
        url?: string;
        name?: string;
        autoupload?: boolean;
        multiple?: boolean;
        appendFormData?: FormDataObjectType;
    }>(),
    {
        url: "",
        name: "file",
        autoupload: false,
        multiple: true,
    }
);

const emit = defineEmits<{
    uploaded: [response: AxiosResponse];
    error: [error: unknown];
}>();

const onDragEnter = (event: DragEvent) => {
    if (!event.dataTransfer) {
        return;
    }

    if (!props.multiple && event.dataTransfer.items.length > 1) {
        event.dataTransfer.dropEffect = "none";
        invalidDrop.value = true;
    } else {
        dragged.value = true;
        invalidDrop.value = false;
    }
};

const onDragOver = (event: DragEvent) => {
    if (!event.dataTransfer) {
        return;
    }

    if (!props.multiple && event.dataTransfer.items.length > 1) {
        event.dataTransfer.dropEffect = "none";
    } else {
        event.dataTransfer.dropEffect = "copy";
    }
};

const onDrop = (event: DragEvent) => {
    dragged.value = false;
    invalidDrop.value = false;

    if (!event.dataTransfer) {
        return;
    }

    if (!props.multiple && event.dataTransfer.files.length > 1) {
        return;
    }

    for (const file of event.dataTransfer.files) {
        files.value.push(file);
    }

    if (props.autoupload) {
        upload();
    }
};

const onDragLeave = () => {
    dragged.value = false;
    invalidDrop.value = false;
};

const onClick = (event: Event) => {
    let input = document.createElement("input");
    input.type = "file";
    input.multiple = props.multiple;

    input.onchange = (e) => {
        if (
            e &&
            e.target !== null &&
            (e.target as HTMLInputElement).files !== null
        ) {
            const inputFiles = (e.target as HTMLInputElement).files;
            if (inputFiles) {
                for (const file of inputFiles) {
                    files.value.push(file);
                }
            }

            if (props.autoupload) {
                upload();
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
        formData.append(props.name + "[]", file);
    });
    if (typeof props.appendFormData !== "undefined") {
        Object.keys(props.appendFormData).forEach((key) => {
            // @ts-expect-error props.appendFormData is not undefined
            if (Array.isArray(props.appendFormData[key])) {
                formData.append(
                    key,
                    // @ts-expect-error props.appendFormData is not undefined
                    props.appendFormData[key][0],
                    // @ts-expect-error props.appendFormData is not undefined
                    props.appendFormData[key][1]
                );
            } else {
                // @ts-expect-error props.appendFormData is not undefined
                formData.append(key, props.appendFormData[key]);
            }
        });
    }

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
            // Πήραμε απάντηση 2XX (όλα πήγαν καλά)
            uploadButtonDisabled.value = false;
            uploadButtonRef.value!.innerHTML = "Ανέβασμα αρχείων";
            files.value = [];
            emit("uploaded", response);
        })
        .catch((error: unknown) => {
            emit("error", error);
        });
};

const dragged = ref(false);
const invalidDrop = ref(false);

const files: Ref<File[]> = ref([]);

const uploadButtonRef: Ref<HTMLButtonElement | undefined> = ref();

const uploadButtonDisabled: Ref<boolean> = ref(false);
</script>

<template>
    <div
        class="cursor-pointer m-3 p-2 w-full h-64 bg-indigo-500 flex flex-col items-center justify-center rounded border-2 border-indigo-700 shadow-2xl"
        :class="{ 'bg-indigo-100': dragged }"
        @drop.prevent="onDrop"
        @dragenter.prevent="onDragEnter"
        @dragover.prevent="onDragOver"
        @dragend="onDragLeave"
        @dragleave="onDragLeave"
        @click="onClick"
    >
        <span
            class="text-white text-center pointer-events-none"
            v-if="!dragged && !invalidDrop"
        >
            Σύρετε τα αρχεία εδώ μέσα ή πατήστε για να εμφανιστεί το παράθυρο
            διαλόγου
        </span>
        <span
            class="text-black text-center pointer-events-none"
            v-if="dragged && !invalidDrop"
        >
            Αφήστε τα αρχεία εδώ μέσα για να προστεθούν στην ομάδα
        </span>
        <span
            class="text-red-700 text-center pointer-events-none font-bold"
            v-if="invalidDrop"
        >
            ⛔ Μόνο ένα αρχείο επιτρέπεται
        </span>
    </div>
    <div v-if="!autoupload && files.length" class="flex flex-col w-full mt-2">
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
