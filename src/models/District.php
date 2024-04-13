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

    public static function getAll(): array
    {
        $districts = self::query("SELECT * FROM district");
        $districtNames = [];
        foreach ($districts as $district) {
            $districtNames[] = $district->name;
        }
        return $districtNames;
    }
}
