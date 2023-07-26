import axios from "axios";
import { Store } from "../store";
import { BH } from "@/logic/bh";
import ModelRelation from "@/logic/data/relations";

interface Permission extends Object {
    can: {
        [key: string]: boolean;
    };
    loading: AskingPermission[];
}

type AskingPermission =
    | string
    | {
          permission: string;
          relation: ModelRelation;
          id: number;
      };

class PermissionStore extends Store<Permission> {
    protected data(): Permission {
        return {
            can: {},
            loading: [],
        };
    }

    askingPermissionToKey(permission: AskingPermission): string {
        if (typeof permission === "string") return permission;

        return `${permission.permission}:${permission.id}:${permission.relation}`;
    }

    can(permission: AskingPermission) {
        if (!BH.user?.admin) return false;
        return this.state.can[this.askingPermissionToKey(permission)] || false;
    }

    canAny(...permissions: AskingPermission[]) {
        if (!BH.user?.admin) return false;
        for (let permission of permissions) {
            if (this.state.can[this.askingPermissionToKey(permission)])
                return true;
        }
        return false;
    }

    canAll(...permissions: AskingPermission[]) {
        if (!BH.user?.admin) return false;
        for (let permission of permissions) {
            if (!this.state.can[this.askingPermissionToKey(permission)])
                return false;
        }
        return true;
    }

    async loadCan(...loadPermissions: AskingPermission[]) {
        if (!BH.user?.admin) return;
        if (!Array.isArray(loadPermissions))
            loadPermissions = [loadPermissions];
        // TODO: add a sort of 'debouncer' here that bundles all requested permissions into a single spot and only executes the request after say 100ms of inactivity on appending to that list
        // TODO: improves the request amounts by making it so separate vue components wont each call their own permission request
        // TODO: this function is async so the code can wait for permissions to appear before loading any further
        // TODO: this creates issues with the above debouncer as there would need to be a loop somewhere just waiting for the variable to become available
        // TODO: as creating a timeout to wait for more permissions would be done in a separate state outside of the async function
        // TODO: this function already has a waiting issue like that as only one permission is allowed to load at a time so if two functions attempt to load the same permission
        // TODO: it would return back instantly on the second function as the filter below would recognize its already attempting to be loaded and assume there were no permissions asked for
        // TODO: overall not that many components even call for permissions so 2 requests per page is fine, since it is admin only anyway
        // TODO: i just like writing long overcomplicated notes on issues that are barely issues
        let permissions = loadPermissions.filter(
            (perm) =>
                typeof perm === "string" &&
                !(this.askingPermissionToKey(perm) in this.state.can) &&
                !this.state.loading.includes(this.askingPermissionToKey(perm))
        );
        let model_permissions = loadPermissions.filter(
            (perm) =>
                typeof perm !== "string" &&
                !(this.askingPermissionToKey(perm) in this.state.can) &&
                !this.state.loading.includes(this.askingPermissionToKey(perm))
        );
        this.state.loading.push(...loadPermissions);
        if (permissions.length > 0 || model_permissions.length > 0)
            await axios
                .post(`/admin/permissions`, { permissions, model_permissions })
                .then(({ data }) => {
                    for (let perm in data) {
                        this.state.can[perm] = data[perm];
                    }
                });

        this.state.loading = this.state.loading.filter(
            (perm) => !permissions.includes(perm)
        );
    }
}

export const permissionStore = new PermissionStore();
