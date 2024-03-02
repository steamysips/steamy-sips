<?php

namespace Steamy\Model;

use Steamy\Core\Utility;

class Client extends User
{
    protected string $table = 'client';

    private District $district;

    private string $street;

    private string $city;

    public static function getByID(int $id)
    {
        $query = <<<EOL
        SELECT * FROM user, administrator
        WHERE user.user_id = administrator.user_id
        EOL;

        $query .= " AND user_id = $id;";

//        $result = self::query($query);
        Utility::show($query);
    }

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $password,
        string $phone_no,
        District $district,
        string $street,
        string $city
    ) {
        parent::__construct($email, $first_name, $last_name, $password, $phone_no);
        $this->district = $district;
        $this->street = $street;
        $this->city = $city;
    }

    public function toArray(): array
    {
        $base_array = parent::toArray();
        $base_array['district'] = $this->district->getName();
        $base_array['street'] = $this->street;
        $base_array['city'] = $this->city;

        return $base_array;
    }
    // TODO: Add getters + setters
}