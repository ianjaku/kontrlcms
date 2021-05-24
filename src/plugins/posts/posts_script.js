class PostsPlugin {

		constructor(context) {
				this.context = context;
				this.postsSettings = JSON.parse(_POSTS_SETTINGS);
				this.setup(context);
		}

		setup(context) {
				this.postsSettings.forEach(settings => {
						const el = context.addButtonRight("Create Post");

						el.addEventListener("click", () => {
								const newPostId = context.generateId();
								this.routeToPostURl(newPostId, "new", settings.url);
						});


						if (typeof _POST_ID !== "undefined") {
							const removeEl = context.addButtonRight("Remove Post")

							removeEl.addEventListener("click", () => {
								const val = prompt("Please type 'yes' in the prompt below to confirm deleting this blog post.")
								if (val.toLowerCase() === "yes") {
									document.location.href = `/kontrlcms/posts/remove/${_POST_ID}`
								}
							})
						}
				});
		}

		routeToPostURl(id, slug, url) {
			document.location.href = url.replace("{id}", id).replace("{slug}", slug);
		}

		enable() {
		}

		disable() {
		}

}

__KONTRL_ADMIN_PLUGINS.push(PostsPlugin);
