<?php

namespace Steamy\Core;

trait Model
{
    use Database;

    protected int $limit = 10; // restricts how many rows SQL query returns
    protected int $offset = 0; // number of rows to skip from the beginning of the returned data

    /**
     * Returns all records from a table, ignoring $limit.
     * @return false|array
     */
    protected function all(): false|array
    {
        $query = "SELECT * FROM $this->table";
        return self::query($query);
    }

    /**
     * Build and execute a SELECT query based on the provided conditions.
     *
     * @param array $data An associative array representing the conditions for the WHERE clause.
     * @param array $data_not An associative array representing the conditions for the NOT part of the WHERE clause.
     *
     * @return false|array Returns false if the query execution fails, otherwise returns an array of the query results.
     */
    protected function where(array $data, array $data_not = []): false|array
    {
        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "SELECT * FROM $this->table WHERE ";

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
     * @return \stdClass|null
     */
    protected function first(array $data): ?\stdClass
    {
        $result = $this->where($data);

        if (!$result) {
            return null;
        }

        return count($result) > 0 ? $result [0] : null;
    }

    /**
     * Insert a record in the table
     * @param array $data An associative array representing the values to be inserted.
     * @return void
     */
    protected function insert(array $data): void
    {
        $keys = array_keys($data);
        $query = "INSERT INTO $this->table(" . join(", ", $keys) . ") ";
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
     * @param string $id_column The name of the ID column. Default is 'id'.
     *
     * @return void
     */
    protected function update(int|string $id, array $data, string $id_column = 'id'): void
    {
        $keys = array_keys($data);
        $query = "UPDATE $this->table SET ";

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
     * @param string $id_column primary key of table or name of column in WHERE clause.
     * @return void
     */
    protected function delete(mixed $id, string $id_column = 'id'): void
    {
        $query = "DELETE FROM $this->table WHERE $id_column = :id";
        self::query($query, array($id_column => $id));
    }
}
