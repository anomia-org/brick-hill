<template>
    <div class="no-pad item-holder">
        <div
            :href="`/shop/${crate.item.id}`"
            class="item-card item-card-trade item-border block"
            :class="{
                'first-serial': crate.serial == 1,
                'second-serial': crate.serial == 2,
                'third-serial': crate.serial == 3,
            }"
        >
            <p class="trade-serial bold">#{{ crate.serial }}</p>
            <img
                draggable="false"
                :src="
                    thumbnailStore.getThumbnail({
                        id: crate.item.id,
                        type: ThumbnailType.ItemFull,
                    })
                "
            />
        </div>
        <div class="item-details ellipsis flex flex-wrap">
            <a
                :href="`/shop/${crate.item.id}`"
                target="_blank"
                class="smedium-text ellipsis"
                @click.stop
            >
                {{ crate.item.name }}
            </a>
            <div class="currency flex" style="height: 33px">
                <div
                    class="bold inline"
                    :class="{
                        'bucks-text': greenLinks,
                        'price-text': !greenLinks,
                    }"
                >
                    <SVGSprite
                        class="svg-icon"
                        :class="{ 'svg-black': !greenLinks }"
                        square="16"
                        :svg="
                            greenLinks
                                ? 'shop/currency/bucks_full_color.svg'
                                : 'shop/currency/bucks_full.svg'
                        "
                    />
                    {{ crate.item.average_price_abbr || 0 }}
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.item-card-trade {
    margin: 0;
    margin-bottom: 10px;

    img {
        margin-bottom: 0;
        height: 100%;
        width: 100%;
    }
}
.item-details {
    height: 60px;

    .price-text {
        font-weight: 600;
        padding: 7px 0;
        margin-right: 10px;
    }

    .currency .price-text {
        max-width: calc(50% - 10px);
    }
}

.item-holder {
    width: 100%;
    padding: 0 !important;
    margin-bottom: 10px;
    cursor: pointer;
}

a.disabled {
    pointer-events: none;
}
</style>

<script setup lang="ts">
import ThumbnailType from "@/logic/data/thumbnails";
import { thumbnailStore } from "@/store/modules/thumbnails";
import SVGSprite from "@/components/global/SvgSprite.vue";

defineProps<{
    crate: any;
    greenLinks?: boolean;
}>();
</script>
