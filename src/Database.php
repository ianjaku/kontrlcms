<?php

namespace invacto\SimpleCMS;

use Exception;
use Illuminate\Database\Capsule\Manager;
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

	/**
	 * @param string $query an SQL query with named parameters ex: "SELECT * FROM users WHERE id = :id"
     * @param array $parameters an array of parameter names to parameter values ex: [":id" => 1]
     * @return PDOStatement
     */
    public function query(string $query, array $parameters = [])
    {
        $this->ensureConnected();

        $stmt = $this->db->prepare($query);
        foreach ($parameters as $name => $value) {
            $stmt->bindValue($name, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    public function table($name) {
    	return Capsule::table($name);
	}

//    public function getDB() {
////    	$this->ensureConnected();
//    	return $this->db;
//	}

//    public function exists(string $query, array $parameters = [])
//    {
//        $stmt = $this->query($query, $parameters);
//        return $stmt->rowCount() > 0;
//    }
//
//    public function select(string $query, array $parameters = [])
//    {
//        $stmt = $this->query($query, $parameters);
//        return $stmt->fetchAll();
//    }
//
//    public function update(string $query, array $parameters = [])
//    {
//        $this->query($query, $parameters);
//    }
//
//    public function insert(string $query, array $parameters = [])
//    {
//        $this->query($query, $parameters);
//    }

    public function testConnection() {
        try {
            $this->connect();
            return true;
        } catch (Exception $e) {
            return false;
        }
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

    private function ensureConnected() {
        if ($this->db === null) {
            $this->connect();
        }
    }

//    public function doTablesExist() {
//        try {
//            $this->db->query("SELECT * FROM users LIMIT 1");
//            return true;
//        } catch (PDOException $e) {
//            return false;
//        }
//    }

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
