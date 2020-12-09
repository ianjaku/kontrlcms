
class TextPlugin {

	constructor(context) {
		this.context = context;
		this.setup();
	}

	setup() {
		this.elements = Array.from(document.getElementsByClassName('simplecms__editable'));

		for (let el of this.elements) {
			el.addEventListener('click', e => {
				if (this.context.isEditing()) {
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
				this.context.updateSnippet(name, value);
			});
		}
	}

	enable() {
		this.makeContentEditable();
	}

	disable() {
		this.makeNonContentEditable();
	}

	makeContentEditable() {
		this.elements.forEach(el => {
			el.contentEditable = "true";
		})
	}

	makeNonContentEditable() {
		this.elements.forEach(el => {
			el.contentEditable = "false";
		})
	}

}

__KONTRL_ADMIN_PLUGINS.push(TextPlugin);