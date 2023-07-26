import { registerComponents, registerGlobal } from "./registerComponents";
import type { BH, userInformation } from "./types/base";

declare global {
    interface Window {
        BH: BH;
    }
}

window.BH = {
    sprite_sheets: {},
    loaded_sprite_sheets: {},
    storage_domain: process.env.STORAGE_DOMAIN,
    avatar_location: `${process.env.STORAGE_DOMAIN}${process.env.STORAGE_AVATARS_LOC}`,
    item_location: `${process.env.STORAGE_DOMAIN}${process.env.STORAGE_ITEMS_LOC}`,
    img_pending_512: `${process.env.STORAGE_DOMAIN}${process.env.STORAGE_PENDING_512}`,
    img_declined_512: `${process.env.STORAGE_DOMAIN}${process.env.STORAGE_DECLINED_512}`,
    img_pending_set: `${process.env.STORAGE_DOMAIN}${process.env.STORAGE_PENDING_SET}`,
    img_declined_set: `${process.env.STORAGE_DOMAIN}${process.env.STORAGE_DECLINED_SET}`,

    api_domain: process.env.API_URL || "https://api.brick-hill.com",

    main_account_id: Number(process.env.MAIN_ACCOUNT_ID || "1003"),
    stripe_public: process.env.STRIPE_PUBLIC || "",

    csrf: document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content"),
    user: getUserInformation(),

    apiUrl: (path: string): string => `${window.BH.api_domain}/${path}`,
    avatarImg: (id: string): string =>
        `${window.BH.api_domain}/v1/thumbnails/single?type=1&id=${id}`,
    assetImg: (hash: string): string => `${window.BH.item_location}${hash}.png`,
};

function getUserInformation(): userInformation | undefined {
    let data: any = Object.assign(
        {},
        (
            (document.querySelector('meta[name="user-data"]') as HTMLElement) ||
            {}
        ).dataset
    );

    if (data.authenticated !== "true") {
        return undefined;
    }

    // TODO: if a user is unauthenticated return a different type to prevent undefined errors by checking for user.authenticated?
    // TODO: not really sure how to implement something like that
    // TODO: solved? instead of using an authenticated value it is simply undefined, auth status can be resolved with !BH.user
    let parsedData: userInformation = {
        id: Number(data.id),
        username: data.username,
        admin: data.admin === "true",

        bits: Number(data.bits),
        bucks: Number(data.bucks),
        taxRate: Number(data.taxRate),
        membership: Number(data.membership),
    };

    return parsedData;
}

// TODO: these should never be used
// TODO: they were a bad idea, dont add more
const globalComponents = {
    Notification: () =>
        import(
            /* webpackMode: "eager" */ "./components/global/Notification.vue"
        ),
    AreYouSure: () =>
        import(/* webpackMode: "eager" */ "./components/global/AreYouSure.vue"),
    Countdown: () =>
        import(/* webpackMode: "eager" */ "./components/global/Countdown.vue"),
    Dropdown: () =>
        import(/* webpackMode: "eager" */ "./components/global/Dropdown.vue"),
    Modal: () =>
        import(/* webpackMode: "eager" */ "./components/global/Modal.vue"),

    Tabs: () =>
        import(/* webpackMode: "eager" */ "./components/global/tabs/Tabs.vue"),
    Tab: () =>
        import(/* webpackMode: "eager" */ "./components/global/tabs/Tab.vue"),

    SvgSprite: () =>
        import(/* webpackMode: "eager" */ "./components/global/SvgSprite.vue"),
};

const components = {
    MainFooter: () =>
        import(/* webpackMode: "eager" */ "./components/global/MainFooter.vue"),

    ShopPage: () => import("./components/shop/main/ShopPage.vue"),
    ItemPage: () => import("./components/shop/item/ItemPage.vue"),
    UploadItem: () => import("./components/shop/create/UploadItem.vue"),
    EditItem: () => import("./components/shop/create/EditItem.vue"),

    Membership: () => import("./components/membership/Membership.vue"),

    DownloadClient: () => import("./components/client/Download.vue"),

    Transactions: () => import("./components/user/Transactions.vue"),
    AvatarEditor: () => import("./components/user/editor/AvatarEditor.vue"),
    Settings: () => import("./components/user/Settings.vue"),
    TfaCard: () => import("./components/user/TfaCard.vue"),
    BannedBilling: () => import("./components/user/BannedBilling.vue"),

    Trade: () => import("./components/trades/sending/Trade.vue"),
    ViewTrades: () => import("./components/trades/ViewTrades.vue"),
    TradeList: () => import("./components/trades/TradeList.vue"),

    ProfileDropdown: () => import("./components/profile/ProfileDropdown.vue"),
    Crate: () => import("./components/profile/Crate.vue"),
    Sets: () => import("./components/profile/Sets.vue"),

    PlayPage: () => import("./components/sets/play/PlayPage.vue"),
    SetPage: () => import("./components/sets/play/SetPage.vue"),
    EditSet: () => import("./components/sets/edit/EditSet.vue"),

    Comments: () => import("./components/polymorphic/Comments.vue"),
    Favorite: () => import("./components/polymorphic/Favorite.vue"),

    RedeemPromo: () => import("./components/event/RedeemPromo.vue"),
    Mover: () => import("./components/event/Mover.vue"),

    BlogCard: () => import("./components/user/BlogCard.vue"),
};

registerComponents(components);
registerGlobal(globalComponents);
