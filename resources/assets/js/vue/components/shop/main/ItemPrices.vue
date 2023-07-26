<template>
    <div :class="{ 'for-was': forWas }">
        <div
            v-if="forWas"
            class="price-text light-text inline ellipsis"
            style="padding-top: 0; margin-right: 7px"
        >
            WAS
        </div>
        <div
            v-if="priceTag == 'free'"
            class="price-text free-text inline ellipsis"
        >
            FREE
        </div>
        <div
            v-if="priceTag == 'currency'"
            class="currency"
            style="height: 33px"
        >
            <div
                v-if="item.bucks !== null"
                class="price-text bucks-text inline ellipsis"
            >
                <SvgSprite
                    class="svg-icon"
                    square="16"
                    svg="shop/currency/bucks_full_color.svg"
                />
                {{ item.bucks }}
            </div>
            <div
                v-if="item.bits !== null"
                class="price-text bits-text inline ellipsis"
            >
                <SvgSprite
                    class="svg-icon"
                    square="16"
                    svg="shop/currency/bits_full_color.svg"
                />
                {{ item.bits }}
            </div>
        </div>
        <div
            v-if="priceTag == 'sold_out'"
            class="currency"
            style="height: 33px"
        >
            <div v-if="item.cheapest_seller !== null">
                <div
                    class="price-text bucks-text inline ellipsis"
                    style="max-width: 40%"
                >
                    <SvgSprite
                        class="svg-icon"
                        square="16"
                        svg="shop/currency/bucks_full_color.svg"
                    />
                    {{ filterNumberCompact(item.cheapest_seller?.bucks ?? 0) }}
                </div>
                <a
                    class="price-text light-text small-text inline ellipsis"
                    style="
                        padding-top: 0;
                        font-weight: 500;
                        margin-right: 0;
                        max-width: 60%;
                    "
                    :href="`/user/${item.cheapest_seller?.user.id}`"
                >
                    from <b>{{ item.cheapest_seller?.user.username }}</b>
                </a>
            </div>
            <div v-else>
                <div
                    class="price-text light-text inline ellipsis"
                    style="max-width: 100%"
                >
                    NO SELLERS
                </div>
            </div>
        </div>
        <div
            v-if="priceTag == 'event_item'"
            class="price-text free-text ellipsis"
            :class="{
                inline: forWas,
            }"
        >
            EVENT ITEM
        </div>
        <div
            v-if="priceTag == 'offsale'"
            class="price-text light-text ellipsis"
            :class="{
                inline: forWas,
            }"
        >
            OFFSALE
        </div>
    </div>
</template>

<style lang="scss">
div.for-was {
    .currency {
        display: inline;
    }

    .price-text {
        padding-top: 0;
    }
}

.price-text {
    font-weight: 600;
    padding: 7px 0;
    margin-right: 10px;
}

.currency .price-text {
    max-width: calc(50% - 10px);
}
</style>

<script setup lang="ts">
import { filterNumberCompact } from "@/filters";
import { computed } from "vue";
import SvgSprite from "@/components/global/SvgSprite.vue";

type PriceTag = "sold_out" | "event_item" | "free" | "offsale" | "currency";

const props = defineProps<{
    item: Item;
    forWas?: boolean;
}>();

const isSpecial = computed<boolean>(() => {
    return props.item.special || props.item.special_edition;
});

const priceTag = computed<PriceTag>(() => {
    if (isSpecial.value && !props.item.stock_left && !props.forWas)
        return "sold_out";
    if (props.item.event !== null && props.item.offsale) return "event_item";
    if (props.item.bucks === 0 || props.item.bits === 0) return "free";
    if (props.item.offsale) return "offsale";

    return "currency";
});
</script>
