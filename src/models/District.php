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
     * @param bool $sanitizeAttributes Whether the attributes should be sanitized. Set this to true if data will
     * be displayed on browser.
     * @return District[] Array of District objects
     */
    public static function getAll(bool $sanitizeAttributes = false): array
    {
        $results = self::query("SELECT * FROM district;");

        if (empty($results)) {
            return [];
        }

        $districts = [];
        foreach ($results as $district) {
            if ($sanitizeAttributes) {
                $districts[] = new District (
                    (int)filter_var($district->district_id, FILTER_SANITIZE_NUMBER_INT),
                    htmlspecialchars($district->name)
                );
            } else {
                $districts[] = new District($district->district_id, $district->name);
            }
        }

        return $districts;
    }
}
