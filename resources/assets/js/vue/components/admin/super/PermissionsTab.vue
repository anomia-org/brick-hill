<template>
    <div>
        <div v-if="loaded">
            <div class="card">
                <div class="top blue">Roles</div>
                <div class="content">
                    <table class="margin-5">
                        <tr>
                            <th>Role</th>
                            <th
                                v-for="permission in permissions"
                                :key="permission.id"
                                scope="col"
                            >
                                {{ permission.name }}
                            </th>
                        </tr>
                        <tr v-for="(role, i) in roles" :key="role.id">
                            <th scope="row">
                                <input v-model="role.name" />
                                <i
                                    v-if="role.new"
                                    class="fas fa-minus pointer"
                                    @click="roles.splice(i, 1)"
                                ></i>
                            </th>
                            <th
                                v-for="permission in permissions"
                                :key="permission.id"
                            >
                                <input
                                    type="checkbox"
                                    v-model="role.permissions"
                                    :value="permission"
                                />
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <i
                                    class="fas fa-plus pointer"
                                    @click="
                                        roles.push({
                                            id: roles.length + 1,
                                            name: null,
                                            permissions: [],
                                            new: true,
                                        })
                                    "
                                ></i>
                            </th>
                        </tr>
                    </table>
                    <AreYouSure @accepted="saveRoles">Save</AreYouSure>
                </div>
            </div>
            <div class="card">
                <div class="top blue">User Roles</div>
                <div class="content">
                    <div>
                        <input
                            v-model="newUser"
                            placeholder="User ID"
                            class="block margin-5"
                        />
                        <button @click="addNewUser" class="green margin-5">
                            New user
                        </button>
                    </div>
                    <div
                        v-for="user in users"
                        :key="user.id"
                        class="col-1-1 margin-5"
                    >
                        <div>
                            <div
                                class="col-1-5"
                                :class="{ strike: user.roles.length == 0 }"
                            >
                                <a :href="`/user/${user.id}`">{{
                                    user.username
                                }}</a>
                            </div>
                            <div class="col-1-3">
                                <select
                                    v-model="user.roles"
                                    :disabled="authedUser.power <= user.power"
                                    multiple
                                    class="width-100 margin-5"
                                >
                                    <option
                                        v-for="role in roles.filter((role: any) => !role.new)"
                                        :key="role.id"
                                        :value="role.id"
                                    >
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-1-3">
                                <input
                                    v-model="user.power"
                                    :disabled="authedUser.power <= user.power"
                                    type="number"
                                    min="1"
                                    :max="authedUser.power - 1"
                                />
                                <AreYouSure @accepted="saveUser(user)"
                                    >Save</AreYouSure
                                >
                            </div>
                        </div>
                    </div>
                    <AreYouSure @accepted="saveUsers">Save</AreYouSure>
                </div>
            </div>
        </div>
        <div v-else class="text-center">Loading</div>
    </div>
</template>

<style scoped lang="scss">
table,
th,
td {
    position: relative;
    border-collapse: collapse;
    padding: 5px;
    background: #fff;
    color: #000;
}
th {
    border: 1px solid;
    position: -webkit-sticky;
    position: sticky;

    &:first-child {
        left: 0;
        z-index: 1;
    }
}
.strike {
    text-decoration: line-through;
}
</style>

<script setup lang="ts">
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { BH } from "@/logic/bh";
import axios from "axios";
import { computed, ref } from "vue";
import AreYouSure from "../../global/AreYouSure.vue";

const apiPath = "/v1/admin/permissions";
const loaded = ref<boolean>(false);
const permissions = ref<any>([]);
const roles = ref<any>([]);
const users = ref<any>([]);
const newUser = ref<any>(null);

const authedUser = computed(() => {
    return users.value.find((user: any) => user.id == BH.user?.id);
});

loadData();

function addNewUser() {
    if (
        !newUser.value ||
        users.value.find((user: any) => user.id == newUser.value)
    )
        return;
    axios
        .get(BH.apiUrl(`v1/user/profile?id=${newUser.value}`))
        .then(({ data }) => {
            // run another check to make sure no button spam
            if (users.value.find((user: any) => user.id == newUser.value))
                return;
            users.value.push({
                id: data.id,
                username: data.username,
                permissions: [],
                roles: [],
                power: 1,
            });
        })
        .catch(axiosSendErrorToNotification);
}

function loadData() {
    // hope we never reach 100 admin accounts
    axios
        .all([
            axios.get(`${apiPath}/all`),
            axios.get(`${apiPath}/roles`),
            axios.get(`${apiPath}/users?limit=100`),
        ])
        .then(
            axios.spread((permissionsData, rolesData, usersData) => {
                permissions.value = permissionsData.data.data;
                roles.value = rolesData.data.data;
                users.value = usersData.data.data;
            })
        )
        .then(() => {
            loaded.value = true;
        })
        .catch(axiosSendErrorToNotification);
}

function saveRoles() {
    axios
        .post(`${apiPath}/save/roles`, {
            roles: roles.value,
        })
        .catch(axiosSendErrorToNotification);
}

function saveUsers() {
    axios
        .post(`${apiPath}/save/users`, {
            users: users.value,
        })
        .catch(axiosSendErrorToNotification);
}

function saveUser(user: any) {
    axios
        .post(`${apiPath}/save/user`, {
            user_id: user.id,
            user_power: user.power,
            user_roles: user.roles,
        })
        .catch(axiosSendErrorToNotification);
}
</script>
