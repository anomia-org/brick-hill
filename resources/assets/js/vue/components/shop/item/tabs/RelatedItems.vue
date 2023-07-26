<template>
    <div>
        <div
            v-if="related && related.outfits?.length > 0"
            class="col-1-1 mobile-col-1-1 mb-20 no-pad"
        >
            <div class="header-3">Outfits</div>
            <div class="col-1-1 mobile-col-1-1">
                <ItemCard
                    v-for="item in related.outfits"
                    :item="item"
                    col="col-1-5"
                ></ItemCard>
            </div>
        </div>

        <div
            v-if="related && related.recommended?.length > 0"
            class="col-1-1 mobile-col-1-1 mb-20 no-pad"
        >
            <div class="header-3">Recommended</div>
            <div class="col-1-1 mobile-col-1-1">
                <ItemCard
                    v-for="item in related.recommended"
                    :item="item"
                    col="col-1-5"
                ></ItemCard>
            </div>
        </div>
    </div>
</template>

<style lang="scss"></style>

<script setup lang="ts">
import axios from "axios";
import { ref, toRef, watch } from "vue";
import { BH } from "@/logic/bh";
import ItemCard from "../../main/ItemCard.vue";

const props = defineProps<{
    itemId: number;
    allowLoad: boolean;
}>();

const related = ref<{
    recommended: Item[];
    outfits: Item[];
}>();

function loadRelated() {
    axios.get(BH.apiUrl(`v1/shop/${props.itemId}/related`)).then(({ data }) => {
        related.value = data.data;
    });
}

const allowLoad = toRef(props, "allowLoad");

watch(allowLoad, () => loadRelated());
</script>
