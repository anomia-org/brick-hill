<template>
    <div>
        <VerticalTabs :border="false" :tabsLoaded="loaded">
            <Tab
                :show="permissionStore.canAny('accept items', 'accept clans')"
                name="Approval"
            >
                <Pending />
            </Tab>
            <Tab :show="permissionStore.can('view reports')" name="Reports">
                <Reports />
            </Tab>
            <Tab
                :show="permissionStore.can('manage shop')"
                name="Shop"
                href="/admin/shop"
            />
            <Tab :show="permissionStore.can('grant items')" name="Items">
                <div class="card">
                    <div class="top blue">Grant items</div>
                    <div class="content">
                        Only administrators can grant items. Abuse of this
                        feature will lead to immediate suspension.
                        <br /><br />
                        <div class="block">
                            <input
                                placeholder="Username"
                                v-model="grantItemUsername"
                                max="2147483647"
                                min="0"
                                name="username"
                                style="margin-bottom: 10px"
                            />
                            <input
                                placeholder="Item ID"
                                v-model="grantItemId"
                                min="0"
                                name="item_id"
                                style="margin-bottom: 10px"
                            />
                        </div>
                        <button type="submit" @click="grantItem()" class="blue">
                            Grant
                        </button>
                    </div>
                </div>
            </Tab>
            <Tab :show="permissionStore.can('grant currency')" name="Currency">
                <div class="card">
                    <div class="top green">Grant or deduct currency</div>
                    <div class="content">
                        Only administrators can change currency. Abuse of this
                        feature will lead to immediate suspension.
                        <br /><br />
                        <div class="block">
                            <input
                                type="number"
                                v-model="currencyQuantity"
                                placeholder="Amount"
                                max="2147483647"
                                min="0"
                                name="quantity"
                                style="margin-bottom: 10px"
                            />
                            <select
                                name="type"
                                v-model="currencyType"
                                style="margin-bottom: 10px"
                            >
                                <option value="bucks">Bucks</option>
                                <option value="bits">Bits</option>
                            </select>
                            <input
                                placeholder="Username"
                                v-model="currencyUsername"
                                name="username"
                                style="margin-bottom: 10px"
                            />
                            <select
                                name="action"
                                v-model="currencyAction"
                                style="margin-bottom: 10px"
                            >
                                <option value="grant">Grant</option>
                                <option value="deduct">Deduct</option>
                            </select>
                        </div>
                        <button
                            type="submit"
                            @click="changeCurrency()"
                            class="green"
                        >
                            Change
                        </button>
                    </div>
                </div>
            </Tab>
            <Tab :show="permissionStore.can('transfer crate')" name="Transfer">
                <div class="card">
                    <div class="top blue">Transfer Crate</div>
                    <div class="content">
                        Any item can be transferred to any account. User ID 1003
                        is the main Brick Hill account
                        <br /><br />
                        <div class="block">
                            <input
                                type="number"
                                required
                                v-model.number="transfer.crate_id"
                                placeholder="Crate ID"
                                min="0"
                                style="margin-bottom: 10px"
                            />
                            <input
                                type="number"
                                required
                                v-model.number="transfer.from_user"
                                placeholder="From User ID"
                                min="0"
                                style="margin-bottom: 10px; width: 150px"
                            />
                            <input
                                type="number"
                                required
                                v-model.number="transfer.to_user"
                                placeholder="To User ID (1003)"
                                min="0"
                                style="margin-bottom: 10px; width: 150px"
                            />
                        </div>
                        <AreYouSure
                            :buttonDisabled="
                                !transfer.crate_id ||
                                !transfer.from_user ||
                                !transfer.to_user
                            "
                            buttonClass="blue"
                            modalButtonText="Transfer"
                            @accepted="submitTransfer"
                            >Transfer</AreYouSure
                        >
                    </div>
                </div>
            </Tab>
            <Tab :show="permissionStore.can('grant client')" name="Client">
                <GrantClient />
            </Tab>
            <Tab
                :show="permissionStore.can('grant membership')"
                name="Membership"
            >
                <GrantMembership />
            </Tab>
            <Tab
                :show="permissionStore.canAny('modify emails', 'recover tfa')"
                name="Support"
            >
                <SupportTab />
            </Tab>
            <Tab name="Logs">
                <LogsTab />
            </Tab>
            <Tab name="Rewards">
                <div class="card">
                    <div class="top orange">Rewards</div>
                    <div class="content">
                        To encourage and reward you for your work, you are
                        awarded points for various moderation tasks.
                        <p>
                            You can exchange points for bucks at a rate of 1.5
                            points per 1 buck, with a minimum exchange amount of
                            300 points.
                        </p>
                        <div class="block">
                            <input
                                placeholder="Points"
                                min="300"
                                v-model="numExchangePoints"
                                style="margin-bottom: 10px"
                            />
                        </div>
                        <button
                            type="submit"
                            @click="exchangePoints()"
                            class="orange"
                        >
                            Exchange
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="top red">Leaderboard</div>
                    <div class="content">
                        <table style="width: 100%">
                            <tbody>
                                <tr v-for="(admin, i) in leaderboard" :key="i">
                                    <td style="width: 20%">#{{ i + 1 }}</td>
                                    <td style="width: 60%">
                                        <a :href="`/user/${admin.id}`">{{
                                            admin.username
                                        }}</a>
                                    </td>
                                    <td style="width: 20%">
                                        {{ admin.admin_points }} points
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </Tab>
        </VerticalTabs>
    </div>
</template>

<script setup lang="ts">
import Tab from "@/components/global/tabs/Tab.vue";
import VerticalTabs from "@/components/global/tabs/VerticalTabs.vue";
import AreYouSure from "@/components/global/AreYouSure.vue";

import Pending from "./tabs/Pending.vue";
import Reports from "./tabs/Reports.vue";
import GrantClient from "./tabs/GrantClient.vue";
import GrantMembership from "./tabs/GrantMembership.vue";
import SupportTab from "./tabs/SupportTab.vue";
import LogsTab from "./tabs/LogsTab.vue";
import { onMounted, reactive, ref } from "vue";
import { permissionStore } from "@/store/modules/permission";
import axios from "axios";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";

const leaderboard = ref<any>([]);

const transfer = reactive({
    crate_id: undefined,
    from_user: undefined,
    to_user: undefined,
});

const loaded = ref<boolean>(false);

onMounted(async () => {
    await permissionStore.loadCan(
        "accept items",
        "accept clans",
        "view reports",
        "grant currency",
        "grant items",
        "transfer crate",
        "manage shop",
        "grant client",
        "grant membership",
        "modify emails",
        "recover tfa"
    );
    loaded.value = true;
    load();
});

function load() {
    axios.get(`/api/admin/leaderboard`).then(({ data }) => {
        leaderboard.value = data;
    });
}

function submitTransfer() {
    axios
        .post(`/admin/transfer`, {
            from_user: transfer.from_user,
            to_user: transfer.to_user,
            crate_id: transfer.crate_id,
        })
        .then(() => successToNotification("Item transferred to user"))
        .catch(axiosSendErrorToNotification);
}

const currencyUsername = ref<string>();
const currencyType = ref<string>();
const currencyQuantity = ref<number>();
const currencyAction = ref<string>();
function changeCurrency() {
    axios
        .post(`/admin/currency`, {
            username: currencyUsername.value,
            currency: currencyType.value,
            quantity: currencyQuantity.value,
            action: currencyAction.value,
        })
        .then(() => successToNotification("Currency transferred"))
        .catch(axiosSendErrorToNotification);
}

const grantItemId = ref<number>();
const grantItemUsername = ref<string>();
function grantItem() {
    axios
        .post(`/admin/grant`, {
            item_id: grantItemId.value,
            username: grantItemUsername.value,
        })
        .then(() => successToNotification("Item granted"))
        .catch(axiosSendErrorToNotification);
}

const numExchangePoints = ref<number>();
function exchangePoints() {
    axios
        .post(`/admin/points`, {
            points: numExchangePoints.value,
        })
        .then(() => successToNotification("Points exchanged"))
        .catch(axiosSendErrorToNotification);
}
</script>
