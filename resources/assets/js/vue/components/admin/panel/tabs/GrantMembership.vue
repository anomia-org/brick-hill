<template>
    <div>
        <div class="card">
            <div class="top green">Grant membership</div>
            <div class="content">
                Grant a user membership. If they already have membership you can
                modify the amount of time they have left.
                <br />
                Membership expiration is only calculated at midnight UTC daily
                but before bucks are granted.
                <br />
                A 1 minute membership would allow for them to use features like
                creating sets for the rest of the day but not get the daily
                bucks.
                <br /><br />
                <div class="block">
                    <input
                        type="number"
                        required
                        v-model.number="grantUser"
                        placeholder="User ID"
                        min="0"
                        style="margin-bottom: 10px; width: 150px"
                    />
                    <input
                        type="number"
                        required
                        v-model.number="membershipMinutes"
                        placeholder="Minutes of membership"
                        min="1"
                        max="2147483647"
                        style="margin-bottom: 10px; width: 200px"
                    />
                    <select v-model="membershipType">
                        <option value="3">Ace</option>
                        <option value="4">Royal</option>
                    </select>
                </div>
                <button
                    type="submit"
                    @click="grantMembership(false)"
                    class="green"
                >
                    Grant
                </button>
            </div>
        </div>
        <modal
            title="Are you sure?"
            v-show="modifyModal"
            @close="modifyModal = false"
        >
            This user already has membership. Are you sure you want to change
            it?
            <br /><br />
            <div class="modal-buttons">
                <button
                    class="green"
                    style="margin-right: 10px"
                    @click="grantMembership(true)"
                >
                    I am sure
                </button>
                <button
                    type="button"
                    class="cancel-button modal-close"
                    @click="modifyModal = false"
                >
                    Cancel
                </button>
            </div>
        </modal>
    </div>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { ref } from "vue";

const grantUser = ref<number>();
const membershipMinutes = ref<number>();
const membershipType = ref<string>("3");
const modifyModal = ref<boolean>(false);
async function grantMembership(modify_membership: boolean) {
    await axios
        .post(`/v1/admin/grant/membership/${grantUser.value}`, {
            membership_minutes: membershipMinutes.value,
            membership_type: membershipType.value,
            modify_membership: modify_membership,
        })
        .then(({ data }) => {
            if (data.success) {
                return successToNotification(
                    "User has been granted membership"
                );
            }
            if (data.can_modify_membership) {
                modifyModal.value = true;
            } else {
                return successToNotification(
                    "User has an active subscription and their membership cannot be modified"
                );
            }
        })
        .catch(axiosSendErrorToNotification);
    if (modify_membership) {
        modifyModal.value = false;
    }
}
</script>
