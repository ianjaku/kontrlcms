import {Plugin} from "prosemirror-state";
import {DOMSerializer} from "prosemirror-model";
import {debounce} from "lodash";


class AutoSave {
    constructor(view, saveCallback) {
        this.view = view;
        this.saveCallback = saveCallback;
        this.serializer = DOMSerializer.fromSchema(view.state.schema);
        this.saveData = debounce(() => {
            const fragment = this.serializer.serializeFragment(this.view.state.doc.content);
            const testEl = document.createElement("div");
            testEl.append(fragment);
            this.saveCallback(testEl.innerHTML);
        }, 250)
    }

    update(view, lastState) {
        this.saveData()
    }
}

export const AutoSavePlugin = (saveCallback) => {
    return new Plugin({
        view(editorView) {
            return new AutoSave(editorView, saveCallback);
        }
    })
};