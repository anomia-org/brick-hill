<template>
    <div class="new-theme">
        <div class="col-5-12">
            <div class="large-text bold" style="margin-bottom: 20px">
                REDEEM PROMO CODE
            </div>
            <div style="margin-bottom: 5px">Enter code here:</div>
            <div>
                <input v-model="promoCode" @keypress.enter="redeemPromo" />
                <button class="blue" @click="redeemPromo">REDEEM</button>
            </div>
            <div class="smaller-text lower-text">
                <SvgSprite
                    v-if="
                        !redeemStatus.error && redeemStatus.message.length > 0
                    "
                    class="svg-icon-text"
                    square="16"
                    svg="notifications/success.svg"
                />
                <SvgSprite
                    v-if="redeemStatus.error && redeemStatus.message.length > 0"
                    class="svg-icon-text"
                    square="16"
                    svg="notifications/error.svg"
                />
                {{ redeemStatus.message }}
            </div>
            <div style="padding-bottom: 50px">
                Promo codes can be obtained through official Brick Hill
                promotions or through events hosted by us. As well as this,
                promocodes may be included in products or merchandise produced
                by us.
                <br /><br />
                All available items that are a part of current promotions can be
                seen below.
            </div>
        </div>

        <div class="col-1-1">
            <div class="large-text bold" style="margin-bottom: 20px">
                AVAILABLE ITEMS
            </div>
            <div class="carousel">
                <div v-for="i in 5" :key="i" class="col-1-5 mobile-col-1-2">
                    <div
                        v-if="typeof promoItems[i - 1] === 'undefined'"
                        class="item"
                    >
                        <img />
                    </div>
                    <div v-else class="item filled">
                        <a :href="`/shop/${promoItems[i - 1].item.id}`">
                            <img
                                :src="
                                    thumbnailStore.getThumbnail({
                                        id: promoItems[i - 1].item.id,
                                        type: ThumbnailType.ItemFull,
                                    })
                                "
                            />
                        </a>
                        <div
                            v-if="promoItems[i - 1].coming_soon"
                            class="soon-text"
                        >
                            Coming Soon
                        </div>
                        <!--
                            coming soon should have priority over leaving soon
                         -->
                        <div
                            v-if="
                                promoItems[i - 1].leaving_soon &&
                                !promoItems[i - 1].coming_soon
                            "
                            class="soon-text"
                        >
                            Leaving Soon
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@media handheld, only screen and (min-width: 900px) {
    body {
        /*background-image: url('@/../../images/events/promocodes-placeholder.png');*/
        background-repeat: no-repeat;
        background-position: bottom;
        background-size: cover;
        background-position-y: 100px;
    }
}
</style>

<style scoped lang="scss">
.carousel .col-1-5 {
    padding-left: 10px;
    padding-right: 10px;
}
.soon-text {
    width: 100%;
    position: absolute;
    bottom: 0;
    left: 0;
    padding: 5px;
    text-align: center;
    background-color: rgb(64 63 63 / 48%);
    color: #fff;
}
.item {
    position: relative;
    padding: 10px;
    border-radius: 2px;
    transition: border-color 150ms;

    @include themify($themes) {
        background-color: themed("events", "promocodes", "item-bg");
    }

    &.filled {
        border: 1px solid #fff;

        @include themify($themes) {
            background: themed("events", "promocodes", "item-gradient");
        }
    }
}
.item:hover {
    border-color: #00a9fe;
}
.item:not(.filled) img {
    height: 0;
    padding-bottom: 100%;
}
.item img {
    width: 100%;
}
.lower-text {
    height: 15px;
    margin: 5px;
    padding-bottom: 50px;
}
</style>

<script setup lang="ts">
import axios from "axios";
import { BH } from "@/logic/bh";
import { reactive, ref } from "vue";
import SvgSprite from "../global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const promoItems = ref<any>([]);
const promoCode = ref<string>("");
const redeemStatus = reactive({
    error: false,
    message: "",
});

axios.get(BH.apiUrl(`v1/events/activePromos`)).then(({ data }) => {
    promoItems.value = data.data;
});

function redeemPromo() {
    axios
        .post(BH.apiUrl(`v1/events/redeemPromo`), {
            code: promoCode.value.replaceAll("-", "").replaceAll(" ", ""),
        })
        .then(({ data }) => {
            if (data.success) {
                redeemStatus.error = false;
                redeemStatus.message = "Item added to your inventory!";
            }
        })
        .catch((err) => {
            redeemStatus.error = true;
            if (err.response.status == 404) {
                redeemStatus.message = "Invalid code entered.";
            } else {
                redeemStatus.message = err.response.data.error.prettyMessage;
            }
        });
}
</script>
