import {schema} from "./schema";
import {EditorState} from "prosemirror-state";
import {EditorView} from "prosemirror-view";
import {history, undo, redo} from "prosemirror-history";
import {keymap} from "prosemirror-keymap";
import {baseKeymap} from "prosemirror-commands";
import {DOMParser, Schema} from "prosemirror-model";
import {dropCursor} from "prosemirror-dropcursor";
import {gapCursor} from "prosemirror-gapcursor";
import {buildInputRules} from "./inputrules";
import {BlockMenuPlugin} from "./blockmenu";
import {AutoSavePlugin} from "./autosave";
import {addListNodes} from "prosemirror-schema-list";
import {buildKeymap} from "./keymap";
import {menuBar} from "prosemirror-menu";
import {buildMenuItems} from "./menubar";
import changeImagePlugin from "./changeimageplugin";


class Editor {

    constructor(el) {
        this.el = el;
        this.name = this.el.dataset.simplecmsName;

        const contentEl = document.createElement("div");
        contentEl.innerHTML = this.el.innerHTML;
        this.el.innerHTML = "";

        // A list of functions that have to be called when readOnly changes
        this.readonlyListeners = [
            (val) => {
                if (val) this.el.classList.remove("simplecms__editor--readonly");
                else this.el.classList.add("simplecms__editor--readonly");
            }
        ];

        const mySchema = new Schema({
            nodes: addListNodes(schema.spec.nodes, "paragraph block*", "block"),
            marks: schema.spec.marks
        });

        this.state = EditorState.create({
            mySchema,
            plugins: [
                buildInputRules(schema),
                // keymap({"Mod-z": undo, "Mod-y": redo}),
                keymap(buildKeymap(mySchema)),
                keymap(baseKeymap),
                history(),
                dropCursor(),
                gapCursor(),
                menuBar({
                    floating: true,
                    content: buildMenuItems(mySchema)
                }),
                changeImagePlugin(),
                // BlockMenuPlugin(this.readonlyListeners),
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
            doc: DOMParser.fromSchema(mySchema).parse(contentEl)
        });
        this.view = new EditorView(el, {state: this.state});
    }

    setReadOnly(value) {
        this.readonlyListeners.forEach(rol => rol(value));
        this.view.dom.contentEditable = (value ? "true" : "false");
    }

}

export default Editor;