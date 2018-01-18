<?php

namespace App\Blog\Entity;

class Comment
{

    public $id;

    public $content;

    public $createdAt;


    public function setCreatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->createdAt = new \DateTime($datetime);
        }
    }
}
