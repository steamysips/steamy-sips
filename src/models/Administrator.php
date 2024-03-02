<?php

namespace Steamy\Model;

class Administrator extends User
{
    protected string $table = 'administrator';
    private string $job_title;
    private bool $is_super_admin = false;

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        string $password,
        string $phone_no,
        string $job_title,
        bool $is_super_admin
    ) {
        parent::__construct($email, $first_name, $last_name, $password, $phone_no);
        $this->job_title = $job_title;
        $this->is_super_admin = $is_super_admin;
    }

    public function getJobTitle(): string
    {
        return $this->job_title;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function setJobTitle(string $job_title): void
    {
        $this->job_title = $job_title;
    }

    public function setSuperAdmin(string $is_super_admin): void
    {
        $this->is_super_admin = $is_super_admin;
    }

    public function toArray(): array
    {
        $base_array = parent::toArray();
        $base_array['job_title'] = $this->job_title;
        $base_array['is_super_admin'] = $this->is_super_admin ? 1 : 0;
        return $base_array;
    }
}