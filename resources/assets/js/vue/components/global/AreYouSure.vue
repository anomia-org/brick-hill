<template>
    <div>
        <button
            :class="buttonClass"
            @click="attemptIntercept"
            :disabled="buttonDisabled"
        >
            <!-- use named and default slot to allow for clean code with no modal text and readable code with modal text -->
            <slot></slot>
            <slot name="button"></slot>
        </button>

        <Modal
            :title="modalTitle"
            v-show="modal"
            @close="closeModal()"
            v-if="!intercepted"
        >
            <div v-if="typeof $slots.modal === 'undefined'">
                Are you sure you want to continue?
            </div>
            <slot name="modal"></slot>
            <div class="modal-buttons">
                <button
                    :class="modalButtonClass"
                    class="button"
                    style="margin-right: 10px"
                    @click="submitAccept"
                    type="button"
                >
                    {{ modalButtonText }}
                </button>
                <button
                    class="cancel-button modal-close"
                    @click="closeModal()"
                    type="button"
                >
                    Cancel
                </button>
            </div>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";
import { ref } from "vue";

const props = defineProps({
    buttonDisabled: Boolean,
    intercepted: Boolean,
    buttonClass: {
        type: String,
        default: "green",
    },
    modalTitle: {
        type: String,
        default: "Are you sure?",
    },
    modalButtonClass: {
        type: String,
        default: "green",
    },
    modalButtonText: {
        type: String,
        default: "Yes",
    },
});

const emit = defineEmits(["intercept", "accepted"]);

const modal = ref<boolean>(false);
const accepted = ref<boolean>(false);

function attemptIntercept() {
    if (!props.buttonDisabled) {
        emit("intercept");
        modal.value = true;
    }
}

function submitAccept() {
    closeModal();
    if (!accepted.value) emit("accepted");
    accepted.value = true;
}

function closeModal() {
    modal.value = false;
    accepted.value = false;
}
</script>
