<template>
    <div ref="modal" class="modal">
        <div class="modal-content" style="display: block">
            <span class="close" @click="close">&times;</span>
            {{ title }}
            <hr />
            <slot></slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeMount, ref } from "vue";

defineProps<{
    title: string;
}>();

const emit = defineEmits(["close"]);

const down = ref<boolean>(false);
const modal = ref(null);

function outsideDownClick(event: Event) {
    let e = event as MouseEvent;
    let path = e.composedPath();
    if (typeof path === "undefined") path = [e.target as EventTarget];
    if (path[0] === modal.value) {
        down.value = true;
    } else if (down.value) {
        down.value = false;
    }
}

function outsideUpClick(event: Event) {
    let e = event as MouseEvent;
    let path = e.composedPath();
    if (typeof path === "undefined") path = [e.target as EventTarget];
    if (path[0] === modal.value && down.value) {
        close();
        down.value = false;
    }
}

function close() {
    emit("close");
}

onBeforeMount(() => {
    document.body.addEventListener("mousedown", outsideDownClick);
    document.body.addEventListener("mouseup", outsideUpClick);
});
</script>
