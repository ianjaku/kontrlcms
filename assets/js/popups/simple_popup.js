// Generic popup which can be used for most simple prompts

const popupEl = document.querySelector(".simplecms__simple-popup");
const rowsEl = document.querySelector(".simplecms__simple-popup__rows");
const titleEl = document.querySelector(".simplecms__simple-popup__title");
const subTitleEl = document.querySelector(".simplecms__simple-popup__sub-title");
const formEl = document.querySelector(".simplecms__simple-popup__content");
const bgEl = document.querySelector(".simplecms__simple-popup__bg");

const activeClass = "simplecms__simple-popup--visible";

let currentCallback = null;
let currentData = {};
function showPopup() {
    popupEl.classList.add(activeClass);
    document.body.classList.add("simplecms__no-scroll");
}

function hidePopup() {
    popupEl.classList.remove(activeClass);
    document.body.classList.remove("simplecms__no-scroll");
}

function setTitle(title, subTitle) {
    titleEl.innerText = title;
    subTitleEl.innerText = subTitle;
}

function createRowEl(row) {
    const id = "simplecms__popup__" + row.type;

    const rowEl = document.createElement("div");
    rowEl.classList.add("simplecms__simple-popup__row");

    const labelEl = document.createElement("label");
    labelEl.classList.add("simplecms__simple-popup__label");
    labelEl.innerText = row.label;
    labelEl.for = row.name;
    labelEl.id = id;
    rowEl.appendChild(labelEl);

    if (["text", "email", "password"].includes(row.type)) {
        const inputEl = document.createElement("input");
        inputEl.classList.add("simplecms__simple-popup__input");
        inputEl.type = row.type;
        inputEl.name = row.name;
        inputEl.id = id;
        inputEl.placeholder = row.placeholder || "";
        rowEl.appendChild(inputEl);

        inputEl.addEventListener("input", e => {
            currentData[row.name] = inputEl.value;
        })
    }

    return rowEl;
}

function setRows(rows) {
    const rowEls = rows.map(r => createRowEl(r));
    rowsEl.innerHTML = "";
    rowsEl.append(...rowEls);
    return rowEls;
}

formEl.addEventListener("submit", e => {
    e.preventDefault();
    e.stopPropagation();
    if (currentCallback == null) return;
    hidePopup();
    currentCallback(currentData);
});

bgEl.addEventListener("click", () => {
    hidePopup();
});

/**
 * @param title
 * @param subTitle
 * @param rows the rows to show in the popup, format [{type: "text", label: "", name: "", placeholder: ""}]
 * @param callback
 */
export default (title, subTitle, rows, callback) => {
    currentData = {};
    currentCallback = callback;
    setTitle(title, subTitle);
    const rowEls = setRows(rows);
    rowEls[0].focus();
    showPopup();
}
