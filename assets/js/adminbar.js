import plugins from "./plugins";

const EDIT_CLASS = "simplecms--edit";

const editButton = document.querySelector(".simplecms__adminbar__button--edit");
const saveButton = document.querySelector(".simplecms__adminbar__button--save");

editButton.addEventListener("click", () => {
    document.body.classList.add(EDIT_CLASS);
    editButton.style.display = "none";
    saveButton.style.display = "block";
    plugins.enable();
});

saveButton.addEventListener("click", () => {
    document.body.classList.remove(EDIT_CLASS);
    editButton.style.display = "block";
    saveButton.style.display = "none";
    plugins.disable();
});


