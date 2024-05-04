<?php

declare(strict_types=1);

namespace Steamy\Model;

/**
 * Stores location details
 */
class Location
{

    private ?string $street;
    private ?string $city;
    private ?int $district_id;
    private ?float $latitude;
    private ?float $longitude;

    public function __construct(
        ?string $street = null,
        ?string $city = null,
        ?int $district_id = null,
        ?float $latitude = null,
        ?float $longitude = null
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->district_id = $district_id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function toArray(): array
    {
        $arr = [];
        if (!empty($this->street)) {
            $arr['street'] = $this->street;
        }
        if (!empty($this->city)) {
            $arr['city'] = $this->city;
        }
        if (!empty($this->district)) {
            $arr['district_id'] = $this->district_id;
        }
        if (!is_null($this->latitude)) {
            $arr['latitude'] = $this->latitude;
        }
        if (!is_null($this->longitude)) {
            $arr['longitude'] = $this->longitude;
        }
        return $arr;
    }


    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getDistrictID(): ?int
    {
        return $this->district_id;
    }

    public function getDistrict(): ?District
    {
        if (empty($this->district_id)) {
            return null;
        }
        return District::getByID($this->district_id);
    }

    public function setDistrictID(int $district_id): void
    {
        $this->district_id = $district_id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getFormattedAddress(): string
    {
        return implode(", ", [$this->street, $this->city, District::getByID($this->district_id)->getName()]);
    }
}
