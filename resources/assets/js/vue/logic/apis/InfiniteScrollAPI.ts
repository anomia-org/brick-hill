import axios from "axios";
import { Ref, ref } from "vue";
import { API, URLParam, APIData } from "./API";

export default class InfiniteScrollAPI<T> extends API<T> {
    nextCursor: Ref<string | null>;

    constructor(
        url: string,
        limit: number = 10,
        startingCursor: string = "",
        useAPISubdomain: boolean = true
    ) {
        super(url, limit, startingCursor, useAPISubdomain);

        this.nextCursor = ref(startingCursor);
        this.pageNumber = ref(0);
    }

    refreshAPI() {
        this.currentData.value = [];
        this.nextCursor.value = "";
        this.loadingURLs.clear();
        this.loadNextPage();
    }

    setParams(params: URLParam[]) {
        this.currentParams = params;
        this.nextCursor.value = "";
        this.encParams = this.paramsToURL(params);
        this.currentData.value = [];
        this.pageNumber.value = 0;
        this.loadNextPage();
    }

    async loadNextPage() {
        if (!this.hasNextPage()) return;

        let cache = this.getFromCache(this.pageNumber.value + 1);
        if (typeof cache !== "undefined") {
            this.currentData.value.push(...cache.data);
            this.nextCursor.value = cache.next_cursor;
            this.pageNumber.value = this.pageNumber.value + 1;
            return;
        }

        let params = this.encParams;

        let URL = this.getURL(this.nextCursor.value, params);
        if (this.loadingURLs.has(URL)) return;

        this.loadingURLs.add(URL);

        await axios.get<APIData<T>>(URL).then(({ data }) => {
            this.pageNumber.value++;
            this.addToCache(this.pageNumber.value, data);

            this.currentData.value.push(...data.data);
            this.nextCursor.value = data.next_cursor;
        });

        this.loadingURLs.delete(URL);
    }

    hasNextPage(): boolean {
        return this.nextCursor.value !== null;
    }
}
