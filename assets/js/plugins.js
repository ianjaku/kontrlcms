import "kontrl-popups/style/dist/style.css";
import kontrlPopups from "kontrl-popups";
import { updateSnippet, uploadAnyImage, uploadSnippetImage } from "./repo";

let _isEditing = false;

const context = {
	updateSnippet,
	uploadAnyImage,
	uploadSnippetImage,
	debounce(func, wait, immediate) {
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
	},
	popups: kontrlPopups,
	isEditing() {
		 return _isEditing
	}
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
