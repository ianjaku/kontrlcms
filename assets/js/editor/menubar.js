

class Menubar {

    constructor() {
    }

    createMenuEl(editorName) {
        const el = document.createElement("div");
        el.classList.add("simplecms__editor__menubar");
        el.dataset.simplecmsEditorName = editorName;
        el.innerHTML = `
            Test
        `;
        return el;
    };

}

export default Menubar;
