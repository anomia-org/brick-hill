<template>
    <input
        class="width-100 thin blend"
        v-model="search"
        @input="debounceSearch"
        placeholder="Search"
    />
    <div class="optional-types">
        <span v-for="category in categories">
            <input
                type="checkbox"
                v-model="selectedCategories"
                :value="category.value"
                :id="category.value"
            />
            <label :for="category.value">{{ category.name }}</label>
        </span>
    </div>
    <div class="text-center">
        <div v-if="inventoryItems.length == 0">No items</div>
        <div
            v-for="inventory in inventoryItems"
            :key="inventory.id"
            class="item-card pointer"
            style="position: relative"
            :class="{ active: wearing.includes(inventory.item.id) }"
            @click="
                emit('toggleItem', inventory.item.id, inventory.item.type_id)
            "
        >
            <img
                :src="
                    thumbnailStore.getThumbnail({
                        id: inventory.item.id,
                        type: ThumbnailType.ItemFull,
                    })
                "
            />
            <!--
            <div
                @click.stop
                style="position: absolute; right: 5px; top: 5px"
                @click="openVersions(inventory)"
                :id="`dd-${inventory.id}`"
            >
                <SvgSprite
                    class="svg-icon svg-white"
                    square="24"
                    svg="shop/main/hat_versions.svg"
                />
            </div>

            <Dropdown :activator="`dd-${inventory.id}`">
                <div
                    style="width: 256px; padding: 3px 3px 8px"
                    class="dropdown-data new-theme"
                >
                    <div
                        class="col-1-2 mobile-col-1-3 no-pad item-holder"
                        v-for="version in inventoryItems.slice(0, 4)"
                    >
                        <div
                            class="item-card versions item-border"
                            style="
                                width: calc(100% - 20px);
                                height: 100%;
                                margin-bottom: 3px;
                            "
                        >
                            <img
                                :src="
                                    thumbnailStore.getThumbnail({
                                        id: version.id,
                                        type: ThumbnailType.ItemVersionFull,
                                    })
                                "
                            />
                        </div>
                        <div class="item-details" style="margin: 0 10px">
                            <div class="small-text ellipsis">
                                {{ version.item.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </Dropdown>
            -->

            <div class="item-title small-text ellipsis">
                <a
                    @click.stop
                    target="_blank"
                    :href="`/shop/${inventory.item.id}`"
                >
                    {{ inventory.item.name }}
                </a>
            </div>
        </div>
        <PageSelector :api="crateAPI" />
    </div>
</template>

<style lang="scss" scoped>
.item-card {
    height: 128px;
    width: 128px;
}
.new-theme .col-2-3 .optional-types {
    padding: 10px 0;

    label {
        margin-right: 35px;
    }
}
.versions {
    &.item-card {
        @include themify() {
            background-color: themed("body");
        }
    }
}
</style>

<script setup lang="ts">
import createDebounce from "@/logic/debounce";
import CursorableAPI from "@/logic/apis/CursorableAPI";
import { BH } from "@/logic/bh";
import { ref, toRef, watchEffect } from "vue";
import PageSelector from "@/components/global/PageSelector.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import SvgSprite from "@/components/global/SvgSprite.vue";
import Dropdown from "@/components/global/Dropdown.vue";

const debounce = createDebounce();

const crateAPI = new CursorableAPI<Crate>(`v1/user/${BH.user?.id}/crate`);
const { currentData: inventoryItems } = crateAPI;

const props = defineProps<{
    categories: { value: string; name: string }[];
    wearing: number[];
    allowLoad: boolean;
}>();

const emit = defineEmits(["toggleItem"]);

defineExpose({
    crateAPI,
});

const selectedCategories = ref<string[]>([]);
const search = ref<string>("");
const debouncedSearch = ref<string>("");

function debounceSearch() {
    debounce(() => {
        debouncedSearch.value = search.value;
    }, 300);
}

function openVersions(inventory: Crate) {
    console.log("opened", inventory);
}

const allowLoad = toRef(props, "allowLoad");

watchEffect(() => {
    if (!allowLoad.value) return;

    let params = [];
    for (let type of selectedCategories.value) {
        params.push({ key: "types[]", value: type });
    }
    if (selectedCategories.value.length == 0) {
        params = props.categories.reduce(
            (acc: any[], v) => acc.concat({ key: "types[]", value: v.value }),
            []
        );
    }
    params.push({ key: "search", value: debouncedSearch.value });
    crateAPI.setParams(params);
});
</script>
