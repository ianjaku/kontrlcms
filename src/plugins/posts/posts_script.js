class PostsPlugin {

		constructor(context) {
				this.context = context;
				this.setup(context);
				this.settings = JSON.parse(_POSTS_SETTINGS);
		}

		setup(context) {
				const el = context.addButtonRight("Create Post");

				el.addEventListener("click", () => {
						const newPostId = context.generateId();
						this.routeToPostURl(newPostId, "new");
				});
		}

		routeToPostURl(id, slug) {
				document.location.href = this.settings.url.replace("{id}", id).replace("{slug}", slug);
		}

		enable() {
		}

		disable() {
		}

}

__KONTRL_ADMIN_PLUGINS.push(PostsPlugin);
