type userInformation = {
    id: number;
    username: string;
    admin: boolean;

    bits: number;
    bucks: number;
    taxRate: number;
    membership: number;
};

type BH = {
    user?: userInformation;

    sprite_sheets: { [key: string]: string };
    loaded_sprite_sheets: { [key: string]: boolean };

    storage_domain: string | undefined;
    avatar_location: string;
    item_location: string;
    img_pending_512: string;
    img_declined_512: string;
    img_pending_set: string;
    img_declined_set: string;

    api_domain: string;

    main_account_id: number;
    stripe_public: string;

    csrf: string | null | undefined;

    apiUrl: (path: string) => string;
    avatarImg: (path: string) => string;
    assetImg: (path: string) => string;
};

export { userInformation, BH };
