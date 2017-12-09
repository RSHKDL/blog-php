<?php

namespace Framework;

use Framework\Auth\UserInterface;

interface Auth
{


    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;
}
