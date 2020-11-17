import "../css/style.scss";
import Editor from "./editor/editor";


const EDIT_CLASS = "simplecms--edit";
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
}

function disableEditors() {
    editors.forEach(e => e.setReadOnly(false))
}


const editButton = document.querySelector(".simplecms__adminbar__button--edit");
const saveButton = document.querySelector(".simplecms__adminbar__button--save");

editButton.addEventListener("click", () => {
    // Start editing
    editing = true;
    document.body.classList.add(EDIT_CLASS);
    repositionEditIcons();
    makeContentEditable();
    enableEditors();
    editButton.style.display = "none";
    saveButton.style.display = "block";
});

saveButton.addEventListener("click", () => {
    // Stop editing
    editing = false;
    document.body.classList.remove(EDIT_CLASS);
    makeNonContentEditable();
    disableEditors();
    editButton.style.display = "block";
    saveButton.style.display = "none";
});

function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        let context = this, args = arguments;
        let later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        let callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

const updateContent = debounce(function(name, value) {
    fetch('/simplecms/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            page: PAGE_NAME,
            name,
            value
        })
    })
}, 300);

const elements = document.getElementsByClassName('simplecms__editable');

for (let el of elements) {
    el.addEventListener('click', e => {
        if (editing) {
            e.stopPropagation();
            e.preventDefault();
            e.target.focus();
        }
    });
    el.addEventListener('input', e => {
        const name = e.target.dataset.name;
        const type = e.target.dataset.type;

        let value;
        if (e.target.innerText) {
            value = e.target.innerText;
        } else {
            value = e.target.innerHTML;
        }
        updateContent(name, value)
    });
}

function makeContentEditable() {
    Array.from(elements).forEach(el => {
        el.contentEditable = "true";
    })
}

function makeNonContentEditable() {
    Array.from(elements).forEach(el => {
        el.contentEditable = "false";
    })
}

let imageDropboxImageName = "";
let imageDropboxImageEl = "";
const imagePopupEl = document.querySelector(".simplecms__img-popup");
const imageDropboxEl = document.querySelector(".simplecms__img-popup__dropbox");
const fileInputEl = document.querySelector(".simplecms__img-popup__file-input");
const fileBrowserButtonEl = document.querySelector(".simplecms__img-popup__dropbox-button");
const imageDropboxBgEl = document.querySelector(".simplecms__img-popup__bg");

const highlightClass = "simplecms__img-popup__dropbox--highlighted";
const showClass = "simplecms__img-popup--visible";

function showImageDropbox(name, el) {
    imageDropboxImageName = name;
    imageDropboxImageEl = el;
    imagePopupEl.classList.add(showClass);
    document.body.classList.add("simplecms__no-scroll");
}

function hideImageDropbox() {
    imagePopupEl.classList.remove(showClass)
    document.body.classList.remove("simplecms__no-scroll");
}

function highlightDropbox() {
    imageDropboxEl.classList.add(highlightClass);
}

function unhighlightDropbox() {
    imageDropboxEl.classList.remove(highlightClass);
}

fileBrowserButtonEl.addEventListener("click", e => {
    fileInputEl.click();
});

fileInputEl.addEventListener("change", e => {
    const files = e.target.files;
    if (files.length === 0) return;

    uploadDropboxImage(files[0]);
});

imageDropboxBgEl.addEventListener("click", () => {
    hideImageDropbox();
});

imagePopupEl.addEventListener("dragenter", e => {
    e.preventDefault();
    e.stopPropagation();
    highlightDropbox();
}, false);
imagePopupEl.addEventListener("dragover", e => {
    e.preventDefault();
    e.stopPropagation();
    highlightDropbox();
}, false);
imagePopupEl.addEventListener("dravleave", e => {
    e.preventDefault();
    e.stopPropagation();
    unhighlightDropbox();
}, false);
imagePopupEl.addEventListener("drop", e => {
    e.preventDefault();
    e.stopPropagation();
    unhighlightDropbox();

    let dataTransfer = e.dataTransfer;
    let files = dataTransfer.files;

    if (files.length === 0) return;

    uploadDropboxImage(files[0])

}, false);

function uploadDropboxImage(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('page', PAGE_NAME);
    formData.append('name', imageDropboxImageName);

    fetch(`/simplecms/upload`, {
        method: "POST",
        body: formData
    }).then(response => {
        // Success!!
        response.json().then(response => {
            if (imageDropboxImageEl.tagName.toLowerCase() === "img") {
                imageDropboxImageEl.src = response.url;
            } else {
                imageDropboxImageEl.style.backgroundImage = `url('${response.url}')`
            }
            hideImageDropbox();
        })
    }).catch(err => {
        // TODO: Show an error or smthng
    })
}

const imageElements = document.querySelectorAll('[data-simplecms-img]');
for (let el of imageElements) {
    el.classList.add('simplecms__image');
    el.addEventListener('click', e => {
        if (!editing) return;
        e.preventDefault();
        e.stopPropagation();
        const name = e.target.dataset['simplecmsImg'];
        const el = e.target;
        showImageDropbox(name, el);
    });
}

function elementTopOffset(el) {
    const boundingBox = el.getBoundingClientRect();
    return boundingBox.top + window.pageYOffset + (boundingBox.height / 2) - 10;
}

const bgImageEls = document.querySelectorAll("[data-simplecms-bg-image]");
const iconEls = [];
bgImageEls.forEach(bgImageEl => {
    const name = bgImageEl.dataset.simplecmsBgImage;

    const newItem = document.createElement("div");
    newItem.classList.add("simplecms__bg-img-edit");
    const boundingBox = bgImageEl.getBoundingClientRect();
    newItem.style.top = elementTopOffset(bgImageEl) + 'px';
    newItem.style.right = boundingBox.right - boundingBox.width + 'px';
    newItem.innerHTML = editIcon();

    newItem.addEventListener("click", () => {
        showImageDropbox(name, bgImageEl);
    });

    document.body.appendChild(newItem);

    iconEls.push({
        imageEl: bgImageEl,
        iconEl: newItem
    })
});

function repositionEditIcons() {
    iconEls.forEach(icon => {
        const boundingBox = icon.imageEl.getBoundingClientRect();
        icon.iconEl.style.top = elementTopOffset(icon.imageEl) + 'px';
        icon.iconEl.style.right = boundingBox.right - boundingBox.width + 'px';
    })
}

window.addEventListener("resize", debounce(() => {
    repositionEditIcons();
}, 250));

function editIcon(theClass = "") {
    return `
    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="edit" class="${theClass}" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
        <path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z"></path>
    </svg>
    `
}





