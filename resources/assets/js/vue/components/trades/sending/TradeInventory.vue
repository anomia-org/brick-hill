<template>
    <div>
        <div class="search-bar flex flex-wrap space-between mb-8">
            <input
                v-model="search"
                @input="searchChanged"
                class="input rigid mobile-col-1-1 blend"
                style="width: 65%"
                type="text"
                placeholder="Search crate"
                autocomplete="off"
            />
            <select
                class="blend mobile-col-1-1"
                style="width: 33%"
                v-model="sortBy"
            >
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
            </select>
        </div>
        <div class="flex mb-20">
            <div style="margin-right: 15px" v-for="category in categories">
                <input
                    type="checkbox"
                    v-model="selectedCategories"
                    :value="category.value"
                    :id="category.value + (isSender ? '1' : '')"
                    class="small-margin"
                />
                <label :for="category.value + (isSender ? '1' : '')">{{
                    category.name
                }}</label>
            </div>
        </div>
        <div style="margin-top: 10px">
            <div class="flex trade-item-holder no-center">
                <div
                    class="override-trade-col inline no-border ellipsis mobile-col-1-4 col-1-4"
                    v-for="crate in inventoryItems"
                    :key="crate.id"
                    :class="{
                        selected: sendingTradeStore.isSelected(user, crate),
                    }"
                    @click="sendingTradeStore.toggleItem({ user, crate })"
                >
                    <trade-card :crate="crate" :green-links="true"></trade-card>
                </div>
            </div>
            <PageSelector :api="crateAPI" />
        </div>
    </div>
</template>

<script setup lang="ts">
import TradeCard from "../TradeCard.vue";
import { onMounted, ref, watchEffect } from "vue";
import { sendingTradeStore } from "@/store/modules/trades/sending";
import CursorableAPI from "@/logic/apis/CursorableAPI";
import PageSelector from "@/components/global/PageSelector.vue";
import createDebounce from "@/logic/debounce";

const debounce = createDebounce();

const props = defineProps<{
    user: string;
    isSender: boolean;
}>();

const crateAPI = new CursorableAPI<Crate>(`v1/user/${props.user}/crate`, 12);
const { currentData: inventoryItems } = crateAPI;

const search = ref<string>("");
const debouncedSearch = ref<string>("");
const selectedCategories = ref<string[]>([]);

const sortBy = ref<string>("newest");

const categories = [
    { name: "Hats", value: "hat" },
    { name: "Tools", value: "tool" },
    { name: "Faces", value: "face" },
    { name: "Heads", value: "head" },
];

function searchChanged() {
    debounce(() => {
        debouncedSearch.value = search.value;
    }, 250);
}

watchEffect(() => {
    let params = [];
    for (let type of selectedCategories.value) {
        params.push({ key: "types[]", value: type });
    }
    if (selectedCategories.value.length == 0) {
        params = categories.reduce(
            (acc: any[], v) => acc.concat({ key: "types[]", value: v.value }),
            []
        );
    }

    params.push({ key: "types[]", value: "special" });
    params.push({ key: "search", value: debouncedSearch.value });
    params.push({ key: "sort", value: sortBy.value });

    crateAPI.setParams(params);
});
</script>
