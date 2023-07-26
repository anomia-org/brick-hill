import { BH } from "@/logic/bh";
import ThumbnailType from "@/logic/data/thumbnails";
import axios from "axios";
import { Store } from "../store";

type ThumbnailState =
    | "pending"
    | "declined"
    | "approved"
    | "awaiting"
    | "awaiting_approval";

interface ThumbnailRetrieve {
    id: number;
    type: ThumbnailType;
    size?: string;
    admin?: boolean;
}

interface QueuedThumbnail {
    data: ThumbnailRetrieve;
    attempts: number;
}

interface Thumbnail {
    state: ThumbnailState;
    thumbnail: string;
}

interface Thumbnails extends Object {
    thumbnails: {
        [key: string]: Thumbnail;
    };
}

class ThumbnailStore extends Store<Thumbnails> {
    protected data(): Thumbnails {
        return {
            thumbnails: {},
        };
    }

    queuedThumbnails: { [key: string]: QueuedThumbnail } = {};
    protected flushDelay: number = 100;
    protected pendingFlushDelay: number = 3000;
    protected flusher: any;

    protected normalUrl: string = BH.apiUrl(`v1/thumbnails/bulk`);
    protected adminUrl: string = `/v1/admin/thumbnails/bulk`;

    protected shouldUseAdmin: boolean = false;

    getKey(data: ThumbnailRetrieve): string {
        return `${data.id}:${data.type}`;
    }

    getThumbnail(data: ThumbnailRetrieve): string {
        let key = this.getKey(data);
        if (typeof this.state.thumbnails[key] === "undefined")
            this.retrieveThumbnail(data);

        return this.getState().thumbnails[key].thumbnail;
    }

    refreshThumbnail(data: ThumbnailRetrieve): void {
        let key = this.getKey(data);
        if (typeof this.state.thumbnails[key] !== "undefined") {
            delete this.state.thumbnails[key];
        }

        this.getThumbnail(data);
    }

    retrieveThumbnail(data: ThumbnailRetrieve) {
        this.state.thumbnails[this.getKey(data)] = {
            state: "awaiting",
            // TODO: need to store the data in JS about what the proper pending image should be for the given type
            thumbnail: BH.img_pending_512,
        };

        this.queuedThumbnails[this.getKey(data)] = {
            data,
            attempts: 0,
        };

        if (data.admin) {
            this.shouldUseAdmin = true;
        }

        this.callFlusher();
    }

    callFlusher(delay: number = this.flushDelay) {
        clearTimeout(this.flusher);
        this.flusher = setTimeout(this.flushThumbnails.bind(this), delay);
    }

    async flushThumbnails() {
        let tooMany = Object.keys(this.queuedThumbnails).length > 100;
        let toFlush = Object.keys(this.queuedThumbnails).slice(0, 99);
        let postData = toFlush.map((key) => this.queuedThumbnails[key].data);
        let url = this.normalUrl;
        if (this.shouldUseAdmin) {
            url = this.adminUrl;
        }

        await axios
            .post(url, {
                thumbnails: postData,
            })
            .then(({ data }) => {
                for (let thumbData of data.data) {
                    switch (thumbData.state) {
                        case "pending":
                            // the flusher can be overwritten by a new thumbnail request causing it to attempt++ before it should
                            // not too important to fix as pending thumbnails arent the end of the world
                            this.queuedThumbnails[thumbData.key].attempts++;
                            if (
                                this.queuedThumbnails[thumbData.key].attempts >
                                3
                            ) {
                                delete this.queuedThumbnails[thumbData.key];
                            } else {
                                this.callFlusher(this.pendingFlushDelay);
                            }
                            break;
                        // all states should simply just set the thumbnail, unless they were intented to do something special on the frontend
                        default:
                            delete this.queuedThumbnails[thumbData.key];
                            this.state.thumbnails[thumbData.key] = {
                                state: thumbData.state,
                                thumbnail: thumbData.thumbnail,
                            };
                    }
                }
            });

        // the api can only accept 100 values, if we have over 100 the api needs to be called again
        if (tooMany) this.callFlusher();
    }
}

export const thumbnailStore = new ThumbnailStore();
