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

        this.state = EditorState.create({
            schema,
            plugins: [
                history(),
                keymap({"Mod-z": undo, "Mod-y": redo}),
                keymap(baseKeymap),
                dropCursor(),
                gapCursor(),
                buildInputRules(schema),
                BlockMenuPlugin(),
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

}

export default Editor;