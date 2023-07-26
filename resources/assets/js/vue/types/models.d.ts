interface Item {
    id: number;
    creator: {
        id: number;
        username: string;
        avatar_hash: string;
        is_verified_designer: boolean;
    };
    type: {
        id: number;
        type: string;
    };
    tags: {
        id: number;
        name: string;
    }[];
    event: {
        id: number;
        name: string;
        start_date: string;
        end_date: string;
    } | null;
    series_id: number | null;
    is_public: boolean;
    name: string;
    description: string;
    bits: number | null;
    bucks: number | null;
    offsale: boolean;
    special_edition: boolean;
    special: boolean;
    stock: number;
    timer: boolean;
    timer_date: string;
    average_price?: number;
    stock_left?: number;
    cheapest_seller?: {
        crate_id: number;
        serial: number;
        bucks: number;
        user: {
            id: number;
            username: string;
            avatar_hash: string;
            is_verified_designer: boolean;
        };
    };
    thumbnail: string;
    created_at: string;
    updated_at: string;
}

interface Crate {
    id: number;
    serial: number;
    item: {
        id: number;
        name: string;
        thumbnail: string;
        is_special: boolean;
        type_id: number;
    };
    created_at: string;
    updated_at: string;
}

interface ItemCrate {
    id: number;
    serial: number;
    user: {
        id: number;
        username: string;
        last_online: string;
    };
    created_at: string;
    updated_at: string;
}

interface ItemVersion {
    id: number;
    created_at: string;
}
