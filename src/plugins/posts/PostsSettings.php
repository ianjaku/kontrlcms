<?php

namespace invacto\KontrlCMS\plugins\posts;


class PostsSettings
{
	/**
	 * @var PostSettings[]
	 */
	private $postSettingsList;

	/**
	 * PostsSettings constructor.
	 * @param array $settings
	 */
	public function __construct($settings)
	{
		foreach ($settings as $postSettingsData) {
			$this->postSettingsList[] = new PostSettings($postSettingsData);
		}
	}

	public function getSettingsFor($postType) {
		foreach ($this->postSettingsList as $postSettings) {
			if ($postSettings->getName() === $postType) {
				return $postSettings;
			}
		}
		return null;
	}

	/**
	 * @return PostSettings[]
	 */
	public function getAll() {
		return $this->postSettingsList;
	}

	public function getJSData() {
		$jsData = [];
		foreach ($this->getAll() as $settings) {
			$jsData[] = $settings->getJavascriptData();
		}
		return $jsData;
	}
}