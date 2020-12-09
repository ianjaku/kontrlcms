import "../css/style.scss";
import Editor from "./editor/editor";
import plugins from "./plugins";

const EDIT_CLASS = "simplecms--edit";
// TODO: turn to false
let editing = false;

const editorEls = document.querySelectorAll(".simplecms__editor");
const editors = [];
Array.from(editorEls).forEach(el => {
    const editor = new Editor(el);
    editor.setReadOnly(editing);
    editors.push(editor);
});

function enableEditors() {
    editors.forEach(e => e.setReadOnly(true))
    plugins.enable();
}

function disableEditors() {
    editors.forEach(e => e.setReadOnly(false))
    plugins.disable();
}


const editButton = document.querySelector(".simplecms__adminbar__button--edit");
const saveButton = document.querySelector(".simplecms__adminbar__button--save");

editButton.addEventListener("click", () => {
    editing = true;
    document.body.classList.add(EDIT_CLASS);
    enableEditors();
    editButton.style.display = "none";
    saveButton.style.display = "block";
});

saveButton.addEventListener("click", () => {
    editing = false;
    document.body.classList.remove(EDIT_CLASS);
    disableEditors();
    editButton.style.display = "block";
    saveButton.style.display = "none";
});


