<?php
namespace App\Account;

class User extends \App\Auth\User
{


    /*
     * @var string|null
     */
    private $firstname;

    /*
     * @var string|null
     */
    private $lastname;

    /*
     * @var string
     */
    private $role;


    /**
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string|null
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }
}
