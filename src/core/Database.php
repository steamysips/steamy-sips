<?php

declare(strict_types=1);

namespace Steamy\Core;

use PDO;
use PDOException;
use stdClass;

trait Database
{

    /**
     * Connects to database and returns a PDO object
     * @return PDO
     */
    private static function connect(): PDO
    {
        $string = "mysql:hostname=" . DB_HOST . ";dbname=" . DB_NAME;
        $pdo_object = new PDO($string, DB_USERNAME, DB_PASSWORD);

        try {
        } catch (PDOException $e) {
            // TODO: Create a page to display the error
            Utility::show(
                $e
            );
            die();
        }
        return $pdo_object;
    }

    /**
     * Executes a prepared statement
     * @param $query string prepared statement
     * @param $data array associative array
     * @return false|array
     */
    protected static function query(string $query, array $data = []): false|array
    {
        $con = self::connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);

        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);

            if ($result && count($result)) {
                return $result;
            }
        }
        return false;
    }

    /**
     * Returns the first row after executing a prepared statement.
     * @param $query string prepared statement
     * @param array $data data for prepared statement
     * @return false|stdClass
     */
    protected static function get_row(string $query, array $data = []): false|\stdClass
    {
        $result = self::query($query, $data);

        if ($result) {
            return $result[0];
        }
        return false;
    }
}
