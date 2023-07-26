<template>
    <div>
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
        <div>
            <ItemCard v-for="item in items" :item="item" col="col-1-4" />
        </div>
    </div>
</template>

<style lang="scss" scoped>
.new-theme .tab-body .optional-types {
    padding: 10px 0;

    label {
        margin-right: 35px;
    }
}
</style>

<script setup lang="ts">
import InfiniteScrollAPI from "@/logic/apis/InfiniteScrollAPI";
import { hasInfiniteScroll } from "@/logic/infinite_scroll";
import { onMounted, ref, toRef, watch } from "vue";
import ItemCard from "./ItemCard.vue";

const props = defineProps<{
    categories: { value: string; name: string }[];
    additionalParams: { key: string; value: any }[];
    selected: boolean;
}>();

const selectedCategories = ref<string[]>([]);

const shopListAPI = new InfiniteScrollAPI<Item>(`v2/shop/list`, 20);
const { currentData: items } = shopListAPI;

const allowLoad = toRef(props, "selected");
const additionalParams = toRef(props, "additionalParams");

let clearScroll: () => void;

onMounted(() => {
    ({ clearScroll } = hasInfiniteScroll(() => {
        if (allowLoad.value) shopListAPI.loadNextPage();
    }));
});

watch(
    [allowLoad, selectedCategories, additionalParams],
    () => {
        if (!allowLoad.value) return;

        let params = [];
        for (let type of selectedCategories.value) {
            params.push({ key: "types[]", value: type });
        }
        if (selectedCategories.value.length == 0) {
            params = props.categories.reduce(
                (acc: any[], v) =>
                    acc.concat({ key: "types[]", value: v.value }),
                []
            );
        }
        for (let param of additionalParams.value) {
            params.push(param);
        }
        if (clearScroll) clearScroll();
        shopListAPI.setParams(params);
    },
    { immediate: true }
);
</script>
