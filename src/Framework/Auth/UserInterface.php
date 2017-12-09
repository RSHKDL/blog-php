<?php

namespace Framework\Auth;

interface UserInterface
{


    /**
     * @return string
     */
    public function getUsername(): string;


    /**
     * @return string[]
     */
    public function getRoles(): array;
}
