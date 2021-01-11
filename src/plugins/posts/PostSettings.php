<?php


namespace invacto\KontrlCMS\plugins\posts;


class PostSettings
{
	private $settings;

	public function __construct($settings)
	{
		$this->settings = $settings;
	}

	public function getPrefix() {
		if (isset($this->settings["prefix"])) {
			return $this->settings["prefix"];
		}
		return "/" . $this->settings["name"];
	}

	public function getPageUrl($id = "{id}", $slug = "{slug}") {
		$prefix = $this->getPrefix();
		return $prefix . "/$id/$slug";
	}

	/**
	 * Compiles the data that the javascript version of this plugin needs
	 */
	public function getJavascriptData() {
		return [
			"name" => $this->getName(),
			"prefix" => $this->getPrefix(),
			"url" => $this->getPageUrl()
		];
	}

	public function getName() {
		return $this->settings["name"];
	}

	public function getView() {
		return $this->settings["view"];
	}

	public function getPageName($id) {
		return $this->getpageNamePrefix() . $id;
	}

	public function getPageNamePrefix() {
		$normalizedName = preg_replace("/[^0-9a-z]+/i", "", $this->getName());
		return "__posts.$normalizedName.";
	}

	public function getIdFromPageName($pageName) {
		return preg_replace("/.*\./i", "", $pageName);
	}

	public function getSlugColumn() {
		return $this->settings["slug"];
	}

	public function getSlugFor($post) {
		$slugColumn = $this->getSlugColumn();
		if (isset($post[$slugColumn])) {
			return $this->slugify($post[$slugColumn]);
		}
		return $this->slugify($this->getName() . "-post");
	}

	/**
	 * Removes any non alphanumeric characters
	 * and replaces spaces with -
	 *
	 * @param $word
	 * @return string|string[]|null
	 */
	private function slugify($word) {
		$noSpaces = str_replace(" ", "-", $word);
		return preg_replace("/[^0-9a-zA-Z]+/i", "", $noSpaces);
	}

}