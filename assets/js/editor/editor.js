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
                BlockMenuPlugin()
            ],
            doc: DOMParser.fromSchema(schema).parse(contentEl)
        });
        this.view = new EditorView(el, {state: this.state});

        // this.menubar = new Menubar();
        // el.appendChild(this.menubar.createMenuEl(this.name))
    }

}

export default Editor;