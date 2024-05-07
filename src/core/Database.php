<?php

declare(strict_types=1);

namespace Steamy\Core;

use PDO;
use PDOException;
use stdClass;
use Steamy\Controller\Error;

trait Database
{

    /**
     * Connects to database and returns a PDO object
     * @return PDO
     */
    protected static function connect(): PDO
    {
        $string = "mysql:hostname=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        try {
            $conn = new PDO($string, DB_USERNAME, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            // if PHPUnit is not running, handle the exception
            if (!defined('PHPUNIT_STEAMY_TESTSUITE')) {
                (new Error())->index("Sorry, we're unable to process your request at the moment. Please try again later.");
                die();
            } else {
                // if PHPUnit is running, re-throw the exception to allow it to propagate
                throw $e;
            }
        }
    }

    /**
     * Queries database using a prepared statement
     * @param $query string prepared statement
     * @param $data array associative array
     * @param int $mode Controls the contents of the returned array as documented in PDOStatement::fetch.
     * Defaults to value of PDO::FETCH_OBJ.
     * @return false|array
     */
    protected static function query(string $query, array $data = [], int $mode = PDO::FETCH_OBJ): false|array
    {
        $con = self::connect();
        $stm = $con->prepare($query);
        $success = $stm->execute($data);

        if (!$success) {
            return false;
        }

        $result = $stm->fetchAll($mode);

        if ($result && count($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the first row after executing a prepared statement.
     * @param $query string prepared statement
     * @param array $data data for prepared statement
     * @return false|stdClass
     */
    protected static function get_row(string $query, array $data = []): false|stdClass
    {
        $result = self::query($query, $data);

        if ($result) {
            return $result[0];
        }
        return false;
    }
}
