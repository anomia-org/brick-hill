<template>
    <div
        @click="eventClicked"
        @dragstart.prevent
        class="mover unselectable pointer"
        :style="{ top: `${top}px`, left: `${left}px` }"
        v-if="!destructed"
    >
        <div
            style="position: relative"
            :style="`transform:rotate(${Math.random() * 20 - 10}deg)`"
        >
            <SvgSprite
                :svg="`events/${mappings[Number(event_key[0]) - 1]}.svg`"
                square="75"
            />
        </div>
    </div>
</template>

<style scoped>
.mover {
    position: absolute;
    z-index: 10000;
}
</style>

<script setup lang="ts">
import axios from "axios";
import { onMounted, ref } from "vue";
import SvgSprite from "../global/SvgSprite.vue";

const props = defineProps<{
    event_key: string;
    event_type: any;
}>();

const eventItem = 354505;

const mappings = ["blue", "green", "red", "yellow"];

const top = ref<number>(0);
const left = ref<number>(0);
const initialLeft = ref<number>(0);
const initialTop = ref<number>(0);

const wWidth = document.documentElement.clientWidth;
const wHeight = document.documentElement.clientHeight;

const destructed = ref<boolean>(false);

onMounted(() => {
    top.value = Math.floor(Math.random() * (wHeight - 250 - 175) + 175);
    left.value = Math.floor(Math.random() * (wWidth - 250 - 175) + 175);
    initialLeft.value = left.value;
    initialTop.value = top.value;
    //animate();
});

function animate() {
    if (destructed.value) {
        return;
    }

    left.value += wWidth / 315;
    top.value =
        initialTop.value + Math.sin(((2 * Math.PI) / 750) * left.value) * 125;

    if (left.value > wWidth - 100) selfDestruct();

    setTimeout(animate, 20);
}

function selfDestruct() {
    destructed.value = true;
}

function eventClicked() {
    axios
        .post(`/events/ornaments2022`, {
            event_key: props.event_key,
        })
        .then(({ data }) => {
            selfDestruct();
            if (data.granted) window.location.href = `/shop/${eventItem}/`;
        });
}
</script>
