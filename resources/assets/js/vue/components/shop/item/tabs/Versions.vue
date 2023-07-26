<template>
    <div class="col-1-1 mb-20">
        <div class="header-3" style="margin-bottom: 0">Versions</div>
        <div v-if="versions.length > 0">
            <div
                class="col-1-5 mobile-col-1-3 no-pad item-holder"
                v-for="version in versions.slice(0, 5)"
            >
                <div class="item-card item-border">
                    <img
                        :src="
                            thumbnailStore.getThumbnail({
                                id: version.id,
                                type: ThumbnailType.ItemVersionFull,
                            })
                        "
                    />
                </div>
                <div class="item-details">
                    <span class="gray-text">
                        {{ filterDate(version.created_at) }}
                    </span>
                </div>
            </div>
        </div>
        <div v-else style="margin-top: 10px">Item has no other versions</div>
    </div>
</template>

<style lang="scss">
.item-details {
    margin: 0 10px;
}
</style>

<script setup lang="ts">
import { toRef, watch } from "vue";
import CursorableAPI from "@/logic/apis/CursorableAPI";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import { filterDate } from "@/filters/index";

const props = defineProps<{
    itemId: number;
    allowLoad: boolean;
}>();

const versionsAPI = new CursorableAPI<ItemVersion>(
    `v1/shop/${props.itemId}/versions`,
    5
);
const { currentData: versions } = versionsAPI;

const allowLoad = toRef(props, "allowLoad");

watch(allowLoad, () => versionsAPI.loadPage(1));
</script>
