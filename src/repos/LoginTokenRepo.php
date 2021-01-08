<?php

namespace invacto\SimpleCMS\repos;


use Illuminate\Database\Capsule\Manager as Capsule;


class LoginTokenRepo
{

	public static function oneByToken($token) {
		return self::loginTokens()->where("token", $token)->first();
	}

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