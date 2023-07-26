import "axios";

declare module "axios" {
    export interface AxiosRequestConfig {
        removeXsrfToken?: boolean;
    }
}
