import Editor from "./editor";

class WYSIWYGPlugin {

    constructor(context) {
        this.context = context;
        this.editors = [];
        this.setup(context);
    }

    setup(context) {
        const editorEls = document.querySelectorAll(".simplecms__editor");
        Array.from(editorEls).forEach(el => {
            const editor = new Editor(el, context);
            editor.setReadOnly(context.isEditing());
            this.editors.push(editor);
        });
    }

    enable() {
        this.editors.forEach(e => e.setReadOnly(true));
    }

    disable() {
        this.editors.forEach(e => e.setReadOnly(false));
    }
}

export default WYSIWYGPlugin;