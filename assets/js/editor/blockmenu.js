/*
    The menu that gets shown at the side of a block
 */

import {Plugin, Selection} from "prosemirror-state";
import {setBlockType} from "prosemirror-commands";
import {TextSelection, NodeSelection} from "prosemirror-state/src";

class BlockMenu {

    constructor(view, readonlyListeners) {
        this.view = view;

        this.blockmenuEl = document.createElement("div");
        this.blockmenuEl.classList.add("simplecms__editor__blockmenu");

        // Add a second element for the text
        this.blockmenuTextEl = document.createElement("div");
        this.blockmenuTextEl.classList.add("simplecms__editor__blockmenu-text");
        this.blockmenuEl.appendChild(this.blockmenuTextEl);

        this.dropdownEl = this.createDropdown();
        this.blockmenuEl.appendChild(this.dropdownEl);
        this.blockmenuTextEl.addEventListener("click", () => {
            this.dropdownEl.classList.toggle("simplecms__editor__blockmenu-dropdown--active")
        });

        view.dom.parentNode.appendChild(this.blockmenuEl);
        this.hide();

        readonlyListeners.push(val => {
            if (!val) {
                this.hide();
            }
        })
    }

    createDropdown() {
        const dropdownEl = document.createElement("div");
        dropdownEl.classList.add("simplecms__editor__blockmenu-dropdown");
        dropdownEl.innerHTML = `
            <ul class="simplecms__editor__blockmenu-list">
                <li class="simplecms__editor__blockmenu-item" data-simplecms-type="h1">
                    <div class="simplecms__editor__blockmenu-type">H1</div>
                    <div class="simplecms__editor__blockmenu-name">Heading 1</div>
                </li>
                <li class="simplecms__editor__blockmenu-item" data-simplecms-type="h2">
                    <div class="simplecms__editor__blockmenu-type">H2</div>
                    <div class="simplecms__editor__blockmenu-name">Heading 2</div>
                </li>
                <li class="simplecms__editor__blockmenu-item" data-simplecms-type="paragraph">
                    <div class="simplecms__editor__blockmenu-type">P</div>
                    <div class="simplecms__editor__blockmenu-name">Paragraph</div>
                </li>
                <li class="simplecms__editor__blockmenu-item" data-simplecms-type="image">
                    <div class="simplecms__editor__blockmenu-type">IMG</div>
                    <div class="simplecms__editor__blockmenu-name">Image</div>
                </li>
            </ul>
        `;
        const itemEls = dropdownEl.querySelectorAll(".simplecms__editor__blockmenu-item");
        itemEls.forEach(ie => {
            ie.addEventListener("click", () => {
                const type = ie.dataset.simplecmsType;
                this.setSelectedBlockType(type);
                dropdownEl.classList.remove("simplecms__editor__blockmenu-dropdown--active");
            });
        });
        return dropdownEl;
    }

    setSelectedBlockType(typeName) {
        const state = this.view.state;
        const schema = state.schema;

        const commands = {
            "h1": this.setAnyBlockType(schema.nodes.heading, { level: 1 }),
            "h2": this.setAnyBlockType(schema.nodes.heading, { level: 2 }),
            "paragraph": this.setAnyBlockType(schema.nodes.paragraph),
            "image": (state, dispatch) => {
                const tr = state.tr;

                if (!tr.selection.node) {
                    const resolvedPos = state.doc.resolve(tr.selection.from);
                    const startPos = state.doc.resolve(resolvedPos.start());
                    const newSelection = new NodeSelection(startPos);
                    tr.setSelection(newSelection)
                }

                const imageNode = schema.nodes.image.createAndFill({ src: "https://via.placeholder.com/150", alt: "" });
                tr.replaceSelectionWith(imageNode);
                dispatch(tr);

                const lastKnownPosition = tr.selection;
                setTimeout(() => {
                    const imageNode = schema.nodes.image.createAndFill({ src: "https://picsum.photos/200/300", alt: "" });
                    const tr = this.view.state.tr.replaceWith(lastKnownPosition.from, lastKnownPosition.to, imageNode);
                    this.view.dispatch(tr);
                    // dispatch(tr);
                }, 250);
            }
        };

        const command = commands[typeName];
        if (command == null) return;
        command(state, this.view.dispatch);
    }

    setAnyBlockType(blockType, attrs = {}) {
        const sbt = setBlockType(blockType, attrs);
        return (state, dispatch) => {
            if (state.selection.node) {
                // remove node and create new field
                let tr = state.tr;
                const newNode = blockType.createAndFill(attrs);
                tr.replaceSelectionWith(newNode);
                dispatch(tr);
            } else {
                sbt(state, dispatch);
            }
        };
    }

    update(view, lastState) {
        const state = view.state;

        if (lastState && lastState.doc.eq(state.doc) && lastState.selection.eq(state.selection)) return;

        if (!state.selection.empty && !state.selection.node) return;

        // Get currently selected node
        this.show();
        let selectedNode;
        let nodePos;
        let nodeEl;

        if (state.selection.node) {
            // Node selection
            selectedNode = state.selection.node;
            nodePos = view.coordsAtPos(state.selection.from);
            nodeEl = view.nodeDOM(state.selection.from);
        } else {
            // Text selection
            const resolvedPos = state.doc.resolve(state.selection.from);
            selectedNode = resolvedPos.node(1);
            nodePos = view.coordsAtPos(resolvedPos.start());
            nodeEl = view.domAtPos(resolvedPos.start()).node;
        }

        this.setNodeText(selectedNode);

        const style = window.getComputedStyle(nodeEl);
        const editorPos = this.blockmenuEl.offsetParent.getBoundingClientRect();

        this.blockmenuEl.style.top = (nodePos.top - editorPos.top) + "px";
        this.blockmenuEl.style.height = style.height;
    }

    setNodeText(node) {
        if (node == null) return;

        const nodeTypeTranslators = {
            "heading": (node) => `H${node.attrs.level}`,
            "paragraph": () => "P",
            "image": () => "IMG"
        };

        const translator = nodeTypeTranslators[node.type.name];
        const text = translator(node);
        this.blockmenuTextEl.innerText = text;
        this.blockmenuEl.dataset.simplecmsNodeType = text;
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

export const BlockMenuPlugin = (readonlyListeners) => {
    return new Plugin({
        view(editorView) { return new BlockMenu(editorView, readonlyListeners); }
    });
};
