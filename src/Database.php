<?php

namespace invacto\SimpleCMS;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Database {

    /**
     * @var PDO
     */
    private $db = null;

    private $connectionString = null;
    private $username = null;
    private $password = null;

    public function __construct($host, $name, $username, $password, $type = "mysql")
    {
        $this->setConnectionData($host, $name, $username, $password, $type);
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

    public function exists(string $query, array $parameters = [])
    {
        $stmt = $this->query($query, $parameters);
        return $stmt->rowCount() > 0;
    }

    public function select(string $query, array $parameters = [])
    {
        $stmt = $this->query($query, $parameters);
        return $stmt->fetchAll();
    }

    public function update(string $query, array $parameters = [])
    {
        $this->query($query, $parameters);
    }

    public function insert(string $query, array $parameters = [])
    {
        $this->query($query, $parameters);
    }

    public function setConnectionData($host, $name, $username, $password, $type = "mysql") {
        $this->connectionString = "$type:host=$host;dbname=$name";
        $this->username = $username;
        $this->password = $password;
    }

    public function testConnection() {
        try {
            $this->connect();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function connect() {
        $this->db = new PDO($this->connectionString, $this->username, $this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    private function ensureConnected() {
        if ($this->db === null) {
            $this->connect();
        }
    }

    public function doTablesExist() {
        try {
            $this->db->query("SELECT * FROM users LIMIT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function setup() {
        $this->ensureConnected();
        if ($this->doTablesExist()) {
            return;
        }
        $this->query("
            CREATE TABLE users (
                id int NOT NULL AUTO_INCREMENT,
                email varchar(255) NOT NULl,
                password varchar(255) NOT NULl,
                salt varchar(255) NOT NULL,
                PRIMARY KEY (id)
            );

            CREATE TABLE snippets (
                id int NOT NULL AUTO_INCREMENT,
                page varchar(255) NOT NULL,
                name varchar(255) NOT NULL,
                value text NOT NULL,
                PRIMARY KEY (id)
            );
			CREATE INDEX snippets_page ON snippets(page);

            CREATE TABLE user_tokens (
                id int NOT NULL AUTO_INCREMENT,
                token varchar(255) NOT NULL,
                inserted_at timestamp NOT NULL,
                PRIMARY KEY (id)
            );

			CREATE TABLE login_tokens (
			    id int NOT NULL AUTO_INCREMENT,
			    user_id int NOT NULL,
			    token varchar(255) NOT NULL,
			    FOREIGN KEY (user_id) REFERENCES users(id)
			);
			CREATE INDEX login_tokens_user_id ON login_tokens(user_id);
        ");
    }

}
