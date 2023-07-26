import { registerComponents } from "./registerComponents";

const super_admin_components = {
    SuperAdmin: () =>
        import(/* webpackMode: "eager" */ "./components/admin/super/Panel.vue"),
};

registerComponents(super_admin_components);
