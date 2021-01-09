<?php


namespace invacto\SimpleCMS\plugins\posts;


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

	public function getPageUrl() {
		$prefix = $this->getPrefix();
		return $prefix . "/{id}/{slug}";
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
		$normalizedName = preg_replace("/[^0-9a-z]+/i", "", $this->getName());
		return "__posts.$normalizedName.$id";
	}

}