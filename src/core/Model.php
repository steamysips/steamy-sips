<?php

trait Model
{
    use Database;
    protected $table = 'user';
    protected $limit = 10;
    protected $offset = 0;
    public $errors = [];


    public function all()
    {
        $query = "select * from $this->table";
        return $this->query($query);
    }

    public function where($data, $data_not = [])
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

    public function insert($data)
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

        return false;
    }

    public function update($id, $data, $id_column = 'id')
    {
    }

    public function delete($id, $id_column = 'id')
    {
        return false;
    }
}
