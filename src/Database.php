<?php 

class Database {

    protected PDO $pdo;

    public function __construct() {

        $driver = $_ENV['DB_DRIVER'];
        $host   = $_ENV['DB_HOST'];
        $user   = $_ENV['DB_USER'];
        $pass   = $_ENV['DB_PASS'];
        $dbname = $_ENV['DB_NAME'];
        $dbport = $_ENV['DB_PORT'];
        $dbchar = $_ENV['DB_CHAR'];

        $dsn = "{$driver}:host={$host};dbname={$dbname};port={$dbport};charset={$dbchar}";

        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
        $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    }

    public function getConnection() {

        if ( $this->pdo !== null ) return $this->pdo;

        echo 'No connection to database has been made';
        exit;

    }

}