<template>
    <span class="unselectable">
        <span v-if="newTheme" @click="toggleFavorite">
            <SvgSprite
                v-if="favorited"
                class="pointer svg-icon-large"
                square="28"
                svg="sets/favorite_full.svg"
            />
            <SvgSprite
                v-else
                class="pointer svg-icon-large"
                square="28"
                svg="sets/favorite.svg"
            />
            {{ filterNumberCompact(favorites) }}
        </span>
        <span
            v-else
            class="item-favorite hover-cursor favorite-text"
            @click="toggleFavorite"
        >
            <i
                :class="{ fas: favorited, far: !favorited }"
                class="fa-star"
                aria-hidden="true"
            ></i>
            <span style="font-size: 0.9rem">
                {{ filterNumberCompact(favorites) }}
            </span>
        </span>
    </span>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref } from "vue";
import { BH } from "@/logic/bh";
import { filterNumberCompact } from "@/filters/index";
import ModelRelation from "@/logic/data/relations";
import SvgSprite from "../global/SvgSprite.vue";

const props = defineProps<{
    poly_id: number;
    on_load_favorites: string | number;
    on_load_favorited?: boolean;
    type: ModelRelation;
    newTheme?: boolean;
}>();

const favorites = ref<number>(Number(props.on_load_favorites) ?? 0);
const favorited = ref<boolean>(props.on_load_favorited ?? false);

function toggleFavorite() {
    if (!BH.user) return;
    axios
        .post("/favorites/create", {
            favoriteable_id: props.poly_id,
            polymorphic_type: props.type,
            toggle: !favorited.value,
        })
        .then(({ data }) => {
            if (data.success) {
                favorites.value -= favorited.value ? 1 : -1;
                favorited.value = !favorited.value;
            }
        });
}
</script>
