<template>
    <div :class="col" class="mobile-col-1-2 no-pad item-holder">
        <a
            :href="`/shop/${item.id}`"
            class="item-card item-border"
            :class="{
                'special-border': isSpecial,
                'event-border': !isSpecial && item.event !== null,
            }"
        >
            <ItemTriangle
                :item="item"
                :is-large="false"
                :owns="false"
            ></ItemTriangle>
            <!-- TODO: implement versions and series -->
            <div v-if="false" class="options">
                <div v-if="item.id % 2 == 0">
                    <SvgSprite
                        class="svg-icon-medium-text svg-white"
                        square="16"
                        svg="shop/main/hat_versions_full.svg"
                    />
                    4
                </div>
                <div v-if="item.id % 3 == 0">
                    <SvgSprite
                        class="svg-icon-medium-text svg-white"
                        square="16"
                        svg="shop/main/hat_series_full.svg"
                    />
                    3
                </div>
            </div>
            <img
                :src="
                    thumbnailStore.getThumbnail({
                        id: item.id,
                        type: ThumbnailType.ItemFull,
                    })
                "
            />
        </a>
        <div class="item-details ellipsis">
            <a :href="`/shop/${item.id}`" class="smedium-text ellipsis">
                {{ item.name }}
            </a>
            <div class="smaller-text light-text ellipsis">
                by
                <a :href="`/user/${item.creator.id}`" style="margin-right: 5px">
                    {{ item.creator.username }}
                </a>
                <SvgSprite
                    v-if="item.creator.is_verified_designer"
                    class="svg-icon svg-white"
                    square="16"
                    svg="shop/main/designer_verified_full.svg"
                />
            </div>
            <ItemPrices :item="item" />
            <div style="min-height: 30px">
                <div class="red-text smaller-text very-bold ellipsis">
                    <span v-if="priceTag == 'sold_out'" style="padding-top: 0">
                        <ItemPrices :item="item" :for-was="true" />
                    </span>
                    <span
                        v-if="priceTag == 'event_item'"
                        style="padding-top: 0"
                    >
                        <span
                            class="price-text free-text ellipsis"
                            style="padding: 0"
                        >
                            EVENT ITEM
                        </span>
                    </span>
                    <span v-if="item.timer && priceTag !== 'sold_out'">
                        <SvgSprite
                            class="svg-icon svg-icon-margin"
                            square="14"
                            svg="shop/main/timer.svg"
                        />
                        <Countdown
                            :countdown-to="item.timer_date"
                            :short-form="true"
                            :add-left="true"
                            @finished="timerFinished"
                        />
                    </span>
                    <span
                        v-else-if="
                            !(typeof item.stock_left === 'undefined') &&
                            item.stock_left > 0
                        "
                    >
                        {{ item.stock_left }} of {{ item.stock }} remaining
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.item-card {
    img {
        margin-bottom: 0;
    }

    &.special-border,
    &.event-border {
        padding: 7px;
    }

    .options {
        position: absolute;
        bottom: 4px;
        left: 3px;
    }
}
.item-details {
    height: 102px;
    margin: 0 10px;
}
</style>

<script setup lang="ts">
import { computed } from "vue";
import Countdown from "@/components/global/Countdown.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import ItemTriangle from "../shared/ItemTriangle.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import ItemPrices from "./ItemPrices.vue";

const props = defineProps<{
    item: Item;
    col: "col-1-4" | "col-1-5";
}>();

const isSpecial = computed<boolean>(() => {
    return props.item.special || props.item.special_edition;
});

type PriceTag = "sold_out" | "event_item" | "free" | "offsale" | "currency";

const priceTag = computed<PriceTag>(() => {
    if (isSpecial.value && !props.item.stock_left) return "sold_out";
    if (props.item.event !== null && !props.item.offsale) return "event_item";
    if (props.item.bucks === 0 || props.item.bits === 0) return "free";
    if (props.item.offsale) return "offsale";

    return "currency";
});

// when the countdown finishes it should make the item offsale
function timerFinished() {
    props.item.offsale = true;
    props.item.timer = false;
}
</script>
