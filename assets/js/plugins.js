import kontrlPopups from "kontrl-popups";
import "kontrl-popups/style/dist/style.css";

let _isEditing = false;

const context = {
	updateSnippet(name, value, page = PAGE_NAME) {
		return fetch('/simplecms/update', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				page,
				name,
				value
			})
		})
	},
	uploadSnippetImage(name, imgFile, page = PAGE_NAME) {
		return new Promise((resolve) => {
		   const formData = new FormData();
		   formData.append('file', imgFile);
		   formData.append('page', page);
		   formData.append('name', name);

		   fetch(`/simplecms/upload`, {
					   method: "POST",
					   body: formData
		   }).then(response => {
					   response.json().then(response => {
								 resolve(response.url)
					   })
		   }).catch(err => {
					   // TODO: Show an error or something
		   })
		})
	},
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
