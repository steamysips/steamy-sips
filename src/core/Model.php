<?php

declare(strict_types=1);

namespace Steamy\Core;

use stdClass;

trait Model
{
    use Database;

    protected int $limit = 10; // restricts how many rows SQL query returns
    protected int $offset = 0; // number of rows to skip from the beginning of the returned data

    /**
     * Returns all records from a table, ignoring $limit.
     * @param string $table_name Name of table. Defaults to $this->table.
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
     * @param string $table_name Name of table. Defaults to $this->table.
     * @param array $data_not An associative array representing the conditions for the NOT part of the WHERE clause.
     *
     * @return false|array Returns false if the query execution fails, otherwise returns an array of the query results.
     */
    protected function where(array $data, string $table_name = "", array $data_not = []): false|array
    {
        $table_name = empty($table_name) ? $this->table : $table_name;

        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "SELECT * FROM $table_name WHERE ";

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
     * @param string $table_name Name of table. Defaults to $this->table.
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
     * Insert a record in the table
     * @param array $data An associative array representing the values to be inserted.
     * @param string $table_name Name of table. Defaults to $this->table.
     * @return void
     */
    protected function insert(array $data, string $table_name = ""): void
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $keys = array_keys($data);
        $query = "INSERT INTO $table_name(" . join(", ", $keys) . ") ";
        $query .= "VALUES (";

        // add placeholders to query
        foreach ($keys as $key) {
            $query .= ":" . $key . ", ";
        }

        // remove extra comma at the end of query
        $query = trim($query, ", ");

        $query .= ")";

        self::query($query, $data);
    }

    /**
     * Update a record in the table based on the provided ID.
     *
     * @param int|string $id The value of the primary key (ID) identifying the record to be updated.
     * @param array $data An associative array representing the columns and their new values to be updated.
     * @param string $table_name Name of table. Defaults to $this->table.
     * @param string $id_column The name of the ID column. Default is 'id'.
     *
     * @return void
     */
    protected function update(int|string $id, array $data, string $table_name, string $id_column = 'id'): void
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $keys = array_keys($data);
        $query = "UPDATE $table_name SET ";

        // add placeholders to query
        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . ",";
        }

        // remove extra comma at the end of query
        $query = trim($query, ", ");

        // add where condition
        $query .= " WHERE $id_column = $id;";

        self::query($query, $data);
    }

    /**
     * Delete a record from the table
     * @param mixed $id value of column name in WHERE clause.
     * @param string $table_name Name of table. Defaults to $this->table.
     * @param string $id_column_name primary key of table or name of column in WHERE clause.
     * @return void
     */
    protected function delete(mixed $id, string $table_name, string $id_column_name = 'id'): void
    {
        $table_name = empty($table_name) ? $this->table : $table_name;
        $query = "DELETE FROM $table_name WHERE $id_column_name = :id";
        self::query($query, array('id' => $id));
    }
}
