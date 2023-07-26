import { Store } from "@/store/store";

interface SetEdit extends Object {
    serverType?: string;
}

class SetEditStore extends Store<SetEdit> {
    protected data(): SetEdit {
        return {};
    }

    setServerType(type: string) {
        this.state.serverType = type;
    }
}

export const setEditStore = new SetEditStore();
