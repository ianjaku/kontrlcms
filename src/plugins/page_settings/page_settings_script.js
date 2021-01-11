const PAGE_TITLE = "__page-settings.page_title";
const PAGE_DESC = "__page-settings.page_description";
const PAGE_KEYS = "__page-settings.page_keywords";

class PageSettingsPlugin {

		constructor(context) {
				this.context = context;
				this.setup(context);
		}

		setup(context) {
				const sidebar = new context.sidebars.Sidebar({
						initState: this.initSidebarState.bind(this),
						icon: this.getIcon(),
						items: this.createItems()
				});

				context.addSidebar(sidebar);
		}

		initSidebarState() {
				return new Promise(resolve => {
						this.context.fetchSnippets([
								{name: PAGE_TITLE, page: this.context.pageName, globalFallback: true},
								{name: PAGE_DESC, page: this.context.pageName, globalFallback: true},
								{name: PAGE_KEYS, page: this.context.pageName, globalFallback: true},
						]).then(snippets => {
										const state = {
												page_title: this.getSnippetValue(snippets, PAGE_TITLE, this.context.pageName),
												page_description: this.getSnippetValue(snippets, PAGE_DESC, this.context.pageName),
												page_keywords: this.getSnippetValue(snippets, PAGE_KEYS, this.context.pageName)
										};
										resolve(state);
								});
				})
		}

		createItems() {
				const {items, Validation} = this.context.sidebars;

				return [
						new items.TitleItem({value: "Page Settings", subTitle: "Settings specific to this page"}),
						new items.FormItem({
								name: "seo_settings",
								onSubmit: this.handleFormSubmit.bind(this),
								onAltButton: this.handleSetDefault.bind(this),
								altButtonText: "Set as default",
								items: [
										new items.InputItem({
												name: "page_title",
												label: "Page Title",
												// validator: Validation.exists(),
												valueFromState: (state => state.page_title || "")
										}),
										new items.InputItem({
												name: "page_keywords",
												label: "Page Keywords",
												// validator: Validation.exists(),
												valueFromState: (state => state.page_keywords || "")
										}),
										new items.TextAreaItem({
												name: "page_description",
												label: "Page Description",
												// validator: Validation.exists(),
												valueFromState: (state => state.page_description || "")
										})
								]
						})
				]
		}

		handleSetDefault(values, validate) {
				const errs = validate();
				if (errs.length !== 0) return;
				const p1 = this.context.updateSnippet(PAGE_TITLE, values["page_title"], "__global__");
				const p2 = this.context.updateSnippet(PAGE_DESC, values["page_description"], "__global__");
				const p3 = this.context.updateSnippet(PAGE_KEYS, values["page_keywords"], "__global__");
				return Promise.all([p1, p2, p3]);
		}

		handleFormSubmit(values, validate) {
				const errs = validate();
				if (errs.length !== 0) return;
				document.title = values["page_title"];
				const p1 = this.context.updateSnippet(PAGE_TITLE, values["page_title"], this.context.pageName);
				const p2 = this.context.updateSnippet(PAGE_DESC, values["page_description"], this.context.pageName);
				const p3 = this.context.updateSnippet(PAGE_KEYS, values["page_keywords"], this.context.pageName);
				return Promise.all([p1, p2, p3]);
		}

		getSnippetValue(snippets, name, page) {
				const snippet = snippets.find(s => s.name === name && s.page === page);
				if (snippet != null) return snippet.value;
				const globalSnippet = snippets.find(s => s.name === name && s.page === "__global__");
				if (!globalSnippet) return null;
				return globalSnippet.value;
		}

		getIcon() {
				return `<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog" class="svg-inline--fa fa-cog fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"></path></svg>`;
		}

		enable() {
		}

		disable() {
		}
}

__KONTRL_ADMIN_PLUGINS.push(PageSettingsPlugin);
