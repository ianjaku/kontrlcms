// Generic popup which can be used for most simple prompts

import {TextNode} from "prosemirror-model/src/node";

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
    const id = "simplecms__popup__" + row.name;

    const rowEl = document.createElement("div");
    rowEl.classList.add("simplecms__simple-popup__row");

    const labelEl = document.createElement("label");
    labelEl.classList.add("simplecms__simple-popup__label");
    labelEl.innerText = row.label;
    labelEl.for = row.name;
    labelEl.id = id;
    rowEl.appendChild(labelEl);

    if (["text", "email", "password"].includes(row.type)) {
        rowEl.appendChild(createInputRow(row, id));
    }

    if (row.type === "file") {
        rowEl.appendChild(createFileRow(row, id));
    }

    return rowEl;
}

function createInputRow(row, id) {
    const inputEl = document.createElement("input");
    inputEl.classList.add("simplecms__simple-popup__input");
    inputEl.type = row.type;
    inputEl.name = row.name;
    inputEl.id = id;
    inputEl.placeholder = row.placeholder || "";

    inputEl.addEventListener("input", e => {
        currentData[row.name] = inputEl.value;
    });

    return inputEl;
}

function readImageFromFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = e => resolve(e.target.result);
        reader.readAsDataURL(file);
    })
}

function createFileRow(row, id) {
    const state = {
        alt: null,
        imageFile: null
    };

    function selectFile(file) {
        state.imageFile = file;
        readImageFromFile(file).then(imgData => {
            previewEl.style.display = "";
            textEl.style.display = "none";
            previewEl.src = imgData;
        })
    }

    const boxEl = document.createElement("div");
    boxEl.classList.add("simplecms__simple-popup__file-box");

    const textEl = document.createElement("p");
    textEl.classList.add("simplecms__simple-popup__file-text");
    textEl.appendChild(document.createTextNode("Drop Image"));
    boxEl.appendChild(textEl);

    const inputEl = document.createElement("input");
    inputEl.type = "file";
    inputEl.id = id;
    inputEl.classList.add("simplecms__simple-popup__file-input");
    boxEl.appendChild(inputEl);

    const previewEl = document.createElement("img");
    previewEl.classList.add("simplecms__simple-popup__file-preview");
    previewEl.style.display = "none";
    boxEl.appendChild(previewEl);

    boxEl.addEventListener("click", () => {
        inputEl.click();
    });

    inputEl.addEventListener("change", e => {
        const files = e.target.files;
        if (files.length === 0) return;
        const file = files[0];
        selectFile(file)
    });

    const highlightClass = "simplecms__simple-popup__file-box--active";
    boxEl.addEventListener("dragenter", e => {
        e.preventDefault();
        e.stopPropagation();
        boxEl.classList.add(highlightClass);
    });
    boxEl.addEventListener("dragover", e => {
        e.preventDefault();
        e.stopPropagation();
        boxEl.classList.add(highlightClass);
    });
    boxEl.addEventListener("dragleave", e => {
        e.preventDefault();
        e.stopPropagation();
        boxEl.classList.remove(highlightClass);
    });
    boxEl.addEventListener("drop", e => {
        e.preventDefault();
        e.stopPropagation();
        boxEl.classList.remove(highlightClass);

        let dataTransfer = e.dataTransfer;
        let files = dataTransfer.files;
        if (files.length === 0) return;
        selectFile(files[0]);
    });

    return boxEl;
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
