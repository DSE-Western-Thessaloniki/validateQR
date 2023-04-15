<script setup lang="ts">
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faSquare as faSquareFull,
    faPencil,
} from "@fortawesome/free-solid-svg-icons";
import { faSquare } from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faSquare, faSquareFull, faPencil);
const props = defineProps<{
    step: number | string;
    totalSteps: number | string;
    colors?: Array<string>;
}>();

const step = Number(props.step);
const totalSteps = Number(props.totalSteps);

let iconColor = { color: "black" };

if (props.colors !== undefined) {
    const partitionSize = 100 / props.colors.length;
    const currentPercent = (step / totalSteps) * 100;
    let colorIndex = Math.floor(currentPercent / partitionSize);
    colorIndex =
        colorIndex > props.colors.length - 1
            ? props.colors.length - 1
            : colorIndex;
    iconColor = { color: props.colors[colorIndex] };
}
</script>

<template>
    <div class="flex flex-row">
        <div v-for="n in step" class="px-0.5">
            <font-awesome-icon
                :icon="faSquareFull"
                size="2xs"
                :style="iconColor"
            />
        </div>
        <div v-for="n in totalSteps - step" class="px-0.5">
            <font-awesome-icon :icon="faSquare" size="2xs" :style="iconColor" />
        </div>
    </div>
</template>
