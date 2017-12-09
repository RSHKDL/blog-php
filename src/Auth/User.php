<?php

namespace App\Auth;

use Framework\Auth\UserInterface;

class User implements UserInterface
{


    public $id;

    public $username;

    public $email;

    public $password;


    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return [];
    }
}
