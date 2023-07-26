<template>
    <div>
        <div
            class="tile-holder tile-transition"
            style="margin-bottom: 0"
            :style="{
                height: `${
                    205 *
                        (Math.round(
                            tradeState.trade_items[user].length / 4 - 0.1
                        ) +
                            1) +
                    120
                }px`,
            }"
        >
            <div class="flex trade-item-holder no-center">
                <div
                    class="mobile-col-1-2 col-1-4 sending-tile small-tile item-card-tile item-border"
                    v-for="n in (Math.round(
                        tradeState.trade_items[user].length / 4 - 0.1
                    ) +
                        1) *
                    4"
                    :key="n"
                    :class="{
                        'no-border':
                            typeof tradeState.trade_items[user][n - 1] !==
                            'undefined',
                        'item-card':
                            typeof tradeState.trade_items[user][n - 1] ==
                            'undefined',
                    }"
                    :style="{
                        'margin-right': n % 4 !== 0 ? '15px' : '1px',
                        height: '120px',
                        'margin-bottom':
                            tradeState.trade_items[user].length > 0
                                ? '80px'
                                : '10px',
                        'margin-left': '0',
                    }"
                    @click="
                        sendingTradeStore.toggleItem({
                            user,
                            crate: tradeState.trade_items[user][n - 1],
                        })
                    "
                >
                    <trade-card
                        v-if="
                            typeof tradeState.trade_items[user][n - 1] !==
                            'undefined'
                        "
                        :crate="tradeState.trade_items[user][n - 1]"
                        :enable-links="true"
                    ></trade-card>
                </div>
            </div>

            <div class="trade-bucks" style="">
                <span class="block flex flex-column">
                    <div class="flex-items-center flex">
                        <p class="small-text bold">You send</p>
                        <SVGSprite
                            class="svg-icon"
                            style="margin: 0 10px; margin-left: 20px"
                            square="25"
                            svg="shop/currency/bucks_full_color.svg"
                        />
                        <input
                            :value="tradeState.trade_bucks[user]"
                            @input="updateBucksFromElement"
                            class="blend bold"
                            style="
                                vertical-align: top;
                                max-width: 90px;
                                margin-right: 20px;
                            "
                            type="number"
                            min="0"
                        />

                        <p class="small-text bold">After Tax</p>
                        <SVGSprite
                            class="svg-icon svg-black"
                            style="margin: 0 10px; margin-left: 20px"
                            square="25"
                            svg="shop/currency/bucks_full.svg"
                        />
                        <input
                            :value="taxed_bucks"
                            @input="reverseTaxBucksFromElement"
                            class="blend bold"
                            style="
                                vertical-align: top;
                                max-width: 90px;
                                margin-right: 20px;
                                padding-right: 0;
                            "
                            type="number"
                            min="0"
                        />
                    </div>
                </span>

                <span class="block flex flex-items-center">
                    <p class="smedium-text very-bold">TOTAL OFFER</p>
                    <SVGSprite
                        class="svg-icon"
                        style="margin: 0 10px; margin-left: 20px"
                        square="25"
                        svg="shop/currency/bucks_full_color.svg"
                    />
                    <p class="medium-text bucks-text very-bold">
                        {{
                            (
                                tradeState.trade_items[user].reduce(
                                    (a: any, b: any) => {
                                        return a + b.item.average_price;
                                    },
                                    0
                                ) + taxed_bucks
                            ).toLocaleString()
                        }}
                    </p>
                </span>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.sending-tile {
    padding: 0 !important;
}
</style>

<script setup lang="ts">
import { sendingTradeStore } from "@/store/modules/trades/sending";
import SVGSprite from "@/components/global/SvgSprite.vue";
import TradeCard from "../TradeCard.vue";
import { computed, ref } from "@vue/reactivity";
import { BH } from "@/logic/bh";
import { watch } from "vue";

const props = defineProps<{
    user: string;
}>();

const tradeState = sendingTradeStore.getState();

const taxed_bucks = ref<number>(0);

function updateBucksFromElement(e: Event) {
    if (!(e.target instanceof HTMLInputElement)) return;
    updateBucks(e.target.value);
}

function reverseTaxBucksFromElement(e: Event) {
    if (!BH.user) return;
    if (!(e.target instanceof HTMLInputElement)) return;

    let val = Number(e.target.value);
    taxed_bucks.value = val;
    sendingTradeStore.updateBucks({
        user: props.user,
        bucks: Math.round(val / BH.user.taxRate),
    });
}

function updateBucks(amount: string) {
    if (!BH.user) return;

    let val = Number(amount);
    if (val < 0) val = 0;
    val = Math.round(val);

    taxed_bucks.value = Math.round(val * BH.user.taxRate);
    sendingTradeStore.updateBucks({ user: props.user, bucks: val });
}

const initUpdate = watch(
    () => tradeState.trade_bucks[props.user],
    (val) => {
        updateBucks(val);
        initUpdate();
    }
);
</script>
