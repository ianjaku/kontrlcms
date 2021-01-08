<?php

namespace invacto\SimpleCMS\repos;

use Illuminate\Database\Capsule\Manager as Capsule;


class UserTokenRepo
{

	public static function create($token) {
		return self::userTokens()->insert([
			"token" => $token,
			"inserted_at" => (new \DateTime())->format("Y-m-d\TH:i:s.u")
		]);
	}

	public static function tokenExists($token) {
		return self::userTokens()->where("token", $token)->exists();
	}

	public static function removeToken($token) {
		return self::userTokens()->where("token", $token)->delete();
	}

	private static function userTokens() {
		return Capsule::table("user_tokens");
	}

}