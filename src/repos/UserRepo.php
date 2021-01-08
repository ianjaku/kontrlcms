<?php

namespace invacto\SimpleCMS\repos;


use Illuminate\Database\Capsule\Manager as Capsule;

class UserRepo
{

	public static function all() {
		return self::users()->get();
	}

	public static function oneByEmail($email) {
		return self::users()->where("email", $email)->first();
	}

	public static function oneById($id) {
		return self::users()->where("id", $id)->first();
	}

	public static function createUser($email, $password, $salt) {
		return self::users()->insert([
			"email" => $email,
			"password" => $password,
			"salt" => $salt
		]);
	}

	public static function countAll() {
		return self::users()->count();
	}

	private static function users() {
		return Capsule::table("users");
	}

}