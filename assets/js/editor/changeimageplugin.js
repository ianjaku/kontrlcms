import {Plugin} from "prosemirror-state";
import imagePopup from "../popups/image_popup";
import {uploadAnyImage} from "../util/uploader";

export default () => {
    return new Plugin({
        props: {
            handleDoubleClick(view, pos, event) {
                if (event.target.tagName !== "IMG") return false;

                imagePopup((imgFile) => {
                    uploadAnyImage(imgFile, "wysiwyg", url => {
                        const attrs = { src: url, alt: "" };
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