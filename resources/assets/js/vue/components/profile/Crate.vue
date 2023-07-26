<template>
    <div>
        <div class="col-2-12">
            <ul class="crate-types">
                <li
                    v-for="(index, type) in types"
                    :key="index"
                    :class="{ active: index == selectedType }"
                    @click="selectedType = index"
                >
                    {{ type }}
                </li>
            </ul>
        </div>
        <div>
            <ul class="col-10-12" style="text-align: center; padding-right: 0">
                <li
                    class="col-1-5 mobile-col-1-2"
                    style="padding-left: 3px; padding-right: 3px"
                    v-for="crate in inventoryItems"
                    :key="crate.id"
                >
                    <a :href="`/shop/${crate.item.id}/`">
                        <div class="profile-card crate">
                            <span
                                v-if="crate.item.is_special"
                                class="trade-serial"
                            >
                                #{{ crate.serial }}
                            </span>
                            <img
                                :src="
                                    thumbnailStore.getThumbnail({
                                        id: crate.item.id,
                                        type: ThumbnailType.ItemFull,
                                    })
                                "
                            />
                            <div
                                class="ellipsis"
                                style="color: #767676; height: 19px"
                            >
                                {{ crate.item.name }}
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="col-1-1 center-text" style="margin-bottom: 5px">
                <button
                    class="blue small"
                    style="margin-right: 5px"
                    :disabled="pageNumber == 1"
                    @click="crateAPI.loadPage(pageNumber - 1)"
                >
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span>{{ pageNumber }}</span>
                <button
                    class="blue small"
                    style="margin-left: 5px"
                    :disabled="!crateAPI.hasNextPage()"
                    @click="crateAPI.loadPage(pageNumber + 1)"
                >
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import CursorableAPI from "@/logic/apis/CursorableAPI";
import ThumbnailType from "@/logic/data/thumbnails";
import { thumbnailStore } from "@/store/modules/thumbnails";
import { ref, watch } from "vue";

const props = defineProps<{
    user: string;
}>();

const crateAPI = new CursorableAPI<any>(`v1/user/${props.user}/crate`);
const { currentData: inventoryItems, pageNumber } = crateAPI;

const selectedType = ref<string>("all");

const types = {
    All: "all",
    Hats: "hat",
    Tools: "tool",
    Faces: "face",
    Heads: "head",
    "T-Shirts": "tshirt",
    Shirts: "shirt",
    Pants: "pants",
    Specials: "special",
};

watch(
    selectedType,
    (val) => {
        crateAPI.setParams([{ key: "type", value: val }]);
    },
    { immediate: true }
);
</script>
