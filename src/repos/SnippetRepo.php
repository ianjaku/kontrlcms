<?php

namespace invacto\SimpleCMS\repos;


use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;

class SnippetRepo
{

	/**
	 * @param $namesAndPages array in format [["name" => "", "page" => "", "globalFallback" => false], ...]
	 *                              StdClasses are also supported
	 * 								globalFallback is optional and false by default
	 *
	 * @return Collection
	 */
	public static function allByNamesAndPages($namesAndPages) {
		$query = self::snippets();
		foreach ($namesAndPages as $nameAndPage) {
			$name = $nameAndPage["name"];
			$page = $nameAndPage["page"];
			$query->orWhere([
				["name", "=", $name],
				["page", "=", $page]
			]);

			if (isset($nameAndPage["globalFallback"]) && $nameAndPage["globalFallback"] === true) {
				$query->orWhere([
					["name", "=", $name],
					["page", "=", "__global__"]
				]);
			}
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
		$exists = self::exists($name, $page);

		if ($exists) {
			return self::update($name, $page, $value);
		}

		return self::insert($name, $page, $value);
	}

	public static function insert($name, $page, $value) {
		return self::snippets()->insert([
			"page" => $page,
			"name" => $name,
			"value" => $value,
			"updated_at" => self::now(),
			"inserted_at" => self::now()
		]);
	}

	public static function update($name, $page, $value) {
		return self::snippets()
			->where("name", $name)
			->where("page", $page)
			->limit(1)->update([
				"page" => $page,
				"name" => $name,
				"value" => $value,
				"updated_at" => self::now(),
			]
		);
	}

	public static function single($name, $page) {
		return self::snippets()->where("name", $name)->where("page", $page)->first();
	}

	public static function exists($name, $page) {
		return self::snippets()->where("name", $name)->where("page", $page)->exists();
	}

	public static function customQuery() {
		return self::snippets();
	}

	private static function snippets() {
		return Capsule::table("snippets");
	}

	private static function now() {
		return (new \DateTime())->format("Y-m-d\TH:i:s.u");
	}
}