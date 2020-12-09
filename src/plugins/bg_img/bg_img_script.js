
class BGImagePlugin {

    constructor(context) {
        this.context = context;
        this.iconEls = [];
        this.popup = this.createPopup();
        this.setup();
    }

    createPopup(baseValue) {
        const { popups } = this.context;
        const imgPopup = new popups.GenericPopup({ title: "Set background image"});
        const imgItem = new popups.ImageItem({ name: "img", label: "Your image", value: baseValue });
        imgPopup.addItem(imgItem);

        return (baseValue) => {
            imgItem.setValue(baseValue);
            return popups.showPopup(imgPopup)
        }
    }

    setup() {
        const bgImageEls = document.querySelectorAll("[data-simplecms-bg-image]");
        bgImageEls.forEach(bgImageEl => {
            const name = bgImageEl.dataset.simplecmsBgImage;

            const newItem = document.createElement("div");
            newItem.classList.add("simplecms__bg-img-edit");
            const boundingBox = bgImageEl.getBoundingClientRect();
            newItem.style.top = this.elementTopOffset(bgImageEl) + 'px';
            newItem.style.right = boundingBox.right - boundingBox.width + 'px';
            newItem.innerHTML = this.editIcon();

            newItem.addEventListener("click", () => {
                console.log(bgImageEl.dataset);
                this.popup(bgImageEl.dataset.simplecmsBgSrc).then(data => {
                    if (data == null) return;
                    this.context.uploadSnippetImage(name, data.content.img).then(url => {
                        console.log("Finish");
                        bgImageEl.style.backgroundImage = `url('${url}')`;
                    });
                });
            });

            document.body.appendChild(newItem);

            this.iconEls.push({
                imageEl: bgImageEl,
                iconEl: newItem
            })
        });

        window.addEventListener("resize", this.context.debounce(() => {
            this.repositionEditIcons();
        }, 250));

    }

    enable() {
        this.repositionEditIcons();
    }

    disable() {

    }

    repositionEditIcons() {
        this.iconEls.forEach(icon => {
            const boundingBox = icon.imageEl.getBoundingClientRect();
            icon.iconEl.style.top = this.elementTopOffset(icon.imageEl) + 'px';
            icon.iconEl.style.right = boundingBox.right - boundingBox.width + 'px';
        })
    }

    elementTopOffset(el) {
        const boundingBox = el.getBoundingClientRect();
        return boundingBox.top + window.pageYOffset + (boundingBox.height / 2) - 10;
    }

    editIcon(theClass = "") {
        return `
    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="edit" class="${theClass}" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
        <path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z"></path>
    </svg>
    `
    }
}

__KONTRL_ADMIN_PLUGINS.push(BGImagePlugin);
