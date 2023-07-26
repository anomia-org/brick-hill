<template>
    <div style="position: relative; margin-top: 5px; padding-bottom: 10px">
        <!-- 
            firefox doesnt like z-indexes on the thumb so create another between-range to simulate the normal input background 
            so the input can be above everything but the actual between-range can be seen
        -->
        <div class="between-range white"></div>
        <div
            class="between-range"
            :style="{
                transform: `translate(${translate}%, 0px) scale(${scale}, 1)`,
            }"
        ></div>
        <input
            type="range"
            :min="minValue"
            :max="maxValue"
            :value="minInput ?? minValue"
            @input="
                emit(
                    'update:minInput',
                    Number(($event.target as HTMLInputElement).value)
                )
            "
        />
        <input
            type="range"
            :min="minValue"
            :max="maxValue"
            :value="maxInput ?? maxValue"
            @input="
                emit(
                    'update:maxInput',
                    Number(($event.target as HTMLInputElement).value)
                )
            "
        />
    </div>
</template>

<style lang="scss" scoped>
@mixin slider-thumb {
    appearance: none;
    -webkit-appearance: none;
    box-sizing: content-box;
    z-index: 3;
    position: relative;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid;
    cursor: pointer;

    @include themify() {
        background-color: themed("body");
        border-color: themed("blue");
    }
}
.between-range {
    position: absolute;
    z-index: 2;
    top: 2px;
    left: 2px;
    height: 2px;
    width: 100%;
    transform-origin: 0 0;

    @include themify() {
        background: themed("blue");
    }

    &.white {
        @include themify() {
            background: themed("inputs", "blend-border");
        }
    }
}
input[type="range"] {
    background: transparent;
    -webkit-appearance: none;
    position: absolute;
    width: 100%;
    top: 0;
    padding: 0;
    height: 2px;
    border: 0;
    outline: none;

    // using a comma between these causes them both to not work?
    // shouldnt it select both of them?
    &::-webkit-slider-thumb {
        @include slider-thumb;
    }

    &::-moz-range-thumb {
        @include slider-thumb;
    }
}

// firefox doesnt play nicely with the input overlays
// the slider thumbs dont listen to the z-index value so i have to make the actual input higher
// this causes the issue where if you click in the middle of the thumb of the minimum, it will be below the input of the maximum
// this in turn causes you to select the maximum input even though you are on the thumb of the minimum
// this is only an issue on firefox though so only apply the z-index fix there to not cause the issue on chrome
// they can just cope with not being able to click on the middle of the minimum thumb
@supports (-moz-appearance: none) {
    input[type="range"] {
        z-index: 2;
    }
}
</style>

<script setup lang="ts">
import { computed, ref, watch } from "vue";

const props = defineProps<{
    min: number;
    max: number;
    minInput?: number | string; // deleting everything from an input leaves an empty string, just update it later to be undefined
    maxInput?: number | string;
}>();

const emit = defineEmits(["update:minInput", "update:maxInput"]);

const minValue = ref<number>(props.min);
const maxValue = ref<number>(props.max);

watch(
    () => props.minInput,
    (newMin) => {
        if (typeof newMin !== "number") {
            emit("update:minInput", undefined);
            return;
        }
        if (newMin < minValue.value) {
            newMin = minValue.value;
        }
        if (newMin > (props.maxInput ?? maxValue)) {
            emit("update:maxInput", newMin);
        }
        emit("update:minInput", newMin);
    }
);

watch(
    () => props.maxInput,
    (newMax) => {
        if (typeof newMax !== "number") {
            emit("update:maxInput", undefined);
            return;
        }
        // if they manually input a higher max, use that number
        if (newMax > maxValue.value) {
            maxValue.value = newMax;
        }
        if (newMax < (props.minInput ?? minValue)) {
            emit("update:minInput", newMax);
        }
        emit("update:maxInput", newMax);
    }
);

const scale = computed(() => {
    let maxInput = Number(props.maxInput ?? maxValue.value);
    let minInput = Number(props.minInput ?? minValue.value);
    return (maxInput - minInput) / (maxValue.value - minValue.value);
});

const translate = computed(() => {
    let minInput = Number(props.minInput ?? minValue.value);
    return (
        ((minInput - minValue.value) / (maxValue.value - minValue.value)) * 100
    );
});
</script>
