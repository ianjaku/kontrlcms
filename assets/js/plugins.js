import "kontrl-popups/style/dist/style.css";
import kontrlPopups from "kontrl-popups";
import { updateSnippet, uploadAnyImage, uploadSnippetImage, fetchSnippets } from "./repo";
import kontrlSidebars from "./sidebars";
import { v4 as uuidv4 } from "uuid";
import {debounce} from "lodash";

let _isEditing = false;

const context = {
		debounce,
		updateSnippet,
		uploadAnyImage,
		uploadSnippetImage,
		fetchSnippets,
		addButtonRight(text) {
				const buttonsEl = document.querySelector(".simplecms__adminbar__user-buttons--right");
				if (buttonsEl == null) return;

				const buttonEl = document.createElement("button");
				buttonEl.classList.add("simplecms__adminbar__button");
				buttonEl.innerText = text;
				buttonsEl.appendChild(buttonEl);

				return buttonEl;
		},
		generateId() {
				return uuidv4().replace(/-/g, "");
		},
		sidebars: kontrlSidebars.sidebars,
		addSidebar: kontrlSidebars.addSidebar,
		addSidebars: kontrlSidebars.addSidebars,
		popups: kontrlPopups,
		isEditing() {
				return _isEditing
		},
		pageName: PAGE_NAME
};

let plugins = [];

for (let pluginClass of __KONTRL_ADMIN_PLUGINS) {
		plugins.push(new pluginClass(context));
}

export default {
		enable() {
				plugins.forEach(p => p.enable());
				_isEditing = true;
		},
		disable() {
				plugins.forEach(p => p.disable());
				_isEditing = false;
		}
}
