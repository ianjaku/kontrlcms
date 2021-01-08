<?php

namespace invacto\SimpleCMS\repos;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;


class UserTokenRepo
{

	/**
	 * @param $token
	 * @return bool
	 * @throws Exception
	 */
	public static function create($token) {
		return self::userTokens()->insert([
			"token" => $token,
			"inserted_at" => (new \DateTime())->format("Y-m-d\TH:i:s.u")
		]);
	}

	/**
	 * Returns whether the given token exists in the database
	 *
	 * @param $token
	 * @return bool
	 */
	public static function tokenExists($token) {
		return self::userTokens()->where("token", $token)->exists();
	}

	/**
	 * Removes a token from the database permanently
	 *
	 * @param $token
	 * @return int
	 */
	public static function removeToken($token) {
		return self::userTokens()->where("token", $token)->delete();
	}

	private static function userTokens() {
		return Capsule::table("user_tokens");
	}

}