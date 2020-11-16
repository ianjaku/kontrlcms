import schema from "./schema";
import {EditorState} from "prosemirror-state";
import {EditorView} from "prosemirror-view";
import {history, undo, redo} from "prosemirror-history";
import {keymap} from "prosemirror-keymap";
import {baseKeymap} from "prosemirror-commands";
import {DOMParser} from "prosemirror-model";
import {dropCursor} from "prosemirror-dropcursor";
import {gapCursor} from "prosemirror-gapcursor";
import {buildInputRules} from "./inputrules";
import {BlockMenuPlugin} from "./blockmenu";
import {AutoSavePlugin} from "./autosave";


class Editor {

    constructor(el) {
        this.el = el;
        this.name = this.el.dataset.simplecmsName;

        const contentEl = document.createElement("div");
        contentEl.innerHTML = this.el.innerHTML;
        this.el.innerHTML = "";

        // A list of functions that have to be called when readOnly changes
        this.readonlyListeners = [];

        this.state = EditorState.create({
            schema,
            plugins: [
                history(),
                keymap({"Mod-z": undo, "Mod-y": redo}),
                keymap(baseKeymap),
                dropCursor(),
                gapCursor(),
                buildInputRules(schema),
                BlockMenuPlugin(this.readonlyListeners),
                AutoSavePlugin(data => {
                    fetch('/simplecms/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            page: PAGE_NAME,
                            name: this.name,
                            value: data
                        })
                    })
                })
            ],
            doc: DOMParser.fromSchema(schema).parse(contentEl)
        });
        this.view = new EditorView(el, {state: this.state});
    }

    setReadOnly(value) {
        this.readonlyListeners.forEach(rol => rol(value));
        this.view.dom.contentEditable = (value ? "true" : "false");
    }

}

export default Editor;