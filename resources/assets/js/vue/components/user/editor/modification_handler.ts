import axios from "axios";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { BH } from "@/logic/bh";
import { ref } from "vue";
import { thumbnailStore } from "@/store/modules/thumbnails";

let previousStates: UndoState[] = [];
let redos: UndoState[] = [];

interface RenderData {
    rebase: boolean;
    colors: any;
    instructions: Modification[];
}

export interface Modification {
    type: string;
    value: any;
}

export interface UndoState {
    undo: Modification[];
    redo: Modification[];
}

export function useModifications(thumbnailData: any) {
    thumbnailStore.getThumbnail(thumbnailData);

    const avatarData = ref<any>({});
    const avatarLoading = ref<boolean>(false);

    // if another change is within this time it will be queued
    const flushTime = 3 * 1000;
    // how long the delay is before flushing the queue
    const flushDelay = 750;
    let lastModification = new Date();
    lastModification.setTime(0);

    let modHistory: Modification[] = [];
    let flusher: any; // not sure on the type of a setTimeout

    function newModification(modifications: UndoState) {
        if (Object.keys(modifications.redo).length == 0) return;

        pushState(modifications);
        applyModifications(modifications.redo);
    }

    function pushState(modifications: UndoState) {
        previousStates.unshift(modifications);
        redos = [];
    }

    function undo() {
        if (previousStates.length == 0) return;
        let undoState = previousStates[0];
        previousStates.shift();

        // TODO: undo functionality gets a bit off when you select hats while at the limit
        // TODO: not really a big deal, probably not worth fixing

        redos.unshift(undoState);

        applyModifications(undoState.undo);
    }

    function redo() {
        if (redos.length == 0) return;
        let redoState = redos[0];
        redos.shift();

        previousStates.unshift(redoState);

        applyModifications(redoState.redo);
    }

    function applyModifications(modifications: Modification[]) {
        modHistory.push(...modifications);

        avatarLoading.value = true;
        if (lastModification.getTime() > Date.now() - flushTime) {
            clearTimeout(flusher);
            lastModification = new Date();
            flusher = setTimeout(flushModifications, flushDelay);
        } else {
            // TODO: maybe it should wait ~200ms for a new instruction?
            // TODO: not very much delay, but could improve reactivity if clicking 2 items next to each other
            flushModifications();
        }
    }

    async function flushModifications() {
        // if this function gets called while the timeout is being waited on it could cause loading to be set to false when its about to send a new request
        avatarLoading.value = true;

        let data: RenderData = {
            rebase: false,
            colors: {},
            instructions: [],
        };
        lastModification = new Date();
        for (let modification of modHistory) {
            switch (modification.type) {
                case "rebase":
                    data.rebase = true;
                    break;
                case "wearOutfit":
                    // throw away the instructions before the outfit change because it will be replaced by the outfit anyway
                    data.instructions = [];
                    data.instructions.push(modification);
                    break;
                case "wear":
                case "remove":
                case "reorderClothing":
                    data.instructions.push(modification);
                    break;
                default:
                    avatarData.value.colors[modification.type] =
                        modification.value;
                    data.colors[modification.type] = modification.value;
            }
        }
        modHistory = [];

        // TODO: pause flush attempts while processing?
        // TODO: stuff might build up here while the site is being slow
        await axios
            .post(BH.apiUrl(`v1/user/render/process`), data)
            .then(({ data }) => {
                thumbnailStore.refreshThumbnail(thumbnailData);
            })
            .catch(axiosSendErrorToNotification);

        avatarLoading.value = false;

        updateWearing();
    }

    function updateWearing() {
        axios
            .get(BH.apiUrl(`v1/user/${BH.user?.id}/wearing`))
            .then(({ data }) => {
                avatarData.value = data.data;
            });
    }

    return {
        newModification,
        updateWearing,
        redo,
        undo,
        avatarData,
        avatarLoading,
    };
}

// you wear a face
// id 1, previously wearing id 2
// {"undo": {2: "wear"}, "redo": {1: "wear"}}
// you wear a face
// id 1, previously wearing id 0
// {"undo": {1: "remove"}, "redo": {1: "wear"}}
// you wear a hat
// id 1, wearing no other hats
// {"undo": {1: "remove"}, "redo": {1: "wear"}}
// you wear a hat
// id 1, wearing 5 other hats, last one being id 2
// {"undo": {1: "remove", 2: "wear"}, "redo": {2: "remove", 1: "wear"}}
// you remove any type, all should be the same
// id 1
// {"undo": {1: "wear"}, "redo": {1: "remove"}}
