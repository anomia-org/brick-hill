<template>
    <div class="card">
        <div class="top green">Grant workshop</div>
        <div class="content">
            Grant a user workshop access for free
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
                <div>
                    <span>Debug:</span>
                    <input v-model="canDebug" type="checkbox" name="debug" />
                    <div class="red-text bold">
                        Only for users who should have access to the debug
                        section of the client (people in client test discord)
                    </div>
                </div>
            </div>
            <button type="submit" @click="grantWorkshop()" class="green">
                Grant
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { ref } from "vue";

const grantUser = ref<string>("");
const canDebug = ref<boolean>(false);

function grantWorkshop() {
    axios
        .post(`/admin/grantWorkshop`, {
            grant_user: grantUser.value,
            can_debug: canDebug.value,
        })
        .then(() =>
            successToNotification("User has been given workshop access")
        )
        .catch(axiosSendErrorToNotification);
}
</script>
