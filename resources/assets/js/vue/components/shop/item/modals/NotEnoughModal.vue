<template>
    <Modal title="You do not have enough" @close="emit('close')">
        <div
            style="text-align: center; padding-top: 25px; padding-bottom: 25px"
        >
            <SvgSprite
                v-if="type == 'bucks'"
                class="svg-icon"
                square="80"
                svg="shop/currency/bucks_full_color.svg"
            />
            <SvgSprite
                v-else
                class="svg-icon"
                square="80"
                svg="shop/currency/bits_full_color.svg"
            />
        </div>
        <div class="modal-buttons">
            <a
                class="button bucks"
                style="margin-right: 10px"
                href="/membership"
            >
                Buy More
            </a>
            <button class="cancel-button modal-close" @click="emit('close')">
                Cancel
            </button>
        </div>
    </Modal>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";

defineProps<{
    type: "free" | "bits" | "bucks";
}>();

const emit = defineEmits(["close"]);
</script>
