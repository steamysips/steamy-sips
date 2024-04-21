<?php

declare(strict_types=1);

namespace Steamy\Model;

class Location
{
    private ?string $street;
    private ?string $city;
    private ?District $district;
    private ?float $latitude;
    private ?float $longitude;

    public function __construct(
        ?string $street,
        ?string $city,
        ?District $district,
        ?float $latitude,
        ?float $longitude
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->district = $district;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function toArray(): string
    {
        return "";
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

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(District $district): void
    {
        $this->district = $district;
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
}
