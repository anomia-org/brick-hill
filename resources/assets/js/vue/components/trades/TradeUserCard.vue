<template>
    <div class="trade-user-card flex flex-column text-center">
        <a :href="`/user/${id}`">
            <img
                style="width: 100%"
                :src="
                    thumbnailStore.getThumbnail({
                        id: id,
                        type: ThumbnailType.AvatarFull,
                    })
                "
            />

            <div class="bold small-margin" style="margin-bottom: 0">
                {{ username }}
            </div>
        </a>

        <div class="flex flex-horiz-center">
            <div class="flex">
                <div class="svg-container small-h-margin-r">
                    <SVGSprite
                        class="svg-black"
                        square="20"
                        svg="user/trade/value/value.svg"
                    />
                </div>
                <p>{{ value?.value ?? 0 }}</p>
            </div>

            <div class="svg-container ml-5" v-if="value">
                <SVGSprite
                    square="12"
                    svg="user/trade/value/arrow_value_up.svg"
                    v-if="value?.direction == 1"
                />
                <SVGSprite
                    square="12"
                    svg="user/trade/value/arrow_value_down.svg"
                    v-if="value?.direction == -1"
                />
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.left-auto {
    margin-left: auto;
}
</style>

<script setup lang="ts">
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import SVGSprite from "@/components/global/SvgSprite.vue";

defineProps<{
    id: number;
    username: string;
    value: {
        value: number;
        direction: number;
    } | null;
}>();
</script>
