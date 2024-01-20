<?php

namespace Steamy\Core;

use PDO;

trait Database
{
    private function connect(): PDO
    {
        $string = "mysql:hostname=" . DB_HOST . ";dbname=" . DB_NAME;
        return new PDO($string, DB_USERNAME, DB_PASSWORD);
    }

    public function query($query, $data = []): false|array
    {
        $con = $this->connect();
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

    public function get_row($query, $data = [])
    {
        $result = $this->query($query, $data);

        if ($result) {
            return $result[0];
        }
        return false;
    }
}
