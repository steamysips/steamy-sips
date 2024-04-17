<?php

declare(strict_types=1);

namespace Steamy\Model;

use Steamy\Core\Model;

class District
{
    use Model;

    protected string $table = 'district';
    private int $district_id;
    private string $name;

    public function __construct(int $id, string $name)
    {
        $this->district_id = $id;
        $this->name = $name;
    }

    public static function getByID(int $district_id): ?District
    {
        if ($district_id < 0) {
            return null;
        }

        $record = self::query("SELECT * FROM district WHERE district_id = :id", ['id' => $district_id]);
        if (!$record) {
            return null;
        }
        return new District($record[0]->district_id, $record[0]->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getID(): int
    {
        return $this->district_id;
    }

    /**
     * Returns all districts from the database as an object
     * @return District[] Array of District objects
     */
    public static function getAll(): array
    {
        $results = self::query("SELECT * FROM district;");

        if (empty($results)) {
            return [];
        }

        $districts = [];
        foreach ($results as $district) {
            $districts[] = new District($district->district_id, $district->name);
        }

        return $districts;
    }
}
