const popupEl = document.querySelector(".simplecms__img-popup");
const fileInputEl = document.querySelector(".simplecms__img-popup__file-input");
const browserButtonEl = document.querySelector(".simplecms__img-popup__dropbox-button");
const dropboxEl = document.querySelector(".simplecms__img-popup__bg");

const popupActiveClass = "simplecms__img-popup--visible";
const highlightClass = "simplecms__img-popup__dropbox--highlighted";

let currentCallback = null;

function hidePopup() {
    popupEl.classList.remove(popupActiveClass);
    document.body.classList.remove("simplecms__no-scroll");
    unhighlightDropbox();
}

function showPopup() {
    popupEl.classList.add(popupActiveClass);
    document.body.classList.add("simplecms__no-scroll");
}

function highlightDropbox() {
    dropboxEl.classList.add(highlightClass);
}

function unhighlightDropbox() {
    dropboxEl.classList.remove(highlightClass);
}

browserButtonEl.addEventListener("click", e => {
    fileInputEl.click();
});

dropboxEl.addEventListener("click", () => {
    hidePopup();
});

popupEl.addEventListener("dragenter", e => {
    e.preventDefault();
    e.stopPropagation();
    highlightDropbox();
}, false);

popupEl.addEventListener("dragover", e => {
    e.preventDefault();
    e.stopPropagation();
    highlightDropbox();
}, false);

popupEl.addEventListener("dravleave", e => {
    e.preventDefault();
    e.stopPropagation();
    unhighlightDropbox();
}, false);
popupEl.addEventListener("drop", e => {
    e.preventDefault();
    e.stopPropagation();
    unhighlightDropbox();

    let dataTransfer = e.dataTransfer;
    let files = dataTransfer.files;

    if (files.length === 0) return;

    if (currentCallback == null) return;

    hidePopup();
    currentCallback(files[0]);
}, false);

fileInputEl.addEventListener("change", e => {
    if (currentCallback == null) return;

    const files = e.target.files;
    if (files.length === 0) return;

    hidePopup();
    currentCallback(files[0]);
});

export default (callback) => {
    currentCallback = callback;
    showPopup();
}
