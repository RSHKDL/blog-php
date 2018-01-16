<?php

namespace App\Auth;

use Framework\Auth;
use Framework\Auth\UserInterface;
use Framework\Database\NoRecordException;
use Framework\Session\SessionInterface;

class DatabaseAuth implements Auth
{


    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var User
     */
    private $user;


    public function __construct(UserTable $userTable, SessionInterface $session)
    {
        $this->userTable = $userTable;
        $this->session = $session;
    }


    /**
     * Log in an user
     *
     * @param string $username
     * @param string $password
     * @return UserInterface|null
     * @throws NoRecordException
     */
    public function login(string $username, string $password): ?UserInterface
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        /** @var User $user */
        $user = $this->userTable->findBy('username', $username);
        if ($user && password_verify($password, $user->password)) {
            $this->setUser($user);
            return $user;
        }
        return null;
    }


    /**
     *  Log out an user
     */
    public function logout(): void
    {
        $this->session->delete('auth.user');
    }


    /**
     * Get an user
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if ($this->user) {
            return $this->user;
        }

        $userId = $this->session->get('auth.user');
        if ($userId) {
            try {
                $this->user = $this->userTable->find($userId);
                return $this->user;
            } catch (NoRecordException $exception) {
                $this->session->delete('auth.user');
                return null;
            }
        }
        return null;
    }


    /**
     * Define an user
     *
     * @param User $user
     */
    public function setUser(\App\Auth\User $user): void
    {
        $this->session->set('auth.user', $user->id);
        $this->user = $user;
    }
}
