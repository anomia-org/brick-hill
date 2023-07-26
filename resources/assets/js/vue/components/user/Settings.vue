<template>
    <div class="settings">
        <div class="card">
            <div class="blue top">Settings</div>
            <div class="content">
                <div v-if="Object.keys(settings).length > 0">
                    <span class="dark-gray-text very-bold block">
                        Information
                    </span>
                    <div class="block">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Username:
                        </span>
                        <span class="light-gray-text">
                            {{ settings.user.username }}
                        </span>
                        <i
                            @click="modals.change_username.active = true"
                            class="f-right light-gray-text far fa-edit"
                            style="cursor: pointer"
                        ></i>
                    </div>
                    <div class="block">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Password:
                        </span>
                        <span class="light-gray-text">*********</span>
                        <i
                            @click="modals.change_password.active = true"
                            class="f-right light-gray-text far fa-edit"
                            style="cursor: pointer"
                        ></i>
                    </div>
                    <div class="block">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Email:
                        </span>
                        <span
                            v-if="settings.user.email"
                            :class="{
                                'red-text': !settings.user.email_verified,
                                'light-gray-text': settings.user.email_verified,
                            }"
                        >
                            {{ settings.user.email }}
                        </span>
                        <span v-if="!settings.user.email" class="red-text">
                            None
                        </span>
                        <i
                            @click="modals.change_email.active = true"
                            class="f-right light-gray-text far fa-edit"
                            style="cursor: pointer"
                        ></i>
                    </div>
                    <div class="block">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Theme:
                        </span>
                        <div class="inline">
                            <select v-model="settings.user.theme">
                                <option
                                    v-for="(theme, i) in settings.themes"
                                    :key="theme"
                                    :value="i"
                                >
                                    {{ theme }}
                                </option>
                            </select>
                        </div>
                        <button
                            @click="submitTheme"
                            class="f-right blue button small"
                        >
                            Save
                        </button>
                    </div>
                    <hr />
                    <span
                        class="dark-gray-text bold block"
                        style="padding-bottom: 5px"
                    >
                        Security
                    </span>
                    <div class="block">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            2FA:
                        </span>
                        <span class="light-gray-text">
                            {{
                                settings.user.is_2fa_active
                                    ? "Active"
                                    : "Disabled"
                            }}
                        </span>
                        <i
                            @click="start2FAProcess"
                            class="f-right light-gray-text far fa-edit"
                            style="cursor: pointer"
                        ></i>
                    </div>
                    <div
                        v-if="settings.user.is_2fa_active"
                        class="block"
                        style="padding-bottom: 10px"
                    >
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Recovery Codes:
                        </span>
                        <button
                            @click="
                                modals.pass_for_recovery_codes.active = true
                            "
                            class="f-right small blue button"
                        >
                            Generate New Codes
                        </button>
                    </div>
                    <div class="block" style="padding-bottom: 5px">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Log out of other devices:
                        </span>
                        <button
                            @click="modals.logout_other_devices.active = true"
                            class="f-right small blue button"
                        >
                            Log out
                        </button>
                    </div>

                    <hr />

                    <span
                        class="dark-gray-text bold block"
                        style="padding-bottom: 5px"
                    >
                        Privacy
                    </span>
                    <div class="block" style="padding-bottom: 5px">
                        <span class="dark-gray-text" style="padding-right: 5px">
                            Cookies:
                        </span>
                        <button
                            @click="openConsentUI"
                            class="f-right small blue button"
                        >
                            Open
                        </button>
                    </div>

                    <div
                        v-if="
                            settings.user.membership ||
                            settings.user.is_stripe_customer
                        "
                    >
                        <hr />
                        <span
                            class="dark-gray-text bold block"
                            style="padding-bottom: 5px"
                        >
                            Billing
                        </span>
                        <div v-if="settings.user.membership">
                            <div
                                v-if="settings.user.subscription_active"
                                style="padding-bottom: 10px"
                            >
                                <span class="dark-gray-text">
                                    Your membership will auto renew on
                                    {{ settings.user.membership_expires_on }}
                                </span>
                                <button
                                    @click="cancelActiveMembership"
                                    class="f-right small blue button"
                                >
                                    Cancel Now
                                </button>
                            </div>
                            <div v-else>
                                <span class="dark-gray-text">
                                    Your membership will expire on
                                    {{ settings.user.membership_expires_on }}
                                </span>
                            </div>
                        </div>
                        <div
                            v-if="settings.user.is_stripe_customer"
                            style="padding-bottom: 5px"
                        >
                            <span class="dark-gray-text">Stripe Settings:</span>
                            <button
                                @click="enterBillingPortal"
                                class="f-right small blue button"
                            >
                                Billing Portal
                            </button>
                        </div>
                    </div>

                    <hr />
                    <span
                        class="dark-gray-text bold block"
                        style="padding-bottom: 5px"
                    >
                        Blurb
                    </span>
                    <textarea
                        v-model="settings.user.description"
                        name="description"
                        class="width-100 block"
                        style="height: 80px; margin-bottom: 6px"
                        :placeholder="`Hi, my name is ${settings.user.username}`"
                    ></textarea>
                    <button @click="submitBlurb" class="button small blue">
                        Save
                    </button>
                </div>
            </div>

            <modal
                title="Change Username"
                v-show="modals.change_username.active"
                @close="modals.change_username.active = false"
            >
                <input
                    v-model="modals.change_username.username"
                    @input="usernameChanged"
                    type="text"
                    id="new-name"
                    name="name"
                    placeholder="New Username"
                    autocomplete="off"
                />
                <span
                    v-if="modals.change_username.error"
                    style="color: red; font-size: 12px"
                    class="block"
                >
                    {{ modals.change_username.error }}
                </span>
                <span style="color: red; font-size: 11px">
                    WARNING: This will take 250 bucks
                </span>
                <div class="modal-buttons">
                    <button
                        @click="buyUsername"
                        :disabled="attemptedUsernameChange"
                        :class="{
                            green:
                                !modals.change_username.error &&
                                modals.change_username.username.length > 0,
                            'no-click':
                                modals.change_username.error ||
                                modals.change_username.username.length == 0,
                        }"
                        style="margin-right: 10px"
                    >
                        Buy
                    </button>
                    <button
                        class="cancel-button modal-close"
                        @click="modals.change_username.active = false"
                    >
                        Cancel
                    </button>
                </div>
            </modal>

            <modal
                title="Change Password"
                v-show="modals.change_password.active"
                @close="modals.change_password.active = false"
            >
                <form method="POST">
                    <input type="hidden" name="type" value="changePassword" />
                    <input type="hidden" name="_token" :value="`${BH.csrf}`" />
                    <input
                        autocomplete="current-password"
                        type="password"
                        name="current_password"
                        placeholder="Current Password"
                    />
                    <input
                        v-model="modals.change_password.newPass"
                        autocomplete="new-password"
                        type="password"
                        name="password"
                        placeholder="New Password"
                    />
                    <input
                        v-model="modals.change_password.newPassConf"
                        autocomplete="confirm-password"
                        @input="confPass"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm New Password"
                    />
                    <span
                        v-if="modals.change_password.error"
                        style="color: red; font-size: 12px"
                        class="block"
                    >
                        Passwords do not match
                    </span>
                    <div class="modal-buttons">
                        <button
                            type="submit"
                            :class="{
                                green:
                                    !modals.change_password.error &&
                                    modals.change_password.newPassConf.length >
                                        0,
                                'no-click':
                                    modals.change_password.error ||
                                    modals.change_password.newPassConf.length ==
                                        0,
                            }"
                            style="margin-right: 10px"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="cancel-button modal-close"
                            @click="modals.change_password.active = false"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </modal>

            <modal
                title="Change Email"
                v-show="modals.change_email.active"
                @close="modals.change_email.active = false"
                v-if="Object.keys(settings).length > 0"
            >
                <form method="POST">
                    <input type="hidden" name="type" value="changeEmail" />
                    <input type="hidden" name="_token" :value="`${BH.csrf}`" />
                    <input
                        v-if="
                            settings.user.email && settings.user.email_verified
                        "
                        autocomplete="off"
                        name="current_email"
                        type="email"
                        placeholder="Current Email"
                        required
                    />
                    <input
                        autocomplete="off"
                        name="email"
                        type="email"
                        placeholder="New Email"
                        required
                    />
                    <input
                        type="password"
                        name="current_password"
                        placeholder="Current Password"
                        autocomplete="password"
                        required
                    />
                    <div class="modal-buttons">
                        <button
                            type="submit"
                            class="green"
                            style="margin-right: 10px"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="cancel-button modal-close"
                            @click="modals.change_email.active = false"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </modal>

            <modal
                title="Activate 2FA"
                v-show="modals.activate_2fa.active"
                @close="modals.activate_2fa.active = false"
            >
                <div style="text-align: center">
                    <div
                        class="tfa-qr-code"
                        style="max-width: 512px"
                        v-html="modals.activate_2fa.svg"
                    ></div>
                    <br />
                    <div class="bold">{{ tfaSplit }}</div>
                    <br />
                    <div>
                        Scan the QR code above in a Google Authenticator
                        compatible application. Use the text above if you are
                        unable to scan the QR code.
                    </div>
                    <br />
                    <div>
                        If you lose access to the code and do not have any of
                        the recovery codes we will
                        <b>NOT</b>
                        be able to restore your account. If you do not
                        understand this
                        <b>do not</b>
                        activate it.
                    </div>
                    <div>
                        I understand
                        <input
                            class="inline"
                            style="width: auto"
                            type="checkbox"
                            v-model="modals.activate_2fa.accepted"
                        />
                    </div>
                </div>
                <br />
                <div v-if="modals.activate_2fa.accepted">
                    <input
                        v-model="modals.activate_2fa.code"
                        type="text"
                        placeholder="2FA Token"
                    />
                    <div style="text-align: center">
                        <div
                            v-show="modals.activate_2fa.error.length > 0"
                            style="color: red"
                        >
                            {{ modals.activate_2fa.error }}
                        </div>
                    </div>
                    <div class="modal-buttons">
                        <button
                            @click="activate2FA"
                            :disabled="
                                modals.activate_2fa.code.length != 6 ||
                                !modals.activate_2fa.accepted
                            "
                            :class="{
                                green: modals.activate_2fa.code.length == 6,
                            }"
                            style="margin-right: 10px"
                        >
                            Activate
                        </button>
                        <button
                            class="cancel-button modal-close"
                            @click="modals.activate_2fa.active = false"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </modal>

            <modal
                title="Remove 2FA"
                v-show="modals.remove_2fa.active"
                @close="modals.remove_2fa.active = false"
            >
                <form method="POST" action="/2fa/remove">
                    <input type="hidden" name="_token" :value="`${BH.csrf}`" />
                    <input
                        type="password"
                        name="current_password"
                        placeholder="Current Password"
                        autocomplete="current-password"
                        required
                    />
                    <input
                        type="text"
                        name="token"
                        placeholder="2FA Token or Recovery Code"
                        autocomplete="off"
                        required
                    />
                    <div class="modal-buttons">
                        <button
                            type="submit"
                            class="red"
                            style="margin-right: 10px"
                        >
                            Remove
                        </button>
                        <button
                            type="button"
                            class="cancel-button modal-close"
                            @click="modals.remove_2fa.active = false"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </modal>

            <modal
                title="Log Out Of Other Sessions"
                v-show="modals.logout_other_devices.active"
                @close="modals.logout_other_devices.active = false"
            >
                <form method="POST" action="/logoutall">
                    <input type="hidden" name="_token" :value="`${BH.csrf}`" />
                    <input
                        type="password"
                        name="current_password"
                        placeholder="Current Password"
                        autocomplete="current-password"
                        required
                    />
                    <div class="modal-buttons">
                        <button
                            type="submit"
                            class="green"
                            style="margin-right: 10px"
                        >
                            Log Out
                        </button>
                        <button
                            type="button"
                            class="cancel-button modal-close"
                            @click="modals.logout_other_devices.active = false"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </modal>

            <modal
                title="Recovery Codes"
                v-if="
                    modals.show_codes.codes.length > 0 &&
                    modals.show_codes.active
                "
                @close="modals.show_codes.active = false"
            >
                <div style="text-align: center">
                    <div
                        v-for="(code, i) in modals.show_codes.codes"
                        :key="i"
                        class="bold"
                    >
                        {{ code }}
                        <hr />
                    </div>
                </div>
                <br />
                <div>
                    These codes can be used in place of a TFA token if you don't
                    have your TFA device with you. You can generate new codes
                    from the settings and each can only be used once. Old codes
                    will no longer be valid.
                </div>
                <div class="modal-buttons">
                    <button
                        @click="downloadCodes"
                        class="button blue"
                        style="margin-right: 10px"
                    >
                        Download Codes
                    </button>
                    <button
                        type="button"
                        class="cancel-button modal-close"
                        @click="modals.show_codes.active = false"
                    >
                        Close
                    </button>
                </div>
            </modal>

            <modal
                title="Generate New Recovery Codes"
                v-show="modals.pass_for_recovery_codes.active"
                @close="modals.pass_for_recovery_codes.active = false"
            >
                <div style="margin-bottom: 10px">
                    Are you sure you want to make new ones? This will make your
                    old codes invalid so make sure you save them!
                </div>
                <input
                    @keyup.enter="generateRecoveryCodes"
                    v-model="modals.pass_for_recovery_codes.current_pass"
                    type="password"
                    name="current_password"
                    placeholder="Current Password"
                    autocomplete="current-password"
                    required
                />
                <div style="text-align: center">
                    <div
                        v-show="modals.pass_for_recovery_codes.error.length > 0"
                        style="color: red"
                    >
                        {{ modals.pass_for_recovery_codes.error }}
                    </div>
                </div>
                <div class="modal-buttons">
                    <button
                        @click="generateRecoveryCodes"
                        class="green"
                        style="margin-right: 10px"
                    >
                        Generate
                    </button>
                    <button
                        type="button"
                        class="cancel-button modal-close"
                        @click="modals.pass_for_recovery_codes.active = false"
                    >
                        Cancel
                    </button>
                </div>
            </modal>
        </div>
    </div>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";
import { notificationStore } from "@/store/modules/notifications";
import { BH } from "@/logic/bh";
import axios from "axios";
import { computed, reactive, ref } from "vue";

const modals = reactive({
    change_username: {
        debounce: false as any,
        active: false,
        error: false,
        username: "",
    },
    change_password: {
        active: false,
        error: false,
        newPass: "",
        newPassConf: "",
    },
    change_email: {
        active: false,
    },
    activate_2fa: {
        active: false,
        accepted: false,
        error: "",
        secret: "",
        code: "",
        svg: "",
    },
    remove_2fa: {
        active: false,
        password: "",
        token: "",
    },
    show_codes: {
        active: false,
        codes: [],
    },
    pass_for_recovery_codes: {
        active: false,
        error: "",
        current_pass: "",
    },
    logout_other_devices: {
        active: false,
    },
});
const settings = ref<any>({});

load();

function load() {
    axios.get(`/settings/data`).then(({ data }) => {
        settings.value = data;
    });
}

function openConsentUI() {
    // @ts-expect-error
    if (typeof window.__tcfapi === "undefined") return;
    // @ts-expect-error
    window.__tcfapi("displayConsentUi", 2, function () {});
}

function generateRecoveryCodes() {
    axios
        .post("/2fa/newrecoverycodes", {
            current_password: modals.pass_for_recovery_codes.current_pass,
        })
        .then(({ data }) => {
            if (data.error) {
                return (modals.pass_for_recovery_codes.error = data.error);
            }
            if (data.recovery_codes) {
                modals.show_codes.codes = data.recovery_codes;
            }
            modals.pass_for_recovery_codes.active = false;
            modals.show_codes.active = true;
        });
}

function downloadCodes() {
    let blob = new Blob([modals.show_codes.codes.join("\n")], {
        type: "text/plain",
    });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "brick_hill_recovery_codes.txt";
    link.click();
    URL.revokeObjectURL(link.href);
}

function start2FAProcess() {
    if (settings.value.user.is_2fa_active) {
        modals.remove_2fa.active = true;
        return;
    }
    if (modals.activate_2fa.secret.length == 0)
        axios.post(`/2fa/gettoken`).then(({ data }) => {
            if (data.error) {
                return (modals.activate_2fa.error = data.error);
            } else {
                modals.activate_2fa.error = "";
            }
            if (!data.secret)
                return (modals.activate_2fa.error =
                    "Error getting 2FA key. Try again later.");

            modals.activate_2fa.secret = data.secret;
            modals.activate_2fa.svg = data.svg;
        });

    modals.activate_2fa.active = true;
}

function activate2FA() {
    axios
        .post(`/2fa/setuptoken`, {
            token: modals.activate_2fa.code,
        })
        .then(({ data }) => {
            if (data.error) {
                return (modals.activate_2fa.error = data.error);
            } else {
                modals.activate_2fa.error = "";
            }
            if (!data.success)
                return (modals.activate_2fa.error =
                    "Error setting up 2FA. Try again later.");

            settings.value.user.is_2fa_active = true;
            modals.activate_2fa.active = false;
            modals.show_codes.codes = data.recovery_codes;
            modals.show_codes.active = true;
        })
        .catch(({ response }) => {
            return (modals.activate_2fa.error =
                response.data.error.prettyMessage);
        });
}

function usernameChanged() {
    if (modals.change_username.debounce)
        clearTimeout(modals.change_username.debounce);
    modals.change_username.debounce = setTimeout(() => {
        axios
            .get(`/api/settings/username?u=${modals.change_username.username}`)
            .then(({ data }) => {
                if (data.v !== true) {
                    modals.change_username.error = data.v;
                } else {
                    modals.change_username.error = false;
                }
            });
    }, 250);
}

const attemptedUsernameChange = ref<boolean>(false);
function buyUsername() {
    attemptedUsernameChange.value = true;
    axios
        .post(`/settings`, {
            type: "changeUsername",
            username: modals.change_username.username,
        })
        .then(({ data }) => {
            if (data.error) {
                modals.change_username.error = data.v;
            } else {
                modals.change_username.error = false;
            }
            if (data.changed) {
                location.reload();
            }
        });
}

function confPass() {
    modals.change_password.error =
        modals.change_password.newPass != modals.change_password.newPassConf;
}

function submitBlurb() {
    axios
        .post(`/settings`, {
            type: "changeDescription",
            description: settings.value.user.description,
        })
        .then(({ data }) => {
            if (data.error) {
                notificationStore.setNotification(data.error, "error");
            } else {
                notificationStore.setNotification(
                    "Blurb has been saved",
                    "success"
                );
            }
        });
}

function submitTheme() {
    axios
        .post(`/settings`, {
            type: "changeTheme",
            theme: settings.value.user.theme,
        })
        .then(({ data }) => {
            if (data.error) {
                notificationStore.setNotification(data.error, "error");
            } else {
                location.reload();
            }
        });
}

function cancelActiveMembership() {
    axios
        .post(BH.apiUrl("v1/billing/cancelSubscription"))
        .then(({ data }) => {
            if (data.error) {
                notificationStore.setNotification(data.error, "error");
            } else {
                location.reload();
            }
        })
        .catch((error) => {
            if (error.response.data.error) {
                notificationStore.setNotification(
                    error.response.data.error,
                    "error"
                );
            }
        });
}

function enterBillingPortal() {
    axios.post(BH.apiUrl("v1/billing/portal")).then(({ data }) => {
        if (data.url) location.href = data.url;
    });
}

const tfaSplit = computed(() => {
    return (modals.activate_2fa.secret.match(/.{4}/g) || []).join("-");
});
</script>
