<template>
    <svg
        :width="width || square"
        :height="height || square"
        :style="{
            height: `${height || square}px`,
            width: `${width || square}px`,
        }"
    >
        <use
            xmlns:xlink="http://www.w3.org/1999/xlink"
            :href="href"
            :xlink-href="href"
        ></use>
    </svg>
</template>

<script setup lang="ts">
import { BH } from "@/logic/bh";
import axios from "axios";
import { computed } from "vue";

const props = defineProps<{
    square?: string;
    width?: string;
    height?: string;
    svg: string;
}>();

const href = computed<string>(() => {
    let dirName = props.svg.substring(0, props.svg.indexOf("/"));
    let fileName = props.svg
        .substring(props.svg.lastIndexOf("/") + 1)
        .replace(".svg", "");
    if (typeof BH.sprite_sheets[dirName] === "undefined") {
        throw new Error(
            `Invalid spritesheet generated for SVG ${dirName}: ${fileName}: ${props.svg}`
        );
    }
    if (typeof BH.loaded_sprite_sheets[dirName] === "undefined") {
        BH.loaded_sprite_sheets[dirName] = true;
        axios
            .get(BH.sprite_sheets[dirName], { withCredentials: false })
            .then(({ data }) => {
                let div = document.createElement("div");
                div.style.display = "none";
                div.innerHTML = data;
                document.body.insertBefore(div, document.body.childNodes[0]);
            });
    }
    return `#${fileName}`;
});
</script>
