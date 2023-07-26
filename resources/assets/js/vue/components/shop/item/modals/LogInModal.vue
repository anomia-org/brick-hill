<template>
    <Modal title="You are not logged in" @close="emit('close')">
        You must login to purchase an item
        <div class="modal-buttons">
            <a class="button bucks" style="margin-right: 10px" href="/login">
                Login
            </a>
            <button class="cancel-button modal-close" @click="emit('close')">
                Cancel
            </button>
        </div>
    </Modal>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";

const emit = defineEmits(["close"]);
</script>
