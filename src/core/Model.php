<?php

declare(strict_types=1);

namespace Steamy\Core;

use stdClass;

/**
 * Trait for all models. It contains some helpful functions for executing queries.
 *
 * Note: Backticks are automatically inserted for table names
 * because some table names such as order are reserved MySQL keywords.
 * Ref: https://dev.mysql.com/doc/refman/8.0/en/keywords.html
 */
trait Model
{
    use Database;

    protected int $limit = 10; // restricts how many rows SQL query returns
    protected int $offset = 0; // number of rows to skip from the beginning of the returned data

    /**
     * Returns all records from a table, ignoring $limit.
     * @param string $table_name Name of table without backticks. Defaults to $this->table.
     * @return false|array
     */
    protected function all(string $table_name = ""): false|array
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $query = "SELECT * FROM $table_name";
        return self::query($query);
    }

    /**
     * Build and execute a SELECT query based on the provided conditions.
     *
     * @param array $data An associative array representing the conditions for the WHERE clause.
     * @param string $table_name Name of table without backticks. Defaults to $this->table.
     * @param array $data_not An associative array representing the conditions for the NOT part of the WHERE clause.
     *
     * @return false|array Returns false if the query execution fails, otherwise returns an array of the query results.
     */
    protected function where(array $data, string $table_name = "", array $data_not = []): false|array
    {
        $table_name = empty($table_name) ? $this->table : $table_name;

        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "SELECT * FROM `$table_name` WHERE ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . " && ";
        }

        foreach ($keys_not as $key) {
            $query .= $key . " = :" . $key . " && ";
        }
        /** @noinspection PhpDuplicatedCharacterInStrFunctionCallInspection */
        $query = trim($query, " && ");

        $query .= " limit $this->limit offset $this->offset";

        return self::query($query, array_merge($data, $data_not));
    }

    /**
     * Returns the first result from an executed SELECT query
     * @param array $data
     * @param string $table_name Name of table without backticks. Defaults to $this->table.
     * @return stdClass|null
     */
    protected function first(array $data, string $table_name = ""): stdClass|null
    {
        $result = $this->where($data, $table_name);

        if (!$result) {
            return null;
        }

        return count($result) > 0 ? $result [0] : null;
    }

    /**
     * Insert a record in a table
     * @param array $data An associative array representing the values to be inserted.
     * @param string $table_name Name of table without backticks. Defaults to $this->table.
     * @return int|null ID of inserted record.
     */
    protected function insert(array $data, string $table_name = ""): ?int
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $keys = array_keys($data);

        // build query with placeholders for prepared statement
        $query = "INSERT INTO `$table_name` (" . join(", ", $keys) . ") ";
        $query .= "VALUES (";

        // add placeholders to query
        foreach ($keys as $key) {
            $query .= ":" . $key . ", ";
        }

        // remove extra comma at the end of query
        $query = trim($query, ", ");

        $query .= ")";

        $con = self::connect();
        $stm = $con->prepare($query);
        $stm->execute($data);

        $lastInsertID = $con->lastInsertId();
        $con = null;

        return empty($lastInsertID) ? null : (int)$lastInsertID;
    }


    /**
     * @param array $new_data Associative array for SET part of query.
     * @param array $condition Associative array representing WHERE condition of query.
     * @param string $table_name Defaults to $this->table.
     * @return bool True on success.
     */
    protected function update(array $new_data, array $condition, string $table_name = ""): bool
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $query = "UPDATE `$table_name` SET ";

        // add placeholders to query
        foreach (array_keys($new_data) as $key) {
            $query .= $key . " = :" . $key . ",";
        }
        $query = trim($query, ", "); // remove extra comma at the end of query

        // add conditions
        $query .= " WHERE ";
        foreach (array_keys($condition) as $key) {
            $query .= $key . " = :" . $key . ",";
        }
        $query = trim($query, ", "); // remove extra comma at the end of query

        $conn = self::connect();
        $stm = $conn->prepare($query);

        return $stm->execute([...$new_data, ...$condition]);
    }

    /**
     * Delete a record from the table
     * @param mixed $id value of column name in WHERE clause.
     * @param string $table_name Name of table without backticks. Defaults to $this->table.
     * @param string $id_column_name primary key of table or name of column in WHERE clause.
     * @return bool Success or not
     */
    protected function delete(mixed $id, string $table_name, string $id_column_name = 'id'): bool
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $query = "DELETE FROM `$table_name` WHERE $id_column_name = :id";

        $con = self::connect();
        $stm = $con->prepare($query);
        $success = $stm->execute(['id' => $id]);
        $con = null;

        return $success;
    }
}
