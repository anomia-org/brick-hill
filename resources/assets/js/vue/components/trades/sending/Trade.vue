<template>
    <div class="new-theme">
        <div class="col-1-2">
            <Tabs class="trade-items">
                <p class="medium-text bold" style="text-align: left">Sending</p>
                <TradeTab :user="sender"></TradeTab>
            </Tabs>
            <Tabs class="trade-items" style="padding-top: 10px">
                <p class="medium-text bold" style="text-align: left">
                    Receiving
                </p>
                <TradeTab :user="receiver"></TradeTab>
            </Tabs>
        </div>
        <div class="col-1-2" style="padding-right: 0">
            <Tabs :new-theme="true">
                <Tab name="Your Crate">
                    <TradeInventory
                        :user="sender"
                        :is-sender="true"
                    ></TradeInventory>
                </Tab>
                <Tab name="Their Crate">
                    <TradeInventory
                        :user="receiver"
                        :is-sender="false"
                    ></TradeInventory>
                </Tab>
            </Tabs>
        </div>
        <div
            v-if="typeof tradeState.trade_items[receiver] !== 'undefined'"
            class="col-1-1 flex flex-column"
            style="text-align: center"
        >
            <div class="flex" v-if="queryParams.counter">
                <div class="svg-container small-h-margin-r">
                    <SvgSprite
                        class="svg-icon"
                        square="20"
                        svg="user/trade/info.svg"
                    />
                </div>
                <p>This will replace the trade you are countering</p>
            </div>
            <div class="flex">
                <AreYouSure
                    :buttonClass="`${
                        tradeState.trade_items[sender].length > 0 ? 'blue' : ''
                    } ${
                        tradeState.trade_items[sender].length == 0
                            ? 'no-click'
                            : ''
                    }`"
                    :buttonDisabled="tradeState.trade_items[sender].length == 0"
                    modalButtonText="Send Trade"
                    modalButtonClass="blue"
                    @accepted="sendTrade"
                >
                    <template v-slot:button v-if="!queryParams.counter">
                        Send Trade
                    </template>
                    <template v-slot:button v-else> Send Counter </template>
                    <template v-slot:modal>
                        Are you sure you want to send this trade?
                        <span
                            v-if="tradeState.trade_items[receiver].length == 0"
                            >You will receive <b>no items</b> in return for this
                            trade.</span
                        >
                    </template>
                </AreYouSure>
                <a class="button clear" style="margin-left: 10px" href="/trades"
                    >Cancel</a
                >
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TradeTab from "./TradeTab.vue";
import TradeInventory from "./TradeInventory.vue";
import AreYouSure from "../../global/AreYouSure.vue";
import Tab from "../../global/tabs/Tab.vue";
import Tabs from "../../global/tabs/Tabs.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";

import { axiosSendErrorToNotification } from "@/logic/notifications";
import { notificationStore } from "@/store/modules/notifications";

import axios from "axios";
import { sendingTradeStore } from "@/store/modules/trades/sending";

import { BH } from "@/logic/bh";

const props = defineProps<{
    sender: string;
    receiver: string;
    counter?: number;
}>();

const queryParams: any = new Proxy(
    new URLSearchParams(window.location.search),
    {
        get: (searchParams, prop: string) => searchParams.get(prop),
    }
);

function loadTradeInfo(tradeId: number) {
    axios
        .get(BH.apiUrl(`v1/user/trades/${tradeId}`))
        .then(({ data }) => {
            let trade = data.data.trade;

            trade.forEach((info: any) => {
                let user = info.user.id.toString();
                sendingTradeStore.updateBucks({ user, bucks: info.bucks });

                info.items.forEach((crate: any) => {
                    sendingTradeStore.toggleItem({ user, crate });
                });
            });
        })
        .catch(() => {
            window.location.href = "/trades";
        });
}

if (queryParams.counter) loadTradeInfo(queryParams.counter);

sendingTradeStore.setUsers([props.receiver, props.sender]);

const tradeState = sendingTradeStore.getState();

function sendTrade() {
    window.scrollTo(0, 0);

    axios
        .post("", {
            asking_bucks: tradeState.trade_bucks[props.receiver],
            giving_bucks: tradeState.trade_bucks[props.sender],
            asking_items: tradeState.trade_items[props.receiver].map(
                (a: any) => a.id
            ),
            giving_items: tradeState.trade_items[props.sender].map(
                (a: any) => a.id
            ),
            receiver: Number(props.receiver),
        })
        .then(({ data }) => {
            if (typeof data.error !== "undefined") {
                notificationStore.setNotification(
                    data.error.prettyMessage,
                    "error"
                );
            } else {
                window.location.href = "/trades";
            }
        })
        .catch(axiosSendErrorToNotification);
}
</script>
