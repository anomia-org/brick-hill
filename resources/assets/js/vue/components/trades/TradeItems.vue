<template>
    <div class="trade-items-view">
        <div class="smedium-text small-margin" v-if="trade.items.length == 0">
            Nothing
        </div>
        <div>
            <div class="flex trade-item-holder no-center">
                <div
                    class="trade-card no-border ellipsis mobile-col-1-2 col-1-4 keep-col-padding mobile-pad"
                    v-for="crate in trade.items"
                    :key="crate.id"
                >
                    <a :href="`/shop/${crate.item.id}`" target="_blank">
                        <trade-card :crate="crate"></trade-card>
                    </a>
                </div>

                <div
                    v-if="trade.bucks > 0"
                    class="flex flex-items-center"
                    style="margin-bottom: 80px"
                >
                    <span class="flex flex-items-center">
                        <span class="large-text">+</span>
                        <SVGSprite
                            class="svg-icon"
                            square="25"
                            style="margin: 10px"
                            svg="shop/currency/bucks_full_color.svg"
                        />
                        <span class="smedium-text bucks-text">{{
                            final_bucks
                        }}</span>
                    </span>
                </div>
            </div>
        </div>
        <div v-if="trade.items.length > 0">
            <span class="flex flex-items-center mobile-flex-horiz-center">
                <p class="smedium-text bold">TOTAL OFFER</p>
                <SVGSprite
                    class="svg-icon"
                    style="margin: 0 10px; margin-left: 20px"
                    square="25"
                    svg="shop/currency/bucks_full_color.svg"
                />
                <p class="medium-text bucks-text very-bold">
                    {{
                        trade.items
                            .reduce(
                                (a: any, { item }: any) =>
                                    a + (item.average_price || 0),
                                final_bucks
                            )
                            .toLocaleString()
                    }}
                </p>
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { BH } from "@/logic/bh";
import TradeCard from "./TradeCard.vue";
import SVGSprite from "@/components/global/SvgSprite.vue";

const props = defineProps<{
    trade: any;
    taxed?: boolean;
}>();

const final_bucks = computed(() => {
    if (!BH.user) return;
    return Math.round(props.trade.bucks * (props.taxed ? BH.user.taxRate : 1));
});
</script>
