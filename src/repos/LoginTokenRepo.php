<?php

namespace invacto\SimpleCMS\repos;


use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;


class LoginTokenRepo
{

	/**
	 * Returns a single loginToken by token, null if missing
	 *
	 * @param $token
	 * @return Model|Builder|object|null
	 */
	public static function oneByToken($token) {
		return self::loginTokens()->where("token", $token)->first();
	}

	/**
	 * Creates a new loginToken
	 *
	 * @param $token
	 * @param $userId
	 * @return bool
	 */
	public static function create($token, $userId) {
		return self::loginTokens()->insert([
			"token" => $token,
			"user_id" => $userId
		]);
	}

	private static function loginTokens() {
		return Capsule::table("login_tokens");
	}

}