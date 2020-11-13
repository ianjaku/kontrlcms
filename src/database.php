<?php

namespace invacto\SimpleCMS;

use PDO;
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
        $this->connectionString = "$type:host=$host;dbname=$name";
        $this->username = $username;
        $this->password = $password;
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

    private function connect() {
        $this->db = new PDO($this->connectionString, $this->username, $this->password);
    }

    private function ensureConnected() {
        if ($this->db === null) {
            $this->connect();
        }
    }

}
