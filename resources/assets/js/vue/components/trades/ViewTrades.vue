<template>
    <div class="new-theme">
        <div class="col-1-3">
            <div class="header">Trades</div>
            <trade-list
                @newTrade="selectNewTrade"
                @typeChanged="selectedTrade = {}"
                :user="user"
            ></trade-list>
        </div>
        <div class="col-2-3" ref="tradeRef">
            <div
                v-if="typeof selectedTrade.trade === 'undefined'"
                class="center-text"
            >
                No trade selected
            </div>
            <div v-else>
                <div v-if="outside_viewer" class="medium-text">
                    Status: {{ tradeStatus(selectedTrade) }}
                </div>
                <div>
                    <div class="small-margin medium-text bold">
                        Items you will give
                    </div>
                    <div class="flex view-trades-flex">
                        <TradeItems :trade="giving"></TradeItems>
                        <TradeUserCard
                            :id="giving.user.id"
                            :username="giving.user.username"
                            :value="giving.user_value"
                        ></TradeUserCard>
                    </div>
                </div>
                <div class="divider small-margin"></div>
                <div>
                    <div class="small-margin medium-text bold">
                        Items you will receive
                    </div>
                    <div class="flex view-trades-flex">
                        <TradeItems
                            :trade="receiving"
                            :taxed="true"
                        ></TradeItems>
                        <TradeUserCard
                            :id="receiving.user.id"
                            :username="receiving.user.username"
                            :value="receiving.user_value"
                        ></TradeUserCard>
                    </div>
                </div>
                <div v-if="selectedTrade.is_pending">
                    <TradeFormButtons
                        :receiver_id="receiving.user.id"
                        :is_sender="!received"
                        :trade_id="selectedTrade.id"
                    ></TradeFormButtons>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TradeList from "./TradeList.vue";
import TradeItems from "./TradeItems.vue";
import TradeFormButtons from "./TradeFormButtons.vue";
import TradeUserCard from "./TradeUserCard.vue";
import { computed, ref } from "vue";

const props = defineProps<{
    user: string;
}>();

const selectedTrade = ref<any>({});
const tradeRef = ref<HTMLDivElement>();

function selectNewTrade(trade: any) {
    let notFirstSelection = false;
    if (Object.keys(selectedTrade.value).length !== 0) notFirstSelection = true;
    selectedTrade.value = trade;
    // mobile design changes at 768 width
    if (
        (notFirstSelection || outside_viewer.value) &&
        window.screen.width < 768
    )
        setTimeout(() => {
            tradeRef.value?.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }, 50);
}

function tradeStatus(trade: any) {
    if (trade.is_pending) return "Pending";
    if (trade.is_accepted) return "Accepted";
    if (trade.has_errored) return "Errored";
    if (trade.is_cancelled) return "Cancelled";
    return "Declined";
}

const giving = computed(() => {
    for (let i of selectedTrade.value.trade) {
        if (i.user.id == props.user) return i;
    }
    return selectedTrade.value.trade[1];
});

const receiving = computed(() => {
    for (let i of selectedTrade.value.trade) {
        if (i.user.id != props.user) return i;
    }
    return selectedTrade.value.trade[0];
});

const outside_viewer = computed(() => {
    return (
        selectedTrade.value.trade[0].user.id != props.user &&
        selectedTrade.value.trade[1].user.id != props.user
    );
});

const received = computed(() => {
    return selectedTrade.value.trade[0].user.id != props.user;
});
</script>
