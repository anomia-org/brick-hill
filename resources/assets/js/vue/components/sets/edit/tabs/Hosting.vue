<template>
    <div class="col-2-3">
        <div v-if="crashReport.length > 0 && serverType == 'dedicated'">
            <div class="small-text">
                <SvgSprite
                    class="svg-icon"
                    square="16"
                    svg="notifications/error.svg"
                />
                Server has experienced an unexpected close in the past day. Here
                are some logs.
            </div>
            <pre
                style="
                    white-space: pre-wrap;
                    word-wrap: break-all;
                    max-height: 300px;
                    overflow-y: scroll;
                "
                >{{ crashReport }}</pre
            >
        </div>
        <div v-if="canUseDedicatedBool || originalServerState == 'dedicated'">
            <div class="mb-10">Server:</div>
            <div class="mb-20 smaller-text">
                If you don't know what this means, pick dedicated. If you want
                to host your set from your own server, pick node-hill. This is
                much more technical and is only recommended for users who know
                what they are doing.
            </div>

            <div class="mb-20">
                <div class="mb-10">
                    <input
                        type="radio"
                        value="dedicated"
                        id="dedicated"
                        v-model="serverType"
                    />
                    <label for="dedicated">Dedicated</label>
                    <br />
                    <input
                        type="radio"
                        value="nh"
                        id="nh"
                        v-model="serverType"
                    />
                    <label for="nh">node-hill</label>
                </div>

                <div
                    v-if="
                        !canUseDedicatedBool &&
                        originalServerState == 'dedicated' &&
                        serverType == 'nh'
                    "
                    class="small-text mb-10"
                >
                    <SvgSprite
                        class="svg-icon"
                        square="16"
                        svg="notifications/error.svg"
                    />
                    Dedicated servers are only available to users with active
                    membership.
                    <br />
                    Swapping your set to node-hill without a membership will
                    prevent it from being dedicated again.
                </div>

                <div>
                    <button class="blue" @click="changeType">Save</button>
                </div>
            </div>
        </div>

        <div v-if="serverType == 'dedicated' && canUseDedicatedBool">
            <div class="mb-20">
                <div class="mb-10">Map:</div>
                <input
                    @change="brkEvent"
                    class="mb-16"
                    style="width: 100%"
                    accept=".brk"
                    type="file"
                />

                <div class="mb-10">Scripts:</div>
                <input
                    @change="jsEvent"
                    style="width: 100%; margin-bottom: 5px"
                    accept=".js"
                    type="file"
                    multiple
                />
                <div class="small-text">
                    <SvgSprite
                        class="svg-icon"
                        square="16"
                        svg="notifications/warning.svg"
                    />
                    Make sure you upload all your scripts at once
                </div>
            </div>
            <div class="mb-20">
                <button class="blue" @click="upload">Upload</button>
            </div>
            <div class="mb-20">
                <div class="mb-10">Restart:</div>
                <are-you-sure buttonClass="red mb-10" @accepted="restart">
                    Restart Set
                </are-you-sure>
                <div class="small-text">
                    <SvgSprite
                        class="svg-icon"
                        square="16"
                        svg="notifications/warning.svg"
                    />
                    This will kick all users out of your set and apply new
                    changes to all servers
                </div>
            </div>
        </div>
        <div v-if="serverType == 'nh'">
            <div class="mb-20">
                <div class="mb-10">Host Key:</div>
                <input v-model="hostKey" class="width-100" readonly disabled />
            </div>
            <div>
                <button class="blue" @click="refreshKey">New Key</button>
                <button class="yellow" @click="showKey">Show Key</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AreYouSure from "@/components/global/AreYouSure.vue";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import { setEditStore } from "@/store/modules/sets/edit";
import { notificationStore } from "@/store/modules/notifications";
import { BH } from "@/logic/bh";
import axios from "axios";
import { computed, ref } from "vue";
import SvgSprite from "@/components/global/SvgSprite.vue";

const props = defineProps<{
    setId: number;
    canUseDedicated: string;
    crashReport: string;
}>();

const hostKey = ref<string>("••••••••••••••••••••");
const canUseDedicatedBool = ref<boolean>(props.canUseDedicated == "1");
const originalServerState = ref<string | undefined>();
const brkFile = ref<File>({} as File);
const scriptFiles = ref<FileList>({} as FileList);

const serverType = computed({
    get: () => {
        let getter = setEditStore.getState().serverType;
        if (typeof originalServerState.value === "undefined")
            originalServerState.value = getter;
        return getter;
    },
    set: (value) => {
        if (typeof value === "undefined") return;
        if (!canUseDedicatedBool.value && value == "dedicated") {
            notificationStore.setNotification(
                "You cannot set a server to dedicated without a membership",
                "error"
            );
        }
        setEditStore.setServerType(value);
    },
});

function changeType() {
    axios
        .post(`/play/${props.setId}/changeType`, {
            server_type: serverType.value,
        })
        .then(() => successToNotification("Set saved successfully"))
        .catch(axiosSendErrorToNotification);
}

function brkEvent(e: Event) {
    if (
        !(e.target instanceof HTMLInputElement) ||
        !e.target.files ||
        e.target.files.length == 0
    )
        return;
    brkFile.value = e.target.files[0];
}

function jsEvent(e: Event) {
    if (
        !(e.target instanceof HTMLInputElement) ||
        !e.target.files ||
        e.target.files.length == 0
    )
        return;
    scriptFiles.value = e.target.files;
}

function restart() {
    axios
        .post(`/play/${props.setId}/restartSet`)
        .then(() =>
            successToNotification(
                "Set restarted. Allow up to 60 seconds for all servers to properly close"
            )
        )
        .catch(axiosSendErrorToNotification);
}

function upload() {
    let form = new FormData();
    form.append("brk", brkFile.value);
    for (let i = 0; i < scriptFiles.value.length; i++) {
        form.append(`scripts[]`, scriptFiles.value[i]);
    }
    axios
        .post(`/play/${props.setId}/uploadBrk`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then(() => successToNotification("Set saved successfully"))
        .catch(axiosSendErrorToNotification);
}

function refreshKey() {
    axios
        .post(BH.apiUrl(`v1/sets/${props.setId}/newHostKey`))
        .then(({ data }) => {
            hostKey.value = data.key;
        })
        .catch(axiosSendErrorToNotification);
}

function showKey() {
    axios.get(BH.apiUrl(`v1/sets/${props.setId}/hostKey`)).then(({ data }) => {
        hostKey.value = data.key;
    });
}
</script>
