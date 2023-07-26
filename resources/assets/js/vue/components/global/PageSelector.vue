<template>
    <div class="unselectable text-center" style="margin: 10px 0">
        <SvgSprite
            v-show="api.pageNumber.value > 1"
            class="svg-icon-small svg-white pointer"
            square="12"
            svg="ui/arrow_left.svg"
            @click="api.loadPage(api.pageNumber.value - 1)"
        />
        <span style="padding: 0 20px"> Page {{ api.pageNumber.value }} </span>
        <SvgSprite
            v-show="hasNextPage"
            class="svg-icon-small svg-white pointer"
            square="12"
            svg="ui/arrow_right.svg"
            @click="api.loadPage(api.pageNumber.value + 1)"
        />
    </div>
</template>

<script setup lang="ts">
import CursorableAPI from "@/logic/apis/CursorableAPI";
import { ref, watch } from "vue";
import SvgSprite from "./SvgSprite.vue";

const props = defineProps<{
    api: CursorableAPI<any>;
}>();

const hasNextPage = ref<boolean>(false);

// TODO: including api.encParams in the cacheKey in API.ts causes api.hasNextPage() to no longer automatically update
// TODO: im only seeing it here though, nowhere else on the site does it fail to watch
// TODO: vue issue with watching prop? maybe im using vue wrong....
watch(props.api.currentData, () => {
    hasNextPage.value = props.api.hasNextPage();
});
</script>
