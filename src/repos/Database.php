<?php

namespace invacto\SimpleCMS\repos;

use Exception;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;
use PDO;
use PDOException;
use PDOStatement;
use Illuminate\Database\Capsule\Manager as Capsule;


class Database {

    private $db = null;

    private $host = null;
    private $type = null;
    private $name = null;
    private $username = null;
    private $password = null;

    public function __construct($host, $name, $username, $password, $type = "mysql")
    {
		$this->type = $type;
		$this->host = $host;
		$this->name = $name;
		$this->username = $username;
		$this->password = $password;

		$this->connect();
	}

    public function table($name) {
    	return Capsule::table($name);
	}

    private function connect() {
		$this->db = new Manager();
		$this->db->addConnection([
			"driver" => $this->type,
			"host" => $this->host,
			"database" => $this->name,
			"username" => $this->username,
			"password" => $this->password,
			"charset"   => "utf8",
			"collation" => "utf8_unicode_ci",
			"prefix"    => "",
		]);
		$this->db->setAsGlobal();
    }

    public function createTables() {
    	Capsule::schema()->create("users", function (Blueprint $table) {
    		$table->increments("id");
    		$table->string("email")->unique();
    		$table->string("password");
    		$table->string("salt");
		});

    	Capsule::schema()->create("snippets", function (Blueprint $table) {
    		$table->increments("id");
    		$table->string("page")->index();
    		$table->string("name");
    		$table->text("value");
    		$table->timestamp("inserted_at")->index();
    		$table->timestamp("updated_at")->index();
		});

    	Capsule::schema()->create("user_tokens", function (Blueprint $table) {
    		$table->increments("id");
    		$table->string("token");
    		$table->timestamp("inserted_at");
		});

    	Capsule::schema()->create("login_tokens", function (Blueprint $table) {
    		$table->increments("id");
    		$table->string("token");
    		$table->unsignedInteger("user_id");
    		$table->foreign("user_id")->references("id")->on("users");
		});
	}
//    public function setup() {
//        $this->ensureConnected();
//        if ($this->doTablesExist()) {
//            return;
//        }
//        $this->query("
//            CREATE TABLE users (
//                id int NOT NULL AUTO_INCREMENT,
//                email varchar(255) NOT NULl,
//                password varchar(255) NOT NULl,
//                salt varchar(255) NOT NULL,
//                PRIMARY KEY (id)
//            );
//
//            CREATE TABLE snippets (
//                id int NOT NULL AUTO_INCREMENT,
//                page varchar(255) NOT NULL,
//                name varchar(255) NOT NULL,
//                value text NOT NULL,
//				  !Created at & Inserted at
//                PRIMARY KEY (id)
//            );
//			CREATE INDEX snippets_page ON snippets(page);
//
//            CREATE TABLE user_tokens (
//                id int NOT NULL AUTO_INCREMENT,
//                token varchar(255) NOT NULL,
//                inserted_at timestamp NOT NULL,
//                PRIMARY KEY (id)
//            );
//
//			CREATE TABLE login_tokens (
//			    id int NOT NULL AUTO_INCREMENT,
//			    user_id int NOT NULL,
//			    token varchar(255) NOT NULL,
//			    FOREIGN KEY (user_id) REFERENCES users(id)
//			);
//			CREATE INDEX login_tokens_user_id ON login_tokens(user_id);
//        ");
//    }

}
