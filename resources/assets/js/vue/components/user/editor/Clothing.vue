<template>
    <div class="preview-data" id="sort">
        <div
            class="item"
            :data-id="i"
            :key="i"
            v-for="i in clothing.slice().reverse()"
        >
            <img
                :src="
                    thumbnailStore.getThumbnail({
                        id: i,
                        type: ThumbnailType.ItemFull,
                    })
                "
            />

            <div class="extension"></div>
            <div class="drag-elem pointer">
                <SvgSprite
                    class="svg-icon block"
                    square="16"
                    style="margin-bottom: 4px"
                    svg="user/customize/layers_arrow_up.svg"
                />
                <SvgSprite
                    class="svg-icon block"
                    square="16"
                    style="margin-bottom: 4px"
                    svg="user/customize/layers_hamburger.svg"
                />
                <SvgSprite
                    class="svg-icon block"
                    square="16"
                    svg="user/customize/layers_arrow_down.svg"
                />
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.sort-ghost {
    opacity: 0.6;
}

.drag-elem {
    position: absolute;
    right: -23px;
    top: 0;
    background-color: #4f5660;
    border-radius: 2px;
    padding: 4px;
    height: 64px;
    border-left: 1px solid;

    @include themify() {
        border-color: themed("inputs", "blend-border");
    }

    &:not(:hover) {
        display: none;
    }
}

.preview-data {
    .item {
        position: relative;
        height: 64px;
        width: 64px;
        padding: 5px;
        margin: 10px;
        border-radius: 2px;
        background-color: #4f5660;

        .extension {
            height: 100%;
            position: absolute;
            opacity: 0;
            width: 24px;
            top: 0;
            right: -23px;
        }

        img {
            width: 100%;
        }

        &:hover .drag-elem {
            display: inline;
        }
    }

    position: absolute;
    top: 0;
    overflow-y: scroll;
    overflow-x: hidden;
    max-height: 100%;
    width: 100%;

    scrollbar-color: #00000088 transparent;
    scrollbar-width: thin;

    &::-webkit-scrollbar {
        width: 15px;
    }
    &::-webkit-scrollbar-track {
        background-color: transparent;
    }

    &::-webkit-scrollbar-thumb {
        background-color: #00000088;
        border-radius: 10px;
        border: 4px solid transparent;
        background-clip: content-box;
    }
}
</style>

<script setup lang="ts">
import Sortable from "sortablejs";
import { onMounted, watch } from "vue";

import SvgSprite from "@/components/global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

type WearingItem = {
    id: number;
    name: string;
    thumbnail: string;
    type_id: number;
};

defineProps<{
    clothing: number[];
    items: WearingItem[];
}>();

const emit = defineEmits(["reorderClothing"]);
let sort: Sortable;

onMounted(() => {
    sort = Sortable.create(document.getElementById("sort") as HTMLElement, {
        draggable: ".item",
        handle: ".item",
        ghostClass: "sort-ghost",
        animation: 200,
        onUpdate: sortUpdate,
    });
});

function sortUpdate() {
    emit("reorderClothing", sort.toArray().slice().reverse());
}
</script>
