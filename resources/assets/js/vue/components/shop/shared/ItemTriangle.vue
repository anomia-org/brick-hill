<template>
    <div
        class="triangle"
        v-if="
            item.special || item.special_edition || item.event !== null || owns
        "
        :class="{
            large: isLarge,
            special: item.special,
            event:
                item.event !== null && !(item.special || item.special_edition),
            speciale: item.special_edition,
            owns: owns && !(item.special || item.special_edition),
        }"
    >
        <div v-if="owns">
            <SvgSprite
                v-if="item.special_edition"
                class="svg-icon"
                :square="svgSize"
                svg="shop/main/item_own_special_e.svg"
            />
            <SvgSprite
                v-else
                class="svg-icon"
                :square="svgSize"
                svg="shop/main/item_own.svg"
            />
        </div>
        <div v-else-if="item.special_edition || item.special">
            <SvgSprite
                v-if="item.special_edition"
                class="svg-icon"
                :square="svgSize"
                svg="shop/main/item_special_e.svg"
            />
            <SvgSprite
                v-else
                class="svg-icon"
                :square="svgSize"
                svg="shop/main/item_special.svg"
            />
        </div>
        <div v-else>
            <SvgSprite
                class="svg-icon"
                :square="svgSize"
                style="filter: brightness(0) invert(1)"
                svg="shop/main/event.svg"
            />
        </div>
    </div>
</template>

<style lang="scss">
.triangle {
    svg {
        height: 18px;
        position: absolute;
        top: 4px;
        right: 5px;
    }

    &.large svg {
        height: 25px;
        position: absolute;
        top: 10px;
        right: 10px;
    }

    &::before,
    &::after {
        content: "";
        position: absolute;
        top: -1px;
        right: -1px;
        z-index: -1;
        border-color: transparent;
        border-style: solid;
    }

    &::before {
        border-width: 30px;
    }

    &.large::before {
        border-width: 47px;
    }

    &::after {
        border-width: 25px;
    }

    &.large::after {
        border-width: 40px;
    }

    &.speciale::before {
        border-right-color: #ff0000;
        border-top-color: #ff0000;
    }

    &.special::after,
    &.speciale::after {
        border-right-color: #ffd52d;
        border-top-color: #ffd52d;
    }

    &.owns::after {
        border-right-color: #4cb04c;
        border-top-color: #4cb04c;
    }

    &.event::after {
        @include themify() {
            border-right-color: themed("blue");
            border-top-color: themed("blue");
        }
    }
}
</style>

<script setup lang="ts">
import SvgSprite from "@/components/global/SvgSprite.vue";
import { computed } from "@vue/reactivity";

const props = defineProps<{
    item: any;
    owns: boolean;
    isLarge: boolean;
}>();

const svgSize = computed<string>(() => {
    return props.isLarge ? "25" : "16";
});
</script>
