<template>
    <span class="unselectable" @click="toggleWishlist">
        <div>
            <SvgSprite
                v-if="wishlisted"
                class="pointer svg-icon-medium-text svg-white"
                square="20"
                svg="shop/main/hat_wishlist_full.svg"
            />
            <SvgSprite
                v-else
                class="pointer svg-icon-medium-text svg-white"
                square="20"
                svg="shop/main/hat_wishlist.svg"
            />
        </div>
        WISHLIST
    </span>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref } from "vue";
import { BH } from "@/logic/bh";
import ModelRelation from "@/logic/data/relations";
import SvgSprite from "../global/SvgSprite.vue";

const props = defineProps<{
    poly_id: number;
    onLoadWishlisted?: boolean;
    type: ModelRelation;
}>();

const wishlisted = ref<boolean>(props.onLoadWishlisted ?? false);

function toggleWishlist() {
    if (!BH.user) return;
    axios
        .post("/wishlists/create", {
            wishlistable_id: props.poly_id,
            polymorphic_type: props.type,
            toggle: !wishlisted.value,
        })
        .then(({ data }) => {
            if (data.success) {
                wishlisted.value = !wishlisted.value;
            }
        });
}
</script>
