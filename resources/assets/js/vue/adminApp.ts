import { registerComponents } from "./registerComponents";

const admin_components = {
    AdminPanel: () =>
        import(
            /* webpackMode: "eager" */ "./components/admin/panel/AdminPanel.vue"
        ),
    ShopScheduler: () =>
        import(
            /* webpackMode: "eager" */ "./components/admin/panel/tabs/scheduler/ShopScheduler.vue"
        ),
};

registerComponents(admin_components);
