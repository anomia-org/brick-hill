<template>
    <div>
        <div class="col-1-3 push-1-3">
            <div class="card">
                <div class="top green">2FA Required</div>
                <div class="content text-center">
                    <div v-if="!useRecoveryCodes">
                        <form @submit.prevent="sendToken()">
                            <div class="tfa-input-holder">
                                <input
                                    v-for="i in 6"
                                    :key="i"
                                    ref="inputs"
                                    v-model="token[i - 1]"
                                    @input="onInput"
                                    @keydown.delete="onDelete"
                                    @keydown.left="onArrow"
                                    @keydown.right="onArrow"
                                    @keydown.enter="onSubmit"
                                    @paste="onPaste"
                                    @focus="onFocus"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    minlength="1"
                                    maxlength="1"
                                    required
                                />
                            </div>
                        </form>
                        <div class="small-text">
                            Enter 2FA token from your Authenticator app
                        </div>
                        <div
                            @click="useRecoveryCodes = true"
                            class="recovery-code smaller-text pointer"
                        >
                            Can't access 2FA? Use a
                            <span>recovery code</span>
                        </div>
                    </div>
                    <div v-else>
                        <form @submit.prevent="sendToken(recoveryCode)">
                            <input
                                v-model="recoveryCode"
                                style="width: 100%; margin-bottom: 10px"
                                type="text"
                                placeholder="Recovery Code"
                                minlength="17"
                                required
                            />
                            <button type="submit" class="green">Submit</button>
                        </form>
                        <div
                            @click="useRecoveryCodes = false"
                            class="recovery-code small-text pointer"
                        >
                            Use 2FA Token
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { errorToNotification } from "@/logic/notifications";
import axios from "axios";
import { onMounted, ref, watch } from "vue";

const token = ref<string[]>([]);
const recoveryCode = ref<string>("");
const useRecoveryCodes = ref<boolean>(false);

const inputs = ref<HTMLInputElement[]>();

onMounted(() => {
    if (!inputs.value || inputs.value?.length == 0) return;

    inputs.value[0].focus();
});

function onInput(e: Event) {
    if (!(e instanceof InputEvent) || !(e.target instanceof Element)) return;
    if (
        e.data &&
        e.target?.nextElementSibling &&
        e.target.nextElementSibling instanceof HTMLElement
    )
        e.target.nextElementSibling.focus();
    else if (!e.target.nextElementSibling) onSubmit();
}

function onDelete(e: KeyboardEvent) {
    if (!(e.target instanceof HTMLInputElement)) return;
    if (
        !e.target.value &&
        e.target.previousElementSibling &&
        e.target.previousElementSibling instanceof HTMLElement
    )
        e.target.previousElementSibling.focus();
}

function onArrow(e: KeyboardEvent) {
    if (!(e.target instanceof HTMLInputElement)) return;
    if (
        e.code == "ArrowLeft" &&
        e.target.previousElementSibling &&
        e.target.previousElementSibling instanceof HTMLElement
    )
        e.target.previousElementSibling.focus();
    else if (
        e.code == "ArrowRight" &&
        e.target.nextElementSibling &&
        e.target.nextElementSibling instanceof HTMLElement
    )
        e.target.nextElementSibling.focus();
}

function onSubmit() {
    if (token.value.join("").length == 6) {
        sendToken();
        if (document.activeElement instanceof HTMLElement)
            document.activeElement.blur();
    }
}

function onPaste(e: ClipboardEvent) {
    e.preventDefault();
    let clipboardData = e.clipboardData;
    let pastedData = clipboardData?.getData("Text") || "";
    if (pastedData.length == 6 && /^\d+$/.test(pastedData)) {
        token.value = pastedData.split("");

        if (e.target instanceof HTMLElement) e.target?.blur();
    }
}

function onFocus(e: FocusEvent) {
    // why does this need to be wrapped in a timeout ???
    setTimeout(() => {
        if (e.target instanceof HTMLInputElement) e.target.select();
    });
}

function sendToken(code = token.value.join("")) {
    axios
        .post("/2fa/check", {
            tfa_token: code,
        })
        .then(({ data }) => {
            location.reload();
        })
        .catch((error) => {
            if (error.response.data.message.length > 0) {
                errorToNotification(error.response.data.message[0]);
                token.value = [];

                if (!inputs.value || inputs.value?.length == 0) return;
                inputs.value[0].focus();
            }
        });
}

watch(token, () => {
    onSubmit();
});
</script>
