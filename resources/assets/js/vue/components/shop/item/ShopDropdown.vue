<template>
    <div>
        <Modal
            title="Are you sure?"
            v-show="modals.show_modal"
            @close="modals.show_modal = false"
        >
            Are you sure you want to continue?
            <div class="modal-buttons">
                <button
                    class="button green"
                    style="margin-right: 10px"
                    @click="declineItem"
                    type="button"
                >
                    Yes
                </button>
                <button
                    class="cancel-button modal-close"
                    @click="modals.show_modal = false"
                    type="button"
                >
                    Cancel
                </button>
            </div>
        </Modal>

        <Dropdown class="dropdown" activator="edit-dropdown">
            <ul>
                <li v-if="BH.user?.id == creatorId || canUpdateItem">
                    <a
                        v-if="creatorId == BH.main_account_id"
                        :href="`/admin/shop?editId=${itemId}`"
                    >
                        Edit
                    </a>
                    <a v-else :href="`/shop/${itemId}/edit`">Edit</a>
                </li>
                <li v-if="creatorId != BH.main_account_id">
                    <a :href="`/report/item/${itemId}`">Report</a>
                </li>
                <li v-if="permissionStore.canAll('scrub items')">
                    <a @click="renderItem">Refresh Render</a>
                </li>
                <li
                    v-if="
                        canUpdateUser && permissionStore.canAll('scrub items')
                    "
                >
                    <a @click="scrubName">Scrub Name</a>
                </li>
                <li
                    v-if="
                        canUpdateUser && permissionStore.canAll('scrub items')
                    "
                >
                    <a @click="scrubDescription">Scrub Description</a>
                </li>
                <li
                    v-if="
                        canUpdateUser && permissionStore.canAll('scrub items')
                    "
                >
                    <a @click="modals.show_modal = true">Decline</a>
                </li>
            </ul>
        </Dropdown>
    </div>
</template>

<script setup lang="ts">
import { BH } from "@/logic/bh";
import Modal from "@/components/global/Modal.vue";
import Dropdown from "@/components/global/Dropdown.vue";
import { reactive } from "vue";
import { permissionStore } from "@/store/modules/permission";
import axios from "axios";

const props = defineProps<{
    itemId: number;
    creatorId: number;
    canUpdateUser: boolean;
    canUpdateItem: boolean;
}>();

const modals = reactive({
    show_modal: false,
});

function scrubName() {
    axios
        .post(`/shop/${props.itemId}/scrubName`)
        .then((res) => location.reload());
}

function scrubDescription() {
    axios
        .post(`/shop/${props.itemId}/scrubDesc`)
        .then((res) => location.reload());
}

function renderItem() {
    axios.post(`/shop/${props.itemId}/render`).then((res) => location.reload());
}

function declineItem() {
    axios
        .post(`/v1/admin/approve/item/${props.itemId}`, {
            toggle: 0,
        })
        .then((res) => location.reload());
}
</script>
