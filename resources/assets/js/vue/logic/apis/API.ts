import { Ref, ref } from "vue";
import { BH } from "@/logic/bh";

export interface URLParam {
    key: string;
    value: any;
}

export interface APIData<T> {
    data: T[];
    next_cursor: string | null;
    previous_cursor: string | null;
}

export abstract class API<T> {
    apiUrl: string;
    useAPISubdomain: boolean;
    apiLimit: number;
    startingCursor: string;
    loadingURLs: Set<string>;
    currentParams: URLParam[];
    cachedData: Ref<{ [key: string]: APIData<T> }>;
    currentData: Ref<T[]>;
    pageNumber: Ref<number>;

    encParams: string;

    constructor(
        url: string,
        limit: number = 10,
        startingCursor: string = "",
        useAPISubdomain: boolean = true
    ) {
        this.useAPISubdomain = useAPISubdomain;
        if (this.useAPISubdomain) {
            this.apiUrl = BH.apiUrl(url);
        } else {
            this.apiUrl = url;
        }
        this.apiLimit = limit;
        this.startingCursor = startingCursor;
        this.cachedData = ref<{ [key: string]: APIData<T> }>({}) as Ref<{
            [key: string]: APIData<T>;
        }>;
        this.currentData = ref<T[]>([]) as Ref<T[]>;
        this.pageNumber = ref<number>(1);

        this.loadingURLs = new Set();
        this.currentParams = [];
        this.encParams = "";
    }

    /**
     * Takes in the Params wanted and sorts them to ensure variations in the order wont change the cache key and then encodes it into a single string to be used later.
     *
     * TODO: swap to https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams
     *
     * @param params
     */
    protected paramsToURL(params: URLParam[]): string {
        let encoded: string[] = [];

        // the data is cached based on the params
        // so we do a simple sort on the params to ensure they are reasonably in the same order
        // sort by the value of the param since those are more likely to be unique than the key
        const orderedParams = params.sort((a, b) => {
            if (a.value > b.value) return 1;
            if (a.value < b.value) return -1;
            return 0;
        });

        orderedParams.forEach((param) => {
            encoded.push(`${param.key}=${encodeURIComponent(param.value)}`);
        });

        return encoded.join("&");
    }

    /**
     * Modify the URL of the API for APIs that ask for some params in the url
     *
     * @param url
     * @param dontRefresh
     */
    public setUrl(url: string, dontRefresh: boolean = false) {
        let newUrl = url;
        if (this.useAPISubdomain) {
            newUrl = BH.apiUrl(url);
        }

        // if they set it to the same thing dont refresh
        if (this.apiUrl != newUrl) {
            this.apiUrl = newUrl;
            if (!dontRefresh) {
                this.refreshAPI();
            }
        }
    }

    /**
     * Get the cache key for the attempted page. Assumes the current stored params are the ones wanted.
     *
     * @param pageNumber
     */
    public getCacheKey(pageNumber: number): string {
        // TODO: url and limit arent accounted for here
        return `${this.apiUrl}:${pageNumber}:${this.encParams}`;
    }

    /**
     * Add the given pageNumber and apiData to the cache.
     *
     * @param pageNumber
     * @param apiData
     */
    public addToCache(pageNumber: number, apiData: APIData<T>) {
        this.cachedData.value[this.getCacheKey(pageNumber)] = apiData;
    }

    /**
     * Returns the cached data for a given page. Will return null if not stored.
     *
     * @param pageNumber
     */
    public getFromCache(pageNumber: number): APIData<T> | undefined {
        return this.cachedData.value[this.getCacheKey(pageNumber)];
    }

    /**
     * Refresh the API. Removes all data from cache.
     */
    public abstract refreshAPI(): void;

    /**
     * Set the parameters being used to request. Will automatically encode them and then request a new page.
     */
    public abstract setParams(params: URLParam[]): void;

    /**
     * Take in the given cursor and params and generate a requestable URL
     *
     * @param cursor
     * @param params
     */
    public getURL(cursor: string | null, params: string) {
        return `${this.apiUrl}?limit=${this.apiLimit}&cursor=${cursor}&${params}`;
    }

    /**
     * Returns if the API has an available next page based on if next_cursor exists.
     */
    public abstract hasNextPage(): boolean;
}
