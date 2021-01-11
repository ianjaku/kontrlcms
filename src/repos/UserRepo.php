<?php

namespace invacto\SimpleCMS\repos;


use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class UserRepo
{

	/**
	 * Fetch all users
	 *
	 * @return Collection
	 */
	public static function all() {
		return self::users()->get();
	}

	/**
	 * Fetch a single user by email, null when missing
	 *
	 * @param $email
	 * @return Model|Builder|object|null
	 */
	public static function oneByEmail($email) {
		return self::users()->where("email", $email)->first();
	}

	/**
	 * Fetch a single user by id, null when missing
	 *
	 * @param $id
	 * @return Model|Builder|object|null
	 */
	public static function oneById($id) {
		return self::users()->where("id", $id)->first();
	}

	/**
	 * @param $email
	 * @param $password
	 * @param $salt
	 * @return bool
	 */
	public static function create($email, $password, $salt) {
		return self::users()->insert([
			"email" => $email,
			"password" => $password,
			"salt" => $salt
		]);
	}

	/**
	 * Counts all users in the database
	 *
	 * @return int
	 */
	public static function countAll() {
		return self::users()->count();
	}

	public static function doesTableExist() {
		try {
			UserRepo::countAll();
		} catch (QueryException $e) {
			if ($e->getCode() === "42S02") {
				return false;
			}
		}
		return true;
	}

	private static function users() {
		return Capsule::table("users");
	}

}