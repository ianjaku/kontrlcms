<?php

namespace invacto\SimpleCMS\repos;


use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;

class SnippetRepo
{

	/**
	 * @param $namesAndPages array in format [["name" => "", "page" => ""], ...]
	 *                               StdClasses are also supported
	 *
	 * @return Collection
	 */
	public static function allByNamesAndPages($namesAndPages) {
		$query = self::snippets();
		foreach ($namesAndPages as $nameAndPage) {
			if (is_object($nameAndPage)) {
				$name = $nameAndPage->name;
				$page = $nameAndPage->page;
			} else {
				$name = $nameAndPage["name"];
				$page = $nameAndPage["page"];
			}
			$query->orWhere([
				["name", "=", $name],
				["page", "=", $page]
			]);
		}
		return $query->get();
	}

	/**
	 * Provides a list with all snippets belonging to the given page
	 * Global snippets are also returned by default and can be turned off by passing false as the second parameter
	 *
	 * @param $page string
	 * @param bool $addGlobals
	 * @return Collection
	 */
	public static function allForPage(string $page, bool $addGlobals = true) {
		$query = self::snippets()->where("page", $page);
		if ($addGlobals) {
			$query->orWhere("page", "__global__");
		}
		return $query->get();
	}

	/**
	 * Inserts a snippet if it does not already exist otherwise updates it
	 *
	 * @param $name
	 * @param $page
	 * @param $value
	 * @return bool
	 */
	public static function updateOrCreate($name, $page, $value) {
		return self::snippets()->updateOrInsert([
			"name" => $name,
			"page" => $page
		], [
			"value" => $value
		]);
	}

	private static function snippets() {
		return Capsule::table("snippets");
	}

}