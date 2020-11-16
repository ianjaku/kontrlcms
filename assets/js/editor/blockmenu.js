/*
    The menu that gets shown at the side of a block
 */

import {Plugin} from "prosemirror-state";
import {setBlockType} from "prosemirror-commands";

class BlockMenu {

    constructor(view) {
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

        // this.blockmenuAngleEl = document.createElement("div");
        // this.blockmenuAngleEl.classList.add("simplecms__editor__blockmenu-angle");
        // this.blockmenuAngleEl.innerHTML = "v";
        // this.blockmenuEl.appendChild(this.blockmenuAngleEl);

        view.dom.parentNode.appendChild(this.blockmenuEl);
        this.hide();

        // view.dom.addEventListener("blur", () => {
        //     this.hide()
        // });
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
        console.log("Change to " + typeName);
        const state = this.view.state;
        const schema = state.schema;

        const commands = {
            "h1": setBlockType(schema.nodes.heading, { level: 1 }),
            "h2": setBlockType(schema.nodes.heading, { level: 2 }),
            "paragraph": setBlockType(schema.nodes.paragraph)
        };

        const command = commands[typeName];
        if (command == null) return;
        command(state, this.view.dispatch);
    }

    update(view, lastState) {
        const state = view.state;

        if (lastState && lastState.doc.eq(state.doc) && lastState.selection.eq(state.selection)) return;

        // if (!state.selection.empty) {
        //     this.hide();
        //     return;
        // }

        // Get currently selected node
        this.show();
        const resolvedPos = state.doc.resolve(state.selection.from);
        const topLevelNode = resolvedPos.node(1);
        const nodePos = view.coordsAtPos(resolvedPos.start());
        const nodeEl = view.domAtPos(resolvedPos.start()).node;
        const nodeType = topLevelNode.type;
        this.setNodeText(topLevelNode);

        const style = window.getComputedStyle(nodeEl);
        const offsetTop = parseFloat(style.marginTop) + parseFloat(style.paddingTop) + (parseFloat(style.borderWidth) || 0);

        const height = style.height;
        console.log(height);

        let editorPos = this.blockmenuEl.offsetParent.getBoundingClientRect();

        // this.blockmenuEl.style.bottom = (box.bottom - coords.top - offsetTop) + "px";
        // this.blockmenuEl.style.bottom = (box.bottom - coords.bottom) + "px";
        this.blockmenuEl.style.top = (nodePos.top - editorPos.top) + "px";
        this.blockmenuEl.style.height = style.height;
    }

    setNodeText(node) {
        const nodeTypeTranslators = {
            "heading": (node) => `H${node.attrs.level}`,
            "paragraph": () => "P"
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

export const BlockMenuPlugin = () => {
    return new Plugin({
        view(editorView) { return new BlockMenu(editorView); }
    });
};
