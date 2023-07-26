import { Store } from "../store";

type NotificationType = "success" | "warning" | "error";

interface Notification extends Object {
    notification?: {
        msg: string;
        type: NotificationType;
    };
}

class NotificationStore extends Store<Notification> {
    protected data(): Notification {
        return {};
    }

    setNotification(msg: string, type: NotificationType) {
        this.state.notification = {
            msg,
            type,
        };
    }
}

export const notificationStore = new NotificationStore();
