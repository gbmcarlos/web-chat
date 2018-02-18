<?php

namespace DB;

/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/28/17
 * Time: 8:56 PM
 */
class SQLiteDatabase {

    /**
     * PDO Instance
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * @return \PDO
     */
    public function connect($file) {
        if (!$file) {
            throw new \Exception('You must specify a file path to store the SQLite database.');
        }
        if (!$this->pdo) {
            $this->pdo = new \PDO("sqlite:" . $file);
        }

        return $this->pdo;
    }

    /**
     * Creates the tables form an array of sql create statements
     *
     * @param $schema array
     */
    public function create($schema) {

        foreach ($schema as $command) {
            $this->pdo->exec($command);
        }

    }

    public function getPDO() {
        return $this->pdo;
    }

}