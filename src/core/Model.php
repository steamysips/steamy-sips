<?php

trait Model
{
    use Database;

    protected string $table = 'user';
    protected int $limit = 10;
    protected int $offset = 0;
    public array $errors = [];


    public function all(): false|array
    {
        $query = "select * from $this->table";
        return $this->query($query);
    }

    public function where($data, $data_not = []): false|array
    {
        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "select * from $this->table where ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . " && ";
        }
        foreach ($keys_not as $key) {
            $query .= $key . " = :" . $key . " && ";
        }
        $query = trim($query, " && ");

        $query .= " limit $this->limit offset $this->offset";

        // echo $query;

        return $this->query($query, array_merge($data, $data_not));
    }

    public function first($data)
    {
    }

    public function insert($data): void
    {
        $keys = array_keys($data);
        $query = "insert into $this->table(" . join(", ", $keys) . ") ";
        $query .= "values (";

        foreach ($keys as $key) {
            $query .= ":" . $key . ", ";
        }
        $query = trim($query, ", ");

        $query .= ")";

        // echo $query;

        $this->query($query, $data);
    }

    public function update($id, $data, $id_column = 'id')
    {
    }

    public function delete($id, $id_column = 'id')
    {
        return false;
    }
}
