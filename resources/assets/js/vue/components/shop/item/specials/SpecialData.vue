<template>
    <div v-if="data">
        <div class="col-1-3 mobile-col-1-2 ellipsis">
            <div class="special-data-box">
                <SvgSprite
                    class="svg-icon-medium-text svg-white"
                    square="35"
                    svg="shop/special/you_own.svg"
                />
            </div>
            <div class="special-data-data">
                <div class="small-text bold light-text ellipsis">YOU OWN</div>
                <div v-if="BH.user" class="smedium-text ellipsis">
                    {{ filterNumber(data.you_own) }}
                </div>
                <div v-else>Not logged in</div>
            </div>
        </div>
        <div class="col-1-3 mobile-col-1-2 ellipsis">
            <div class="special-data-box">
                <SvgSprite
                    class="svg-icon-medium-text svg-white"
                    square="35"
                    svg="shop/special/active_copies.svg"
                />
            </div>
            <div class="special-data-data">
                <div
                    class="small-text bold light-text ellipsis"
                    title="Amount of copies where the owner has been online in the past 3 months"
                >
                    ACTIVE COPIES
                </div>
                <div class="smedium-text ellipsis">
                    {{ filterNumber(data.active_copies) }}
                </div>
            </div>
        </div>
        <div class="col-1-3 mobile-col-1-2 ellipsis">
            <div class="special-data-box">
                <SvgSprite
                    class="svg-icon-medium-text svg-white"
                    square="35"
                    svg="shop/special/unique_owners.svg"
                />
            </div>
            <div class="special-data-data">
                <div class="small-text bold light-text ellipsis">
                    UNIQUE OWNERS
                </div>
                <div class="smedium-text ellipsis">
                    {{ filterNumber(data.unique_owners) }}
                </div>
            </div>
        </div>
        <div class="col-1-3 mobile-col-1-2 ellipsis">
            <div class="special-data-box">
                <SvgSprite
                    class="svg-icon-medium-text svg-white"
                    square="35"
                    svg="shop/special/avg_daily_views.svg"
                />
            </div>
            <div class="special-data-data">
                <div class="small-text bold light-text ellipsis">
                    AVG DAILY VIEWS
                </div>
                <div class="smedium-text ellipsis">
                    {{ filterNumber(data.daily_views) }}
                </div>
            </div>
        </div>
        <div class="col-1-3 mobile-col-1-2 ellipsis">
            <div class="special-data-box">
                <SvgSprite
                    class="svg-icon-medium-text svg-white"
                    square="35"
                    svg="shop/special/recent_avg_price.svg"
                />
            </div>
            <div class="special-data-data">
                <div class="small-text bold light-text ellipsis">
                    RECENT AVG PRICE
                </div>
                <div class="smedium-text ellipsis">
                    {{ filterNumber(data.avg_price) }}
                </div>
            </div>
        </div>
        <div class="col-1-3 mobile-col-1-2 ellipsis">
            <div class="special-data-box">
                <SvgSprite
                    class="svg-icon-medium-text svg-white"
                    square="35"
                    svg="shop/special/volume_hoarded.svg"
                />
            </div>
            <div class="special-data-data">
                <div
                    class="small-text bold light-text ellipsis"
                    title="Amount of copies where the owner owns more than one of the item"
                >
                    VOLUME HOARDED
                </div>
                <div class="smedium-text ellipsis">
                    {{ filterNumber(data.volume_hoarded) }}
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.special-data-box {
    height: 40px;
    width: 40px;
    position: absolute;
    //background-color: #fff;

    svg {
        margin: 2.5px;
    }
}
.special-data-data {
    position: relative;
    right: -45px;
    margin-right: 45px;
    top: 0;
    margin-bottom: 20px;
}
</style>

<script setup lang="ts">
import { filterNumber } from "@/filters";
import { BH } from "@/logic/bh";
import SvgSprite from "@/components/global/SvgSprite.vue";

defineProps<{
    data: SpecialData | undefined;
}>();
</script>
