import {Plugin} from "prosemirror-state";

export default (kontrlContext) => {
    return new Plugin({
        props: {
            handleDoubleClick(view, pos, event) {
                if (event.target.tagName !== "IMG") return false;

                const { popups } = kontrlContext;
                const popup = new popups.GenericPopup({ title: "Choose Image" });
                popup.addItem(new popups.InputItem({ name: "altVal", label: "Image description" }));
                const imgItem = new popups.ImageItem({ name: "img", label: "Your image", value: attrs && attrs.src });
                popup.addItem(imgItem);

                popups.showPopup(popup).then(data => {
                    if (data == null) return;

                    kontrlContext.uploadAnyImage(data.content.img, "wysiwyg", url => {
                        const attrs = { src: url, alt: data.content.altVal };
                        const nodeType = view.state.schema.nodes.image;
                        view.dispatch(view.state.tr.replaceSelectionWith(nodeType.createAndFill(attrs)));
                    });
                });

                // TODO: return true and select the image ourselves
                return false;
            }
        }
    })
}