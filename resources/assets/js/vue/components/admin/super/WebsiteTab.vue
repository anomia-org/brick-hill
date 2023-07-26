<template>
    <div>
        <div class="card">
            <div class="top blue">Site Banner</div>
            <div class="content">
                <input
                    class="width-100 margin-5"
                    v-model="banner"
                    placeholder="Banner (submit as empty to remove)"
                />
                <input
                    class="width-100 margin-5"
                    v-model="banner_url"
                    placeholder="Banner url"
                />
                <AreYouSure @accepted="submitBanner">Submit</AreYouSure>
            </div>
        </div>
        <div class="card">
            <div class="top red">Maintenance Mode</div>
            <div class="content">
                <AreYouSure @accepted="enableMaintenance"
                    >Toggle Maintenance Mode</AreYouSure
                >
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    axiosReloadOnJSONSuccess,
    axiosSendErrorToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { ref } from "vue";
import AreYouSure from "../../global/AreYouSure.vue";

const banner = ref<string>();
const banner_url = ref<string>();

function submitBanner() {
    axios
        .post("/v1/admin/banner", {
            banner: banner.value,
            banner_url: banner_url.value,
        })
        .then(axiosReloadOnJSONSuccess)
        .catch(axiosSendErrorToNotification);
}

function enableMaintenance() {
    axios
        .post("/v1/admin/maintenance")
        .then(axiosReloadOnJSONSuccess)
        .catch(axiosSendErrorToNotification);
}
</script>
