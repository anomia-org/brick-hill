<template>
    <div>
        <div :show="permissionStore.can('modify emails')" class="card">
            <div class="top blue">Replace User Email</div>
            <div class="content">
                <div class="block">
                    <input
                        class="margin-5"
                        v-model="replaceEmail.user"
                        type="number"
                        min="1"
                        placeholder="User ID"
                    />
                    <input
                        class="margin-5"
                        v-model="replaceEmail.email"
                        type="email"
                        placeholder="New Email"
                    />
                </div>
                <AreYouSure @accepted="submitReplaceEmail">Submit</AreYouSure>
            </div>
        </div>
        <div :show="permissionStore.can('modify emails')" class="card">
            <div class="top blue">Email Attached To User</div>
            <div class="content">
                <div class="block">
                    <input
                        class="margin-5"
                        v-model="isAttached.user"
                        type="number"
                        min="1"
                        placeholder="User ID"
                    />
                    <input
                        class="margin-5"
                        v-model="isAttached.email"
                        type="email"
                        placeholder="Email to check for"
                    />
                </div>
                <div v-if="typeof isAttached.isAttached !== 'undefined'">
                    <div v-if="isAttached.isAttached">
                        Email is attached to user
                        <span v-if="isAttached.isVerified">
                            and is currently verified</span
                        >
                        <span class="red-text" v-else>
                            and is not currently verified</span
                        >
                    </div>
                    <div class="red-text" v-else>
                        Email is not associated to user
                    </div>
                </div>
                <button class="green" @click="checkForAttached">CHECK</button>
            </div>
        </div>
        <div :show="permissionStore.can('recover tfa')" class="card">
            <div class="top blue">Recover Two Factor Authentication</div>
            <div class="content">
                <div>
                    Input a users ID and their currently verified email and the
                    user will receive an email containing a single recovery code
                    that they can use to login, with included instructions on
                    how to remove TFA.
                    <br />
                    This should only be used if you are <b>100%</b> sure the
                    user is who they say they are. If there is any slight doubt
                    that they aren't them <b>DO NOT</b> use this.
                </div>
                <div class="block">
                    <input
                        class="margin-5"
                        v-model="recoverTFA.user"
                        type="number"
                        min="1"
                        placeholder="User ID"
                    />
                    <input
                        class="margin-5"
                        v-model="recoverTFA.email"
                        type="email"
                        placeholder="Users current verified email"
                    />
                </div>
                <button class="green" @click="postRecoverTFA">
                    Send Email
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { reactive } from "vue";
import AreYouSure from "@/components/global/AreYouSure.vue";
import { permissionStore } from "@/store/modules/permission";

const replaceEmail = reactive({
    user: undefined,
    email: undefined,
});

const isAttached = reactive({
    user: undefined,
    email: undefined,
    isAttached: undefined,
    isVerified: undefined,
});

const recoverTFA = reactive({
    user: undefined,
    email: undefined,
});

function submitReplaceEmail() {
    axios
        .post(`/v1/admin/${replaceEmail.user}/replaceEmail`, {
            email: replaceEmail.email,
        })
        .then(() => successToNotification("User email changed"))
        .catch(axiosSendErrorToNotification);
}

function checkForAttached() {
    axios
        .post(`/v1/admin/${isAttached.user}/checkEmail`, {
            email: isAttached.email,
        })
        .then(({ data }) => {
            isAttached.isAttached = data.is_attached;
            isAttached.isVerified = data.is_currently_verified;
        })
        .catch(axiosSendErrorToNotification);
}

function postRecoverTFA() {
    axios
        .post(`/v1/admin/${recoverTFA.user}/recoverTFA`, {
            email: recoverTFA.email,
        })
        .then(() => successToNotification("Recovery email sent"))
        .catch(axiosSendErrorToNotification);
}
</script>
