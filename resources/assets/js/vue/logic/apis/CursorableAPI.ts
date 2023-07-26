import axios from "axios";
import { API, URLParam, APIData } from "./API";

export default class CursorableAPI<T> extends API<T> {
    refreshAPI() {
        this.cachedData.value = {};
        this.loadingURLs.clear();
        this.loadPage(1);
    }

    setParams(params: URLParam[]) {
        this.currentParams = params;
        this.encParams = this.paramsToURL(params);
        this.loadPage(1);
    }

    async loadPage(page: number) {
        let params = this.encParams;
        let cache = this.getFromCache(page);
        if (typeof cache !== "undefined") {
            this.currentData.value = cache.data;
            this.pageNumber.value = page;
            return;
        }

        let cursor: string | null = this.startingCursor;
        if (page > 1) {
            let previousPage = this.getFromCache(page - 1);
            // TODO: allow the API to find the first loaded page and then loop to the current page?
            // TODO: could also modify the apiLimits to scan through pages faster, as cursors can be easily modified locally
            if (typeof previousPage === "undefined") {
                throw new Error(
                    "CursorableAPI attempting to get pages out of order"
                );
            }
            cursor = previousPage.next_cursor;
        }

        let URL = this.getURL(cursor, params);
        if (this.loadingURLs.has(URL)) return;

        this.loadingURLs.add(URL);

        await axios.get<APIData<T>>(URL).then(({ data }) => {
            this.addToCache(page, data);

            // TODO: fix ordering issue
            // ex user clicks one tab and then quickly swaps to another before the request finishes
            // and the first tabs request takes longer to complete than the last
            // TODO: need to change retrieval of this class, this shouldnt set currentData but instead only set the cachedData
            // TODO: consuming component should then just read the data it wants from the cachedData
            this.currentData.value = data.data;
            this.pageNumber.value = page;
        });

        this.loadingURLs.delete(URL);
    }

    hasNextPage(): boolean {
        return !!this.getFromCache(this.pageNumber.value)?.next_cursor;
    }
}
