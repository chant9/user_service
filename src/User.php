<?php

namespace Chant9\UserService;

use JsonSerializable;

class User implements JsonSerializable
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $job;

    public function __construct(int $id, string $firstName, string $lastName, string $email = '', string $job = '')
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->job = $job;
    }

    /**
     * Return ID property.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Return first name property.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Return last name property.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Return first and last names properties concatenated.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim("{$this->firstName} {$this->lastName}");
    }

    /**
     * Return email property.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Return job property.
     *
     * @return string
     */
    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * Return all the properties in JSON form.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'job' => $this->job,
        ];
    }
}
