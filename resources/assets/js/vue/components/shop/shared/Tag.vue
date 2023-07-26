<template>
    <div class="tag ellipsis unselectable">
        <SvgSprite
            v-if="!noRemove && !forDisplay"
            class="svg-icon pointer"
            square="10"
            style="vertical-align: inherit"
            svg="ui/remove.svg"
            @click="$emit('remove')"
        />
        <SvgSprite
            v-if="forDisplay"
            class="svg-icon pointer"
            square="10"
            style="vertical-align: inherit; margin-right: 5px"
            svg="shop/main/tag_dot.svg"
        />
        {{ tag }}
    </div>
</template>

<style lang="scss" scoped>
.tag {
    z-index: 1;
    position: relative;
    display: inline-block;
    color: #fff;
    border-radius: 2px;
    padding: 2px 10px;
    margin-right: 5px;
    margin-top: 5px;
    font-size: 14px;
    max-width: 50%;

    @include themify($themes) {
        background-color: themed("tag-color");
    }
}
</style>

<script setup lang="ts">
import SvgSprite from "@/components/global/SvgSprite.vue";

defineProps<{
    tag: string;
    // should the remove button be displayed
    noRemove?: boolean;
    // is this tag for display only, replaces remove button with separate svg
    forDisplay?: boolean;
}>();

defineEmits(["remove"]);
</script>
