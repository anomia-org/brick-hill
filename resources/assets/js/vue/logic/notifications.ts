import { notificationStore } from "@/store/modules/notifications";

export function axiosReloadOnJSONSuccess({ data }: any) {
    if (typeof data.success === "undefined") throw "Error in request";
    location.reload();
}

export function successToNotification(success: string) {
    notificationStore.setNotification(success, "success");
}

export function errorToNotification(error: string) {
    notificationStore.setNotification(error, "error");
}

export function axiosSendErrorToNotification(err: any) {
    notificationStore.setNotification(
        err.response
            ? err.response.data.error.prettyMessage
            : err || "Unknown error",
        "error"
    );
}
