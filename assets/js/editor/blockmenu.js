/*
    The menu that gets shown at the side of a block
 */

import {Plugin} from "prosemirror-state";

class BlockMenu {

    constructor(view) {
        this.blockmenuEl = document.createElement("div");
        this.blockmenuEl.classList.add("simplecms__editor__blockmenu");
        view.dom.parentNode.appendChild(this.blockmenuEl);
        this.hide();

        view.dom.addEventListener("blur", () => {
            this.hide()
        });
    }

    update(view, lastState) {
        const state = view.state;

        if (lastState && lastState.doc.eq(state.doc) && lastState.selection.eq(state.selection)) return;

        if (!state.selection.empty) {
            this.hide();
            return;
        }

        // Get currently selected node
        this.show();
        const resolvedPos = state.doc.resolve(state.selection.from);
        const topLevelNode = resolvedPos.node(1);
        const coords = view.coordsAtPos(resolvedPos.start());
        const nodeEl = view.domAtPos(resolvedPos.start()).node;
        const nodeType = topLevelNode.type;
        this.setNodeType(nodeType);

        const style = window.getComputedStyle(nodeEl);
        const offsetTop = parseFloat(style.marginTop) + parseFloat(style.paddingTop) + (parseFloat(style.borderWidth) || 0);

        let box = this.blockmenuEl.offsetParent.getBoundingClientRect();

        this.blockmenuEl.style.bottom = (box.bottom - coords.top - offsetTop) + "px";
    }

    setNodeType(nodeType) {
        this.blockmenuEl.dataset.simplecmsNodeType = nodeType.name;
    }

    destroy() {
        this.blockmenuEl.remove();
    }

    hide() {
        this.blockmenuEl.style.display = "none";
    }

    show() {
        this.blockmenuEl.style.display = "";
    }

}

export const BlockMenuPlugin = () => {
    console.log("test");
    return new Plugin({
        view(editorView) { return new BlockMenu(editorView); }
    });
};
