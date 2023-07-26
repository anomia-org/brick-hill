import axios from "axios";
import { Store } from "@/store/store";
import { BH } from "@/logic/bh";

interface SendingTrade extends Object {
    cursors: any;
    previous_items: any;
    items: any;
    trade_items: any;
    trade_bucks: any;
}

class SendingTradeStore extends Store<SendingTrade> {
    protected data(): SendingTrade {
        return {
            cursors: {},
            previous_items: {},
            items: {},
            trade_items: {},
            trade_bucks: {},
        };
    }

    isSelected(user: string, crate: any) {
        return (
            this.state.trade_items[user].filter(
                (val: any) => val.id === crate.id
            ).length > 0
        );
    }

    getPreviousItems(user: string, page: number) {
        return this.state.previous_items[user][page];
    }

    getCursor(user: string, page: number) {
        return this.state.cursors[user][page] ?? "";
    }

    hasNextPage(user: string, page: number) {
        return this.state.cursors[user][page] !== null;
    }

    async loadCrate(from: any) {
        let prev = this.getPreviousItems(from.user, from.page);
        if (typeof prev !== "undefined") {
            this.setItems({ user: from.user, data: prev });
            return;
        }
        return axios
            .get(
                BH.apiUrl(
                    `v1/user/${from.user}/crate?type=special&search=${
                        from.search || ""
                    }&cursor=${this.getCursor(from.user, from.page)}&limit=9`
                )
            )
            .then(({ data }) => {
                this.addCursor({
                    user: from.user,
                    page: from.page + 1,
                    cursor: data.next_cursor,
                });
                this.setItems({ user: from.user, data: data.data });
                this.setPreviousItems({ user: from.user, page: from.page });
            });
    }

    toggleItem(data: any) {
        if (typeof data.crate === "undefined") return;
        if (this.isSelected(data.user, data.crate)) this.removeItem(data);
        else this.addItem(data);
    }

    updateBucks(data: any) {
        this.state.trade_bucks[data.user] = data.bucks;
    }

    addItem(data: any) {
        this.state.trade_items[data.user].push(data.crate);
    }

    removeItem(data: any) {
        this.state.trade_items[data.user] = this.state.trade_items[
            data.user
        ].filter((val: any) => val.id !== data.crate.id);
    }

    setUsers(users: any) {
        for (let i of users) {
            this.state.items[i] = [];
            this.state.cursors[i] = {};
            this.state.previous_items[i] = {};
            this.state.trade_items[i] = [];
            this.state.trade_bucks[i] = 0;
        }
    }

    clearPreviousItems(data: any) {
        this.state.previous_items[data.user] = {};
    }

    setPreviousItems(data: any) {
        this.state.previous_items[data.user][data.page] =
            this.state.items[data.user];
    }

    addCursor(data: any) {
        this.state.cursors[data.user][data.page] = data.cursor;
    }

    setItems(data: any) {
        this.state.items[data.user] = data.data;
    }
}

export const sendingTradeStore = new SendingTradeStore();
